<?php
/**
 * Plugin Name: TSO-Tabla-Liga
 * Description: Widget Tabla de Clasificación de la Liga LFP (ESPN API, caché 1h)
 * Plugin URI:  https://tusoporteonline.es
 * Version:     1.6
 * Author:      Tu Soporte Online
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Tested up to: 6.9
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* CSS */
add_action( 'wp_head', function() {
    echo '<style id="tso-clas-css">
.tso-clas-wrap{width:100%;overflow-x:auto;}
.tso-clas-loading{color:#888;font-size:12px;padding:10px 0;text-align:center;}
table.tso-clas-table{
    display:table !important;
    width:100% !important;
    max-width:100% !important;
    border-collapse:collapse !important;
    font-size:12px !important;
    color:#ddd !important;
    table-layout:auto !important;
}
table.tso-clas-table thead,
table.tso-clas-table tbody{display:table-row-group !important;}
table.tso-clas-table thead tr,
table.tso-clas-table tbody tr{display:table-row !important;background:transparent;}
table.tso-clas-table thead tr{background:#1a1a1a !important;}
table.tso-clas-table thead th{
    display:table-cell !important;
    color:#fff !important;
    font-weight:bold !important;
    padding:6px 4px !important;
    text-align:center !important;
    border-bottom:2px solid #d6993a !important;
    white-space:nowrap !important;
    width:auto !important;
    max-width:none !important;
    margin:0 !important;
}
table.tso-clas-table tbody td{
    display:table-cell !important;
    padding:5px 4px !important;
    text-align:center !important;
    color:#ddd !important;
    border-bottom:1px solid #2e2e2e !important;
    width:auto !important;
    max-width:none !important;
    margin:0 !important;
}
table.tso-clas-table tbody tr:hover{background:#303030 !important;}
table.tso-clas-table td.tso-col-team{text-align:left !important;white-space:nowrap !important;}
table.tso-clas-table td.tso-col-pos{width:20px !important;text-align:center !important;}
table.tso-clas-table td.tso-col-pts{
    background:#d6993a !important;
    color:#fff !important;
    font-weight:bold !important;
    font-size:13px !important;
}
.tso-clas-legend{font-size:10px;color:#888;margin:6px 0 0;text-align:center;}
</style>';
} );

/* Converteix URL de logo ESPN al combiner optimitzat */
function tso_espn_logo_url( $href, $size = 40 ) {
    if ( empty( $href ) ) return '';
    if ( strpos( $href, 'espncdn.com/combiner' ) !== false ) return $href;
    if ( preg_match( '#espncdn\.com(/i/.+)#', $href, $m ) ) {
        return 'https://a.espncdn.com/combiner/i?img=' . rawurlencode( $m[1] )
               . '&h=' . intval( $size ) . '&w=' . intval( $size );
    }
    return $href;
}

/* Obtenir dades amb caché */
function tso_get_laliga_standings() {
    $cached = get_transient( 'tso_laliga_standings' );
    if ( $cached !== false ) return $cached;

    $response = wp_remote_get( 'https://site.api.espn.com/apis/v2/sports/soccer/esp.1/standings', array(
        'timeout' => 8,
    ) );

    if ( is_wp_error( $response ) ) return array();

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $data['children'][0]['standings']['entries'] ) ) return array();

    $teams = array();
    $pos   = 1;
    foreach ( $data['children'][0]['standings']['entries'] as $entry ) {
        $stats = array();
        foreach ( $entry['stats'] as $stat ) {
            if ( ! isset( $stat['name'] ) ) continue;
            $stats[ $stat['name'] ] = intval( $stat['value'] ?? 0 );
        }
        $name = '';
        if ( ! empty( $entry['team']['displayName'] ) )      $name = $entry['team']['displayName'];
        elseif ( ! empty( $entry['team']['name'] ) )         $name = $entry['team']['name'];
        elseif ( ! empty( $entry['team']['abbreviation'] ) ) $name = $entry['team']['abbreviation'];
        else $name = '?';

        $teams[] = array(
            'pos'  => $pos++,
            'name' => $name,
            'logo' => tso_espn_logo_url( isset( $entry['team']['logos'][0]['href'] ) ? $entry['team']['logos'][0]['href'] : '' ),
            'pj'   => isset( $stats['gamesPlayed'] ) ? $stats['gamesPlayed'] : 0,
            'g'    => isset( $stats['wins'] )        ? $stats['wins']        : 0,
            'e'    => isset( $stats['ties'] )        ? $stats['ties']        : 0,
            'p'    => isset( $stats['losses'] )      ? $stats['losses']      : 0,
            'pts'  => isset( $stats['points'] )      ? $stats['points']      : 0,
        );
    }

    set_transient( 'tso_laliga_standings', $teams, 1 * HOUR_IN_SECONDS );
    return $teams;
}

/* Genera el HTML de la taula (reutilitzat per widget i AJAX) */
function tso_render_tabla( $teams ) {
    if ( empty( $teams ) ) {
        return '<p style="color:#aaa;font-size:12px;margin:0">No se pudieron cargar los datos.</p>';
    }

    $html  = '<div class="tso-clas-wrap">';
    $html .= '<table class="tso-clas-table" cellspacing="0" cellpadding="0">';
    $html .= '<thead><tr>';
    $html .= '<th class="tso-col-pos">#</th>';
    $html .= '<th class="tso-col-team">Equipo</th>';
    $html .= '<th>PJ</th>';
    $html .= '<th style="background:#d6993a !important;color:#fff !important;">Pts</th>';
    $html .= '<th>G</th><th>E</th><th>P</th>';
    $html .= '</tr></thead><tbody>';

    foreach ( $teams as $i => $t ) {
        if ( $t['pos'] <= 4 )      $col = '#4da3ff';
        elseif ( $t['pos'] <= 6 )  $col = '#f39c12';
        elseif ( $t['pos'] >= 18 ) $col = '#e74c3c';
        else                       $col = '#bbb';

        $bg = ( $i % 2 === 0 ) ? '#2a2a2a' : '#222';

        $html .= '<tr style="background:' . esc_attr( $bg ) . ' !important">';
        $html .= '<td class="tso-col-pos" style="color:' . esc_attr( $col ) . ' !important;font-weight:bold">' . intval( $t['pos'] ) . '</td>';
        $html .= '<td class="tso-col-team" style="color:' . esc_attr( $col ) . ' !important;font-weight:' . ( '#bbb' !== $col ? 'bold' : 'normal' ) . '">';
        if ( $t['logo'] ) {
            $html .= '<img src="' . esc_url( $t['logo'] ) . '" alt="" width="14" height="14" style="vertical-align:middle;margin-right:5px;object-fit:contain">';
        }
        $html .= esc_html( $t['name'] );
        $html .= '</td>';
        $html .= '<td>' . intval( $t['pj'] ) . '</td>';
        $html .= '<td class="tso-col-pts">' . intval( $t['pts'] ) . '</td>';
        $html .= '<td>' . intval( $t['g'] ) . '</td>';
        $html .= '<td>' . intval( $t['e'] ) . '</td>';
        $html .= '<td>' . intval( $t['p'] ) . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody></table>';
    $html .= '<p class="tso-clas-legend">';
    $html .= '<span style="color:#4da3ff">&#9632;</span> Champions &nbsp;';
    $html .= '<span style="color:#f39c12">&#9632;</span> Europa &nbsp;';
    $html .= '<span style="color:#e74c3c">&#9632;</span> Descenso';
    $html .= '</p>';
    $html .= '</div>';

    return $html;
}

/* AJAX endpoint — accessible sense login (wp_ajax_nopriv) per a visitants */
add_action( 'wp_ajax_tso_get_tabla',        'tso_ajax_get_tabla' );
add_action( 'wp_ajax_nopriv_tso_get_tabla', 'tso_ajax_get_tabla' );
function tso_ajax_get_tabla() {
    check_ajax_referer( 'tso_tabla_nonce', 'nonce' );
    $teams = tso_get_laliga_standings();
    wp_send_json_success( tso_render_tabla( $teams ) );
}

/* Enqueue script AJAX al frontend */
add_action( 'wp_enqueue_scripts', function() {
    wp_register_script(
        'tso-tabla-ajax',
        '',   // script inline, sense fitxer extern
        array( 'jquery' ),
        '1.6',
        true  // al footer
    );
    wp_enqueue_script( 'tso-tabla-ajax' );
    wp_add_inline_script( 'tso-tabla-ajax', '
jQuery(function($){
    $(".tso-tabla-ajax-wrap").each(function(){
        var $wrap = $(this);
        $.post(' . wp_json_encode( admin_url( 'admin-ajax.php' ) ) . ', {
            action: "tso_get_tabla",
            nonce:  ' . wp_json_encode( wp_create_nonce( 'tso_tabla_nonce' ) ) . '
        }, function(res){
            if(res.success){
                $wrap.html(res.data);
            } else {
                $wrap.html("<p style=\"color:#aaa;font-size:12px\">Error carregant dades.</p>");
            }
        });
    });
});
    ' );
} );

/* Widget */
class TSO_Clasificacion_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'tso_clasificacion_widget',
            'TSO-Tabla-Liga',
            array( 'description' => 'TSO-Tabla-Liga en temps real' )
        );
    }

    public function widget( $args, $instance ) {
        $show_title = isset( $instance['show_title'] ) ? (bool) $instance['show_title'] : false;
        $title      = empty( $instance['title'] ) ? 'TSO-Tabla-Liga' : $instance['title'];

        echo wp_kses_post( $args['before_widget'] );

        if ( $show_title ) {
            echo wp_kses_post( $args['before_title'] ) . esc_html( $title ) . wp_kses_post( $args['after_title'] );
        }

        // Contenidor buit — JavaScript carregarà la taula via AJAX
        // Bypassa la caché de pàgina (LiteSpeed, W3TC, etc.)
        echo '<div class="tso-tabla-ajax-wrap"><p class="tso-clas-loading">Carregant classificaci&oacute;...</p></div>';

        echo wp_kses_post( $args['after_widget'] );
    }

    public function form( $instance ) {
        $show_title = isset( $instance['show_title'] ) ? (bool) $instance['show_title'] : false;
        $title      = empty( $instance['title'] ) ? 'TSO-Tabla-Liga' : $instance['title'];
        ?>
        <p>
            <input type="checkbox"
                   id="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'show_title' ) ); ?>"
                   value="1"
                   <?php checked( $show_title ); ?>>
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>">Mostrar t&iacute;tol</label>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">T&iacute;tol:</label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                   type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php?action=tso_clear_standings_cache' ), 'tso_clear_cache' ) ); ?>"
               style="color:#d6993a;font-size:12px;">&#x1F504; Netejar cach&eacute;</a>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        return array(
            'title'      => sanitize_text_field( $new_instance['title'] ),
            'show_title' => isset( $new_instance['show_title'] ) ? 1 : 0,
        );
    }
}

add_action( 'widgets_init', function() {
    register_widget( 'TSO_Clasificacion_Widget' );
} );

add_action( 'wp_ajax_tso_clear_standings_cache', function() {
    check_admin_referer( 'tso_clear_cache' );
    delete_transient( 'tso_laliga_standings' );
    wp_safe_redirect( admin_url( 'widgets.php' ) );
    exit;
} );

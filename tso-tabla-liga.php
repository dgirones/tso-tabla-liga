<?php
/**
 * Plugin Name:       TSO-Tabla-Liga
 * Description:       La Liga standings widget. Data from the ESPN API, cached for one hour.
 * Plugin URI:        https://tusoporteonline.es
 * Version:           1.8.1
 * Author:            Tu Soporte Online
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tso-tabla-liga
 * Domain Path:       /languages
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Tested up to:      7.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TSOTL_FILE', __FILE__ );
define( 'TSOTL_PATH', plugin_dir_path( __FILE__ ) );
define( 'TSOTL_VERSION', '1.8.1' );

/**
 * Load the bundled Catalan or Spanish catalog when WordPress has no language pack.
 */
function tsotl_load_textdomain() {
	$domain = 'tso-tabla-liga';
	$locale = determine_locale();

	if ( 'en_US' === $locale || 'en' === $locale ) {
		return;
	}

	$pack = WP_LANG_DIR . '/plugins/' . $domain . '-' . $locale . '.mo';
	if ( is_readable( $pack ) ) {
		return;
	}

	$candidates = array( $locale );
	if ( 0 === strpos( $locale, 'ca' ) && 'ca' !== $locale ) {
		$candidates[] = 'ca';
	}
	if ( 0 === strpos( $locale, 'es' ) && 'es_ES' !== $locale ) {
		$candidates[] = 'es_ES';
	}

	foreach ( array_unique( $candidates ) as $try_locale ) {
		$mofile = TSOTL_PATH . 'languages/' . $domain . '-' . $try_locale . '.mo';
		if ( is_readable( $mofile ) ) {
			load_textdomain( $domain, $mofile );
			return;
		}
	}
}
add_action( 'init', 'tsotl_load_textdomain', 1 );

/* CSS */
add_action( 'wp_head', function() {
    echo '<style id="tso-clas-css">
.tso-clas-wrap,.tso-tabla-ajax-wrap{
    --tso-clas-text:#ddd;
    --tso-clas-muted:#888;
    --tso-clas-head-bg:#1a1a1a;
    --tso-clas-head-text:#fff;
    --tso-clas-border:#2e2e2e;
    --tso-clas-row-a:#2a2a2a;
    --tso-clas-row-b:#222;
    --tso-clas-hover:#303030;
    --tso-clas-champ:#4da3ff;
    --tso-clas-europa:#f39c12;
    --tso-clas-conference:#2ecc71;
    --tso-clas-down:#e74c3c;
    --tso-clas-neutral:#bbb;
    --tso-clas-accent:var(--tso-color-accent,#d6993a);
    --tso-clas-accent-text:var(--tso-color-accent-contrast,#fff);
}
/* Modo día del tema TSO Blog (html[data-theme="light"]): tabla clara en tarjeta blanca, como el resto de widgets. */
html[data-theme="light"] .tso-clas-wrap,
html[data-theme="light"] .tso-tabla-ajax-wrap{
    --tso-clas-text:#333;
    --tso-clas-muted:#666;
    --tso-clas-head-bg:#f1f1f1;
    --tso-clas-head-text:#222;
    --tso-clas-border:#e0e0e0;
    --tso-clas-row-a:#fff;
    --tso-clas-row-b:#f7f7f7;
    --tso-clas-hover:#eaf1f8;
    --tso-clas-champ:#1e73be;
    --tso-clas-europa:#a85a00;
    --tso-clas-conference:#1a7a45;
    --tso-clas-down:#c0392b;
    --tso-clas-neutral:#444;
}
html[data-theme="light"] .tso-tabla-ajax-wrap{
    background:var(--tso-clas-row-a);
    border:1px solid var(--tso-clas-border);
    border-radius:4px;
    padding:0 0 8px;
}
.tso-clas-wrap{width:100%;overflow-x:auto;}
.tso-clas-loading{color:var(--tso-clas-muted);font-size:12px;padding:10px 0;text-align:center;}
.tso-clas-error{color:var(--tso-clas-muted);font-size:12px;margin:0;}
table.tso-clas-table{
    display:table !important;
    width:100% !important;
    max-width:100% !important;
    border-collapse:collapse !important;
    font-size:12px !important;
    color:var(--tso-clas-text) !important;
    table-layout:auto !important;
}
table.tso-clas-table thead,
table.tso-clas-table tbody{display:table-row-group !important;}
table.tso-clas-table thead tr,
table.tso-clas-table tbody tr{display:table-row !important;background:transparent;}
table.tso-clas-table thead tr{background:var(--tso-clas-head-bg) !important;}
table.tso-clas-table thead th{
    display:table-cell !important;
    color:var(--tso-clas-head-text) !important;
    font-weight:bold !important;
    padding:6px 4px !important;
    text-align:center !important;
    border-bottom:2px solid var(--tso-clas-accent) !important;
    white-space:nowrap !important;
    width:auto !important;
    max-width:none !important;
    margin:0 !important;
}
table.tso-clas-table tbody td{
    display:table-cell !important;
    padding:5px 4px !important;
    text-align:center !important;
    color:var(--tso-clas-text) !important;
    border-bottom:1px solid var(--tso-clas-border) !important;
    width:auto !important;
    max-width:none !important;
    margin:0 !important;
}
table.tso-clas-table tbody tr.tso-row-a{background:var(--tso-clas-row-a) !important;}
table.tso-clas-table tbody tr.tso-row-b{background:var(--tso-clas-row-b) !important;}
table.tso-clas-table tbody tr:hover{background:var(--tso-clas-hover) !important;}
table.tso-clas-table td.tso-zone-champ{color:var(--tso-clas-champ) !important;font-weight:bold !important;}
table.tso-clas-table td.tso-zone-europa{color:var(--tso-clas-europa) !important;font-weight:bold !important;}
table.tso-clas-table td.tso-zone-conference{color:var(--tso-clas-conference) !important;font-weight:bold !important;}
table.tso-clas-table td.tso-zone-down{color:var(--tso-clas-down) !important;font-weight:bold !important;}
table.tso-clas-table td.tso-zone-neutral{color:var(--tso-clas-neutral) !important;}
table.tso-clas-table td.tso-col-pos{font-weight:bold !important;}
table.tso-clas-table td.tso-zone-neutral.tso-col-team{font-weight:normal !important;}
table.tso-clas-table td.tso-col-team{text-align:left !important;white-space:nowrap !important;}
table.tso-clas-table td.tso-col-team img{
    width:14px !important;
    height:14px !important;
    max-width:14px !important;
    display:inline !important;
    vertical-align:middle !important;
    margin:0 5px 0 0 !important;
    object-fit:contain !important;
    border:0 !important;
}
table.tso-clas-table td.tso-col-pos,table.tso-clas-table th.tso-col-pos{width:28px !important;min-width:28px !important;text-align:center !important;white-space:nowrap !important;}
table.tso-clas-table td.tso-col-pts,
table.tso-clas-table th.tso-col-pts-head{
    background:var(--tso-clas-accent) !important;
    color:var(--tso-clas-accent-text) !important;
    font-weight:bold !important;
    font-size:13px !important;
}
.tso-clas-legend{font-size:10px;color:var(--tso-clas-muted);margin:6px 0 0;text-align:center;}
.tso-clas-legend .tso-sq-champ{color:var(--tso-clas-champ);}
.tso-clas-legend .tso-sq-europa{color:var(--tso-clas-europa);}
.tso-clas-legend .tso-sq-conference{color:var(--tso-clas-conference);}
.tso-clas-legend .tso-sq-down{color:var(--tso-clas-down);}
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
        'timeout'    => 8,
        'user-agent' => sprintf(
            'Apache-HttpClient/4.5.14 (TSO-Tabla-Liga/%s; WordPress/%s)',
            TSOTL_VERSION,
            get_bloginfo( 'version' )
        ),
    ) );

    if ( is_wp_error( $response ) ) {
        return array();
    }

    if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
        return array();
    }

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
        return '<p class="tso-clas-error">' . esc_html__( 'Could not load the standings.', 'tso-tabla-liga' ) . '</p>';
    }

    $html  = '<div class="tso-clas-wrap">';
    $html .= '<table class="tso-clas-table" cellspacing="0" cellpadding="0">';
    $html .= '<thead><tr>';
    $html .= '<th class="tso-col-pos">#</th>';
    $html .= '<th class="tso-col-team">' . esc_html__( 'Team', 'tso-tabla-liga' ) . '</th>';
    /* translators: Short table header for matches played. */
    $html .= '<th>' . esc_html__( 'MP', 'tso-tabla-liga' ) . '</th>';
    /* translators: Short table header for points. */
    $html .= '<th class="tso-col-pts-head">' . esc_html__( 'Pts', 'tso-tabla-liga' ) . '</th>';
    /* translators: Short table header for wins. */
    $html .= '<th>' . esc_html__( 'W', 'tso-tabla-liga' ) . '</th>';
    /* translators: Short table header for draws. */
    $html .= '<th>' . esc_html__( 'D', 'tso-tabla-liga' ) . '</th>';
    /* translators: Short table header for losses. */
    $html .= '<th>' . esc_html__( 'L', 'tso-tabla-liga' ) . '</th>';
    $html .= '</tr></thead><tbody>';

    foreach ( $teams as $i => $t ) {
        if ( $t['pos'] <= 4 )      $zone = 'champ';
        elseif ( 5 === (int) $t['pos'] ) $zone = 'europa';
        elseif ( 6 === (int) $t['pos'] ) $zone = 'conference';
        elseif ( $t['pos'] >= 18 ) $zone = 'down';
        else                       $zone = 'neutral';

        $row = ( $i % 2 === 0 ) ? 'tso-row-a' : 'tso-row-b';

        $html .= '<tr class="' . esc_attr( $row ) . '">';
        $html .= '<td class="tso-col-pos tso-zone-' . esc_attr( $zone ) . '">' . intval( $t['pos'] ) . '</td>';
        $html .= '<td class="tso-col-team tso-zone-' . esc_attr( $zone ) . '">';
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
    $html .= '<span class="tso-sq-champ">&#9632;</span> ' . esc_html__( 'Champions', 'tso-tabla-liga' ) . ' &nbsp;';
    $html .= '<span class="tso-sq-europa">&#9632;</span> ' . esc_html__( 'Europa', 'tso-tabla-liga' ) . ' &nbsp;';
    $html .= '<span class="tso-sq-conference">&#9632;</span> ' . esc_html__( 'Conference', 'tso-tabla-liga' ) . ' &nbsp;';
    $html .= '<span class="tso-sq-down">&#9632;</span> ' . esc_html__( 'Relegation', 'tso-tabla-liga' );
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
        TSOTL_VERSION,
        true
    );
    wp_enqueue_script( 'tso-tabla-ajax' );
    $tsotl_ajax_error = wp_json_encode( esc_html__( 'Could not load the standings.', 'tso-tabla-liga' ) );
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
                $wrap.html("<p class=\"tso-clas-error\">" + ' . $tsotl_ajax_error . ' + "</p>");
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
            array( 'description' => __( 'Live La Liga standings.', 'tso-tabla-liga' ) )
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
        echo '<div class="tso-tabla-ajax-wrap"><p class="tso-clas-loading">' . esc_html__( 'Loading standings…', 'tso-tabla-liga' ) . '</p></div>';

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
            <label for="<?php echo esc_attr( $this->get_field_id( 'show_title' ) ); ?>"><?php esc_html_e( 'Show title', 'tso-tabla-liga' ); ?></label>
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'tso-tabla-liga' ); ?></label>
            <input class="widefat"
                   id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
                   name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
                   type="text"
                   value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php?action=tso_clear_standings_cache' ), 'tso_clear_cache' ) ); ?>"><?php esc_html_e( 'Clear cache', 'tso-tabla-liga' ); ?></a>
        </p>
        <?php
    }

    public function update( $new_instance, $old_instance ) {
        $show_title = 0;
        if ( isset( $new_instance['show_title'] ) ) {
            $raw = $new_instance['show_title'];
            // The block widget editor saves the whole instance, so an unchecked
            // box arrives as 0 (the key is present). isset() alone would keep it visible.
            if ( true === $raw || 1 === $raw || '1' === (string) $raw || 'true' === $raw || 'on' === $raw ) {
                $show_title = 1;
            }
        }

        $title = '';
        if ( isset( $new_instance['title'] ) ) {
            $title = sanitize_text_field( wp_unslash( $new_instance['title'] ) );
        }

        return array(
            'title'      => $title,
            'show_title' => $show_title,
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

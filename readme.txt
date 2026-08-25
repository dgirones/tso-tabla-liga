=== TSO-Tabla-Liga ===
Contributors: tusoporteonline
Tags: football, laliga, standings, widget, espn
Requires at least: 5.0
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Real-time La Liga standings widget via ESPN API with 1-hour cache.

== Description ==

**English**

Displays the current La Liga standings (ESPN API) in a WordPress widget.

* Automatic update every hour
* Team logos optimised via ESPN Combiner (~92% size reduction)
* Compatible with cache plugins (LiteSpeed Cache, W3 Total Cache, etc.)
* Dark design adapted for sidebar widgets
* Visual indicators: Champions League, Europa League and Relegation zones

---

**Català**

Mostra la classificació actual de La Liga (ESPN API) en un widget de WordPress.

* Actualització automàtica cada hora
* Logos dels equips optimitzats via ESPN Combiner (reducció ~92% de pes)
* Compatible amb plugins de caché (LiteSpeed Cache, W3 Total Cache, etc.)
* Disseny fosc adaptat per a widgets laterals
* Indicadors visuals: Champions, Europa i Descens

---

**Español**

Muestra la clasificación actual de La Liga (ESPN API) en un widget de WordPress.

* Actualización automática cada hora
* Logos de los equipos optimizados via ESPN Combiner (reducción ~92% de peso)
* Compatible con plugins de caché (LiteSpeed Cache, W3 Total Cache, etc.)
* Diseño oscuro adaptado para widgets laterales
* Indicadores visuales: Champions, Europa y Descenso

== Installation ==

**English**

1. Upload the `TSO-Tabla-Liga` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin from the "Plugins" menu in the WordPress admin.
3. Go to Appearance → Widgets and add the "TSO-Tabla-Liga" widget to your desired sidebar.

---

**Català**

1. Puja la carpeta `TSO-Tabla-Liga` al directori `/wp-content/plugins/`.
2. Activa el plugin des de "Plugins" al menú d'administració de WordPress.
3. Ves a Aparença → Widgets i afegeix el widget "TSO-Tabla-Liga" a la sidebar desitjada.

---

**Español**

1. Sube la carpeta `TSO-Tabla-Liga` al directorio `/wp-content/plugins/`.
2. Activa el plugin desde "Plugins" en el menú de administración de WordPress.
3. Ve a Apariencia → Widgets y añade el widget "TSO-Tabla-Liga" a la sidebar deseada.

== Frequently Asked Questions ==

= How often is the data updated? =
The standings are cached for 1 hour. You can manually clear the cache from the widget settings in Appearance → Widgets.

= Com s'actualitzen les dades? =
La classificació es guarda en caché durant 1 hora. Pots netejar la caché manualment des de la configuració del widget a Aparença → Widgets.

= ¿Con qué frecuencia se actualizan los datos? =
La clasificación se guarda en caché durante 1 hora. Puedes limpiar la caché manualmente desde la configuración del widget en Apariencia → Widgets.

= Is it compatible with caching plugins? =
Yes. The plugin uses WordPress transients, which are fully compatible with LiteSpeed Cache, W3 Total Cache, WP Super Cache and similar plugins.

= És compatible amb plugins de caché? =
Sí. El plugin utilitza transients de WordPress, totalment compatibles amb LiteSpeed Cache, W3 Total Cache, WP Super Cache i similars.

= ¿Es compatible con plugins de caché? =
Sí. El plugin utiliza transients de WordPress, totalmente compatibles con LiteSpeed Cache, W3 Total Cache, WP Super Cache y similares.

== External services ==

This plugin retrieves La Liga standings from the ESPN public API (`site.api.espn.com`) when the widget loads or the cache expires (approximately once per hour). The request sends only a standard HTTP GET with a plugin-identifying User-Agent; no site content or visitor data is transmitted.

* Service: ESPN Sports API
* Terms of use: https://www.espn.com/espn/news/story/_/id/8730930/espn-terms-use
* Privacy policy: https://privacy.thewaltdisneycompany.com/en/current-privacy-policy/

== Changelog ==

= 1.7 =
* Fixed ESPN API requests blocked by WordPress default User-Agent (HTTP 403)
* Added HTTP response code validation before parsing standings JSON

= 1.5 =
* Renamed plugin to TSO-Tabla-Liga
* Fixed all security escaping errors detected by Plugin Checker
* Added GPLv2 license header
* Replaced wp_redirect with wp_safe_redirect
* Logos optimised via ESPN Combiner (~92% weight reduction)
* Cache reduced to 1 hour

= 1.4 =
* Fixed "Undefined array key value" warning on ESPN API stats
* Logo optimisation via ESPN Combiner

= 1.3 =
* First public release

== Upgrade Notice ==

= 1.5 =
Security and compatibility improvements. Update recommended.

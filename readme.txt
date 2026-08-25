=== TSO-Tabla-Liga ===
Contributors: deadko
Tags: football, laliga, standings, widget, soccer
Requires at least: 6.1
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.7
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

La Liga standings widget for WordPress sidebars. ESPN data, hourly cache, AJAX loading, and full-page cache friendly.

== Description ==

TSO-Tabla-Liga adds a classic WordPress widget that displays the current Spanish La Liga (LALIGA) standings table in your sidebar or widget area.

Standings are fetched from the public ESPN Sports API, cached for one hour with WordPress transients, and loaded on the front end via AJAX so full-page caching plugins (LiteSpeed Cache, W3 Total Cache, WP Super Cache, and similar) can still serve cached HTML.

= Features =

* Current La Liga standings (position, team, played, points, wins, draws, losses)
* Team logos optimised through the ESPN CDN combiner
* Dark table styling suited to sidebar widgets
* Colour-coded zones: Champions League, Europa League, and relegation
* Optional widget title
* Manual cache clear link in the widget settings screen
* No API key or registration required

= How it works =

1. Add the **TSO-Tabla-Liga** widget under **Appearance → Widgets** (or the block-based widget screen).
2. Visitors see a short loading message while the widget requests standings through `admin-ajax.php`.
3. The plugin checks its transient cache. If data is older than one hour, it requests fresh standings from ESPN.
4. The rendered HTML table replaces the loading message.

= Requirements =

* WordPress 6.1 or later
* PHP 7.4 or later
* Outbound HTTPS access from your server to `site.api.espn.com`

== Installation ==

= Automatic installation =

1. In your WordPress dashboard, go to **Plugins → Add New**.
2. Search for **TSO-Tabla-Liga**.
3. Click **Install Now**, then **Activate**.

= Manual installation =

1. Download the plugin ZIP or clone this repository.
2. Upload the `tso-tabla-liga` folder to `/wp-content/plugins/`.
3. Activate the plugin through the **Plugins** screen in WordPress.
4. Go to **Appearance → Widgets** and add **TSO-Tabla-Liga** to the sidebar or widget area you want.

= After activation =

No settings page is required. Configure the widget title and visibility from the widget instance under **Appearance → Widgets**.

== Frequently Asked Questions ==

= Does this plugin require an API key? =

No. It uses ESPN's public standings endpoint. No account or API key is needed.

= How often are standings updated? =

Standings are stored in a WordPress transient for one hour. After that, the next front-end request refreshes the data from ESPN.

= How do I clear the cache manually? =

Open **Appearance → Widgets**, expand the **TSO-Tabla-Liga** widget, and click **Netejar caché** (Clear cache).

= Is it compatible with caching plugins? =

Yes. The widget shell is rendered in the page, but standings load through AJAX. That design avoids embedding time-sensitive data in HTML cached by page-cache plugins.

= What happens if ESPN is unreachable? =

The widget shows a short error message and does not cache failed responses. When ESPN responds again, the next request after cache expiry (or a manual cache clear) restores the table.

= Can I use this widget in block themes? =

Yes. Add it from **Appearance → Widgets** or the widget block in the Site Editor, depending on your theme setup.

= Does the plugin collect visitor data? =

No. The plugin does not track visitors. Only your web server contacts ESPN to download public standings data.

== Screenshots ==

1. La Liga standings table in a sidebar widget with zone colours and team logos.

== External services ==

This plugin relies on one third-party service to display standings.

= ESPN Sports API =

* **What it is:** ESPN's public sports data API (`site.api.espn.com`).
* **What it is used for:** Downloading current La Liga standings (team names, positions, results, and logo URLs).
* **When data is sent:** When the widget loads on the front end and the one-hour transient cache has expired, or after you clear the cache. Your server sends an HTTP GET request to ESPN.
* **What data is sent:** A standard HTTP GET request with a plugin-identifying User-Agent string. No visitor personal data, site content, or WordPress user accounts are transmitted.
* **Terms of use:** https://www.espn.com/espn/news/story/_/id/8730930/espn-terms-use
* **Privacy policy:** https://privacy.thewaltdisneycompany.com/en/current-privacy-policy/

== Changelog ==

= 1.7 =
* Fixed ESPN API requests blocked by the WordPress default User-Agent (HTTP 403).
* Added HTTP response code validation before parsing standings JSON.
* Rewrote readme.txt to WordPress.org standards.

= 1.6 =
* AJAX widget loading to support full-page caching plugins.
* Widget cache clear action in the admin.

= 1.5 =
* Renamed plugin to TSO-Tabla-Liga.
* Fixed security escaping issues reported by Plugin Check.
* Added GPLv2 license header.
* Replaced wp_redirect with wp_safe_redirect.
* Optimised team logos via ESPN Combiner.
* Reduced cache duration to one hour.

= 1.4 =
* Fixed "Undefined array key value" warning on ESPN API stats.
* Added logo optimisation via ESPN Combiner.

= 1.3 =
* Initial public release.

== Upgrade Notice ==

= 1.7 =
Fixes standings failing to load when ESPN blocks the WordPress User-Agent. Clear the widget cache after updating.

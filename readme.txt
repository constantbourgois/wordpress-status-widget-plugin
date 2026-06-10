=== OpenStatus Badge ===
Contributors: openstatus
Tags: status, badge, monitoring, uptime
Requires at least: 6.1
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display your OpenStatus status page badge on your WordPress site.

== Description ==

OpenStatus Badge allows you to easily embed your OpenStatus status page badge anywhere on your WordPress site using the block editor.

**Features:**

* Simple setup - just enter your status page slug
* Customizable appearance - choose theme (light/dark), size (sm/md/lg/xl), and variant (outline)
* Block editor integration - full preview in the editor with real-time attribute changes
* Lightweight - badge is loaded as a native `<img>` tag, no inline SVG fetching

**How it works:**

1. Configure your OpenStatus status page slug in Settings > OpenStatus
2. Add the "OpenStatus Badge" block to any page or post
3. Customize the badge appearance using the block settings
4. The badge automatically links to your status page

== Installation ==

**From source (developers):**

1. Clone or download the repository
2. Run `npm install` then `npm run build` to compile the block assets
3. Upload the `openstatus-badge` folder to the `/wp-content/plugins/` directory
4. Activate the plugin through the 'Plugins' menu in WordPress
5. Go to Settings > OpenStatus to configure your status page slug
6. Add the "OpenStatus Badge" block to any page or post

Note: The `build/` directory is not included in the repository. You must run the build step before the plugin will work.

== Frequently Asked Questions ==

= Where do I find my status page slug? =

Your status page slug is the subdomain of your OpenStatus URL. For example, if your status page is at `acme.openstatus.dev`, your slug is `acme`.

= How often is the badge updated? =

The badge is cached for 5 minutes to improve performance. You can clear the cache manually from Settings > OpenStatus if needed.

= Can I use multiple badges with different settings? =

Yes! Each block can have its own theme, size, and variant settings. However, all badges will use the same status page slug configured in settings.

= What happens if the badge cannot be loaded? =

If the badge cannot be fetched from OpenStatus, the text "Status unavailable" will be displayed instead.

== Screenshots ==

1. The OpenStatus Badge block in the editor
2. Badge settings panel
3. Plugin settings page
4. Example badge on a page

== Changelog ==

= 1.0.1 =
* Fix badge display: replaced inline SVG fetching with a native `<img>` tag to avoid CORS issues
* Fix render.php: use `echo` instead of `return` for block output
* Tested and confirmed working on https://testwp.constantbourgois.com/sample-page/

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of OpenStatus Badge plugin.

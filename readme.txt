=== OpenStatus Badge ===
Contributors: openstatus
Tags: status, badge, monitoring, uptime
Requires at least: 6.1
Tested up to: 6.7
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
* Server-side caching - badges are cached for 5 minutes to ensure fast page loads
* Block editor integration - full preview in the editor with real-time attribute changes
* Graceful fallback - displays "Status unavailable" if the badge cannot be fetched

**How it works:**

1. Configure your OpenStatus status page slug in Settings > OpenStatus
2. Add the "OpenStatus Badge" block to any page or post
3. Customize the badge appearance using the block settings
4. The badge automatically links to your status page

== Installation ==

1. Upload the `openstatus-badge` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings > OpenStatus to configure your status page slug
4. Add the "OpenStatus Badge" block to any page or post

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

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release of OpenStatus Badge plugin.

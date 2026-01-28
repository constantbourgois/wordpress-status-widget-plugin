<?php
/**
 * Plugin Name:       OpenStatus Badge
 * Plugin URI:        https://www.openstatus.dev
 * Description:       Display your OpenStatus status page badge on your WordPress site.
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            OpenStatus
 * Author URI:        https://www.openstatus.dev
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       openstatus-badge
 * Domain Path:       /languages
 *
 * @package OpenStatusBadge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OPENSTATUS_BADGE_VERSION', '1.0.0' );
define( 'OPENSTATUS_BADGE_PATH', plugin_dir_path( __FILE__ ) );
define( 'OPENSTATUS_BADGE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Load plugin text domain.
 */
function openstatus_badge_load_textdomain() {
	load_plugin_textdomain( 'openstatus-badge', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'openstatus_badge_load_textdomain' );

// Include required files.
require_once OPENSTATUS_BADGE_PATH . 'includes/class-cache.php';
require_once OPENSTATUS_BADGE_PATH . 'includes/class-settings.php';

/**
 * Initialize the settings page.
 */
function openstatus_badge_init_settings() {
	new OpenStatus_Badge_Settings();
}
add_action( 'admin_init', 'openstatus_badge_init_settings' );
add_action( 'admin_menu', array( 'OpenStatus_Badge_Settings', 'add_settings_page' ) );

/**
 * Register the block.
 */
function openstatus_badge_register_block() {
	register_block_type( OPENSTATUS_BADGE_PATH . 'build' );
}
add_action( 'init', 'openstatus_badge_register_block' );

/**
 * Enqueue editor assets and pass settings to JavaScript.
 */
function openstatus_badge_editor_assets() {
	$slug = get_option( 'openstatus_badge_slug', '' );

	wp_localize_script(
		'openstatus-badge-editor-script',
		'openstatusBadgeSettings',
		array(
			'slug'        => $slug,
			'settingsUrl' => admin_url( 'options-general.php?page=openstatus-badge' ),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'openstatus_badge_editor_assets' );

/**
 * Handle cache purge action.
 */
function openstatus_badge_handle_purge() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to perform this action.', 'openstatus-badge' ) );
	}

	check_admin_referer( 'openstatus_badge_purge_cache' );

	OpenStatus_Badge_Cache::purge_all();

	wp_safe_redirect( add_query_arg( 'cache_purged', '1', admin_url( 'options-general.php?page=openstatus-badge' ) ) );
	exit;
}
add_action( 'admin_post_openstatus_badge_purge_cache', 'openstatus_badge_handle_purge' );

<?php
/**
 * Settings page handler for OpenStatus Badge plugin.
 *
 * @package OpenStatusBadge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OpenStatus_Badge_Settings
 *
 * Handles the plugin settings page.
 */
class OpenStatus_Badge_Settings {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->register_settings();
	}

	/**
	 * Add the settings page to the admin menu.
	 */
	public static function add_settings_page() {
		add_options_page(
			__( 'OpenStatus Badge', 'openstatus-badge' ),
			__( 'OpenStatus', 'openstatus-badge' ),
			'manage_options',
			'openstatus-badge',
			array( __CLASS__, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings.
	 */
	public function register_settings() {
		register_setting(
			'openstatus_badge_settings',
			'openstatus_badge_slug',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		add_settings_section(
			'openstatus_badge_main',
			__( 'Configuration', 'openstatus-badge' ),
			array( $this, 'render_section_description' ),
			'openstatus-badge'
		);

		add_settings_field(
			'openstatus_badge_slug',
			__( 'Status Page Slug', 'openstatus-badge' ),
			array( $this, 'render_slug_field' ),
			'openstatus-badge',
			'openstatus_badge_main'
		);
	}

	/**
	 * Render the section description.
	 */
	public function render_section_description() {
		echo '<p>' . esc_html__( 'Enter your OpenStatus status page slug to display the badge on your site.', 'openstatus-badge' ) . '</p>';
	}

	/**
	 * Render the slug input field.
	 */
	public function render_slug_field() {
		$slug = get_option( 'openstatus_badge_slug', '' );
		?>
		<input
			type="text"
			id="openstatus_badge_slug"
			name="openstatus_badge_slug"
			value="<?php echo esc_attr( $slug ); ?>"
			class="regular-text"
			placeholder="<?php esc_attr_e( 'your-status-page', 'openstatus-badge' ); ?>"
		/>
		<p class="description">
			<?php
			printf(
				/* translators: %s: example URL */
				esc_html__( 'The slug from your OpenStatus URL. For example, if your status page is at %s, enter "acme".', 'openstatus-badge' ),
				'<code>acme.openstatus.dev</code>'
			);
			?>
		</p>
		<?php
	}

	/**
	 * Render the settings page.
	 */
	public static function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Show cache purged notice.
		if ( isset( $_GET['cache_purged'] ) && '1' === $_GET['cache_purged'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_settings_error(
				'openstatus_badge_messages',
				'cache_purged',
				__( 'Badge cache has been cleared.', 'openstatus-badge' ),
				'updated'
			);
		}

		settings_errors( 'openstatus_badge_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form action="options.php" method="post">
				<?php
				settings_fields( 'openstatus_badge_settings' );
				do_settings_sections( 'openstatus-badge' );
				submit_button();
				?>
			</form>

			<hr />

			<h2><?php esc_html_e( 'Cache Management', 'openstatus-badge' ); ?></h2>

			<p>
				<?php
				$cache_count = OpenStatus_Badge_Cache::get_cache_count();
				printf(
					/* translators: %d: number of cached badges */
					esc_html( _n( '%d badge currently cached.', '%d badges currently cached.', $cache_count, 'openstatus-badge' ) ),
					intval( $cache_count )
				);
				?>
			</p>
			<p class="description">
				<?php esc_html_e( 'Badges are cached for 5 minutes to improve performance. Use the button below to clear the cache if you need to see updates immediately.', 'openstatus-badge' ); ?>
			</p>

			<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" style="margin-top: 1em;">
				<?php wp_nonce_field( 'openstatus_badge_purge_cache' ); ?>
				<input type="hidden" name="action" value="openstatus_badge_purge_cache" />
				<?php submit_button( __( 'Clear Badge Cache', 'openstatus-badge' ), 'secondary', 'purge_cache', false ); ?>
			</form>
		</div>
		<?php
	}
}

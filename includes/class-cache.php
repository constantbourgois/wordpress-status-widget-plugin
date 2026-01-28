<?php
/**
 * Cache manager for OpenStatus Badge plugin.
 *
 * @package OpenStatusBadge
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class OpenStatus_Badge_Cache
 *
 * Handles caching of badge SVGs using WordPress transients.
 */
class OpenStatus_Badge_Cache {

	/**
	 * Cache TTL in seconds (5 minutes).
	 */
	const CACHE_TTL = 300;

	/**
	 * Transient prefix.
	 */
	const TRANSIENT_PREFIX = 'openstatus_badge_';

	/**
	 * Get a cached badge or fetch it fresh.
	 *
	 * @param string $slug    The status page slug.
	 * @param string $theme   The badge theme (light/dark).
	 * @param string $size    The badge size (sm/md/lg/xl).
	 * @param string $variant The badge variant (outline or empty).
	 * @return string|false The SVG content or false on failure.
	 */
	public static function get_badge( $slug, $theme = 'light', $size = 'sm', $variant = '' ) {
		$cache_key = self::generate_cache_key( $slug, $theme, $size, $variant );
		$cached    = get_transient( $cache_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$url = self::build_badge_url( $slug, $theme, $size, $variant );
		$svg = self::fetch_badge( $url );

		if ( false !== $svg ) {
			set_transient( $cache_key, $svg, self::CACHE_TTL );
		}

		return $svg;
	}

	/**
	 * Fetch a badge from the OpenStatus API.
	 *
	 * @param string $url The badge URL.
	 * @return string|false The SVG content or false on failure.
	 */
	public static function fetch_badge( $url ) {
		$response = wp_remote_get(
			$url,
			array(
				'timeout' => 10,
				'headers' => array(
					'Accept' => 'image/svg+xml',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$code = wp_remote_retrieve_response_code( $response );
		if ( 200 !== $code ) {
			return false;
		}

		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			return false;
		}

		return $body;
	}

	/**
	 * Build the badge URL with parameters.
	 *
	 * @param string $slug    The status page slug.
	 * @param string $theme   The badge theme.
	 * @param string $size    The badge size.
	 * @param string $variant The badge variant.
	 * @return string The full badge URL.
	 */
	public static function build_badge_url( $slug, $theme, $size, $variant ) {
		$base_url = sprintf( 'https://%s.openstatus.dev/badge', sanitize_title( $slug ) );

		$params = array();

		if ( ! empty( $theme ) && 'light' !== $theme ) {
			$params['theme'] = $theme;
		}

		if ( ! empty( $size ) && 'sm' !== $size ) {
			$params['size'] = $size;
		}

		if ( ! empty( $variant ) ) {
			$params['variant'] = $variant;
		}

		if ( ! empty( $params ) ) {
			$base_url = add_query_arg( $params, $base_url );
		}

		return $base_url;
	}

	/**
	 * Generate a cache key from badge parameters.
	 *
	 * @param string $slug    The status page slug.
	 * @param string $theme   The badge theme.
	 * @param string $size    The badge size.
	 * @param string $variant The badge variant.
	 * @return string The cache key.
	 */
	public static function generate_cache_key( $slug, $theme, $size, $variant ) {
		$key_parts = array(
			sanitize_title( $slug ),
			sanitize_key( $theme ),
			sanitize_key( $size ),
			sanitize_key( $variant ),
		);

		return self::TRANSIENT_PREFIX . implode( '_', array_filter( $key_parts ) );
	}

	/**
	 * Purge all OpenStatus badge transients.
	 *
	 * @return int Number of transients deleted.
	 */
	public static function purge_all() {
		global $wpdb;

		$prefix  = '_transient_' . self::TRANSIENT_PREFIX;
		$deleted = 0;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$transients = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( $prefix ) . '%'
			)
		);

		foreach ( $transients as $transient ) {
			$key = str_replace( '_transient_', '', $transient );
			if ( delete_transient( $key ) ) {
				++$deleted;
			}
		}

		return $deleted;
	}

	/**
	 * Get the count of cached badges.
	 *
	 * @return int Number of cached badges.
	 */
	public static function get_cache_count() {
		global $wpdb;

		$prefix = '_transient_' . self::TRANSIENT_PREFIX;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE %s",
				$wpdb->esc_like( $prefix ) . '%'
			)
		);
	}
}

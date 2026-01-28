<?php
/**
 * Server-side rendering for the OpenStatus Badge block.
 *
 * @package OpenStatusBadge
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$slug = get_option( 'openstatus_badge_slug', '' );

if ( empty( $slug ) ) {
	if ( current_user_can( 'manage_options' ) ) {
		return sprintf(
			'<p class="openstatus-badge-notice">%s <a href="%s">%s</a></p>',
			esc_html__( 'OpenStatus Badge: Please configure your status page slug in', 'openstatus-badge' ),
			esc_url( admin_url( 'options-general.php?page=openstatus-badge' ) ),
			esc_html__( 'Settings', 'openstatus-badge' )
		);
	}
	return '';
}

$theme   = isset( $attributes['theme'] ) ? sanitize_key( $attributes['theme'] ) : 'light';
$size    = isset( $attributes['size'] ) ? sanitize_key( $attributes['size'] ) : 'sm';
$variant = isset( $attributes['variant'] ) ? sanitize_key( $attributes['variant'] ) : '';

$svg = OpenStatus_Badge_Cache::get_badge( $slug, $theme, $size, $variant );

if ( false === $svg ) {
	return sprintf(
		'<span class="openstatus-badge-unavailable">%s</span>',
		esc_html__( 'Status unavailable', 'openstatus-badge' )
	);
}

$status_page_url = sprintf( 'https://%s.openstatus.dev', sanitize_title( $slug ) );

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'openstatus-badge',
	)
);

return sprintf(
	'<div %s><a href="%s" target="_blank" rel="noopener noreferrer">%s</a></div>',
	$wrapper_attributes,
	esc_url( $status_page_url ),
	$svg
);

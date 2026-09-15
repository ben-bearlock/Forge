<?php
/**
 * User-facing Forge branding for the Custom Astra fork.
 *
 * PHP function names, option keys, CSS classes, and the astra text domain
 * stay unchanged so the theme keeps working.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'FORGE_THEME_NAME' ) ) {
	define( 'FORGE_THEME_NAME', 'Forge' );
}

if ( ! defined( 'FORGE_THEME_AUTHOR' ) ) {
	define( 'FORGE_THEME_AUTHOR', 'Ben Roos (TechJunkyBen)' );
}

if ( ! defined( 'FORGE_THEME_URI' ) ) {
	define( 'FORGE_THEME_URI', 'https://hire.techjunkyben.nl' );
}

/**
 * Theme name in wp-admin, Customizer, and metaboxes.
 *
 * @return string
 */
function forge_theme_name() {
	return FORGE_THEME_NAME;
}

add_filter( 'astra_theme_name', 'forge_theme_name' );
add_filter( 'astra_page_title', 'forge_theme_name' );
add_filter( 'astra_addon_name', 'forge_theme_name' );

/**
 * Admin page slug: Appearance still uses the folder name; this is the Astra dashboard page.
 *
 * @return string
 */
function forge_theme_page_slug() {
	return 'forge';
}

add_filter( 'astra_theme_page_slug', 'forge_theme_page_slug' );

/**
 * Old Astra dashboard bookmarks should land on Forge.
 *
 * @return void
 */
function forge_redirect_legacy_admin_slug() {
	if ( defined( 'ASTRA_EXT_VER' ) ) {
		return;
	}

	if ( empty( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$page = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'astra' !== $page ) {
		return;
	}

	$args = $_GET; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$args['page'] = 'forge';
	wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
	exit;
}

add_action( 'admin_init', 'forge_redirect_legacy_admin_slug', 1 );

/**
 * Dashboard React header logo.
 *
 * @return string
 */
function forge_admin_menu_icon() {
	return ASTRA_THEME_URI . 'inc/assets/images/forge-logo.svg';
}

add_filter( 'astra_admin_menu_icon', 'forge_admin_menu_icon' );

/**
 * WordPress admin menu SVG (dashicon slot). File URLs do not render reliably here.
 *
 * @return string
 */
function forge_wp_admin_menu_icon() {
	$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="#a7aaad"><path d="M5 3h10v2.4H8.2v3.2h6v2.4h-6V17H5V3z"/></svg>';

	return 'data:image/svg+xml;base64,' . base64_encode( $svg );
}

add_filter( 'astra_menu_icon', 'forge_wp_admin_menu_icon' );

/**
 * Footer [theme_author] replacement: Powered by Forge.
 *
 * @param array $author Author name and URL.
 * @return array
 */
function forge_theme_author( $author ) {
	return array(
		'theme_name'       => FORGE_THEME_NAME,
		'theme_author_url' => FORGE_THEME_URI,
	);
}

add_filter( 'astra_theme_author', 'forge_theme_author' );

/**
 * Rewrite leftover Astra credit text already saved in the footer option.
 *
 * @param mixed $value Copyright HTML.
 * @return mixed
 */
function forge_footer_copyright_option( $value ) {
	if ( ! is_string( $value ) || '' === $value ) {
		return $value;
	}

	$value = str_replace( 'Astra WordPress Theme', FORGE_THEME_NAME, $value );
	$value = str_replace( 'https://www.wpastra.com', FORGE_THEME_URI, $value );
	$value = str_replace( 'https://wpastra.com', FORGE_THEME_URI, $value );

	return $value;
}

add_filter( 'astra_get_option_footer-copyright-editor', 'forge_footer_copyright_option' );
add_filter( 'astra_get_option_footer-sml-section-1-credit', 'forge_footer_copyright_option' );
add_filter( 'astra_get_option_footer-sml-section-2-credit', 'forge_footer_copyright_option' );

/**
 * Hide leftover Astra marketplace chrome (starter-templates tab, rating footer, Woo upsell submenu).
 *
 * @return bool
 */
function forge_is_white_labelled() {
	return true;
}

add_filter( 'astra_is_white_labelled', 'forge_is_white_labelled' );

/**
 * Theme details / Live Preview should not fall back to the wordpress.org Astra demo.
 *
 * @param array $themes Prepared theme objects for themes.php.
 * @return array
 */
function forge_prepare_themes_for_js( $themes ) {
	if ( ! is_array( $themes ) ) {
		return $themes;
	}

	$template = get_template();

	foreach ( $themes as $key => $theme ) {
		$id   = isset( $theme['id'] ) ? $theme['id'] : '';
		$name = isset( $theme['name'] ) ? wp_strip_all_tags( $theme['name'] ) : '';

		if ( $template !== $id && false === stripos( $name, 'Forge' ) ) {
			continue;
		}

		$themes[ $key ]['name'] = FORGE_THEME_NAME;

		if ( isset( $themes[ $key ]['author'] ) ) {
			$themes[ $key ]['author'] = FORGE_THEME_AUTHOR;
		}

		if ( isset( $themes[ $key ]['authorAndUri'] ) ) {
			$themes[ $key ]['authorAndUri'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( FORGE_THEME_URI ),
				esc_html( FORGE_THEME_AUTHOR )
			);
		}

		foreach ( array( 'preview_url', 'demo_url', 'livePreviewURL' ) as $preview_key ) {
			if ( empty( $themes[ $key ][ $preview_key ] ) || ! is_string( $themes[ $key ][ $preview_key ] ) ) {
				continue;
			}
			if ( false !== strpos( $themes[ $key ][ $preview_key ], 'astra' ) || false !== strpos( $themes[ $key ][ $preview_key ], 'wpastra' ) || false !== strpos( $themes[ $key ][ $preview_key ], 'wp-themes.com' ) ) {
				$themes[ $key ][ $preview_key ] = FORGE_THEME_URI;
			}
		}
	}

	return $themes;
}

add_filter( 'wp_prepare_themes_for_js', 'forge_prepare_themes_for_js' );

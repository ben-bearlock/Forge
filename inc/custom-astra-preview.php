<?php
/**
 * Forge Live Preview: restyle the Customizer/theme-browser preview.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether this request is the Themes-screen Live Preview (or a fresh-site starter preview).
 *
 * @return bool
 */
function forge_is_theme_browser_preview() {
	if ( ! is_customize_preview() ) {
		return false;
	}

	if ( ! empty( $_GET['theme'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return true;
	}

	if ( get_option( 'fresh_site' ) ) {
		return true;
	}

	$referer = wp_get_referer();
	if ( $referer && false !== strpos( $referer, 'themes.php' ) ) {
		return true;
	}

	return false;
}

/**
 * Forge palette used by the preview (dark + burnt orange).
 *
 * @return array
 */
function forge_preview_palette_colors() {
	return array(
		'#e85d04',
		'#c44d03',
		'#f4f1ea',
		'#c4c0b8',
		'#1a1a1a',
		'#111111',
		'#2a2a2a',
		'#e85d04',
		'#000000',
	);
}

/**
 * Recolor the preview palette so it is not Astra purple.
 *
 * @param mixed $defaults Palette object.
 * @return mixed
 */
function forge_preview_color_palette( $defaults ) {
	if ( ! forge_is_theme_browser_preview() ) {
		return $defaults;
	}

	$colors = forge_preview_palette_colors();

	if ( ! is_array( $defaults ) ) {
		$defaults = array();
	}

	$defaults['currentPalette'] = 'palette_1';
	if ( empty( $defaults['palettes'] ) || ! is_array( $defaults['palettes'] ) ) {
		$defaults['palettes'] = array();
	}
	$defaults['palettes']['palette_1'] = $colors;

	return $defaults;
}

add_filter( 'astra_global_color_palette', 'forge_preview_color_palette', 20 );

/**
 * Overlay header/footer colors on saved Astra settings during Live Preview.
 *
 * @param mixed $value Option value.
 * @return mixed
 */
function forge_preview_option_astra_settings( $value ) {
	if ( ! forge_is_theme_browser_preview() || ! is_array( $value ) ) {
		return $value;
	}

	$header_bg = array(
		'background-color'      => '#1a1a1a',
		'background-image'      => '',
		'background-repeat'     => 'repeat',
		'background-position'   => 'center center',
		'background-size'       => 'auto',
		'background-attachment' => 'scroll',
		'background-type'       => 'color',
	);

	$value['hb-header-bg-obj-responsive'] = array(
		'desktop' => $header_bg,
		'tablet'  => $header_bg,
		'mobile'  => $header_bg,
	);

	$footer_bg = $header_bg;
	$value['footer-bg-obj-responsive'] = array(
		'desktop' => $footer_bg,
		'tablet'  => $footer_bg,
		'mobile'  => $footer_bg,
	);

	$value['header-color']              = '#e85d04';
	$value['header-link-color']         = '#f4f1ea';
	$value['header-link-h-color']       = '#e85d04';
	$value['header-logo-color']         = '';
	$value['transparent-header-logo-color'] = '';
	$value['use-logo-svg-icon']         = false;
	$value['header-color-site-title']   = '#e85d04';
	$value['header-color-h-site-title'] = '#ff7a2f';
	$value['display-site-title-responsive'] = array(
		'desktop' => true,
		'tablet'  => true,
		'mobile'  => true,
	);
	$value['display-site-tagline-responsive'] = array(
		'desktop' => false,
		'tablet'  => false,
		'mobile'  => false,
	);
	$value['footer-copyright-editor'] = 'Copyright [copyright] [current_year] [site_title] | Powered by [theme_author]';

	return $value;
}

add_filter( 'option_astra-settings', 'forge_preview_option_astra_settings' );

/**
 * Apply the dark Forge header/footer skin onto a settings array.
 *
 * @param array $value Settings.
 * @return array
 */
function forge_preview_apply_header_skin( $value ) {
	if ( ! is_array( $value ) ) {
		return $value;
	}

	return forge_preview_option_astra_settings( $value );
}

/**
 * Show FORGE as the site title in the theme-browser preview.
 *
 * @param mixed $name Site title.
 * @return mixed
 */
function forge_preview_blogname( $name ) {
	if ( forge_is_theme_browser_preview() ) {
		return 'FORGE';
	}

	return $name;
}

add_filter( 'option_blogname', 'forge_preview_blogname' );

/**
 * Never use a raster logo in Live Preview. The color mask + crop turns it into a square.
 * The FORGE site title is the brand mark instead.
 *
 * @param mixed $logo Logo id.
 * @return mixed
 */
function forge_preview_custom_logo( $logo ) {
	if ( forge_is_theme_browser_preview() ) {
		return 0;
	}

	return $logo;
}

add_filter( 'theme_mod_custom_logo', 'forge_preview_custom_logo' );

/**
 * Force the title wordmark even if a logo attachment still exists.
 *
 * @param bool $has Whether a custom logo is set.
 * @return bool
 */
function forge_preview_has_custom_logo( $has ) {
	if ( forge_is_theme_browser_preview() ) {
		return false;
	}

	return $has;
}

add_filter( 'astra_has_custom_logo', 'forge_preview_has_custom_logo' );
add_filter( 'has_custom_logo', 'forge_preview_has_custom_logo' );

/**
 * Make the preview site title read as a wordmark.
 *
 * @param string $css Dynamic CSS.
 * @return string
 */
function forge_preview_wordmark_css( $css ) {
	if ( ! forge_is_theme_browser_preview() ) {
		return $css;
	}

	$css .= '.site-branding .site-title,.site-branding .site-title a{color:#e85d04!important;font-weight:800;letter-spacing:.18em;text-transform:uppercase;font-size:1.35rem;text-decoration:none;}';
	$css .= '.site-branding .site-logo-img,.site-branding .ast-logo-svg-icon{display:none!important;}';

	return $css;
}

add_filter( 'astra_dynamic_theme_css', 'forge_preview_wordmark_css', 99 );

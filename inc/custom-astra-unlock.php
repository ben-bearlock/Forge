<?php
/**
 * Custom Astra fork helpers: quiet dashboard + drop Upgrade Customizer UI.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Persist dashboard flags so compiled admin JS stays quiet.
 *
 * @return void
 */
function custom_astra_fork_force_quiet_settings() {
	if ( function_exists( 'astra_update_option' ) ) {
		astra_update_option( 'ast-disable-upgrade-notices', false );
	}

	if ( class_exists( 'Astra_API_Init' ) ) {
		Astra_API_Init::update_admin_settings_option( 'show_ai_assistant', false );
		Astra_API_Init::update_admin_settings_option( 'show_learn_tab', false );
		Astra_API_Init::update_admin_settings_option( 'enable_abilities', false );
		Astra_API_Init::update_admin_settings_option( 'enable_mcp_server', false );
	}

	update_option( 'astra_usage_optin', 'no' );
}

add_action( 'after_setup_theme', 'custom_astra_fork_force_quiet_settings', 20 );

/**
 * REST dashboard payload.
 *
 * @param array $options Dashboard options.
 * @return array
 */
function custom_astra_fork_dashboard_rest_options( $options ) {
	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$options['use_upgrade_notices'] = false;
	$options['analytics_enabled']   = false;
	$options['show_learn_tab']      = false;
	$options['show_ai_assistant']   = false;
	$options['enable_abilities']    = false;
	$options['enable_mcp_server']   = false;

	if ( class_exists( 'Astra_Menu' ) ) {
		$modules = Astra_Menu::astra_get_pro_extensions();
		$active  = array();
		if ( is_array( $modules ) ) {
			foreach ( array_keys( $modules ) as $slug ) {
				$active[ $slug ] = $slug;
			}
		}
		$options['pro_addons'] = $active;
	}

	return $options;
}

add_filter( 'astra_dashboard_rest_options', 'custom_astra_fork_dashboard_rest_options' );

add_filter( 'astra_nps_survey_disable', '__return_true' );
add_filter( 'astra_showcase_starter_templates_notice', '__return_false' );
add_filter( 'astra_disable_starter_templates_promotions', '__return_true' );

/**
 * Drop leftover Upgrade / Toolkit Customizer controls.
 *
 * @param array $configurations Customizer configs.
 * @return array
 */
function custom_astra_fork_strip_upsell_configs( $configurations ) {
	if ( ! is_array( $configurations ) ) {
		return $configurations;
	}

	$kept = array();
	foreach ( $configurations as $config ) {
		if ( ! is_array( $config ) ) {
			$kept[] = $config;
			continue;
		}

		$control = isset( $config['control'] ) ? $config['control'] : '';
		$name    = isset( $config['name'] ) ? (string) $config['name'] : '';
		$section = isset( $config['section'] ) ? (string) $config['section'] : '';

		if ( 'ast-upgrade' === $control ) {
			continue;
		}
		if ( 'astra-pro' === $section || 'astra-pro' === $name ) {
			continue;
		}
		if ( false !== strpos( $name, 'astra-pro' ) ) {
			continue;
		}
		if ( false !== strpos( $name, 'more-feature' ) ) {
			continue;
		}
		$help = isset( $config['help'] ) ? (string) $config['help'] : '';
		if ( false !== strpos( $help, 'Toolkit' ) || false !== strpos( $help, 'Learn More' ) ) {
			continue;
		}

		$kept[] = $config;
	}

	return $kept;
}

add_filter( 'astra_customizer_configurations', 'custom_astra_fork_strip_upsell_configs', 999 );

add_filter( 'astra_show_free_extend_plugins', '__return_false' );

/**
 * Keep theme modules on the dashboard. Drop only Pro-locked White Label.
 *
 * @param array $list Addon list.
 * @return array
 */
function custom_astra_fork_addon_list( $list ) {
	if ( ! is_array( $list ) ) {
		return $list;
	}

	unset( $list['white-label'] );
	unset( $list['advanced-hooks'] );

	$customizer = array(
		'colors-and-background' => admin_url( 'customize.php?autofocus[section]=section-colors-background' ),
		'typography'            => admin_url( 'customize.php?autofocus[section]=section-typography' ),
		'spacing'               => admin_url( 'customize.php?autofocus[section]=section-container-layout' ),
		'blog-pro'              => admin_url( 'customize.php?autofocus[section]=section-blog-group' ),
		'mobile-header'         => admin_url( 'customize.php?autofocus[panel]=panel-header-group' ),
		'header-sections'       => admin_url( 'customize.php?autofocus[panel]=panel-header-group' ),
		'sticky-header'         => admin_url( 'customize.php?autofocus[panel]=panel-header-group' ),
		'site-layouts'          => admin_url( 'customize.php?autofocus[section]=section-container-layout' ),
		'advanced-footer'       => admin_url( 'customize.php?autofocus[section]=section-footer-group' ),
		'nav-menu'              => admin_url( 'customize.php?autofocus[panel]=panel-header-group' ),
		'woocommerce'           => admin_url( 'customize.php' ),
	);

	foreach ( $list as $slug => $info ) {
		if ( ! is_array( $info ) ) {
			continue;
		}
		$list[ $slug ]['isActive']  = true;
		$list[ $slug ]['title_url'] = isset( $customizer[ $slug ] ) ? $customizer[ $slug ] : admin_url( 'customize.php' );
	}

	return $list;
}

add_filter( 'astra_addon_list', 'custom_astra_fork_addon_list', 100 );

/**
 * Hide dashboard upgrade / Pro extension cards.
 *
 * @param array $localize Admin React data.
 * @return array
 */
function custom_astra_fork_admin_localize( $localize ) {
	if ( ! is_array( $localize ) ) {
		return $localize;
	}

	$localize['pro_available']      = true;
	$localize['upgrade_notice']     = false;
	$localize['show_plugins']       = false;
	$localize['useful_plugins']     = array();
	$localize['woo_extensions']     = array();
	$localize['show_banner_video']  = false;
	$localize['upgrade_url']        = '';
	$localize['astra_cta_btn_url']  = '';
	$localize['free_vs_pro_link']   = '';
	$localize['site_builder_url']   = '';
	$localize['is_whitelabel']      = true;
	$localize['show_self_branding'] = true;
	$localize['astra_rating_url']   = '';
	$localize['astra_docs_data']    = array();
	$localize['show_learn_tab']     = false;
	$localize['show_ai_assistant']  = false;

	$localize['starter_templates_data'] = array(
		'title'        => '',
		'description'  => '',
		'is_available' => false,
		'is_promoting' => false,
		'redirection'  => '',
		'icon_path'    => '',
		'slug'         => '',
		'status'       => '',
		'path'         => '',
	);

	if ( isset( $localize['astraWebsite'] ) && is_array( $localize['astraWebsite'] ) ) {
		$localize['astraWebsite']['baseUrl']                = FORGE_THEME_URI;
		$localize['astraWebsite']['docsUrl']                = '';
		$localize['astraWebsite']['docsCategoryDynamicUrl'] = '';
		$localize['astraWebsite']['vipPrioritySupportUrl']  = FORGE_THEME_URI;
		$localize['astraWebsite']['templatesUrl']           = '';
		$localize['astraWebsite']['whatsNewFeedUrl']        = '';
	}

	return $localize;
}

add_filter( 'astra_react_admin_localize', 'custom_astra_fork_admin_localize' );

/**
 * Hide leftover Astra dashboard routes and Pro-locked settings screens.
 *
 * @return void
 */
function custom_astra_fork_enqueue_dashboard_cleanup() {
	if ( empty( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$page = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'forge' !== $page && 'astra' !== $page ) {
		return;
	}

	wp_enqueue_style(
		'forge-dashboard',
		ASTRA_THEME_URI . 'inc/assets/css/forge-dashboard.css',
		array( 'astra-admin-dashboard-app' ),
		ASTRA_THEME_VERSION
	);

	wp_enqueue_script(
		'forge-dashboard',
		ASTRA_THEME_URI . 'inc/assets/js/forge-dashboard.js',
		array( 'wp-hooks' ),
		ASTRA_THEME_VERSION,
		true
	);

	wp_add_inline_script(
		'astra-admin-dashboard-app',
		'window.astra_addon_admin=window.astra_addon_admin||{update_nonce:"",forge_stub:true};'
		. 'if(window.wp&&wp.hooks){wp.hooks.addFilter("astra_dashboard.main_navigation","forge",function(i){return(i||[]).filter(function(t){return["free-vs-pro","starter-templates","site-builder","learn"].indexOf(t.path)===-1})});'
		. 'wp.hooks.addFilter("astra_dashboard.settings_navigation","forge",function(i){return(i||[]).filter(function(t){return["version-control","white-label","mcp"].indexOf(t.slug)===-1})});}',
		'before'
	);
}

add_action( 'admin_enqueue_scripts', 'custom_astra_fork_enqueue_dashboard_cleanup', 20 );

/**
 * Bust cached Astra dashboard JS after Forge patches.
 *
 * @param string $src    Script URL.
 * @param string $handle Script handle.
 * @return string
 */
function custom_astra_fork_bust_dashboard_cache( $src, $handle ) {
	if ( 'astra-admin-dashboard-app' === $handle ) {
		$src = add_query_arg( 'forge', '3', $src );
	}

	return $src;
}

add_filter( 'script_loader_src', 'custom_astra_fork_bust_dashboard_cache', 10, 2 );

<?php
/**
 * Native Forge admin dashboard. Replaces the empty Astra React app.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stop the compiled Astra React dashboard from loading on the Forge page.
 *
 * @return void
 */
function forge_dequeue_react_dashboard() {
	if ( empty( $_GET['page'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$page = sanitize_key( wp_unslash( $_GET['page'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'forge' !== $page && 'astra' !== $page ) {
		return;
	}

	wp_dequeue_script( 'astra-admin-dashboard-app' );
	wp_dequeue_style( 'astra-admin-dashboard-app' );
	wp_deregister_script( 'astra-admin-dashboard-app' );
	wp_deregister_style( 'astra-admin-dashboard-app' );

	wp_enqueue_style(
		'forge-admin-dashboard',
		ASTRA_THEME_URI . 'inc/assets/css/forge-admin-dashboard.css',
		array(),
		ASTRA_THEME_VERSION
	);
}

add_action( 'admin_enqueue_scripts', 'forge_dequeue_react_dashboard', 100 );

/**
 * Render the Forge dashboard.
 *
 * @return void
 */
function forge_render_admin_dashboard() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$quick   = class_exists( 'Astra_Menu' ) ? Astra_Menu::astra_get_quick_links() : array();
	$modules = class_exists( 'Astra_Menu' ) ? Astra_Menu::astra_get_pro_extensions() : array();
	$support = defined( 'FORGE_THEME_URI' ) ? FORGE_THEME_URI : 'https://hire.techjunkyben.nl';

	$settings = array(
		'global' => array(
			'title' => __( 'Global', 'astra' ),
			'items' => array(
				array(
					'title' => __( 'Site Identity', 'astra' ),
					'desc'  => __( 'Logo, site title, and favicon.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[control]=site_icon' ),
				),
				array(
					'title' => __( 'Container / Layout', 'astra' ),
					'desc'  => __( 'Page width, sidebar, and content layout.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[section]=section-container-layout' ),
				),
				array(
					'title' => __( 'Colors', 'astra' ),
					'desc'  => __( 'Global palette, links, and theme color.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[section]=section-colors-background' ),
				),
				array(
					'title' => __( 'Typography', 'astra' ),
					'desc'  => __( 'Body and heading fonts.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[section]=section-typography' ),
				),
				array(
					'title' => __( 'Buttons', 'astra' ),
					'desc'  => __( 'Button colors, padding, and radius.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[section]=section-buttons' ),
				),
				array(
					'title' => __( 'Blog', 'astra' ),
					'desc'  => __( 'Archive and single post layout.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[section]=section-blog-group' ),
				),
			),
		),
		'builder' => array(
			'title' => __( 'Header & footer builder', 'astra' ),
			'items' => array(
				array(
					'title' => __( 'Header', 'astra' ),
					'desc'  => __( 'Logo row, menus, and header slots.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[panel]=panel-header-group' ),
				),
				array(
					'title' => __( 'Footer', 'astra' ),
					'desc'  => __( 'Copyright, widgets, and footer slots.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[section]=section-footer-group' ),
				),
				array(
					'title' => __( 'Transparent header', 'astra' ),
					'desc'  => __( 'Overlap the header on the hero or page.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[section]=section-transparent-header' ),
				),
				array(
					'title' => __( 'Breadcrumbs', 'astra' ),
					'desc'  => __( 'Breadcrumb position and typography.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[section]=section-breadcrumb' ),
				),
				array(
					'title' => __( 'Scroll to top', 'astra' ),
					'desc'  => __( 'Back-to-top button.', 'astra' ),
					'url'   => admin_url( 'customize.php?autofocus[section]=section-scroll-to-top' ),
				),
			),
		),
	);
	?>
	<div class="wrap forge-dashboard">
		<h1><?php echo esc_html( defined( 'FORGE_THEME_NAME' ) ? FORGE_THEME_NAME : 'Forge' ); ?></h1>
		<p class="forge-dashboard__lead">
			<?php esc_html_e( 'Theme settings live in the Customizer. Use the shortcuts below.', 'astra' ); ?>
		</p>

		<p class="forge-dashboard__actions">
			<a class="button button-primary button-hero" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">
				<?php esc_html_e( 'Open Customizer', 'astra' ); ?>
			</a>
			<a class="button button-hero" href="<?php echo esc_url( $support ); ?>" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'VIP priority support', 'astra' ); ?>
			</a>
		</p>

		<?php if ( ! empty( $quick ) ) : ?>
			<h2><?php esc_html_e( 'Quick settings', 'astra' ); ?></h2>
			<div class="forge-dashboard__grid">
				<?php foreach ( $quick as $item ) : ?>
					<?php
					if ( empty( $item['title'] ) || empty( $item['quick_url'] ) ) {
						continue;
					}
					?>
					<a class="forge-dashboard__card" href="<?php echo esc_url( $item['quick_url'] ); ?>">
						<strong><?php echo esc_html( $item['title'] ); ?></strong>
						<span><?php esc_html_e( 'Customize', 'astra' ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php foreach ( $settings as $group ) : ?>
			<h2><?php echo esc_html( $group['title'] ); ?></h2>
			<div class="forge-dashboard__grid">
				<?php foreach ( $group['items'] as $item ) : ?>
					<a class="forge-dashboard__card" href="<?php echo esc_url( $item['url'] ); ?>">
						<strong><?php echo esc_html( $item['title'] ); ?></strong>
						<span><?php echo esc_html( $item['desc'] ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endforeach; ?>

		<?php if ( ! empty( $modules ) ) : ?>
			<h2><?php esc_html_e( 'Theme modules', 'astra' ); ?></h2>
			<p class="description">
				<?php esc_html_e( 'These open the matching Customizer panel.', 'astra' ); ?>
			</p>
			<div class="forge-dashboard__grid">
				<?php foreach ( $modules as $module ) : ?>
					<?php
					if ( empty( $module['title'] ) ) {
						continue;
					}
					$url = ! empty( $module['title_url'] ) ? $module['title_url'] : admin_url( 'customize.php' );
					?>
					<a class="forge-dashboard__card" href="<?php echo esc_url( $url ); ?>">
						<strong><?php echo esc_html( $module['title'] ); ?></strong>
						<span><?php esc_html_e( 'Open settings', 'astra' ); ?></span>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
	<?php
}

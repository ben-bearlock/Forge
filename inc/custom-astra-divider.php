<?php
/**
 * Header/footer builder dividers for the Custom Astra fork.
 *
 * Original simple line component. Not copied from Astra Pro.
 *
 * @package Astra
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default divider options.
 *
 * @param array $defaults Theme defaults.
 * @return array
 */
function custom_astra_fork_divider_defaults( $defaults ) {
	$limit = class_exists( 'Astra_Builder_Helper' ) ? (int) Astra_Builder_Helper::$component_limit : 10;

	for ( $index = 1; $index <= $limit; $index++ ) {
		$defaults[ 'header-divider-' . $index . '-layout' ] = 'vertical';
		$defaults[ 'header-divider-' . $index . '-color' ]  = '#d1d5db';
		$defaults[ 'header-divider-' . $index . '-size' ]   = 1;
		$defaults[ 'footer-divider-' . $index . '-layout' ] = 'horizontal';
		$defaults[ 'footer-divider-' . $index . '-color' ]  = '#d1d5db';
		$defaults[ 'footer-divider-' . $index . '-size' ]   = 1;
	}

	return $defaults;
}

add_filter( 'astra_theme_defaults', 'custom_astra_fork_divider_defaults' );

/**
 * Parse a builder slug like divider-3.
 *
 * @param string $slug Component slug.
 * @return int
 */
function custom_astra_fork_divider_index_from_slug( $slug ) {
	if ( preg_match( '/^divider-(\d+)$/', (string) $slug, $matches ) ) {
		return (int) $matches[1];
	}
	return 0;
}

/**
 * Render one divider.
 *
 * @param string $builder header|footer.
 * @param int    $index   1-based index.
 * @return void
 */
function custom_astra_fork_render_divider( $builder, $index ) {
	$index = absint( $index );
	if ( $index < 1 ) {
		return;
	}

	$prefix  = 'footer' === $builder ? 'footer' : 'header';
	$layout  = astra_get_option( $prefix . '-divider-' . $index . '-layout', 'header' === $prefix ? 'vertical' : 'horizontal' );
	$section = 'header' === $prefix ? 'section-hb-divider-' . $index : 'section-fb-divider-' . $index;
	$class   = 'header' === $prefix
		? 'ast-builder-layout-element ast-flex site-header-focus-item ast-header-divider-element ast-header-divider-' . $index . ' ast-hb-divider-layout-' . $layout
		: 'footer-widget-area widget-area ast-flex site-footer-focus-item ast-footer-divider-element ast-footer-divider-' . $index . ' ast-fb-divider-layout-' . $layout;

	if ( is_customize_preview() && class_exists( 'Astra_Builder_UI_Controller' ) ) {
		echo '<div class="' . esc_attr( $class ) . '" data-section="' . esc_attr( $section ) . '">';
		Astra_Builder_UI_Controller::render_customizer_edit_button();
		echo '<span class="ast-divider-wrapper"></span></div>';
		return;
	}

	echo '<div class="' . esc_attr( $class ) . '" data-section="' . esc_attr( $section ) . '"><span class="ast-divider-wrapper"></span></div>';
}

/**
 * Header builder fallback for divider-N.
 *
 * @param string $slug   Component slug.
 * @param string $device Device.
 * @return void
 */
function custom_astra_fork_render_header_divider( $slug, $device = '' ) {
	unset( $device );
	$index = custom_astra_fork_divider_index_from_slug( $slug );
	if ( $index ) {
		custom_astra_fork_render_divider( 'header', $index );
	}
}

add_action( 'astra_render_header_components', 'custom_astra_fork_render_header_divider', 10, 2 );

/**
 * Footer builder fallback for divider-N.
 *
 * @param string $slug Component slug.
 * @return void
 */
function custom_astra_fork_render_footer_divider_slug( $slug ) {
	$index = custom_astra_fork_divider_index_from_slug( $slug );
	if ( $index ) {
		custom_astra_fork_render_divider( 'footer', $index );
	}
}

add_action( 'astra_render_footer_components', 'custom_astra_fork_render_footer_divider_slug' );

/**
 * Footer template still fires astra_footer_divider_1 for the first slot.
 *
 * @return void
 */
function custom_astra_fork_bind_footer_divider_actions() {
	$limit = class_exists( 'Astra_Builder_Helper' ) ? (int) Astra_Builder_Helper::$num_of_footer_divider : 10;
	for ( $index = 1; $index <= $limit; $index++ ) {
		add_action(
			'astra_footer_divider_' . $index,
			static function () use ( $index ) {
				echo '<span class="ast-divider-wrapper"></span>';
			}
		);
	}
}

add_action( 'init', 'custom_astra_fork_bind_footer_divider_actions' );

/**
 * Divider CSS.
 *
 * @param string $css Dynamic CSS.
 * @return string
 */
function custom_astra_fork_divider_css( $css ) {
	$limit = class_exists( 'Astra_Builder_Helper' ) ? (int) Astra_Builder_Helper::$component_limit : 10;
	$out   = '
	.ast-header-divider-element,
	.ast-footer-divider-element {
		align-items: center;
		justify-content: center;
	}
	.ast-divider-wrapper {
		display: block;
		border-style: solid;
		border-width: 0;
	}
	';

	for ( $index = 1; $index <= $limit; $index++ ) {
		foreach ( array( 'header', 'footer' ) as $builder ) {
			$color  = astra_get_option( $builder . '-divider-' . $index . '-color', '#d1d5db' );
			$size   = absint( astra_get_option( $builder . '-divider-' . $index . '-size', 1 ) );
			$layout = astra_get_option( $builder . '-divider-' . $index . '-layout', 'header' === $builder ? 'vertical' : 'horizontal' );
			if ( $size < 1 ) {
				$size = 1;
			}

			$sel = 'header' === $builder
				? '.ast-header-divider-' . $index . ' .ast-divider-wrapper'
				: '.ast-footer-divider-' . $index . ' .ast-divider-wrapper';

			if ( 'horizontal' === $layout ) {
				$out .= $sel . '{border-top-width:' . $size . 'px;border-color:' . esc_attr( $color ) . ';width:24px;height:0;}';
			} else {
				$out .= $sel . '{border-left-width:' . $size . 'px;border-color:' . esc_attr( $color ) . ';height:24px;width:0;}';
			}
		}
	}

	if ( class_exists( 'Astra_Enqueue_Scripts' ) ) {
		return $css . Astra_Enqueue_Scripts::trim_css( $out );
	}

	return $css . $out;
}

add_filter( 'astra_dynamic_theme_css', 'custom_astra_fork_divider_css' );

/**
 * Customizer controls for each divider.
 *
 * @param array $configurations Configs.
 * @return array
 */
function custom_astra_fork_divider_customizer( $configurations ) {
	$limit  = class_exists( 'Astra_Builder_Helper' ) ? (int) Astra_Builder_Helper::$component_limit : 10;
	$extra  = array();
	$panels = array(
		'header' => array(
			'count' => class_exists( 'Astra_Builder_Helper' ) ? (int) Astra_Builder_Helper::$num_of_header_divider : $limit,
			'panel' => 'panel-header-builder-group',
			'sec'   => 'section-hb-divider-',
			'opt'   => 'header-divider-',
		),
		'footer' => array(
			'count' => class_exists( 'Astra_Builder_Helper' ) ? (int) Astra_Builder_Helper::$num_of_footer_divider : $limit,
			'panel' => 'panel-footer-builder-group',
			'sec'   => 'section-fb-divider-',
			'opt'   => 'footer-divider-',
		),
	);

	foreach ( $panels as $builder => $info ) {
		for ( $index = 1; $index <= $info['count']; $index++ ) {
			$section = $info['sec'] . $index;
			$prefix  = $info['opt'] . $index;

			$extra[] = array(
				'name'     => $section,
				'type'     => 'section',
				'priority' => 50,
				'title'    => sprintf(
					/* translators: %d divider index */
					__( 'Divider %d', 'astra' ),
					$index
				),
				'panel'    => $info['panel'],
			);

			$extra[] = array(
				'name'     => ASTRA_THEME_SETTINGS . '[' . $prefix . '-layout]',
				'default'  => astra_get_option( $prefix . '-layout' ),
				'type'     => 'control',
				'control'  => 'ast-select',
				'section'  => $section,
				'priority' => 10,
				'title'    => __( 'Layout', 'astra' ),
				'choices'  => array(
					'vertical'   => __( 'Vertical', 'astra' ),
					'horizontal' => __( 'Horizontal', 'astra' ),
				),
			);

			$extra[] = array(
				'name'     => ASTRA_THEME_SETTINGS . '[' . $prefix . '-size]',
				'default'  => astra_get_option( $prefix . '-size' ),
				'type'     => 'control',
				'control'  => 'ast-slider',
				'section'  => $section,
				'priority' => 15,
				'title'    => __( 'Thickness', 'astra' ),
				'suffix'   => 'px',
				'input_attrs' => array(
					'min'  => 1,
					'step' => 1,
					'max'  => 12,
				),
			);

			$extra[] = array(
				'name'     => ASTRA_THEME_SETTINGS . '[' . $prefix . '-color]',
				'default'  => astra_get_option( $prefix . '-color' ),
				'type'     => 'control',
				'control'  => 'ast-color',
				'section'  => $section,
				'priority' => 20,
				'title'    => __( 'Color', 'astra' ),
			);
		}
	}

	return array_merge( $configurations, $extra );
}

add_filter( 'astra_customizer_configurations', 'custom_astra_fork_divider_customizer', 40 );

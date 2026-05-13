<?php
/**
 * Window Shopping theme setup.
 *
 * @package Window_Shopping
 */

defined( 'ABSPATH' ) || exit;

/**
 * Configure theme support.
 *
 * @return void
 */
function window_shopping_setup() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'responsive-embeds' );

	add_editor_style( 'assets/css/theme.css' );
}
add_action( 'after_setup_theme', 'window_shopping_setup' );

/**
 * Register pattern categories used by the theme.
 *
 * @return void
 */
function window_shopping_register_pattern_categories() {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	register_block_pattern_category(
		'window-shopping',
		array( 'label' => __( 'Window Shopping', 'window-shopping' ) )
	);

	register_block_pattern_category(
		'window-shopping-store',
		array( 'label' => __( 'Window Shopping: Store', 'window-shopping' ) )
	);

	register_block_pattern_category(
		'window-shopping-product',
		array( 'label' => __( 'Window Shopping: Product', 'window-shopping' ) )
	);

	register_block_pattern_category(
		'window-shopping-checkout',
		array( 'label' => __( 'Window Shopping: Checkout', 'window-shopping' ) )
	);
}
add_action( 'init', 'window_shopping_register_pattern_categories' );

/**
 * Curated commerce block styles used by Window Shopping.
 *
 * @return array<string, array<int, array{slug:string,label:string}>>
 */
function window_shopping_block_styles() {
	return array(
		'woocommerce/product-collection' => array(
			array( 'slug' => 'classic-refined', 'label' => __( 'Classic Refined', 'window-shopping' ) ),
			array( 'slug' => 'editorial-magazine', 'label' => __( 'Editorial Magazine', 'window-shopping' ) ),
			array( 'slug' => 'showcase-hero', 'label' => __( 'Showcase Hero', 'window-shopping' ) ),
			array( 'slug' => 'bento-mixed', 'label' => __( 'Bento Mixed', 'window-shopping' ) ),
			array( 'slug' => 'index-card', 'label' => __( 'Index Card', 'window-shopping' ) ),
			array( 'slug' => 'feature-and-supporting', 'label' => __( 'Feature + Supporting', 'window-shopping' ) ),
		),
		'woocommerce/product-details' => array(
			array( 'slug' => 'preview-strip', 'label' => __( 'Preview Strip', 'window-shopping' ) ),
			array( 'slug' => 'description-first', 'label' => __( 'Description First', 'window-shopping' ) ),
			array( 'slug' => 'spec-sheet', 'label' => __( 'Spec Sheet', 'window-shopping' ) ),
			array( 'slug' => 'field-notebook', 'label' => __( 'Field Notebook', 'window-shopping' ) ),
		),
		'woocommerce/product-image-gallery' => array(
			array( 'slug' => 'mosaic-gallery', 'label' => __( 'Mosaic Gallery', 'window-shopping' ) ),
			array( 'slug' => 'side-rail-gallery', 'label' => __( 'Side Rail Gallery', 'window-shopping' ) ),
		),
		'woocommerce/product-rating' => array(
			array( 'slug' => 'score-pill', 'label' => __( 'Score Pill', 'window-shopping' ) ),
			array( 'slug' => 'compact-stars', 'label' => __( 'Compact Stars', 'window-shopping' ) ),
		),
		'woocommerce/cart' => array(
			array( 'slug' => 'summary-rail', 'label' => __( 'Summary Rail', 'window-shopping' ) ),
			array( 'slug' => 'checkout-funnel', 'label' => __( 'Checkout Funnel', 'window-shopping' ) ),
		),
		'woocommerce/checkout' => array(
			array( 'slug' => 'summary-sidebar', 'label' => __( 'Summary Sidebar', 'window-shopping' ) ),
			array( 'slug' => 'details-ledger', 'label' => __( 'Details Ledger', 'window-shopping' ) ),
		),
		'woocommerce/mini-cart' => array(
			array( 'slug' => 'header-pill', 'label' => __( 'Header Pill', 'window-shopping' ) ),
			array( 'slug' => 'icon-badge', 'label' => __( 'Icon Badge', 'window-shopping' ) ),
		),
		'woocommerce/customer-account' => array(
			array( 'slug' => 'account-pill', 'label' => __( 'Account Pill', 'window-shopping' ) ),
			array( 'slug' => 'icon-only-entry', 'label' => __( 'Icon Only Entry', 'window-shopping' ) ),
		),
		'woocommerce/store-notices' => array(
			array( 'slug' => 'banner-notice', 'label' => __( 'Banner Notice', 'window-shopping' ) ),
			array( 'slug' => 'split-notice', 'label' => __( 'Split Notice', 'window-shopping' ) ),
		),
	);
}

/**
 * Register editor-selectable block styles.
 *
 * @return void
 */
function window_shopping_register_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) {
		return;
	}

	foreach ( window_shopping_block_styles() as $block_name => $styles ) {
		foreach ( $styles as $style ) {
			register_block_style(
				$block_name,
				array(
					'name'  => $style['slug'],
					'label' => $style['label'],
				)
			);
		}
	}
}
add_action( 'init', 'window_shopping_register_block_styles' );

/**
 * Use larger WooCommerce-generated images for the roomy product layouts.
 *
 * @return array{width:int,height:int,crop:int}
 */
function window_shopping_woocommerce_single_image_size() {
	return array(
		'width'  => 1600,
		'height' => 1600,
		'crop'   => 1,
	);
}
add_filter( 'woocommerce_get_image_size_single', 'window_shopping_woocommerce_single_image_size' );

/**
 * Keep archive thumbnails crisp on dense product grids and high-DPI screens.
 *
 * @return array{width:int,height:int,crop:int}
 */
function window_shopping_woocommerce_thumbnail_image_size() {
	return array(
		'width'  => 900,
		'height' => 900,
		'crop'   => 1,
	);
}
add_filter( 'woocommerce_get_image_size_thumbnail', 'window_shopping_woocommerce_thumbnail_image_size' );

/**
 * Enqueue the small CSS layer used for WooCommerce runtime polish.
 *
 * @return void
 */
function window_shopping_enqueue_assets() {
	$theme         = wp_get_theme();
	$css_path      = get_theme_file_path( 'assets/css/theme.css' );
	$asset_version = file_exists( $css_path ) ? (string) filemtime( $css_path ) : $theme->get( 'Version' );

	wp_enqueue_style(
		'window-shopping-theme',
		get_theme_file_uri( 'assets/css/theme.css' ),
		array(),
		$asset_version
	);
}
add_action( 'enqueue_block_assets', 'window_shopping_enqueue_assets' );

/**
 * Add a stable body class so theme CSS can target only this theme.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function window_shopping_body_classes( $classes ) {
	$classes[] = 'window-shopping-theme';
	$classes[] = 'window-shopping-style-' . window_shopping_active_style_slug();

	return $classes;
}
add_filter( 'body_class', 'window_shopping_body_classes' );

/**
 * Style variation slugs supported by the theme.
 *
 * @return string[]
 */
function window_shopping_style_slugs() {
	return array( 'studio', 'oddities', 'atelier', 'field-supply', 'pantry', 'signal' );
}

/**
 * Get the active Site Editor style slug.
 *
 * WordPress stores the chosen style variation in the wp_global_styles post.
 * The theme uses this to render a matching front-page hero pattern while
 * leaving the rest of the design in theme.json and block markup.
 *
 * @return string
 */
function window_shopping_active_style_slug() {
	static $active_slug = null;

	if ( null === $active_slug ) {
		$active_slug = 'studio';
		$valid_slugs = window_shopping_style_slugs();
		$posts       = get_posts(
			array(
				'post_type'        => 'wp_global_styles',
				'post_status'      => 'any',
				'numberposts'      => 1,
				'tax_query'        => array(
					array(
						'taxonomy' => 'wp_theme',
						'field'    => 'name',
						'terms'    => get_stylesheet(),
					),
				),
				'suppress_filters' => false,
			)
		);

		if ( ! empty( $posts ) ) {
			$data       = json_decode( $posts[0]->post_content, true );
			$candidates = array(
				isset( $data['title'] ) ? $data['title'] : '',
				$posts[0]->post_title,
				$posts[0]->post_name,
			);

			foreach ( $candidates as $candidate ) {
				$slug = sanitize_title( $candidate );

				if ( in_array( $slug, $valid_slugs, true ) ) {
					$active_slug = $slug;
					break;
				}
			}
		}
	}

	return apply_filters( 'window_shopping_active_style_slug', $active_slug );
}

/**
 * Hero pattern copy and merchandising labels for each style variation.
 *
 * @return array<string, array<string, string>>
 */
function window_shopping_hero_variants() {
	return array(
		'studio'       => array(
			'kicker'        => __( 'Curated objects', 'window-shopping' ),
			'title'         => __( 'Timeless design. Considered living.', 'window-shopping' ),
			'body'          => __( 'A curated edit of objects and home goods designed to be lived with and loved for years to come. Simple, functional, beautiful.', 'window-shopping' ),
			'label'         => __( 'Curated objects', 'window-shopping' ),
			'note'          => __( 'New arrivals in the window', 'window-shopping' ),
			'primary'       => __( 'Shop new in', 'window-shopping' ),
			'secondary'     => __( 'Browse collections', 'window-shopping' ),
			'primary_url'   => '/shop/?orderby=date',
			'secondary_url' => '#featured-products',
		),
		'oddities'    => array(
			'kicker'        => __( 'Weird is wonderful', 'window-shopping' ),
			'title'         => __( "Gifts they didn't know they needed.", 'window-shopping' ),
			'body'          => __( 'Delightfully odd gifts, curious finds, and joyful pieces for every kind of wonder.', 'window-shopping' ),
			'label'         => __( 'Live shelf', 'window-shopping' ),
			'note'          => __( 'New arrivals in the window', 'window-shopping' ),
			'primary'       => __( 'Shop the oddities', 'window-shopping' ),
			'secondary'     => __( 'Explore collections', 'window-shopping' ),
			'primary_url'   => '/shop/',
			'secondary_url' => '#featured-products',
		),
		'atelier'     => array(
			'kicker'        => __( 'Curated editorial', 'window-shopping' ),
			'title'         => __( 'Pieces that wear well. Live well.', 'window-shopping' ),
			'body'          => __( 'Timeless design. Modern craft.', 'window-shopping' ),
			'label'         => __( 'Live well', 'window-shopping' ),
			'note'          => __( 'New arrivals in the window', 'window-shopping' ),
			'primary'       => __( 'Explore the edit', 'window-shopping' ),
			'secondary'     => '',
			'primary_url'   => '/shop/',
			'secondary_url' => '',
		),
		'field-supply' => array(
			'kicker'        => __( 'Built for the everyday', 'window-shopping' ),
			'title'         => __( 'Gear that goes farther.', 'window-shopping' ),
			'body'          => __( "Durable, tested, and ready for real life. Thoughtful gear for wherever you're headed.", 'window-shopping' ),
			'label'         => __( 'Featured gear', 'window-shopping' ),
			'note'          => __( 'View all', 'window-shopping' ),
			'primary'       => __( 'Shop new arrivals', 'window-shopping' ),
			'secondary'     => __( 'Explore collections', 'window-shopping' ),
			'primary_url'   => '/shop/?orderby=date',
			'secondary_url' => '#featured-products',
		),
		'pantry'      => array(
			'kicker'        => __( 'Good food, made simple', 'window-shopping' ),
			'title'         => __( 'Quality ingredients. Made for real life.', 'window-shopping' ),
			'body'          => __( 'Thoughtfully sourced groceries and home staples for everyday cooking and living.', 'window-shopping' ),
			'label'         => __( 'Pantry staples', 'window-shopping' ),
			'note'          => __( 'New arrivals in the pantry', 'window-shopping' ),
			'primary'       => __( 'Shop new arrivals', 'window-shopping' ),
			'secondary'     => __( 'Explore collections', 'window-shopping' ),
			'primary_url'   => '/shop/?orderby=date',
			'secondary_url' => '#featured-products',
		),
		'signal'      => array(
			'kicker'        => __( 'Tech. Accessories. Essentials.', 'window-shopping' ),
			'title'         => __( 'Designed to connect and perform.', 'window-shopping' ),
			'body'          => __( 'Smart gear for modern life. Engineered for performance. Built to move with you.', 'window-shopping' ),
			'label'         => __( 'Featured gear', 'window-shopping' ),
			'note'          => __( 'View all', 'window-shopping' ),
			'primary'       => __( 'Shop new arrivals', 'window-shopping' ),
			'secondary'     => __( 'Explore collections', 'window-shopping' ),
			'primary_url'   => '/shop/?orderby=date',
			'secondary_url' => '#featured-products',
		),
	);
}

/**
 * Render one storefront hero variant.
 *
 * @param string $slug Style variation slug.
 * @return void
 */
function window_shopping_render_hero_pattern( $slug = 'studio' ) {
	$variants = window_shopping_hero_variants();
	$slug     = isset( $variants[ $slug ] ) ? $slug : 'studio';
	$args     = $variants[ $slug ];
	$class    = 'ws-hero ws-hero--' . $slug;
	$buttons  = array(
		'primary'   => array(
			'label' => $args['primary'],
			'url'   => $args['primary_url'],
			'class' => '',
		),
		'secondary' => array(
			'label' => $args['secondary'],
			'url'   => $args['secondary_url'],
			'class' => ' is-style-outline',
		),
	);

	echo '<!-- wp:group {"align":"full","className":"' . esc_attr( $class ) . '","style":{"border":{"bottom":{"color":"var:preset|color|muted","width":"1px"}},"dimensions":{"minHeight":"min(82vh, 820px)"},"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"backgroundColor":"base","layout":{"type":"constrained"}} -->';
	echo '<div class="wp-block-group alignfull ' . esc_attr( $class ) . ' has-base-background-color has-background" style="border-bottom-color:var(--wp--preset--color--muted);border-bottom-width:1px;min-height:min(82vh, 820px);padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">';
	echo '<!-- wp:group {"align":"wide","layout":{"type":"constrained"}} -->';
	echo '<div class="wp-block-group alignwide">';
	echo '<!-- wp:columns {"align":"wide","verticalAlignment":"stretch","className":"ws-hero-columns","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60"}}}} -->';
	echo '<div class="wp-block-columns alignwide are-vertically-aligned-stretch ws-hero-columns">';
	echo '<!-- wp:column {"verticalAlignment":"top","width":"46%","className":"ws-hero-copy","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","bottom":"var:preset|spacing|50"}}}} -->';
	echo '<div class="wp-block-column is-vertically-aligned-top ws-hero-copy" style="padding-top:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);flex-basis:46%">';
	echo '<!-- wp:paragraph {"className":"ws-kicker","style":{"typography":{"fontWeight":"760","letterSpacing":"0","textTransform":"uppercase"}},"textColor":"accent","fontFamily":"mono","fontSize":"small"} -->';
	echo '<p class="ws-kicker has-accent-color has-text-color has-mono-font-family has-small-font-size" style="font-weight:760;letter-spacing:0;text-transform:uppercase">' . esc_html( $args['kicker'] ) . '</p>';
	echo '<!-- /wp:paragraph -->';

	if ( 'oddities' === $slug ) {
		echo '<!-- wp:html -->';
		echo '<div class="ws-hero-decals" aria-hidden="true"><span>Fun stuff</span><span>!</span></div>';
		echo '<!-- /wp:html -->';
	}

	echo '<!-- wp:heading {"level":1,"style":{"spacing":{"margin":{"top":"var(--wp--custom--window--hero-heading-margin-top,var(--wp--preset--spacing--30))","bottom":"var(--wp--custom--window--hero-heading-margin-bottom,var(--wp--preset--spacing--30))"}},"typography":{"lineHeight":"1"}},"fontSize":"display"} -->';
	echo '<h1 class="wp-block-heading has-display-font-size" style="margin-top:var(--wp--custom--window--hero-heading-margin-top,var(--wp--preset--spacing--30));margin-bottom:var(--wp--custom--window--hero-heading-margin-bottom,var(--wp--preset--spacing--30));line-height:1">' . esc_html( $args['title'] ) . '</h1>';
	echo '<!-- /wp:heading -->';
	echo '<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|muted"},"spacing":{"margin":{"top":"var(--wp--custom--window--hero-text-margin-top,0)","bottom":"var(--wp--custom--window--hero-text-margin-bottom,var(--wp--preset--spacing--40))"}},"typography":{"fontSize":"var(--wp--custom--window--hero-text-font-size,1.15rem)","lineHeight":"var(--wp--custom--window--hero-text-line-height,1.55)"}}} -->';
	echo '<p class="has-text-color" style="color:var(--wp--preset--color--muted);margin-top:var(--wp--custom--window--hero-text-margin-top,0);margin-bottom:var(--wp--custom--window--hero-text-margin-bottom,var(--wp--preset--spacing--40));font-size:var(--wp--custom--window--hero-text-font-size,1.15rem);line-height:var(--wp--custom--window--hero-text-line-height,1.55)">' . esc_html( $args['body'] ) . '</p>';
	echo '<!-- /wp:paragraph -->';
	echo '<!-- wp:buttons {"style":{"spacing":{"blockGap":"var(--wp--custom--window--hero-button-gap,0.75rem)","margin":{"top":"var(--wp--custom--window--hero-button-margin-top,0)"}}}} -->';
	echo '<div class="wp-block-buttons" style="gap:var(--wp--custom--window--hero-button-gap,0.75rem);margin-top:var(--wp--custom--window--hero-button-margin-top,0)">';

	foreach ( $buttons as $button ) {
		if ( empty( $button['label'] ) || empty( $button['url'] ) ) {
			continue;
		}

		echo '<!-- wp:button {"className":"' . esc_attr( trim( $button['class'] ) ) . '"} -->';
		echo '<div class="wp-block-button' . esc_attr( $button['class'] ) . '"><a class="wp-block-button__link wp-element-button" href="' . esc_url( $button['url'] ) . '">' . esc_html( $button['label'] ) . '</a></div>';
		echo '<!-- /wp:button -->';
	}

	echo '</div>';
	echo '<!-- /wp:buttons -->';
	echo '</div>';
	echo '<!-- /wp:column -->';
	echo '<!-- wp:column {"verticalAlignment":"center","width":"54%","className":"ws-hero-display"} -->';
	echo '<div class="wp-block-column is-vertically-aligned-center ws-hero-display" style="flex-basis:54%">';
	echo '<!-- wp:group {"className":"ws-window-display","style":{"border":{"color":"var:preset|color|muted","width":"1px","radius":"18px"},"dimensions":{"minHeight":"34rem"},"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"shadow":"var:preset|shadow|crisp"},"backgroundColor":"surface","layout":{"type":"constrained"}} -->';
	echo '<div class="wp-block-group ws-window-display has-surface-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:18px;min-height:34rem;padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40);box-shadow:var(--wp--preset--shadow--crisp)">';
	echo '<!-- wp:group {"className":"ws-window-label","style":{"spacing":{"margin":{"bottom":"var(--wp--custom--window--display-label-gap,var(--wp--preset--spacing--30))"}}},"textColor":"muted","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"center"}} -->';
	echo '<div class="wp-block-group ws-window-label has-muted-color has-text-color" style="margin-bottom:var(--wp--custom--window--display-label-gap,var(--wp--preset--spacing--30))">';
	echo '<!-- wp:paragraph {"className":"ws-kicker","style":{"spacing":{"margin":{"top":"0","bottom":"0"}},"typography":{"fontWeight":"760","letterSpacing":"0","textTransform":"uppercase"}},"textColor":"accent","fontFamily":"mono","fontSize":"small"} -->';
	echo '<p class="ws-kicker has-accent-color has-text-color has-mono-font-family has-small-font-size" style="margin-top:0;margin-bottom:0;font-weight:760;letter-spacing:0;text-transform:uppercase">' . esc_html( $args['label'] ) . '</p>';
	echo '<!-- /wp:paragraph -->';
	echo '<!-- wp:paragraph {"style":{"spacing":{"margin":{"top":"0","bottom":"0"}}},"fontSize":"small"} -->';
	echo '<p class="has-small-font-size" style="margin-top:0;margin-bottom:0">' . esc_html( $args['note'] ) . '</p>';
	echo '<!-- /wp:paragraph -->';
	echo '</div>';
	echo '<!-- /wp:group -->';
	echo '<!-- wp:pattern {"slug":"window-shopping/product-shelf"} /-->';
	echo '</div>';
	echo '<!-- /wp:group -->';
	echo '</div>';
	echo '<!-- /wp:column -->';
	echo '</div>';
	echo '<!-- /wp:columns -->';
	echo '</div>';
	echo '<!-- /wp:group -->';
	echo '</div>';
	echo '<!-- /wp:group -->';
}

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
 * Whether the current request is rendering the front-page merchandising surface.
 *
 * The home page opens with a product-heavy hero shelf, so those product photos
 * need to be available immediately instead of waiting on lazy-loading heuristics.
 *
 * @return bool
 */
function window_shopping_is_home_merchandising_image_context() {
	if ( is_admin() ) {
		return false;
	}

	return is_front_page() || is_home();
}

/**
 * Load front-page Woo Product Image block photos eagerly.
 *
 * @param string $loading_attr The loading attribute chosen by WooCommerce.
 * @param int    $image_id     Target attachment ID.
 * @return string
 */
function window_shopping_home_product_image_loading_attr( $loading_attr, $image_id ) {
	if ( window_shopping_is_home_merchandising_image_context() ) {
		return 'eager';
	}

	return $loading_attr;
}
add_filter( 'woocommerce_product_image_loading_attr', 'window_shopping_home_product_image_loading_attr', 10, 2 );

/**
 * Keep WooCommerce thumbnail output consistent with the eager home shelf images.
 *
 * @param array<string, string> $attr       Attachment image attributes.
 * @param WP_Post              $attachment Attachment post object.
 * @param string|int[]         $size       Requested image size.
 * @return array<string, string>
 */
function window_shopping_home_woocommerce_thumbnail_attributes( $attr, $attachment, $size ) {
	if ( ! window_shopping_is_home_merchandising_image_context() || empty( $attr['class'] ) ) {
		return $attr;
	}

	if ( false === strpos( (string) $attr['class'], 'woocommerce_thumbnail' ) ) {
		return $attr;
	}

	$attr['loading'] = 'eager';

	if ( ! empty( $attr['sizes'] ) ) {
		$attr['sizes'] = preg_replace( '/^auto,\s*/', '', (string) $attr['sizes'] );
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'window_shopping_home_woocommerce_thumbnail_attributes', 10, 3 );

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
				'orderby'          => 'date',
				'order'            => 'DESC',
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
 * Render a style-aware storefront hero product shelf.
 *
 * @param string $slug Style variation slug.
 * @return void
 */
function window_shopping_render_hero_product_shelf( $slug = 'studio' ) {
	$shelves = array(
		'studio'       => array( 'category' => 'studio', 'per_page' => 4, 'columns' => 4, 'products' => array( 'velvet-utility-tote', 'desk-beacon-dock', 'bottled-morning', 'cable-index-pack' ) ),
		'oddities'    => array( 'category' => 'oddities', 'per_page' => 3, 'columns' => 3, 'products' => array( 'moonlit-receipt-box', 'pocket-thunder', 'trail-tin-kit' ) ),
		'atelier'     => array( 'category' => 'atelier', 'per_page' => 3, 'columns' => 3, 'products' => array( 'ribbon-hem-shirt', 'velvet-utility-tote', 'camp-ledger-jacket' ) ),
		'field-supply' => array( 'category' => 'field-supply', 'per_page' => 3, 'columns' => 3, 'products' => array( 'camp-ledger-jacket', 'trail-tin-kit', 'cable-index-pack' ) ),
		'pantry'      => array( 'category' => 'pantry', 'per_page' => 4, 'columns' => 4, 'products' => array( 'bottled-morning', 'countertop-oil-duo', 'market-citrus-crate', 'sunday-jam-set' ) ),
		'signal'      => array( 'category' => 'signal', 'per_page' => 3, 'columns' => 3, 'products' => array( 'signal-wireless-headphones', 'signal-usb-c-hub', 'signal-tech-pouch' ) ),
	);
	$config  = isset( $shelves[ $slug ] ) ? $shelves[ $slug ] : $shelves['studio'];
	$term    = get_term_by( 'slug', $config['category'], 'product_cat' );
	$product_ids = array();
	foreach ( $config['products'] as $product_slug ) {
		$product = get_page_by_path( $product_slug, OBJECT, 'product' );
		if ( $product ) {
			$product_ids[] = (int) $product->ID;
		}
	}
	$query   = array(
		'perPage'                    => $config['per_page'],
		'pages'                      => 0,
		'offset'                     => 0,
		'postType'                   => 'product',
		'order'                      => 'asc',
		'orderBy'                    => $product_ids ? 'post__in' : 'title',
		'author'                     => '',
		'search'                     => '',
		'exclude'                    => array(),
		'sticky'                     => '',
		'inherit'                    => false,
		'taxQuery'                   => $product_ids ? array() : ( $term ? array( 'product_cat' => array( (int) $term->term_id ) ) : array() ),
		'isProductCollectionBlock'   => true,
		'featured'                   => false,
		'woocommerceOnSale'          => false,
		'woocommerceStockStatus'     => array( 'instock', 'outofstock', 'onbackorder' ),
		'woocommerceAttributes'      => array(),
		'woocommerceHandPickedProducts' => $product_ids,
		'filterable'                 => false,
	);
	$attrs   = array(
		'queryId'              => 110 + array_search( $slug, array_keys( $shelves ), true ),
		'query'                => $query,
		'tagName'              => 'div',
		'displayLayout'        => array(
			'type'          => 'flex',
			'columns'       => $config['columns'],
			'shrinkColumns' => false,
		),
		'dimensions'           => array( 'widthType' => 'fill' ),
		'queryContextIncludes' => array( 'collection' ),
		'align'                => 'wide',
		'className'            => 'ws-product-grid ws-hero-shelf ws-hero-shelf--columns-' . $config['columns'] . ' is-style-index-card',
	);

	echo '<!-- wp:woocommerce/product-collection ' . wp_json_encode( $attrs ) . ' -->';
	echo '<div class="wp-block-woocommerce-product-collection alignwide ws-product-grid ws-hero-shelf ws-hero-shelf--columns-' . esc_attr( $config['columns'] ) . ' is-style-index-card">';
	echo '<!-- wp:woocommerce/product-template -->';
	echo '<!-- wp:woocommerce/product-image {"showProductLink":true,"imageSizing":"thumbnail","isDescendentOfQueryLoop":true,"scale":"cover"} /-->';
	echo '<!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"lineHeight":"var(--wp--custom--window--showcase-title-line-height,1.2)"},"spacing":{"margin":{"top":"var(--wp--custom--window--showcase-title-margin-top,0.85rem)","bottom":"var(--wp--custom--window--showcase-title-margin-bottom,0.35rem)"}}},"fontSize":"medium","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->';
	echo '<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"fontSize":"small"} /-->';
	echo '<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true,"textAlign":"left","fontSize":"small","style":{"spacing":{"margin":{"top":"var(--wp--custom--window--showcase-button-margin-top,0.85rem)"}}}} /-->';
	echo '<!-- /wp:woocommerce/product-template -->';
	echo '<!-- wp:woocommerce/product-collection-no-results --><div class="wp-block-woocommerce-product-collection-no-results"><!-- wp:paragraph --><p>Add a few products to see this shelf come alive.</p><!-- /wp:paragraph --></div><!-- /wp:woocommerce/product-collection-no-results -->';
	echo '</div>';
	echo '<!-- /wp:woocommerce/product-collection -->';
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

	echo '<!-- wp:group {"align":"full","className":"' . esc_attr( $class ) . '","style":{"border":{"bottom":{"color":"var:preset|color|muted","width":"1px"}},"dimensions":{"minHeight":"var(--wp--custom--window--hero-min-height)"},"spacing":{"padding":{"top":"var(--wp--custom--window--hero-padding-top)","bottom":"var(--wp--custom--window--hero-padding-bottom)"}}},"backgroundColor":"base","layout":{"type":"constrained"}} -->';
	echo '<div class="wp-block-group alignfull ' . esc_attr( $class ) . ' has-base-background-color has-background" style="border-bottom-color:var(--wp--preset--color--muted);border-bottom-width:1px;min-height:var(--wp--custom--window--hero-min-height);padding-top:var(--wp--custom--window--hero-padding-top);padding-bottom:var(--wp--custom--window--hero-padding-bottom)">';
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
	echo '<!-- wp:group {"className":"ws-window-display","style":{"border":{"color":"var:preset|color|muted","width":"1px","radius":"var(--wp--custom--window--display-radius)"},"dimensions":{"minHeight":"var(--wp--custom--window--display-min-height)"},"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"shadow":"var:preset|shadow|crisp"},"backgroundColor":"surface","layout":{"type":"constrained"}} -->';
	echo '<div class="wp-block-group ws-window-display has-surface-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:var(--wp--custom--window--display-radius);min-height:var(--wp--custom--window--display-min-height);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40);box-shadow:var(--wp--preset--shadow--crisp)">';
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
	window_shopping_render_hero_product_shelf( $slug );
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

<?php
/**
 * Seed a WordPress Playground install for the Window Shopping theme.
 *
 * @package Window_Shopping
 */

if ( ! defined( 'ABSPATH' ) || ! class_exists( 'WooCommerce' ) ) {
	return;
}

update_option( 'blogname', 'Window Shopping' );
update_option( 'blogdescription', 'One shop theme, many storefront moods.' );
update_option( 'woocommerce_coming_soon', 'no' );
update_option( 'woocommerce_task_list_complete', 'yes' );
update_option( 'woocommerce_use_legacy_product_template', 'no' );
update_option( 'woocommerce_block_theme_has_blockified_templates', 'yes' );
update_option( 'woocommerce_single_product_block_template', 'yes' );
update_option( 'permalink_structure', '/%postname%/' );

/**
 * Install the Playground-only style switcher MU plugin.
 *
 * @return void
 */
function window_shopping_playground_install_style_switcher() {
	$source = get_theme_file_path( 'playground/mu-plugins/window-shopping-style-switcher.php' );
	if ( ! file_exists( $source ) || ! defined( 'WPMU_PLUGIN_DIR' ) ) {
		return;
	}

	if ( ! is_dir( WPMU_PLUGIN_DIR ) && ! wp_mkdir_p( WPMU_PLUGIN_DIR ) ) {
		return;
	}

	copy( $source, trailingslashit( WPMU_PLUGIN_DIR ) . 'window-shopping-style-switcher.php' );
}

window_shopping_playground_install_style_switcher();
delete_option( 'window_shopping_switcher_active_style' );

if ( ! class_exists( 'WC_Install' ) && defined( 'WC_ABSPATH' ) ) {
	require_once WC_ABSPATH . 'includes/class-wc-install.php';
}

if ( class_exists( 'WC_Install' ) ) {
	WC_Install::create_pages();
}

/**
 * Create or update a page by slug.
 *
 * @param string $slug    Page slug.
 * @param string $title   Page title.
 * @param string $content Page content.
 * @return int
 */
function window_shopping_playground_upsert_page( $slug, $title, $content ) {
	$page = get_page_by_path( $slug );
	$data = array(
		'post_title'   => $title,
		'post_name'    => $slug,
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_content' => $content,
	);

	if ( $page instanceof WP_Post ) {
		$data['ID'] = $page->ID;
		$result     = wp_update_post( $data, true );
	} else {
		$result = wp_insert_post( $data, true );
	}

	return is_wp_error( $result ) ? 0 : (int) $result;
}

$cart_page_id = window_shopping_playground_upsert_page( 'cart', 'Cart', '<!-- wp:woocommerce/cart /-->' );
$checkout_id  = window_shopping_playground_upsert_page( 'checkout', 'Checkout', '<!-- wp:woocommerce/checkout /-->' );
$shop_id      = window_shopping_playground_upsert_page( 'shop', 'Shop', '' );

window_shopping_playground_upsert_page(
	'coming-soon',
	'Coming Soon',
	'<!-- wp:paragraph --><p>The window is almost ready.</p><!-- /wp:paragraph -->'
);

window_shopping_playground_upsert_page(
	'sample-page',
	'About',
	'<!-- wp:paragraph --><p>Window Shopping is a WooCommerce block theme built around expressive storefront styles.</p><!-- /wp:paragraph -->'
);

if ( $cart_page_id ) {
	update_option( 'woocommerce_cart_page_id', $cart_page_id );
}
if ( $checkout_id ) {
	update_option( 'woocommerce_checkout_page_id', $checkout_id );
}
if ( $shop_id ) {
	update_option( 'woocommerce_shop_page_id', $shop_id );
}

update_option(
	'woocommerce_cod_settings',
	array(
		'enabled' => 'yes',
		'title'   => 'Cash on delivery',
	)
);

update_option(
	'woocommerce_bacs_settings',
	array(
		'enabled' => 'yes',
		'title'   => 'Direct bank transfer',
	)
);

/**
 * Resolve or create a product category.
 *
 * @param string $name Category name.
 * @param string $slug Category slug.
 * @return int
 */
function window_shopping_playground_category_id( $name, $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $term ) {
		return (int) $term->term_id;
	}

	$result = wp_insert_term( $name, 'product_cat', array( 'slug' => $slug ) );
	return is_wp_error( $result ) ? 0 : (int) $result['term_id'];
}

/**
 * Sideload a bundled theme image into the media library.
 *
 * @param string $filename   Theme image filename.
 * @param int    $product_id Product ID.
 * @return int
 */
function window_shopping_playground_sideload_image( $filename, $product_id ) {
	$source = get_theme_file_path( 'assets/sample-products/' . $filename );
	if ( ! file_exists( $source ) ) {
		return 0;
	}

	$image_version = '2026-05-13-2400-jpeg-signal';

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$existing = get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_window_shopping_sample_image',
			'meta_value'     => $filename,
		)
	);

	if ( ! empty( $existing ) ) {
		foreach ( $existing as $attachment_id ) {
			if ( $image_version === get_post_meta( $attachment_id, '_window_shopping_sample_image_version', true ) ) {
				return (int) $attachment_id;
			}

			wp_delete_attachment( (int) $attachment_id, true );
		}
	}

	$tmp = wp_tempnam( $filename );
	if ( ! $tmp || ! copy( $source, $tmp ) ) {
		return 0;
	}

	$attachment_id = media_handle_sideload(
		array(
			'name'     => basename( $filename ),
			'tmp_name' => $tmp,
		),
		$product_id
	);

	if ( is_wp_error( $attachment_id ) ) {
		@unlink( $tmp );
		return 0;
	}

	update_post_meta( $attachment_id, '_window_shopping_sample_image', $filename );
	update_post_meta( $attachment_id, '_window_shopping_sample_image_version', $image_version );
	return (int) $attachment_id;
}

/**
 * Remove outdated bundled sample images from previous demo seed versions.
 *
 * @param string[] $filenames Current image filenames.
 * @return void
 */
function window_shopping_playground_cleanup_sample_images( $filenames ) {
	$filenames = array_unique( array_filter( $filenames ) );
	$existing  = get_posts(
		array(
			'post_type'      => 'attachment',
			'posts_per_page' => -1,
			'fields'         => 'ids',
			'meta_key'       => '_window_shopping_sample_image',
		)
	);

	foreach ( $existing as $attachment_id ) {
		$filename = get_post_meta( (int) $attachment_id, '_window_shopping_sample_image', true );
		if ( ! in_array( $filename, $filenames, true ) ) {
			wp_delete_attachment( (int) $attachment_id, true );
		}
	}
}

/**
 * Assign real product images to the demo WooCommerce categories.
 *
 * @param array<string, int>    $categories            Category IDs keyed by demo category.
 * @param array<string, int>    $image_ids_by_filename Attachment IDs keyed by source filename.
 * @param array<string, string> $category_thumbnails   Source filenames keyed by demo category.
 * @return void
 */
function window_shopping_playground_assign_category_thumbnails( $categories, $image_ids_by_filename, $category_thumbnails ) {
	foreach ( $category_thumbnails as $category_key => $filename ) {
		if ( empty( $categories[ $category_key ] ) || empty( $image_ids_by_filename[ $filename ] ) ) {
			continue;
		}

		update_term_meta(
			(int) $categories[ $category_key ],
			'thumbnail_id',
			(int) $image_ids_by_filename[ $filename ]
		);
	}
}

/**
 * Add a small demo review so product rating blocks have real content.
 *
 * @param int $product_id Product ID.
 * @param int $rating     Star rating.
 * @return void
 */
function window_shopping_playground_add_review( $product_id, $rating ) {
	$existing = get_comments(
		array(
			'post_id'  => $product_id,
			'meta_key' => '_window_shopping_sample_review',
			'number'   => 1,
			'status'   => 'approve',
		)
	);

	if ( ! empty( $existing ) ) {
		return;
	}

	$comment_id = wp_insert_comment(
		array(
			'comment_post_ID'      => $product_id,
			'comment_author'       => 'Window Shopper',
			'comment_author_email' => 'shopper@example.com',
			'comment_content'      => 'Exactly the kind of product this demo store was made to show.',
			'comment_type'         => 'review',
			'comment_approved'     => 1,
		)
	);

	if ( ! $comment_id ) {
		return;
	}

	$rating = max( 1, min( 5, (int) $rating ) );
	update_comment_meta( $comment_id, 'rating', $rating );
	update_comment_meta( $comment_id, 'verified', 1 );
	update_comment_meta( $comment_id, '_window_shopping_sample_review', 1 );
	update_post_meta( $product_id, '_wc_average_rating', number_format( $rating, 2, '.', '' ) );
	update_post_meta( $product_id, '_wc_rating_count', array( $rating => 1 ) );
	update_post_meta( $product_id, '_wc_review_count', 1 );
	wc_delete_product_transients( $product_id );
}

$categories = array(
	'studio'       => window_shopping_playground_category_id( 'Studio', 'studio' ),
	'oddities'    => window_shopping_playground_category_id( 'Oddities', 'oddities' ),
	'atelier'     => window_shopping_playground_category_id( 'Atelier', 'atelier' ),
	'field'       => window_shopping_playground_category_id( 'Field Supply', 'field-supply' ),
	'pantry'      => window_shopping_playground_category_id( 'Pantry', 'pantry' ),
	'signal'      => window_shopping_playground_category_id( 'Signal', 'signal' ),
);

$products = array(
	array( 'sku' => 'WS-VELVET-TOTE', 'name' => 'Velvet Utility Tote', 'price' => '148', 'image' => 'velvet-utility-tote.jpg', 'cats' => array( 'studio', 'atelier' ), 'rating' => 5, 'short' => 'A structured velvet tote with generous pockets and a polished silhouette.' ),
	array( 'sku' => 'WS-MOON-BOX', 'name' => 'Moonlit Receipt Box', 'price' => '42', 'image' => 'moonlit-receipt-box.jpg', 'cats' => array( 'studio', 'oddities' ), 'rating' => 4, 'short' => 'A small keepsake box for notes, receipts, and quiet little mysteries.' ),
	array( 'sku' => 'WS-RIBBON-SHIRT', 'name' => 'Ribbon Hem Shirt', 'price' => '126', 'image' => 'ribbon-hem-shirt.jpg', 'cats' => array( 'atelier', 'studio' ), 'rating' => 5, 'short' => 'A relaxed woven shirt with a quiet ribbon detail and an easy drape.' ),
	array( 'sku' => 'WS-CAMP-JACKET', 'name' => 'Camp Ledger Jacket', 'price' => '188', 'image' => 'camp-ledger-jacket.jpg', 'cats' => array( 'field', 'atelier' ), 'rating' => 5, 'short' => 'A hard-wearing utility jacket with notebook pockets and a dry hand feel.' ),
	array( 'sku' => 'WS-TRAIL-TIN', 'name' => 'Trail Tin Kit', 'price' => '58', 'image' => 'trail-tin-kit.jpg', 'cats' => array( 'field', 'oddities' ), 'rating' => 4, 'short' => 'A compact field kit for tidy repairs, tiny tools, and useful odds.' ),
	array( 'sku' => 'WS-CABLE-PACK', 'name' => 'Cable Index Pack', 'price' => '28', 'image' => 'cable-index-pack.jpg', 'cats' => array( 'signal', 'studio' ), 'rating' => 4, 'short' => 'Color-coded cable keepers for the drawer that always fights back.' ),
	array( 'sku' => 'WS-DESK-DOCK', 'name' => 'Desk Beacon Dock', 'price' => '96', 'image' => 'desk-beacon-dock.jpg', 'cats' => array( 'signal', 'studio' ), 'rating' => 5, 'short' => 'A compact dock with a small signal light and a calm desk footprint.' ),
	array( 'sku' => 'WS-SIGNAL-DOCK', 'name' => 'Signal Charging Dock', 'price' => '96', 'image' => 'signal-charging-dock.jpg', 'cats' => array( 'signal' ), 'rating' => 5, 'short' => 'A clean charging stand for modern accessories and nightly resets.' ),
	array( 'sku' => 'WS-SIGNAL-POUCH', 'name' => 'Signal Tech Pouch', 'price' => '72', 'image' => 'signal-tech-pouch.jpg', 'cats' => array( 'signal' ), 'rating' => 4, 'short' => 'A slim pouch with divided storage for cables, drives, and adapters.' ),
	array( 'sku' => 'WS-SIGNAL-HUB', 'name' => 'Signal USB-C Hub', 'price' => '84', 'image' => 'signal-usb-c-hub.jpg', 'cats' => array( 'signal' ), 'rating' => 5, 'short' => 'A low-profile hub with ports where your hands expect them.' ),
	array( 'sku' => 'WS-SIGNAL-HEADPHONES', 'name' => 'Signal Wireless Headphones', 'price' => '164', 'image' => 'signal-wireless-headphones.jpg', 'cats' => array( 'signal' ), 'rating' => 5, 'short' => 'Wireless headphones with soft pads, long battery life, and a quiet profile.' ),
	array( 'sku' => 'WS-BOTTLED-MORNING', 'name' => 'Bottled Morning', 'price' => '24', 'image' => 'bottled-morning.jpg', 'cats' => array( 'pantry', 'studio' ), 'rating' => 5, 'short' => 'A bright pantry staple for toast, bowls, and slow weekend counters.' ),
	array( 'sku' => 'WS-OIL-DUO', 'name' => 'Countertop Oil Duo', 'price' => '36', 'image' => 'countertop-oil-duo.jpg', 'cats' => array( 'pantry' ), 'rating' => 4, 'short' => 'Two finishing oils with a tidy pour and a warm kitchen presence.' ),
	array( 'sku' => 'WS-CITRUS-CRATE', 'name' => 'Market Citrus Crate', 'price' => '32', 'image' => 'market-citrus-crate.jpg', 'cats' => array( 'pantry' ), 'rating' => 5, 'short' => 'A small crate of bright citrus for displays, gifting, and garnish duty.' ),
	array( 'sku' => 'WS-SUNDAY-JAM', 'name' => 'Sunday Jam Set', 'price' => '30', 'image' => 'sunday-jam-set.jpg', 'cats' => array( 'pantry' ), 'rating' => 5, 'short' => 'A trio of jams made for breakfast tables and small thank-you gifts.' ),
	array( 'sku' => 'WS-POCKET-THUNDER', 'name' => 'Pocket Thunder', 'price' => '18', 'image' => 'pocket-thunder.jpg', 'cats' => array( 'oddities', 'field' ), 'rating' => 5, 'short' => 'A tiny strange object with an unreasonable amount of personality.' ),
);

$category_thumbnails = array(
	'studio'   => 'velvet-utility-tote.jpg',
	'oddities' => 'pocket-thunder.jpg',
	'atelier'  => 'ribbon-hem-shirt.jpg',
	'field'    => 'camp-ledger-jacket.jpg',
	'pantry'   => 'market-citrus-crate.jpg',
	'signal'   => 'signal-charging-dock.jpg',
);

window_shopping_playground_cleanup_sample_images( wp_list_pluck( $products, 'image' ) );

$image_ids_by_filename = array();

foreach ( $products as $item ) {
	$product_id = wc_get_product_id_by_sku( $item['sku'] );
	if ( ! $product_id ) {
		$legacy_product = get_page_by_path( sanitize_title( $item['name'] ), OBJECT, 'product' );
		if ( $legacy_product instanceof WP_Post ) {
			$product_id = (int) $legacy_product->ID;
		}
	}

	$product    = $product_id ? wc_get_product( $product_id ) : new WC_Product_Simple();

	if ( ! $product instanceof WC_Product ) {
		continue;
	}

	$product->set_name( $item['name'] );
	$product->set_sku( $item['sku'] );
	$product->set_status( 'publish' );
	$product->set_catalog_visibility( 'visible' );
	$product->set_regular_price( $item['price'] );
	$product->set_price( $item['price'] );
	$product->set_short_description( $item['short'] );
	$product->set_description( $item['short'] . ' Built for the Window Shopping demo catalog.' );
	$product->set_stock_status( 'instock' );
	$product->set_reviews_allowed( true );

	$cat_ids = array();
	foreach ( $item['cats'] as $cat_key ) {
		if ( ! empty( $categories[ $cat_key ] ) ) {
			$cat_ids[] = (int) $categories[ $cat_key ];
		}
	}
	$product->set_category_ids( array_values( array_unique( $cat_ids ) ) );

	$product_id = $product->save();
	if ( ! $product_id ) {
		continue;
	}

	$desired_slug = sanitize_title( $item['name'] );
	$legacy_post  = get_page_by_path( $desired_slug, OBJECT, 'product' );
	if ( $legacy_post instanceof WP_Post && (int) $legacy_post->ID !== (int) $product_id ) {
		$legacy_product = wc_get_product( $legacy_post->ID );
		$legacy_sku     = $legacy_product instanceof WC_Product ? $legacy_product->get_sku() : '';
		if ( '' === $legacy_sku || $legacy_sku !== $item['sku'] ) {
			wp_delete_post( $legacy_post->ID, true );
			wp_update_post(
				array(
					'ID'        => $product_id,
					'post_name' => $desired_slug,
				)
			);
		}
	}

	$image_id = window_shopping_playground_sideload_image( $item['image'], $product_id );
	if ( $image_id ) {
		$image_ids_by_filename[ $item['image'] ] = $image_id;
	}

	if ( $image_id && (int) $product->get_image_id() !== $image_id ) {
		$product = wc_get_product( $product_id );
		$product->set_image_id( $image_id );
		$product->save();
	}

	window_shopping_playground_add_review( $product_id, $item['rating'] );
}

window_shopping_playground_assign_category_thumbnails( $categories, $image_ids_by_filename, $category_thumbnails );

flush_rewrite_rules();

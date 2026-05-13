<?php
/**
 * Title: Featured collection
 * Slug: window-shopping/featured-collection
 * Categories: window-shopping-store
 * Description: A full-width featured collection section.
 *
 * @package Window_Shopping
 */
?>
<!-- wp:group {"anchor":"featured-products","align":"full","className":"ws-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}},"border":{"top":{"color":"var:preset|color|muted","width":"1px"}}},"layout":{"type":"constrained"}} -->
<div id="featured-products" class="wp-block-group alignfull ws-section" style="border-top-color:var(--wp--preset--color--muted);border-top-width:1px;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:group {"align":"wide","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between","verticalAlignment":"bottom"}} -->
	<div class="wp-block-group alignwide">
		<!-- wp:group {"layout":{"type":"constrained","justifyContent":"left","contentSize":"560px"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"className":"ws-kicker","style":{"typography":{"fontWeight":"760","letterSpacing":"0","textTransform":"uppercase"}},"textColor":"accent","fontFamily":"mono","fontSize":"small"} -->
			<p class="ws-kicker has-accent-color has-text-color has-mono-font-family has-small-font-size" style="font-weight:760;letter-spacing:0;text-transform:uppercase">Featured collection</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"fontSize":"xx-large"} -->
			<h2 class="wp-block-heading has-xx-large-font-size">The shelf customers notice first.</h2>
			<!-- /wp:heading -->
		</div>
		<!-- /wp:group -->

		<!-- wp:buttons -->
		<div class="wp-block-buttons">
			<!-- wp:button {"className":"is-style-outline"} -->
			<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/shop/">View all</a></div>
			<!-- /wp:button -->
		</div>
		<!-- /wp:buttons -->
	</div>
	<!-- /wp:group -->

	<!-- wp:woocommerce/product-collection {"queryId":20,"query":{"perPage":4,"pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{},"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","outofstock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[],"filterable":false},"tagName":"div","displayLayout":{"type":"flex","columns":4,"shrinkColumns":false},"dimensions":{"widthType":"fill"},"queryContextIncludes":["collection"],"align":"wide","className":"ws-product-grid is-style-index-card"} -->
	<div class="wp-block-woocommerce-product-collection alignwide ws-product-grid is-style-index-card">
		<!-- wp:woocommerce/product-template -->
			<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","isDescendentOfQueryLoop":true} /-->
			<!-- wp:woocommerce/product-sale-badge {"isDescendentOfQueryLoop":true} /-->
			<!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"lineHeight":"1.2"},"spacing":{"margin":{"top":"0.85rem","bottom":"0.35rem"}}},"fontSize":"medium","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->
			<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"fontSize":"small"} /-->
			<!-- wp:woocommerce/product-rating {"isDescendentOfQueryLoop":true,"className":"is-style-compact-stars"} /-->
			<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true,"textAlign":"left","fontSize":"small","style":{"spacing":{"margin":{"top":"0.85rem"}}}} /-->
		<!-- /wp:woocommerce/product-template -->

		<!-- wp:woocommerce/product-collection-no-results -->
		<div class="wp-block-woocommerce-product-collection-no-results">
			<!-- wp:paragraph -->
			<p>No featured products yet.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:woocommerce/product-collection-no-results -->
	</div>
	<!-- /wp:woocommerce/product-collection -->
</div>
<!-- /wp:group -->

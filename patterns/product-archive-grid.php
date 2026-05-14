<?php
/**
 * Title: Product archive grid
 * Slug: window-shopping/product-archive-grid
 * Categories: window-shopping-store
 * Inserter: false
 * Description: Inherited WooCommerce archive grid tuned for balanced catalog rows.
 *
 * @package Window_Shopping
 */
?>
<!-- wp:woocommerce/product-collection {"queryId":30,"query":{"perPage":15,"pages":0,"offset":0,"postType":"product","order":"asc","orderBy":"title","author":"","search":"","exclude":[],"sticky":"","inherit":true,"taxQuery":{},"isProductCollectionBlock":true,"woocommerceStockStatus":["instock","outofstock","onbackorder"],"filterable":true},"tagName":"div","displayLayout":{"type":"flex","columns":5,"shrinkColumns":false},"dimensions":{"widthType":"fill"},"queryContextIncludes":["collection"],"className":"ws-product-grid is-style-index-card"} -->
<div class="wp-block-woocommerce-product-collection ws-product-grid is-style-index-card">
	<!-- wp:woocommerce/product-template -->
		<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","isDescendentOfQueryLoop":true} /-->
		<!-- wp:woocommerce/product-sale-badge {"isDescendentOfQueryLoop":true} /-->
		<!-- wp:post-title {"level":2,"isLink":true,"style":{"typography":{"lineHeight":"1.2"},"spacing":{"margin":{"top":"0.85rem","bottom":"0.35rem"}}},"fontSize":"medium","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->
		<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"fontSize":"small"} /-->
		<!-- wp:woocommerce/product-rating {"isDescendentOfQueryLoop":true,"className":"is-style-compact-stars"} /-->
		<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true,"textAlign":"left","fontSize":"small","style":{"spacing":{"margin":{"top":"0.85rem"}}}} /-->
	<!-- /wp:woocommerce/product-template -->

	<!-- wp:woocommerce/product-collection-no-results -->
	<div class="wp-block-woocommerce-product-collection-no-results">
		<!-- wp:paragraph -->
		<p>No products found. Try another filter or search.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:woocommerce/product-collection-no-results -->
</div>
<!-- /wp:woocommerce/product-collection -->

<?php
/**
 * Title: Product shelf
 * Slug: window-shopping/product-shelf
 * Categories: window-shopping-store
 * Description: A compact dynamic product shelf for merchandising.
 *
 * @package Window_Shopping
 */
?>
<!-- wp:woocommerce/product-collection {"queryId":10,"query":{"perPage":4,"pages":0,"offset":0,"postType":"product","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false,"taxQuery":{},"isProductCollectionBlock":true,"featured":false,"woocommerceOnSale":false,"woocommerceStockStatus":["instock","outofstock","onbackorder"],"woocommerceAttributes":[],"woocommerceHandPickedProducts":[],"filterable":false},"tagName":"div","displayLayout":{"type":"flex","columns":4,"shrinkColumns":true},"dimensions":{"widthType":"fill"},"queryContextIncludes":["collection"],"className":"ws-product-grid is-style-showcase-hero"} -->
<div class="wp-block-woocommerce-product-collection ws-product-grid is-style-showcase-hero">
	<!-- wp:woocommerce/product-template -->
		<!-- wp:woocommerce/product-image {"imageSizing":"thumbnail","isDescendentOfQueryLoop":true} /-->
		<!-- wp:post-title {"level":3,"isLink":true,"style":{"typography":{"lineHeight":"var(--wp--custom--window--showcase-title-line-height,1.2)"},"spacing":{"margin":{"top":"var(--wp--custom--window--showcase-title-margin-top,0.85rem)","bottom":"var(--wp--custom--window--showcase-title-margin-bottom,0.35rem)"}}},"fontSize":"medium","__woocommerceNamespace":"woocommerce/product-collection/product-title"} /-->
		<!-- wp:woocommerce/product-price {"isDescendentOfQueryLoop":true,"fontSize":"small"} /-->
		<!-- wp:woocommerce/product-button {"isDescendentOfQueryLoop":true,"textAlign":"left","fontSize":"small","style":{"spacing":{"margin":{"top":"var(--wp--custom--window--showcase-button-margin-top,0.85rem)"}}}} /-->
	<!-- /wp:woocommerce/product-template -->

	<!-- wp:woocommerce/product-collection-no-results -->
	<div class="wp-block-woocommerce-product-collection-no-results">
		<!-- wp:paragraph -->
		<p>Add a few products to see this shelf come alive.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:woocommerce/product-collection-no-results -->
</div>
<!-- /wp:woocommerce/product-collection -->

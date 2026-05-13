<?php
/**
 * Title: Category browse
 * Slug: window-shopping/category-browse
 * Categories: window-shopping-store
 * Description: A scannable category section with dynamic WooCommerce categories.
 *
 * @package Window_Shopping
 */
?>
<!-- wp:group {"align":"full","className":"ws-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}},"border":{"top":{"color":"var:preset|color|muted","width":"1px"}}},"layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull ws-section" style="border-top-color:var(--wp--preset--color--muted);border-top-width:1px;padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"34%"} -->
		<div class="wp-block-column" style="flex-basis:34%">
			<!-- wp:paragraph {"className":"ws-kicker","style":{"typography":{"fontWeight":"760","letterSpacing":"0","textTransform":"uppercase"}},"textColor":"accent","fontFamily":"mono","fontSize":"small"} -->
			<p class="ws-kicker has-accent-color has-text-color has-mono-font-family has-small-font-size" style="font-weight:760;letter-spacing:0;text-transform:uppercase">Browse</p>
			<!-- /wp:paragraph -->
			<!-- wp:heading {"fontSize":"xx-large"} -->
			<h2 class="wp-block-heading has-xx-large-font-size">Find the right shelf fast.</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|muted"}}} -->
			<p class="has-text-color" style="color:var(--wp--preset--color--muted)">Use the native WooCommerce category list for stores that need real catalog navigation.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"66%"} -->
		<div class="wp-block-column" style="flex-basis:66%">
			<!-- wp:group {"className":"ws-surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"var:preset|spacing|50","bottom":"var:preset|spacing|50","left":"var:preset|spacing|50"}},"border":{"color":"var:preset|color|muted","width":"1px","radius":"var(--wp--custom--radius)"},"shadow":"var:preset|shadow|crisp"},"layout":{"type":"constrained"},"backgroundColor":"surface"} -->
			<div class="wp-block-group ws-surface has-surface-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:var(--wp--custom--radius);box-shadow:var(--wp--preset--shadow--crisp);padding-top:var(--wp--preset--spacing--50);padding-right:var(--wp--preset--spacing--50);padding-bottom:var(--wp--preset--spacing--50);padding-left:var(--wp--preset--spacing--50)">
				<!-- wp:woocommerce/product-categories {"hasCount":true,"hasImage":true,"isHierarchical":true} /-->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->


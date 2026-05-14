<?php
/**
 * Title: Product dossier
 * Slug: window-shopping/product-dossier
 * Categories: window-shopping-product
 * Description: A stronger product information section with WooCommerce details and support notes.
 *
 * @package Window_Shopping
 */
?>
<!-- wp:group {"align":"full","className":"ws-product-dossier ws-section","style":{"spacing":{"padding":{"top":"var:preset|spacing|50","right":"clamp(1rem, 4vw, var(--wp--preset--spacing--60))","bottom":"var:preset|spacing|50","left":"clamp(1rem, 4vw, var(--wp--preset--spacing--60))"}},"border":{"top":{"color":"var:preset|color|muted","width":"1px"}}},"layout":{"type":"constrained","contentSize":"96rem"}} -->
<div class="wp-block-group alignfull ws-product-dossier ws-section" style="border-top-color:var(--wp--preset--color--muted);border-top-width:1px;padding-top:var(--wp--preset--spacing--50);padding-right:clamp(1rem, 4vw, var(--wp--preset--spacing--60));padding-bottom:var(--wp--preset--spacing--50);padding-left:clamp(1rem, 4vw, var(--wp--preset--spacing--60))">
	<!-- wp:columns {"className":"ws-product-dossier__grid","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|40"}}}} -->
	<div class="wp-block-columns ws-product-dossier__grid">
		<!-- wp:column {"width":"30%"} -->
		<div class="wp-block-column" style="flex-basis:30%">
			<!-- wp:group {"className":"ws-product-dossier__rail ws-surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"border":{"color":"var:preset|color|muted","width":"1px","radius":"var(--wp--custom--radius)"},"shadow":"var:preset|shadow|crisp"},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
			<div class="wp-block-group ws-product-dossier__rail ws-surface has-surface-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:var(--wp--custom--radius);box-shadow:var(--wp--preset--shadow--crisp);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
				<!-- wp:paragraph {"className":"ws-kicker","fontFamily":"mono","fontSize":"small","textColor":"accent","style":{"typography":{"fontWeight":"700","textTransform":"uppercase"},"spacing":{"margin":{"bottom":"var:preset|spacing|30"}}}} -->
				<p class="ws-kicker has-accent-color has-text-color has-mono-font-family has-small-font-size" style="margin-bottom:var(--wp--preset--spacing--30);font-weight:700;text-transform:uppercase">Product notes</p>
				<!-- /wp:paragraph -->

				<!-- wp:heading {"level":2,"fontSize":"x-large"} -->
				<h2 class="wp-block-heading has-x-large-font-size">The quick read</h2>
				<!-- /wp:heading -->

				<!-- wp:paragraph {"style":{"color":{"text":"var:preset|color|muted"}}} -->
				<p class="has-text-color" style="color:var(--wp--preset--color--muted)">Details, proof, and practical notes stay close to the buying decision.</p>
				<!-- /wp:paragraph -->

				<!-- wp:list {"className":"ws-product-dossier__list","fontFamily":"mono","fontSize":"small"} -->
				<ul class="wp-block-list ws-product-dossier__list has-mono-font-family has-small-font-size">
					<li><span>01</span><strong>Materials</strong></li>
					<li><span>02</span><strong>Sizing</strong></li>
					<li><span>03</span><strong>Delivery</strong></li>
				</ul>
				<!-- /wp:list -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"70%"} -->
		<div class="wp-block-column" style="flex-basis:70%">
			<!-- wp:woocommerce/product-details {"className":"is-style-field-notebook"} /-->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

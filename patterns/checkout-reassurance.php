<?php
/**
 * Title: Checkout reassurance
 * Slug: window-shopping/checkout-reassurance
 * Categories: window-shopping-checkout
 * Description: A compact checkout reassurance strip.
 *
 * @package Window_Shopping
 */
?>
<!-- wp:group {"align":"wide","className":"ws-surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}},"border":{"color":"var:preset|color|muted","width":"1px","radius":"var(--wp--custom--radius)"},"shadow":"var:preset|shadow|crisp"},"layout":{"type":"grid","minimumColumnWidth":"13rem"},"backgroundColor":"surface"} -->
<div class="wp-block-group alignwide ws-surface has-surface-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:var(--wp--custom--radius);box-shadow:var(--wp--preset--shadow--crisp);padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
	<!-- wp:paragraph {"className":"ws-kicker","style":{"typography":{"fontWeight":"760","letterSpacing":"0","textTransform":"uppercase"}},"textColor":"accent","fontFamily":"mono","fontSize":"small"} -->
	<p class="ws-kicker has-accent-color has-text-color has-mono-font-family has-small-font-size" style="font-weight:760;letter-spacing:0;text-transform:uppercase">Secure payment</p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph {"fontSize":"small"} -->
	<p class="has-small-font-size">Encrypted checkout</p>
	<!-- /wp:paragraph -->

	<!-- wp:paragraph {"fontSize":"small"} -->
	<p class="has-small-font-size">Order updates by email</p>
	<!-- /wp:paragraph -->

	<!-- wp:woocommerce/payment-method-icons {"numberOfIcons":5} /-->
</div>
<!-- /wp:group -->


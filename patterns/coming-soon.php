<?php
/**
 * Title: Coming soon storefront
 * Slug: window-shopping/coming-soon
 * Categories: window-shopping-store
 * Description: A polished coming soon surface with editorial copy and product-window imagery.
 *
 * @package Window_Shopping
 */

$primary_image   = esc_url( get_theme_file_uri( 'assets/sample-products/moonlit-receipt-box.jpg' ) );
$secondary_image = esc_url( get_theme_file_uri( 'assets/sample-products/velvet-utility-tote.jpg' ) );
$tertiary_image  = esc_url( get_theme_file_uri( 'assets/sample-products/cable-index-pack.jpg' ) );
?>
<!-- wp:group {"align":"wide","className":"ws-coming-soon ws-surface","style":{"color":{"text":"var(--wp--custom--window--surface-text-color, var(--wp--preset--color--contrast))"},"spacing":{"padding":{"top":"var:preset|spacing|60","right":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|60"},"blockGap":"var:preset|spacing|50"},"border":{"color":"var:preset|color|muted","width":"1px","radius":"var(--wp--custom--radius)"},"shadow":"var:preset|shadow|crisp"},"layout":{"type":"constrained"},"backgroundColor":"surface"} -->
<div class="wp-block-group alignwide ws-coming-soon ws-surface has-surface-background-color has-text-color has-background" style="color:var(--wp--custom--window--surface-text-color, var(--wp--preset--color--contrast));border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:var(--wp--custom--radius);box-shadow:var(--wp--preset--shadow--crisp);padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--60)">
	<!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|60","top":"var:preset|spacing|50"}}}} -->
	<div class="wp-block-columns are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center","width":"43%","className":"ws-coming-soon__copy"} -->
		<div class="wp-block-column is-vertically-aligned-center ws-coming-soon__copy" style="flex-basis:var(--wp--custom--window--coming-soon-copy-basis,43%)">
			<!-- wp:paragraph {"className":"ws-kicker","style":{"typography":{"fontWeight":"760","letterSpacing":"0","textTransform":"uppercase"}},"textColor":"accent","fontFamily":"mono","fontSize":"small"} -->
			<p class="ws-kicker has-accent-color has-text-color has-mono-font-family has-small-font-size" style="font-weight:760;letter-spacing:0;text-transform:uppercase">Opening soon</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":1,"className":"ws-coming-soon__heading","style":{"color":{"text":"var(--wp--custom--window--surface-text-color, var(--wp--preset--color--contrast))"},"spacing":{"margin":{"top":"var:preset|spacing|30","bottom":"var:preset|spacing|30"}},"typography":{"fontSize":"var(--wp--custom--window--coming-soon-heading-size, clamp(2.75rem, 7.5vw, var(--wp--preset--font-size--display)))"}}} -->
			<h1 class="wp-block-heading ws-coming-soon__heading has-text-color" style="color:var(--wp--custom--window--surface-text-color, var(--wp--preset--color--contrast));margin-top:var(--wp--preset--spacing--30);margin-bottom:var(--wp--preset--spacing--30);font-size:var(--wp--custom--window--coming-soon-heading-size, clamp(2.75rem, 7.5vw, var(--wp--preset--font-size--display)))">The window is almost ready.</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"style":{"color":{"text":"var(--wp--custom--window--surface-muted-color, var(--wp--preset--color--muted))"},"typography":{"fontSize":"1.15rem","lineHeight":"1.55"}}} -->
			<p class="has-text-color" style="color:var(--wp--custom--window--surface-muted-color, var(--wp--preset--color--muted));font-size:1.15rem;line-height:1.55">A new storefront is being arranged behind the glass. First shelves, odd finds, and useful favorites are being set into place.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"style":{"spacing":{"margin":{"top":"var:preset|spacing|40"}}}} -->
			<div class="wp-block-buttons" style="margin-top:var(--wp--preset--spacing--40)">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/shop/">Browse the shop</a></div>
				<!-- /wp:button -->

				<!-- wp:button {"className":"is-style-outline"} -->
				<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="/">Back to the window</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"57%","className":"ws-coming-soon__media"} -->
		<div class="wp-block-column is-vertically-aligned-center ws-coming-soon__media" style="flex-basis:var(--wp--custom--window--coming-soon-media-basis,57%)">
			<!-- wp:group {"className":"ws-coming-soon__window","style":{"spacing":{"blockGap":"var:preset|spacing|30"}},"layout":{"type":"grid","minimumColumnWidth":"var(--wp--custom--window--coming-soon-card-min, 8.5rem)"}} -->
			<div class="wp-block-group ws-coming-soon__window">
				<!-- wp:group {"className":"ws-surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}},"border":{"color":"var:preset|color|muted","width":"1px","radius":"var(--wp--custom--radius)"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
				<div class="wp-block-group ws-surface has-surface-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:var(--wp--custom--radius);padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">
					<!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"calc(var(--wp--custom--radius) * .75)"},"dimensions":{"aspectRatio":"1","scale":"cover"}}} -->
					<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $primary_image; ?>" alt="<?php esc_attr_e( 'A dark receipt box with a crescent detail.', 'window-shopping' ); ?>" style="border-radius:calc(var(--wp--custom--radius) * .75);aspect-ratio:1;object-fit:cover"/></figure>
					<!-- /wp:image -->

					<!-- wp:paragraph {"style":{"typography":{"fontWeight":"760"}},"fontSize":"medium"} -->
					<p class="has-medium-font-size" style="font-weight:760">First look</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"className":"ws-surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}},"border":{"color":"var:preset|color|muted","width":"1px","radius":"var(--wp--custom--radius)"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
				<div class="wp-block-group ws-surface has-surface-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:var(--wp--custom--radius);padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">
					<!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"calc(var(--wp--custom--radius) * .75)"},"dimensions":{"aspectRatio":"1","scale":"cover"}}} -->
					<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $secondary_image; ?>" alt="<?php esc_attr_e( 'A structured velvet utility tote.', 'window-shopping' ); ?>" style="border-radius:calc(var(--wp--custom--radius) * .75);aspect-ratio:1;object-fit:cover"/></figure>
					<!-- /wp:image -->

					<!-- wp:paragraph {"style":{"typography":{"fontWeight":"760"}},"fontSize":"medium"} -->
					<p class="has-medium-font-size" style="font-weight:760">In the window</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->

				<!-- wp:group {"className":"ws-surface","style":{"spacing":{"padding":{"top":"var:preset|spacing|20","right":"var:preset|spacing|20","bottom":"var:preset|spacing|20","left":"var:preset|spacing|20"}},"border":{"color":"var:preset|color|muted","width":"1px","radius":"var(--wp--custom--radius)"}},"backgroundColor":"surface","layout":{"type":"constrained"}} -->
				<div class="wp-block-group ws-surface has-surface-background-color has-background" style="border-color:var(--wp--preset--color--muted);border-width:1px;border-radius:var(--wp--custom--radius);padding-top:var(--wp--preset--spacing--20);padding-right:var(--wp--preset--spacing--20);padding-bottom:var(--wp--preset--spacing--20);padding-left:var(--wp--preset--spacing--20)">
					<!-- wp:image {"sizeSlug":"large","style":{"border":{"radius":"calc(var(--wp--custom--radius) * .75)"},"dimensions":{"aspectRatio":"1","scale":"cover"}}} -->
					<figure class="wp-block-image size-large has-custom-border"><img src="<?php echo $tertiary_image; ?>" alt="<?php esc_attr_e( 'A tidy cable index pack.', 'window-shopping' ); ?>" style="border-radius:calc(var(--wp--custom--radius) * .75);aspect-ratio:1;object-fit:cover"/></figure>
					<!-- /wp:image -->

					<!-- wp:paragraph {"style":{"typography":{"fontWeight":"760"}},"fontSize":"medium"} -->
					<p class="has-medium-font-size" style="font-weight:760">Being arranged</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

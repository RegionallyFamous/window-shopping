# Window Shopping

One shop theme, many storefront moods.

Window Shopping is a standalone WooCommerce block theme with six Site Editor
style variations:

- Studio
- Oddities
- Atelier
- Field Supply
- Pantry
- Signal

## Playground

Try the theme with WooCommerce and sample products:

https://playground.wordpress.net/?blueprint-url=https://raw.githubusercontent.com/RegionallyFamous/window-shopping/main/playground/blueprint.json

The Playground blueprint installs WooCommerce, installs this theme from the
repository, activates it, creates the core WooCommerce pages, and seeds the
demo catalog using the bundled sample product images. The seed also creates
the six visible product categories with richer archive descriptions and real
thumbnail images, so the category browse tiles render as merchandised
collections instead of placeholder taxonomy links. It also installs a small MU
plugin that adds a front-end style switcher for the six storefront moods.

To refresh an existing Playground or local WordPress install after theme files
change, run the seed script again from WP-CLI:

```bash
wp eval 'require get_theme_file_path( "playground/seed-window-shopping.php" );'
```

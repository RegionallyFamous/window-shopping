<?php
/**
 * Plugin Name: Window Shopping Style Switcher
 * Description: Adds a front-end toolbar for switching Window Shopping style variations in demo installs.
 * Version: 0.1.0
 * Author: Regionally Famous
 *
 * @package Window_Shopping
 */

defined( 'ABSPATH' ) || exit;

/**
 * Style variations available in the Window Shopping theme.
 *
 * @return array<string, string>
 */
function window_shopping_switcher_styles() {
	return array(
		'studio'       => 'Studio',
		'oddities'    => 'Oddities',
		'atelier'     => 'Atelier',
		'field-supply' => 'Field Supply',
		'pantry'      => 'Pantry',
		'signal'      => 'Signal',
	);
}

/**
 * Whether the switcher should run for the current request.
 *
 * @return bool
 */
function window_shopping_switcher_enabled() {
	return 'window-shopping' === get_stylesheet() && ! is_admin();
}

/**
 * Read the active style slug from the theme helper or the newest global styles post.
 *
 * @return string
 */
function window_shopping_switcher_active_slug() {
	$styles = window_shopping_switcher_styles();
	$option = get_option( 'window_shopping_switcher_active_style', '' );
	if ( isset( $styles[ $option ] ) ) {
		return $option;
	}

	if ( function_exists( 'window_shopping_active_style_slug' ) ) {
		return window_shopping_active_style_slug();
	}

	$posts  = get_posts(
		array(
			'post_type'        => 'wp_global_styles',
			'post_status'      => 'any',
			'numberposts'      => 1,
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

	if ( empty( $posts ) ) {
		return 'studio';
	}

	$data       = json_decode( $posts[0]->post_content, true );
	$candidates = array(
		isset( $data['title'] ) ? $data['title'] : '',
		$posts[0]->post_title,
		$posts[0]->post_name,
	);

	foreach ( $candidates as $candidate ) {
		$slug = sanitize_title( $candidate );
		if ( isset( $styles[ $slug ] ) ) {
			return $slug;
		}
	}

	return 'studio';
}

/**
 * Load a style variation JSON file.
 *
 * @param string $slug Style variation slug.
 * @return array<string, mixed>
 */
function window_shopping_switcher_style_data( $slug ) {
	$styles = window_shopping_switcher_styles();
	if ( ! isset( $styles[ $slug ] ) ) {
		return array();
	}

	$style_file = trailingslashit( get_stylesheet_directory() ) . 'styles/' . $slug . '.json';
	if ( ! file_exists( $style_file ) ) {
		return array();
	}

	$data = json_decode( file_get_contents( $style_file ), true );
	if ( ! is_array( $data ) ) {
		return array();
	}

	unset( $data['$schema'] );
	$data['version'] = 3;
	$data['title']   = $styles[ $slug ];

	return $data;
}

/**
 * Merge the selected style variation into the theme origin.
 *
 * @param WP_Theme_JSON_Data $theme_json Theme JSON data object.
 * @return WP_Theme_JSON_Data
 */
function window_shopping_switcher_filter_theme_json( $theme_json ) {
	if ( 'window-shopping' !== get_stylesheet() ) {
		return $theme_json;
	}

	$data = window_shopping_switcher_style_data( window_shopping_switcher_active_slug() );
	if ( empty( $data ) || ! method_exists( $theme_json, 'update_with' ) ) {
		return $theme_json;
	}

	return $theme_json->update_with( $data );
}
add_filter( 'wp_theme_json_data_theme', 'window_shopping_switcher_filter_theme_json' );

/**
 * Get the newest global styles post ID for the active theme.
 *
 * @return int
 */
function window_shopping_switcher_global_styles_post_id() {
	$posts = get_posts(
		array(
			'post_type'        => 'wp_global_styles',
			'post_status'      => 'any',
			'numberposts'      => 1,
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

	return empty( $posts ) ? 0 : (int) $posts[0]->ID;
}

/**
 * Apply a style variation by writing it to the active wp_global_styles post.
 *
 * @param string $slug Style variation slug.
 * @return bool
 */
function window_shopping_switcher_apply_style( $slug ) {
	$styles = window_shopping_switcher_styles();
	if ( ! isset( $styles[ $slug ] ) ) {
		return false;
	}

	$data = window_shopping_switcher_style_data( $slug );
	if ( empty( $data ) ) {
		return false;
	}

	$data['isGlobalStylesUserThemeJSON'] = true;

	$post_id = window_shopping_switcher_global_styles_post_id();
	$post    = array(
		'post_content' => wp_json_encode( $data, JSON_UNESCAPED_SLASHES ),
		'post_name'    => $slug,
		'post_status'  => 'publish',
		'post_title'   => $styles[ $slug ],
		'post_type'    => 'wp_global_styles',
	);

	if ( $post_id ) {
		$post['ID'] = $post_id;
		$result     = wp_update_post( $post, true );
	} else {
		$result = wp_insert_post( $post, true );
	}

	if ( is_wp_error( $result ) || ! $result ) {
		return false;
	}

	wp_set_object_terms( (int) $result, get_stylesheet(), 'wp_theme' );
	update_option( 'window_shopping_switcher_active_style', $slug, false );

	if ( class_exists( 'WP_Theme_JSON_Resolver' ) ) {
		WP_Theme_JSON_Resolver::clean_cached_data();
	}

	return true;
}

/**
 * Handle style switch requests.
 *
 * @return void
 */
function window_shopping_switcher_handle_request() {
	if ( ! window_shopping_switcher_enabled() || empty( $_GET['window_shopping_style'] ) ) {
		return;
	}

	$slug = sanitize_key( wp_unslash( $_GET['window_shopping_style'] ) );
	if ( ! isset( window_shopping_switcher_styles()[ $slug ] ) ) {
		return;
	}

	$nonce = isset( $_GET['_ws_style_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['_ws_style_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'window_shopping_switch_style_' . $slug ) ) {
		return;
	}

	window_shopping_switcher_apply_style( $slug );

	wp_safe_redirect(
		remove_query_arg(
			array(
				'window_shopping_style',
				'_ws_style_nonce',
			)
		)
	);
	exit;
}
add_action( 'template_redirect', 'window_shopping_switcher_handle_request', 1 );

/**
 * Add a body class when the switcher is visible.
 *
 * @param string[] $classes Body classes.
 * @return string[]
 */
function window_shopping_switcher_body_class( $classes ) {
	if ( window_shopping_switcher_enabled() ) {
		$classes[] = 'window-shopping-has-style-switcher';
	}

	return $classes;
}
add_filter( 'body_class', 'window_shopping_switcher_body_class' );

/**
 * Enqueue the switcher style.
 *
 * @return void
 */
function window_shopping_switcher_enqueue_style() {
	if ( ! window_shopping_switcher_enabled() ) {
		return;
	}

	wp_register_style( 'window-shopping-style-switcher', false, array(), '0.1.0' );
	wp_enqueue_style( 'window-shopping-style-switcher' );
	wp_add_inline_style(
		'window-shopping-style-switcher',
		'
		.window-shopping-style-switcher {
			position: sticky;
			top: 0;
			z-index: 99999;
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 0.45rem;
			min-height: 42px;
			padding: 0.45rem 1rem;
			background: #11100e;
			color: #fffdf8;
			box-sizing: border-box;
			box-shadow: 0 1px 0 rgba(255, 255, 255, 0.16) inset, 0 12px 30px rgba(0, 0, 0, 0.18);
			font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
			font-size: 12px;
			font-weight: 700;
			line-height: 1;
		}
		.window-shopping-style-switcher__label {
			margin-right: 0.35rem;
			color: rgba(255, 253, 248, 0.68);
			text-transform: uppercase;
			white-space: nowrap;
		}
		.window-shopping-style-switcher__link {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			min-height: 28px;
			padding: 0 0.72rem;
			border: 1px solid rgba(255, 253, 248, 0.24);
			border-radius: 999px;
			color: #fffdf8;
			text-decoration: none;
			white-space: nowrap;
		}
		.window-shopping-style-switcher__link:hover,
		.window-shopping-style-switcher__link[aria-current="true"] {
			border-color: #fffdf8;
			background: #fffdf8;
			color: #11100e;
		}
		.window-shopping-has-style-switcher .ws-site-header {
			top: 42px;
		}
		.admin-bar .window-shopping-style-switcher {
			top: 32px;
		}
		.admin-bar.window-shopping-has-style-switcher .ws-site-header {
			top: 74px;
		}
		@media (max-width: 782px) {
			.admin-bar .window-shopping-style-switcher {
				top: 46px;
			}
			.admin-bar.window-shopping-has-style-switcher .ws-site-header {
				top: 88px;
			}
		}
		@media (max-width: 640px) {
			.window-shopping-style-switcher {
				justify-content: flex-start;
				overflow-x: auto;
				padding-right: 1rem;
				padding-left: 1rem;
				scrollbar-width: none;
			}
			.window-shopping-style-switcher::-webkit-scrollbar {
				display: none;
			}
		}
		'
	);
}
add_action( 'wp_enqueue_scripts', 'window_shopping_switcher_enqueue_style' );

/**
 * Render the switcher toolbar.
 *
 * @return void
 */
function window_shopping_switcher_render() {
	if ( ! window_shopping_switcher_enabled() ) {
		return;
	}

	$current = window_shopping_switcher_active_slug();
	?>
	<nav class="window-shopping-style-switcher" aria-label="<?php esc_attr_e( 'Window Shopping styles', 'window-shopping' ); ?>">
		<span class="window-shopping-style-switcher__label"><?php esc_html_e( 'Style', 'window-shopping' ); ?></span>
		<?php foreach ( window_shopping_switcher_styles() as $slug => $label ) : ?>
			<a
				class="window-shopping-style-switcher__link"
				href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'window_shopping_style', $slug ), 'window_shopping_switch_style_' . $slug, '_ws_style_nonce' ) ); ?>"
				<?php echo $current === $slug ? 'aria-current="true"' : ''; ?>
			><?php echo esc_html( $label ); ?></a>
		<?php endforeach; ?>
	</nav>
	<?php
}
add_action( 'wp_body_open', 'window_shopping_switcher_render', 1 );

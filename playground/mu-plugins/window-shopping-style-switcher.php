<?php
/**
 * Plugin Name: Window Shopping Style Switcher
 * Description: Adds a per-browser front-end toolbar for previewing Window Shopping style variations in demo installs.
 * Version: 0.1.3
 * Author: Regionally Famous
 *
 * @package Window_Shopping
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WINDOW_SHOPPING_SWITCHER_COOKIE' ) ) {
	define( 'WINDOW_SHOPPING_SWITCHER_COOKIE', 'window_shopping_style' );
}

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
 * Read a valid style slug from the preview cookie.
 *
 * @return string
 */
function window_shopping_switcher_cookie_slug() {
	$styles = window_shopping_switcher_styles();
	if ( empty( $_COOKIE[ WINDOW_SHOPPING_SWITCHER_COOKIE ] ) ) {
		return '';
	}

	$slug = sanitize_key( wp_unslash( $_COOKIE[ WINDOW_SHOPPING_SWITCHER_COOKIE ] ) );
	return isset( $styles[ $slug ] ) ? $slug : '';
}

/**
 * Read the active style slug from the preview cookie or theme helper.
 *
 * @return string
 */
function window_shopping_switcher_active_slug() {
	$cookie_slug = window_shopping_switcher_cookie_slug();
	if ( $cookie_slug ) {
		return $cookie_slug;
	}

	if ( function_exists( 'window_shopping_active_style_slug' ) ) {
		return window_shopping_active_style_slug();
	}

	return 'studio';
}

/**
 * Let theme helpers such as body classes honor the per-browser preview style.
 *
 * @param string $slug Active style slug.
 * @return string
 */
function window_shopping_switcher_filter_active_style_slug( $slug ) {
	$cookie_slug = window_shopping_switcher_cookie_slug();
	return $cookie_slug ? $cookie_slug : $slug;
}
add_filter( 'window_shopping_active_style_slug', 'window_shopping_switcher_filter_active_style_slug' );

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
	if ( ! window_shopping_switcher_enabled() ) {
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
 * Short visible labels for tight demo-toolbar viewports.
 *
 * @param string $slug  Style variation slug.
 * @param string $label Full style variation label.
 * @return string
 */
function window_shopping_switcher_short_label( $slug, $label ) {
	$short_labels = array(
		'field-supply' => 'Field',
	);

	return isset( $short_labels[ $slug ] ) ? $short_labels[ $slug ] : $label;
}

/**
 * Store the selected preview style for the current browser.
 *
 * @param string $slug Style variation slug.
 * @return void
 */
function window_shopping_switcher_set_cookie( $slug ) {
	$cookie_args = array(
		'expires'  => time() + MONTH_IN_SECONDS,
		'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
		'secure'   => is_ssl(),
		'httponly' => false,
		'samesite' => 'Lax',
	);

	if ( defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ) {
		$cookie_args['domain'] = COOKIE_DOMAIN;
	}

	setcookie( WINDOW_SHOPPING_SWITCHER_COOKIE, $slug, $cookie_args );
	$_COOKIE[ WINDOW_SHOPPING_SWITCHER_COOKIE ] = $slug;
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

	window_shopping_switcher_set_cookie( $slug );
	delete_option( 'window_shopping_switcher_active_style' );

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

	wp_register_style( 'window-shopping-style-switcher', false, array(), '0.1.3' );
	wp_enqueue_style( 'window-shopping-style-switcher' );
	wp_add_inline_style(
		'window-shopping-style-switcher',
		'
			.window-shopping-style-switcher {
				position: sticky;
				top: 0;
				z-index: 60;
				display: flex;
				align-items: center;
				justify-content: center;
				gap: 0.35rem;
				min-height: 34px;
				max-width: 100%;
				padding: 0.28rem 0.85rem;
				border-bottom: 1px solid rgba(255, 253, 248, 0.12);
				background: rgba(17, 16, 14, 0.94);
				color: #fffdf8;
				box-sizing: border-box;
				box-shadow: 0 8px 18px rgba(0, 0, 0, 0.12);
				backdrop-filter: blur(14px);
				font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
				font-size: 11px;
				font-weight: 700;
				line-height: 1;
			}
			.window-shopping-style-switcher__label {
				margin-right: 0.25rem;
				color: rgba(255, 253, 248, 0.68);
				text-transform: uppercase;
				white-space: nowrap;
		}
			.window-shopping-style-switcher__link {
				display: inline-flex;
				align-items: center;
				justify-content: center;
				min-height: 24px;
				padding: 0 0.62rem;
				border: 1px solid rgba(255, 253, 248, 0.24);
				border-radius: 999px;
				color: #fffdf8;
				text-decoration: none;
				white-space: nowrap;
			}
			.window-shopping-style-switcher__short {
				display: none;
			}
			.window-shopping-style-switcher__link:hover,
			.window-shopping-style-switcher__link[aria-current="true"] {
			border-color: #fffdf8;
			background: #fffdf8;
			color: #11100e;
			}
			.window-shopping-has-style-switcher .ws-site-header {
				top: 34px;
			}
			.admin-bar .window-shopping-style-switcher {
				top: 32px;
			}
			.admin-bar.window-shopping-has-style-switcher .ws-site-header {
				top: 66px;
			}
		@media (max-width: 782px) {
			.admin-bar .window-shopping-style-switcher {
				top: 46px;
				}
				.admin-bar.window-shopping-has-style-switcher .ws-site-header {
					top: 80px;
				}
			}
			@media (max-width: 640px) {
				.window-shopping-style-switcher {
					gap: 0.28rem;
					justify-content: flex-start;
					overflow-x: auto;
					overscroll-behavior-x: contain;
					padding-right: 0.45rem;
					padding-left: 0.45rem;
					scroll-padding-inline: 0.45rem;
					scrollbar-width: none;
					-webkit-overflow-scrolling: touch;
					font-size: clamp(9px, 2.65vw, 11px);
				}
				.window-shopping-style-switcher__label {
					position: absolute;
					width: 1px;
					height: 1px;
					overflow: hidden;
					clip: rect(0, 0, 0, 0);
					white-space: nowrap;
				}
				.window-shopping-style-switcher__link {
					flex: 1 1 auto;
					min-width: 0;
					padding-right: 0.44rem;
					padding-left: 0.44rem;
				}
				.window-shopping-style-switcher__text {
					display: none;
				}
				.window-shopping-style-switcher__short {
					display: inline;
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
					href="<?php echo esc_url( add_query_arg( 'window_shopping_style', $slug ) ); ?>"
					aria-label="<?php echo esc_attr( $label ); ?>"
					<?php echo $current === $slug ? 'aria-current="true"' : ''; ?>
				>
					<span class="window-shopping-style-switcher__text"><?php echo esc_html( $label ); ?></span>
					<span class="window-shopping-style-switcher__short" aria-hidden="true"><?php echo esc_html( window_shopping_switcher_short_label( $slug, $label ) ); ?></span>
				</a>
			<?php endforeach; ?>
	</nav>
	<?php
}
add_action( 'wp_body_open', 'window_shopping_switcher_render', 1 );

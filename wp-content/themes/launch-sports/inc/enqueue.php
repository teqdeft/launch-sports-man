<?php
/**
 * Styles and scripts.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Front-end assets.
 *
 * ORDER MATTERS, twice over.
 *
 * CSS: desktop.css must load before responsive.css. The responsive sheet
 * overrides the desktop one by coming later, and in a handful of places it
 * relies on that rather than on specificity. Swapping them changes the design.
 *
 * JS: the motion layer needs all four vendor libraries present, so it declares
 * them as dependencies rather than trusting registration order. fallbacks.js
 * has no dependencies at all and is registered first, exactly as it sat first
 * in the static build. nav.js is deliberately independent of the motion layer:
 * navigation must keep working even if GSAP fails to load.
 */
function lsm_enqueue_assets() {
	$dir = get_template_directory();
	$uri = get_template_directory_uri();

	// filemtime() as the version so a changed file busts the cache by itself.
	$ver = static function ( $rel ) use ( $dir ) {
		$path = $dir . '/' . ltrim( $rel, '/' );
		return file_exists( $path ) ? (string) filemtime( $path ) : '1.0.0';
	};

	// ---- fonts -------------------------------------------------------------
	wp_enqueue_style(
		'lsm-fonts',
		'https://fonts.googleapis.com/css2?family=Big+Shoulders+Display:wght@500;700;800&family=Hanken+Grotesk:wght@300;400;500&family=IBM+Plex+Mono:wght@400;500;600&display=swap',
		array(),
		null
	);

	// ---- the two stylesheets ----------------------------------------------
	wp_enqueue_style( 'lsm-desktop', $uri . '/assets/css/desktop.css', array( 'lsm-fonts' ), $ver( 'assets/css/desktop.css' ) );
	wp_enqueue_style( 'lsm-responsive', $uri . '/assets/css/responsive.css', array( 'lsm-desktop' ), $ver( 'assets/css/responsive.css' ) );

	// ---- vendor ------------------------------------------------------------
	wp_enqueue_script( 'lsm-fallbacks', $uri . '/assets/js/fallbacks.js', array(), $ver( 'assets/js/fallbacks.js' ), true );
	wp_enqueue_script( 'gsap', $uri . '/assets/js/vendor/gsap.min.js', array(), '3.15.0', true );
	wp_enqueue_script( 'gsap-scrolltrigger', $uri . '/assets/js/vendor/scrolltrigger.min.js', array( 'gsap' ), '3.15.0', true );
	wp_enqueue_script( 'gsap-splittext', $uri . '/assets/js/vendor/splittext.min.js', array( 'gsap' ), '3.15.0', true );
	wp_enqueue_script( 'lenis', $uri . '/assets/js/vendor/lenis.min.js', array(), '1.3.26', true );

	// ---- legal pages -------------------------------------------------------
	/*
	 * Only where there is a contents column to track. Loading it everywhere
	 * would put a scroll listener on four pages that have no use for one.
	 */
	if ( is_page_template( 'templates/page-legal.php' ) ) {
		wp_enqueue_script( 'lsm-legal', $uri . '/assets/js/legal.js', array(), $ver( 'assets/js/legal.js' ), true );
	}

	// ---- ours --------------------------------------------------------------
	wp_enqueue_script(
		'lsm-motion',
		$uri . '/assets/js/motion.js',
		array( 'gsap', 'gsap-scrolltrigger', 'gsap-splittext', 'lenis' ),
		$ver( 'assets/js/motion.js' ),
		true
	);
	wp_enqueue_script( 'lsm-nav', $uri . '/assets/js/nav.js', array(), $ver( 'assets/js/nav.js' ), true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'lsm_enqueue_assets' );

/**
 * motion.js reads document.body as soon as it runs, so it must never end up in
 * <head>. Everything above is registered with $in_footer = true, but a plugin
 * that moves or defers scripts can still break that assumption. Marking our own
 * scripts defer keeps them after parsing wherever they are printed.
 */
function lsm_script_loader_tag( $tag, $handle ) {
	$defer = array( 'lsm-motion', 'lsm-nav', 'lsm-fallbacks', 'gsap', 'gsap-scrolltrigger', 'gsap-splittext', 'lenis' );
	if ( in_array( $handle, $defer, true ) && false === strpos( $tag, ' defer' ) ) {
		$tag = str_replace( ' src=', ' defer src=', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'lsm_script_loader_tag', 10, 2 );

/**
 * The design has no comment thread and no emoji; dropping the emoji detection
 * script removes a render-blocking inline blob the static build never had.
 */
function lsm_trim_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
}
add_action( 'init', 'lsm_trim_head' );

/**
 * The static build shipped no block-library CSS and the theme uses none of it.
 * Loading it would put ~90KB in front of desktop.css and restyle core elements.
 */
function lsm_dequeue_block_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'lsm_dequeue_block_styles', 100 );

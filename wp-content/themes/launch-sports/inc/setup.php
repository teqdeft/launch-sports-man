<?php
/**
 * Theme supports, menus and image sizes.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Core theme supports.
 */
function lsm_theme_setup() {
	load_theme_textdomain( 'launch-sports', get_template_directory() . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	/*
	 * The static build ships its own <svg> logo inline in the header and the
	 * footer, so there is no custom-logo support here on purpose: adding it
	 * would give the client a second, conflicting place to set a logo.
	 */

	register_nav_menus(
		array(
			'primary' => __( 'Primary (header)', 'launch-sports' ),
			'mobile'  => __( 'Mobile overlay', 'launch-sports' ),
			'footer'  => __( 'Footer', 'launch-sports' ),
		)
	);
}
add_action( 'after_setup_theme', 'lsm_theme_setup' );

/**
 * The design uses fixed aspect ratios per slot. Registering them keeps
 * uploaded media from being served at full size into a 3:4 card.
 */
function lsm_image_sizes() {
	add_image_size( 'lsm-roster', 720, 960, true );   // 3:4 player cards
	add_image_size( 'lsm-panel', 900, 1125, true );   // 4:5 team panels
	add_image_size( 'lsm-wide', 1920, 1080, false );  // hero / band imagery
}
add_action( 'after_setup_theme', 'lsm_image_sizes' );

/**
 * Body classes.
 *
 * The stylesheets scope every page-specific rule with :where(.page-home),
 * :where(.page-what-we-do), :where(.page-about) or :where(.page-lets-talk).
 * Those classes are load-bearing: without the right one, a page loses its own
 * layout rules. They are derived from the template so an editor renaming a
 * page cannot break the CSS.
 */
function lsm_body_classes( $classes ) {
	$slug = lsm_page_slug();
	if ( $slug ) {
		$classes[] = 'page-' . $slug;
	}
	return $classes;
}
add_filter( 'body_class', 'lsm_body_classes' );

/**
 * Allow SVG uploads, for administrators only.
 *
 * The header and footer lockups have to be SVG. The responsive sheet sizes and
 * recolours them with element selectors that reach inside the artwork -
 * `.lsm-shell > header svg { width: 100px !important }` and several
 * `svg [fill="#fff"]` overrides - none of which can touch the inside of an
 * <img>. So a raster logo would break the mobile header.
 *
 * SVG can carry script, which is why this is capped at users who can already
 * install plugins, and why lsm_logo() strips <script> and inline event
 * handlers before printing the file.
 *
 * @param array $mimes Allowed mime types.
 * @return array
 */
function lsm_allow_svg_upload( $mimes ) {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'lsm_allow_svg_upload' );

/**
 * WordPress sniffs file contents and rejects SVG because it does not look like
 * the declared type. Trust the extension, for the same capped set of users.
 *
 * @param array  $data     File data.
 * @param string $file     Path on disk.
 * @param string $filename Original name.
 * @param array  $mimes    Allowed mimes.
 * @return array
 */
function lsm_fix_svg_mime( $data, $file, $filename, $mimes ) {
	if ( current_user_can( 'manage_options' ) && preg_match( '/\.svg$/i', $filename ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'lsm_fix_svg_mime', 10, 4 );

/**
 * Which of the four designed page identities the current request is.
 *
 * Returns one of: home, what-we-do, about, lets-talk, or '' when the request
 * is something the static build never had (blog, archive, 404), which falls
 * back to the shared chrome only.
 */
function lsm_page_slug() {
	if ( is_front_page() ) {
		return 'home';
	}

	if ( is_404() ) {
		return 'error';
	}

	if ( is_page() ) {
		$template = get_page_template_slug();
		$map      = array(
			'templates/page-what-we-do.php' => 'what-we-do',
			'templates/page-about.php'      => 'about',
			'templates/page-lets-talk.php'  => 'lets-talk',
			'templates/page-legal.php'      => 'legal',
		);
		if ( isset( $map[ $template ] ) ) {
			return $map[ $template ];
		}
	}

	return '';
}

/**
 * Keep the document title set the way the design sets it.
 *
 * WordPress runs titles through wptexturize, which curls the apostrophe in
 * "Let's Talk" and turns the title separator into an en dash. The static build
 * uses a straight apostrophe and an em dash throughout, so both are put back.
 *
 * the_title covers the navigation menus, where "Let's Talk" appears in the
 * footer and the mobile menu on every page. document_title and
 * single_post_title cover the browser tab.
 *
 * Only wptexturize is removed - esc_html stays on, so titles are still escaped.
 * Post *content* is untouched, so the blog still gets typographic quotes where
 * an editor writes prose.
 */
function lsm_plain_document_title() {
	remove_filter( 'document_title', 'wptexturize' );
	remove_filter( 'single_post_title', 'wptexturize' );
	remove_filter( 'the_title', 'wptexturize' );
}
add_action( 'init', 'lsm_plain_document_title' );

/**
 * @return string
 */
function lsm_title_separator() {
	return '—';
}
add_filter( 'document_title_separator', 'lsm_title_separator' );

<?php
/**
 * Navigation output.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Mobile overlay walker: bare <a> children, no <ul>/<li>.
 *
 * This is not a style preference. The overlay's CSS is:
 *
 *     #menu a              { display:block; padding:16px 0; border-bottom:1px … }
 *     #menu a:last-child   { border-bottom:0; }
 *     #menu a[aria-current="page"] { color:#CC9D39; }
 *
 * wp_nav_menu()'s default <ul><li><a> wrapper would make every anchor the only
 * child of its <li>, so :last-child would match all of them and every divider
 * would disappear. Emitting the anchors as siblings keeps that rule meaning
 * what it says.
 */
class LSM_Mobile_Menu_Walker extends Walker_Nav_Menu {

	/** No list wrapper at any depth. */
	public function start_lvl( &$output, $depth = 0, $args = null ) {}

	/** No list wrapper at any depth. */
	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	/**
	 * One anchor per item.
	 *
	 * @param string   $output Accumulated markup, by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Menu args.
	 * @param int      $id     Item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$current = ! empty( $item->current ) || ! empty( $item->current_item_ancestor );
		$title   = apply_filters( 'the_title', $item->title, $item->ID );

		$output .= sprintf(
			'<a href="%s"%s%s>%s</a>',
			esc_url( $item->url ),
			$current ? ' aria-current="page"' : '',
			$item->target ? ' target="' . esc_attr( $item->target ) . '" rel="noopener"' : '',
			esc_html( $title )
		);
	}

	/** Anchors are self-contained. */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}

/**
 * Strip WordPress's generated menu-item classes.
 *
 * The design's <li> elements carry no classes at all, and the layout lives on
 * the <ul>. Leaving menu-item / menu-item-type-… / menu-item-1234 on every item
 * adds nothing the CSS uses and makes the markup noisy. The "current" state is
 * expressed with aria-current on the anchor instead, which the CSS does use.
 *
 * @param array $classes Item classes.
 * @return array
 */
function lsm_clean_menu_item_classes( $classes ) {
	return array();
}
add_filter( 'nav_menu_css_class', 'lsm_clean_menu_item_classes', 10, 1 );

/**
 * Drop the id="menu-item-123" attribute for the same reason.
 *
 * @return string
 */
function lsm_clean_menu_item_id() {
	return '';
}
add_filter( 'nav_menu_item_id', 'lsm_clean_menu_item_id' );

/**
 * WordPress only adds aria-current for the exact current item. The design also
 * marks the ancestor, and the mobile overlay styles it, so normalise here.
 *
 * @param array   $atts Anchor attributes.
 * @param WP_Post $item Menu item.
 * @return array
 */
function lsm_menu_link_attributes( $atts, $item ) {
	if ( ! empty( $item->current ) || ! empty( $item->current_item_ancestor ) ) {
		$atts['aria-current'] = 'page';
	}
	if ( ! empty( $item->target ) ) {
		$atts['rel'] = 'noopener';
	}
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'lsm_menu_link_attributes', 10, 2 );

/**
 * Render one of the theme's menus, or nothing at all if it is unassigned.
 *
 * Falling back to wp_page_menu() (the WordPress default) would dump every page
 * into the header in a structure the CSS does not expect, so an unassigned
 * menu prints nothing.
 *
 * @param string $location Registered menu location.
 * @param array  $overrides wp_nav_menu args.
 */
function lsm_nav( $location, $overrides = array() ) {
	if ( ! has_nav_menu( $location ) ) {
		return;
	}
	wp_nav_menu(
		wp_parse_args(
			$overrides,
			array(
				'theme_location' => $location,
				'container'      => false,
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		)
	);
}

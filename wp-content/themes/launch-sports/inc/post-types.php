<?php
/**
 * Custom post types.
 *
 * The static build has two repeating collections big enough to deserve their
 * own post type, so the client can add, remove and reorder entries without a
 * developer:
 *
 *   lsm_player   the Our Players roster on Home
 *   lsm_member   the Team panels on About (horizontal scroller)
 *
 * The What We Do process steps and register stages are deliberately not here.
 * They are short, text-only and belong to a single page, so they are ACF
 * repeaters edited on that page rather than posts on a screen of their own.
 *
 * Blogging uses the native "post" type rather than a fifth CPT - it is a blog,
 * and reusing core gets categories, tags, feeds, search and the archive
 * templates for free.
 *
 * Both are non-public: they have no useful standalone URL in this design,
 * they only ever appear as part of a page. publicly_queryable => false keeps
 * WordPress from generating single views the theme has no template for.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shared arguments for the collection types.
 *
 * @param string $singular Singular label.
 * @param string $plural   Plural label.
 * @param string $icon     Dashicon.
 * @param int    $position Menu position.
 * @return array
 */
function lsm_cpt_args( $singular, $plural, $icon, $position ) {
	return array(
		'labels'              => array(
			'name'               => $plural,
			'singular_name'      => $singular,
			'add_new_item'       => sprintf( 'Add %s', $singular ),
			'edit_item'          => sprintf( 'Edit %s', $singular ),
			'new_item'           => sprintf( 'New %s', $singular ),
			'view_item'          => sprintf( 'View %s', $singular ),
			'search_items'       => sprintf( 'Search %s', $plural ),
			'not_found'          => sprintf( 'No %s yet', strtolower( $plural ) ),
			'menu_name'          => $plural,
			'all_items'          => $plural,
		),
		'public'              => false,
		'publicly_queryable'  => false,
		'exclude_from_search' => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_rest'        => true,
		'menu_icon'           => $icon,
		'menu_position'       => $position,
		'hierarchical'        => false,
		'has_archive'         => false,
		'rewrite'             => false,
		'supports'            => array( 'title', 'thumbnail', 'page-attributes' ),
	);
}

/**
 * Register the collections.
 */
function lsm_register_post_types() {
	register_post_type( 'lsm_player', lsm_cpt_args( 'Player', 'Players', 'dashicons-groups', 21 ) );
	register_post_type( 'lsm_member', lsm_cpt_args( 'Team member', 'Team', 'dashicons-businessperson', 22 ) );
}
add_action( 'init', 'lsm_register_post_types' );

/**
 * These collections are ordered by hand, so sort by menu_order everywhere.
 *
 * The design depends on it: the roster and the team panels are read in the
 * order they have been laid out.
 *
 * @param WP_Query $query Query object.
 */
function lsm_order_collections( $query ) {
	if ( is_admin() && ! wp_doing_ajax() ) {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'edit' === $screen->base && in_array( $screen->post_type, lsm_collection_types(), true ) ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}
		return;
	}
}
add_action( 'pre_get_posts', 'lsm_order_collections' );

/**
 * @return string[]
 */
function lsm_collection_types() {
	return array( 'lsm_player', 'lsm_member' );
}

/**
 * Fetch an ordered collection.
 *
 * @param string $type  One of lsm_collection_types().
 * @param int    $limit -1 for all.
 * @return WP_Post[]
 */
function lsm_get_collection( $type, $limit = -1 ) {
	return get_posts(
		array(
			'post_type'        => $type,
			'posts_per_page'   => $limit,
			'orderby'          => 'menu_order',
			'order'            => 'ASC',
			'post_status'      => 'publish',
			'suppress_filters' => false,
		)
	);
}

/**
 * Give the collections a sortable "Order" column so drag-free reordering is
 * still obvious in the list table.
 *
 * @param array $cols Columns.
 * @return array
 */
function lsm_collection_columns( $cols ) {
	$cols['menu_order'] = 'Order';
	return $cols;
}
foreach ( lsm_collection_types() as $lsm_t ) {
	add_filter( "manage_{$lsm_t}_posts_columns", 'lsm_collection_columns' );
}

/**
 * @param string $col Column key.
 * @param int    $post_id Post ID.
 */
function lsm_collection_column_content( $col, $post_id ) {
	if ( 'menu_order' === $col ) {
		echo (int) get_post_field( 'menu_order', $post_id );
	}
}
foreach ( lsm_collection_types() as $lsm_t ) {
	add_action( "manage_{$lsm_t}_posts_custom_column", 'lsm_collection_column_content', 10, 2 );
}

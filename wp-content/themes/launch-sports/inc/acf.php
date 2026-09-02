<?php
/**
 * ACF Pro integration.
 *
 * Field groups are stored as JSON in the theme's acf-json/ directory rather
 * than only in the database. That makes them part of the theme: they travel
 * with a deploy, they can be diffed, and a fresh install picks them up without
 * an import step.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Write field-group JSON into the theme.
 *
 * @param string $path Default save path.
 * @return string
 */
function lsm_acf_json_save_point( $path ) {
	return LSM_DIR . '/acf-json';
}
add_filter( 'acf/settings/save_json', 'lsm_acf_json_save_point' );

/**
 * Load field groups from the theme.
 *
 * @param array $paths Load paths.
 * @return array
 */
function lsm_acf_json_load_point( $paths ) {
	unset( $paths[0] );
	$paths[] = LSM_DIR . '/acf-json';
	return $paths;
}
add_filter( 'acf/settings/load_json', 'lsm_acf_json_load_point' );


/**
 * Site-wide options: header CTA, footer wordmark and credits, contact details.
 *
 * These sit in one place because they appear on every page. Putting them on
 * each page instead would mean editing the footer four times.
 */
function lsm_acf_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}
	acf_add_options_page(
		array(
			'page_title' => 'Site settings',
			'menu_title' => 'Site settings',
			'menu_slug'  => 'lsm-site-settings',
			'capability' => 'edit_theme_options',
			'position'   => 20,
			'icon_url'   => 'dashicons-admin-settings',
			'redirect'   => false,
			'autoload'   => true,
		)
	);
}
add_action( 'acf/init', 'lsm_acf_options_page' );

/**
 * The theme depends on ACF Pro for all page content. If it is ever switched
 * off, say so plainly in the admin rather than letting editors stare at pages
 * that render only their chrome.
 */
function lsm_acf_missing_notice() {
	if ( function_exists( 'get_field' ) ) {
		return;
	}
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-error"><p><strong>Launch Sports:</strong> Advanced Custom Fields PRO is not active. '
		. 'Page content is stored in ACF fields, so the site will render its header, footer and navigation only until it is re-enabled.</p></div>';
}
add_action( 'admin_notices', 'lsm_acf_missing_notice' );

/**
 * ACF's WYSIWYG toolbar for the few rich-text fields in the design.
 *
 * The design has no place for headings inside body copy, so the editor offers
 * inline emphasis and links and nothing that can break a layout.
 *
 * @param array $toolbars Toolbars.
 * @return array
 */
function lsm_acf_toolbars( $toolbars ) {
	$toolbars['Launch inline'] = array(
		1 => array( 'bold', 'italic', 'link', 'unlink', 'undo', 'redo' ),
	);
	return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars', 'lsm_acf_toolbars' );

<?php
/**
 * Launch Sports Management theme bootstrap.
 *
 * The theme is deliberately thin: this file only wires includes together.
 * Everything else lives in inc/ so each concern stays findable.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

define( 'LSM_VERSION', '1.0.0' );
define( 'LSM_DIR', get_template_directory() );
define( 'LSM_URI', get_template_directory_uri() );

require_once LSM_DIR . '/inc/setup.php';
require_once LSM_DIR . '/inc/enqueue.php';
require_once LSM_DIR . '/inc/template-tags.php';
require_once LSM_DIR . '/inc/nav.php';
require_once LSM_DIR . '/inc/post-types.php';
require_once LSM_DIR . '/inc/acf.php';
require_once LSM_DIR . '/inc/acf-fields-options.php';
require_once LSM_DIR . '/inc/acf-fields-home.php';
require_once LSM_DIR . '/inc/acf-fields-about.php';
require_once LSM_DIR . '/inc/acf-fields-what-we-do.php';
require_once LSM_DIR . '/inc/acf-fields-lets-talk.php';
require_once LSM_DIR . '/inc/acf-fields-legal.php';
require_once LSM_DIR . '/inc/contact.php';
require_once LSM_DIR . '/inc/legal.php';

/**
 * The design is built for a fixed set of pages and a blog. It has no sidebar,
 * no widget area and no comment thread, so none are registered. Adding one
 * later means adding the markup to match - there is no CSS for them.
 */

/**
 * Excerpt length and ending, used by the blog index cards.
 *
 * @param int $length Default length.
 * @return int
 */
function lsm_excerpt_length( $length ) {
	return 28;
}
add_filter( 'excerpt_length', 'lsm_excerpt_length' );

/**
 * @return string
 */
function lsm_excerpt_more() {
	return '&hellip;';
}
add_filter( 'excerpt_more', 'lsm_excerpt_more' );

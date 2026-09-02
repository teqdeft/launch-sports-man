<?php
/**
 * 404.
 *
 * Same chrome as every other page, so someone who lands here is still clearly
 * on the site rather than looking at a server error.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

get_header();

/*
 * No <main> wrapper: two mobile rules in responsive.css target the sections as
 * siblings of the header, and a wrapper between them breaks both. See
 * front-page.php.
 */
?>
	<?php lsm_section( 'error-404' ); ?>
<?php
get_footer();

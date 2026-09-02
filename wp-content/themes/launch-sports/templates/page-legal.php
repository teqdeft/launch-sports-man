<?php
/**
 * Template Name: Legal page
 *
 * The privacy policy uses this, and so would terms or a cookie notice - it is
 * deliberately not called page-privacy.php.
 *
 * Unlike the other page templates, the body here is the editor's, not a set of
 * ACF sections: a policy is prose that gets revised, and the Classic Editor is
 * the right tool for prose. The template supplies the frame and the typography.
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
	<?php lsm_section( 'legal-content' ); ?>
<?php
get_footer();

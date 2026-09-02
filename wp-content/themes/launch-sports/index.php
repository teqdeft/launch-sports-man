<?php
/**
 * Fallback template. WordPress requires index.php; in practice the blog index
 * is served by home.php and single posts by single.php.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
	<?php
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		}
	}
	?>
<?php
get_footer();

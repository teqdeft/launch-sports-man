<?php
/**
 * Generic page. Used for anything without its own designed template
 * (privacy policy, thank-you pages), so editor content still has a home.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<section class="u-band-paper">
			<div class="sec-shell">
				<h1 class="xl"><?php the_title(); ?></h1>
				<div class="lsm-prose"><?php the_content(); ?></div>
			</div>
		</section>
		<?php
	endwhile;
	?>
<?php
get_footer();

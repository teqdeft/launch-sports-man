<?php
/**
 * Template Name: About Launch
 *
 * Sections in the order the design lays them out. Each part decides for itself
 * whether it has enough content to render.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

get_header();

/*
 * No <main> wrapper: two mobile rules in responsive.css target the sections as
 * siblings of the header, and a wrapper between them breaks both. The static
 * build has no <main> either. See front-page.php.
 */
?>
	<?php
	lsm_section(
		'hero-interior',
		array(
			'band_class'   => 'hero-band-interior',
			'figure_class' => 'hero-figure-about',
			'title_class'  => 'hero-title-about',
		)
	);
	lsm_section( 'team-panels' );
	lsm_section(
		'closing-cta',
		array(
			'sec'          => 'closing-band',
			'title_class'  => 'cta-title-about',
			'button_class' => 'cta-button-about',
			'separator'    => '<br>',
		)
	);
	?>
<?php
get_footer();

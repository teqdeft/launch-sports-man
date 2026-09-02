<?php
/**
 * Template Name: What We Do
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

get_header();

/*
 * No <main> wrapper - see front-page.php. Two mobile rules target the sections
 * as siblings of the header, and a wrapper between them breaks both.
 */
?>
	<?php
	lsm_section(
		'hero-interior',
		array(
			'band_class'   => 'hero-band-interior',
			'figure_class' => 'hero-figure-what-we-do',
			'figure_attr'  => 'data-hero-fig',
			'title_class'  => 'hero-title-what-we-do',
		)
	);

	get_template_part(
		'template-parts/components/marquee',
		null,
		array(
			'items' => lsm_field( 'marquee_items' ),
			'sec'   => 'six-part-band',
		)
	);

	lsm_section( 'process-steps' );
	lsm_section( 'closing-line' );
	lsm_section( 'register-stages' );
	lsm_section(
		'closing-cta',
		array(
			'sec'          => 'closing-cta',
			'title_class'  => 'cta-title-what-we-do',
			'button_class' => 'cta-button',
		)
	);
	?>
<?php
get_footer();

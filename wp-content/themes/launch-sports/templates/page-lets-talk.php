<?php
/**
 * Template Name: Let's Talk
 *
 * Sections in the order the design lays them out. Each part decides for itself
 * whether it has enough content to render.
 *
 * The hero here is the interior hero with different clothes: no photograph, its
 * own shell and standfirst classes, and no data-hero-kop - the static build
 * leaves that attribute off this one page, so it stays off.
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
			'band_class'   => 'hero-band-contact',
			'shell_class'  => 'hero-shell-contact',
			'title_class'  => 'hero-title-contact',
			'lede_class'   => 'hero-lede-contact',
			'title_marker' => false,
		)
	);
	lsm_section( 'contact-form' );
	lsm_section(
		'closing-cta',
		array(
			'sec'         => 'closing-band',
			'shell_class' => 'cta-shell-contact',
			'title_class' => 'cta-title-contact',
			'separator'   => '<br>',
		)
	);
	?>
<?php
get_footer();

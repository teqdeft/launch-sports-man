<?php
/**
 * Home.
 *
 * Sections are rendered in the order the design lays them out. Each part
 * decides for itself whether it has enough content to render, so an empty
 * section in the CMS produces no markup at all.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

get_header();

/*
 * No <main> wrapper on purpose. Two mobile rules in responsive.css target the
 * sections as SIBLINGS of the header:
 *   .lsm-shell > header + section            { padding-top: 68px !important; }
 *   .lsm-shell > header ~ section:first-of-type > div
 * A <main> between them breaks both, and the first section loses its top
 * padding on every phone. The static build has no <main> either.
 */
?>
	<?php
	lsm_section( 'hero-home' );
	lsm_section( 'why-launch' );
	lsm_section( 'pillars' );
	lsm_section( 'philosophy' );
	lsm_section( 'roster' );
	lsm_section( 'closing-cta', array( 'sec' => 'closing', 'title_class' => 'cta-title-home', 'button_class' => 'cta-button' ) );
	?>
<?php
get_footer();

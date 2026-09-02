<?php
/**
 * Home hero.
 *
 * Structural notes that are easy to lose in translation:
 *  - data-sec="hero" is read by motion.js and by six rules in responsive.css.
 *  - [data-aurora] holds the two animated gold bands; it must keep its two
 *    empty <i> children, which are what actually animate.
 *  - The running band is nested INSIDE this section in the design, not a
 *    sibling of it. Moving it out changes the hero's height and the way the
 *    gold band meets the fold.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$image   = lsm_field( 'hero_image' );
$title   = lsm_field( 'hero_title' );
$sub     = lsm_field( 'hero_subhead' );
$intro   = lsm_field( 'hero_intro' );
$note    = lsm_field( 'hero_note' );
$cta     = lsm_field( 'hero_cta' );
$marquee = lsm_field( 'marquee_items' );

$has_lead = lsm_filled( $title ) || lsm_filled( $sub );
$has_side = lsm_filled( $intro ) || lsm_filled( $note ) || is_array( $cta );

if ( ! $has_lead && ! $has_side && ! lsm_filled( $image ) && ! lsm_filled( $marquee ) ) {
	return;
}
?>
<section data-sec="hero" class="hero-band-home">
	<div data-aurora aria-hidden="true"><i></i><i></i></div>

	<?php
	if ( lsm_filled( $image ) ) {
		/*
		 * The hero photo is the one image on the page that must not lazy-load:
		 * it is the largest contentful paint. object-fit:cover fills any ratio
		 * and object-position keeps the player in frame when it crops.
		 */
		lsm_image(
			$image,
			'lsm-wide',
			array(
				'class'         => 'hero-bg-image',
				'data-hero-bg'  => '',
				'alt'           => '',
				'loading'       => 'eager',
				'fetchpriority' => 'high',
			)
		);
		?>
		<div class="hero-scrim" data-hero-scrim aria-hidden="true"></div>
		<?php
	}
	?>

	<div class="hero-shell-home">
		<div class="hero-row">
			<?php if ( $has_lead ) : ?>
				<div class="hero-lead-col">
					<?php if ( lsm_filled( $title ) ) : ?>
						<?php
						/*
						 * Line breaks are part of the composition, so newlines become <br>.
						 * Built by hand rather than with nl2br(), which emits "<br />\n" and
						 * leaves a stray newline inside the heading.
						 */
						?>
						<h1 class="hero-title-home"><?php echo implode( '<br>', array_map( 'esc_html', preg_split( '/\R/', $title ) ) ); ?></h1>
					<?php endif; ?>
					<?php if ( lsm_filled( $sub ) ) : ?>
						<p data-hero-sub class="hero-sub hero-subhead"><?php echo esc_html( $sub ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $has_side ) : ?>
				<div class="hero-side-col" data-hero-kolom>
					<?php if ( lsm_filled( $intro ) ) : ?>
						<p class="hero-body hero-intro"><?php echo esc_html( $intro ); ?></p>
					<?php endif; ?>
					<?php if ( lsm_filled( $note ) ) : ?>
						<p class="hero-note"><?php echo esc_html( $note ); ?></p>
					<?php endif; ?>
					<?php lsm_button( $cta, 'btn hero-cta' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php get_template_part( 'template-parts/components/marquee', null, array( 'items' => $marquee ) ); ?>
</section>

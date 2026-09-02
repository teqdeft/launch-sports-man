<?php
/**
 * What We Do - "What We Keep an Eye On", the five-stage register.
 *
 * The design splits the stages across two columns either side of a cut-out
 * photograph: odd numbers on the left, even on the right. That is why the DOM
 * order is 1, 3, 5 then 2, 4 rather than 1-5. The split is derived from
 * position here, so the numbering stays correct however many stages there are.
 *
 * data-stage carries the real sequence number. registerMobiel() in motion.js
 * reads it to re-sort the entries into reading order on phones, where the two
 * columns collapse into one, so it must stay accurate.
 *
 * The closing note sits at the foot of the right column, which is where the
 * design puts it.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$heading = lsm_field( 'stage_heading' );
$figure  = lsm_field( 'stage_figure' );
$note    = lsm_field( 'stage_note' );
$rows    = lsm_field( 'register_stages' );

// An unnamed row would still consume a number and a column slot, so drop it.
$stages = array();
foreach ( (array) $rows as $row ) {
	if ( is_array( $row ) && lsm_filled( $row['stage_name'] ) ) {
		$stages[] = $row['stage_name'];
	}
}

if ( ! lsm_filled( $heading ) && ! $stages ) {
	return;
}

/** One entry. $n is the real 1-based position, $side is 'left' or 'right'. */
$entry = static function ( $name, $n, $side ) {
	printf(
		'<div class="stage-entry-%s" data-stage="%d"><span class="stage-number" data-label>%s</span><p class="stage-name">%s</p></div>',
		esc_attr( $side ),
		(int) $n,
		esc_html( str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) ),
		esc_html( $name )
	);
};
?>
<section data-sec="five-stage-register" class="sec-band-light">
	<div class="stage-shell">
		<?php if ( lsm_filled( $heading ) ) : ?>
			<?php /* Broken across two lines in the design; new lines become <br>. */ ?>
			<h2 class="xl stage-heading"><?php echo implode( '<br>', array_map( 'esc_html', preg_split( '/\R/', $heading ) ) ); ?></h2>
		<?php endif; ?>

		<div class="stage-row">
			<div class="stage-col-left">
				<?php
				foreach ( $stages as $i => $stage ) {
					if ( 0 === $i % 2 ) {          // positions 1, 3, 5
						$entry( $stage, $i + 1, 'left' );
					}
				}
				?>
			</div>

			<?php if ( lsm_filled( $figure ) ) : ?>
				<figure class="stage-figure" data-register-figure>
					<i class="stage-figure-shadow" data-register-shadow aria-hidden="true"></i>
					<?php lsm_image( $figure, 'full', array( 'class' => 'stage-figure-image' ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="stage-col-right">
				<?php
				foreach ( $stages as $i => $stage ) {
					if ( 0 !== $i % 2 ) {          // positions 2, 4
						$entry( $stage, $i + 1, 'right' );
					}
				}
				?>
				<?php if ( lsm_filled( $note ) ) : ?>
					<p class="stage-note" data-register-note><?php echo esc_html( $note ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>

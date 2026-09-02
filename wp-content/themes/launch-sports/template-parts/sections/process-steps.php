<?php
/**
 * What We Do - the six-step spine.
 *
 * Attributes the motion layer binds to, all required:
 *   [data-spine-wrap]  the scroll container
 *   [data-ball-index]  the ball that rolls down the spine, with
 *                      [data-ball-shadow] and [data-ball-img] inside it
 *   [data-spine]       the 1px rule that draws itself
 *   [data-step]        each row; [data-word], [data-chip], [data-claim], [data-body]
 *   [data-active]      the intro label that swaps to the current step's word
 *
 * The chip numbers (01-06) come from position, so reordering the steps
 * renumbers them.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$label = lsm_field( 'steps_label' );
$ball  = lsm_field( 'steps_ball_image' );
$rows  = lsm_field( 'process_steps' );

// A row with no name has nothing to put on the spine, so it is skipped rather
// than rendered as an empty step - that would still take a number.
$steps = array();
foreach ( (array) $rows as $row ) {
	if ( is_array( $row ) && lsm_filled( $row['step_name'] ) ) {
		$steps[] = $row;
	}
}

if ( ! $steps ) {
	return;
}

$first = $steps[0]['step_name'];
?>
<section data-sec="six-steps-spine" class="sec-band-light">
	<div class="step-shell">
		<div class="step-intro">
			<?php if ( lsm_filled( $label ) ) : ?>
				<p class="step-intro-label" data-label><?php echo esc_html( $label ); ?></p>
			<?php endif; ?>
			<?php /* Ships showing the first step; motion.js swaps it as you scroll. */ ?>
			<p class="step-intro-active" data-active><?php echo esc_html( $first ); ?></p>
		</div>

		<div class="step-spine-wrap" data-spine-wrap>
			<?php if ( lsm_filled( $ball ) ) : ?>
				<div class="step-ball-track" data-ball-index aria-hidden="true">
					<i class="step-ball-shadow" data-ball-shadow></i>
					<?php lsm_image( $ball, 'full', array( 'class' => 'step-ball-image', 'data-ball-img' => '', 'alt' => '' ) ); ?>
				</div>
			<?php endif; ?>
			<i class="step-spine-line" data-spine></i>

			<div class="step-list">
				<?php
				foreach ( $steps as $i => $step ) :
					$word  = $step['step_name'];
					$claim = isset( $step['step_claim'] ) ? $step['step_claim'] : '';
					$body  = isset( $step['step_body'] ) ? $step['step_body'] : '';
					?>
					<article class="step-row" data-step>
						<h2 class="xl step-word" data-word><?php echo esc_html( $word ); ?></h2>
						<span class="step-chip" data-label data-chip><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<?php if ( lsm_filled( $claim ) || lsm_filled( $body ) ) : ?>
							<div class="step-copy">
								<?php if ( lsm_filled( $claim ) ) : ?>
									<p class="step-claim" data-claim><?php echo esc_html( $claim ); ?></p>
								<?php endif; ?>
								<?php if ( lsm_filled( $body ) ) : ?>
									<p class="step-body" data-body><?php echo esc_html( $body ); ?></p>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</section>

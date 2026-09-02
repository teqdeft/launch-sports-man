<?php
/**
 * "Opportunity Changes Everything." - the dark band.
 *
 * Three pieces are driven by JS and must keep their attributes:
 *  - [data-basketball] holds the hero ball; [data-ball-photo] is the photo and
 *    [data-ball-drawn] the SVG fallback that fallbacks.js swaps in if the photo
 *    404s. The drawn ball is decorative and stays in the template.
 *  - [data-motif="pileup"] is an empty canvas mount; motion.js draws into it.
 *    It must stay empty.
 *  - [data-opp-row], [data-opp-kop] and [data-opp-tekst] are targeted by the
 *    1800px and 1799px breakpoints.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$ball      = lsm_field( 'why_ball_image' );
$heading   = lsm_field( 'why_heading' );
$lead      = lsm_field( 'why_lead' );
$body      = lsm_field( 'why_body' );
$statement = lsm_field( 'why_statement' );
$accent    = lsm_field( 'why_statement_accent' );
$note      = lsm_field( 'why_note' );
$figure    = lsm_field( 'why_figure' );
$show_mot  = lsm_field( 'why_show_motif' );

if ( ! lsm_filled( $heading ) && ! lsm_filled( $lead ) && ! lsm_filled( $body )
	&& ! lsm_filled( $statement ) && ! lsm_filled( $note ) && ! lsm_filled( $figure ) ) {
	return;
}
?>
<section data-sec="why-launch" class="sec-why-band">
	<?php if ( lsm_filled( $ball ) ) : ?>
		<div data-basketball aria-hidden="true">
			<?php lsm_image( $ball, 'full', array( 'data-ball-photo' => '', 'alt' => '' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="sec-why-shell">
		<?php if ( lsm_filled( $heading ) || lsm_filled( $lead ) || lsm_filled( $body ) ) : ?>
			<div class="sec-why-row" data-opp-row>
				<?php if ( lsm_filled( $heading ) ) : ?>
					<h2 data-opp-kop class="xl sec-why-heading"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( lsm_filled( $lead ) || lsm_filled( $body ) ) : ?>
					<div class="sec-why-copy" data-opp-tekst>
						<?php if ( lsm_filled( $lead ) ) : ?>
							<p class="sec-why-lead"><?php echo esc_html( $lead ); ?></p>
						<?php endif; ?>
						<?php if ( lsm_filled( $body ) ) : ?>
							<p class="sec-why-body"><?php echo esc_html( $body ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php
		if ( $show_mot ) :
			/*
			 * The mount stays empty - motion.js appends a <canvas> and draws into
			 * it. The words ride in as attributes so they can be edited in the CMS;
			 * without them motion.js falls back to its built-in list, which is what
			 * the static build relies on.
			 */
			$motif_words   = lsm_field( 'motif_words' );
			$motif_caption = lsm_field( 'motif_caption' );
			$motif_attr    = '';

			if ( lsm_filled( $motif_words ) ) {
				$rows = array();
				foreach ( (array) $motif_words as $row ) {
					$word = isset( $row['word'] ) ? trim( (string) $row['word'] ) : '';
					if ( '' === $word ) {
						continue;
					}
					$entry = array( 't' => $word );
					// A blank position means "space it evenly"; motion.js works that out.
					if ( isset( $row['position'] ) && '' !== $row['position'] && null !== $row['position'] ) {
						$entry['x'] = round( ( (float) $row['position'] ) / 100, 4 );
					}
					$rows[] = $entry;
				}
				if ( $rows ) {
					$motif_attr = ' data-motif-words="' . esc_attr( wp_json_encode( $rows ) ) . '"';
				}
			}

			if ( lsm_filled( $motif_caption ) ) {
				$motif_attr .= ' data-motif-caption="' . esc_attr( $motif_caption ) . '"';
			}
			?>
			<div class="sec-why-motif-band" data-motif-scroll><div class="sec-why-motif-canvas" data-motif="pileup"<?php echo $motif_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- assembled from esc_attr above. ?>></div></div>
		<?php endif; ?>

		<?php if ( lsm_filled( $statement ) || lsm_filled( $accent ) ) : ?>
			<p class="xl sec-why-statement"><?php echo lsm_accent_heading( $statement, $accent, '<br>' ); ?></p>
		<?php endif; ?>

		<?php if ( lsm_filled( $note ) ) : ?>
			<p class="sec-why-note"><?php echo esc_html( $note ); ?></p>
		<?php endif; ?>
	</div>

	<?php if ( lsm_filled( $figure ) ) : ?>
		<figure class="sec-why-figure"><?php lsm_image( $figure, 'lsm-wide', array( 'class' => 'sec-why-figure-image' ) ); ?></figure>
	<?php endif; ?>
</section>

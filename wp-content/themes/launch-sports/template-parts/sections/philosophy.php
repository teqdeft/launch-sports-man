<?php
/**
 * "Representation Should Feel Like a Relationship" - dark band with the
 * square photo bleeding off the edge and a slow single-word marquee under it.
 *
 * [data-mq-plain] is a second marquee variant: one word, no separators, and
 * motion.js clones it the same way it does the gold band.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$figure = lsm_field( 'philosophy_image' );
$title  = lsm_field( 'philosophy_title' );
$accent = lsm_field( 'philosophy_title_accent' );
$lead   = lsm_field( 'philosophy_lead' );
$word   = lsm_field( 'philosophy_marquee_word' );

if ( ! lsm_filled( $figure ) && ! lsm_filled( $title ) && ! lsm_filled( $lead ) && ! lsm_filled( $word ) ) {
	return;
}
?>
<section data-sec="our-philosophy" class="sec-band-dark">
	<?php if ( lsm_filled( $figure ) ) : ?>
		<figure class="fig-abs sec-philosophy-figure"><?php lsm_image( $figure, 'large', array( 'class' => 'u-frame-image' ) ); ?></figure>
	<?php endif; ?>

	<div class="sec-philosophy-shell">
		<?php if ( lsm_filled( $title ) || lsm_filled( $accent ) || lsm_filled( $lead ) ) : ?>
			<div class="sec-philosophy-cols">
				<div class="sec-philosophy-gutter"></div>
				<div class="sec-philosophy-copy">
					<?php if ( lsm_filled( $title ) || lsm_filled( $accent ) ) : ?>
						<h2 class="xl sec-philosophy-title"><?php echo lsm_accent_heading( $title, $accent ); ?></h2>
					<?php endif; ?>
					<?php if ( lsm_filled( $lead ) ) : ?>
						<p class="sec-philosophy-lead"><?php echo esc_html( $lead ); ?></p>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( lsm_filled( $word ) ) : ?>
			<div class="mq-plain-band" data-mq-plain aria-label="<?php echo esc_attr( $word ); ?>"><div class="mq-track" data-mq-track><div class="mq-set" data-mq-set><span class="mq-plain-word"><?php echo esc_html( $word ); ?></span></div></div></div>
		<?php endif; ?>
	</div>
</section>

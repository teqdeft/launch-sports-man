<?php
/**
 * What We Do - the dark full-bleed line, "One Athlete. One Team. The Whole Picture."
 *
 * The three phrases are separate spans with different classes; the design
 * staggers them across the band, so they are three fields rather than one
 * string with markup in it.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$image = lsm_field( 'closing_line_image' );
$one   = lsm_field( 'closing_line_one' );
$two   = lsm_field( 'closing_line_two' );
$three = lsm_field( 'closing_line_three' );

if ( ! lsm_filled( $one ) && ! lsm_filled( $two ) && ! lsm_filled( $three ) && ! lsm_filled( $image ) ) {
	return;
}
?>
<section data-sec="closing-line" class="sec-band-dark">
	<?php
	if ( lsm_filled( $image ) ) {
		lsm_image( $image, 'lsm-wide', array( 'class' => 'cta-line-media', 'alt' => '', 'aria-hidden' => 'true' ) );
		?>
		<i class="cta-line-scrim" aria-hidden="true"></i>
		<?php
	}
	?>
	<div class="cta-line-shell">
		<?php if ( lsm_filled( $one ) || lsm_filled( $two ) || lsm_filled( $three ) ) : ?>
			<?php /* No whitespace between these spans: they are inline, so a newline here would render as a space between the phrases. */ ?>
			<h2 class="xl slot-kop cta-line-title"><?php if ( lsm_filled( $one ) ) : ?><span class="u-block"><?php echo esc_html( $one ); ?></span><?php endif; ?><?php if ( lsm_filled( $two ) ) : ?><span class="cta-line-second"><?php echo esc_html( $two ); ?></span><?php endif; ?><?php if ( lsm_filled( $three ) ) : ?><span class="cta-line-third"><?php echo esc_html( $three ); ?></span><?php endif; ?></h2>
		<?php endif; ?>
	</div>
</section>

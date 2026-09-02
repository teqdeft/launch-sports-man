<?php
/**
 * "The Launch Approach" - four numbered pillars.
 *
 * The four rows are NOT interchangeable. The design gives each its own type
 * scale and rhythm, so the classes vary by position:
 *
 *   row 1-3  .pillar-row        row 4  .pillar-row-04   (largest, hung right)
 *   claim    .pillar-claim-01 … -04                     (four distinct sizes)
 *   copy     .pillar-copy       row 3  .pillar-copy-flush
 *
 * They are derived from the row index rather than stored per row, so an editor
 * cannot break the composition by picking the wrong one. The layout is built
 * for exactly four; a fifth row would render with the default classes.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$title  = lsm_field( 'pillars_title' );
$label  = lsm_field( 'pillars_label' );
$rows   = lsm_field( 'pillars' );

if ( ! lsm_filled( $title ) && ! lsm_filled( $rows ) ) {
	return;
}
?>
<section data-sec="the-launch-approach" class="pillar-band">
	<div class="pillar-shell">
		<?php if ( lsm_filled( $title ) || lsm_filled( $label ) ) : ?>
			<div class="pillar-header">
				<?php if ( lsm_filled( $title ) ) : ?>
					<h2 class="xl pillar-title"><?php echo esc_html( $title ); ?></h2>
				<?php endif; ?>
				<?php if ( lsm_filled( $label ) ) : ?>
					<p class="u-section-label" data-label><?php echo esc_html( $label ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php
		if ( lsm_filled( $rows ) ) :
			foreach ( array_values( (array) $rows ) as $i => $row ) :
				$n     = $i + 1;
				$claim = isset( $row['claim'] ) ? $row['claim'] : '';
				$copy  = isset( $row['copy'] ) ? $row['copy'] : '';
				$plabel = isset( $row['label'] ) ? $row['label'] : '';

				if ( ! lsm_filled( $claim ) && ! lsm_filled( $copy ) && ! lsm_filled( $plabel ) ) {
					continue;
				}

				$row_class   = ( 4 === $n ) ? 'pillar-row-04' : 'pillar-row';
				$claim_class = 'xl pillar-claim-' . str_pad( (string) min( $n, 4 ), 2, '0', STR_PAD_LEFT );
				$copy_class  = ( 3 === $n ) ? 'pillar-copy-flush' : 'pillar-copy';
				?>
				<article class="<?php echo esc_attr( $row_class ); ?>" data-pillar>
					<?php if ( lsm_filled( $plabel ) ) : ?>
						<p class="pillar-label" data-label><?php echo esc_html( $plabel ); ?></p>
					<?php endif; ?>
					<?php if ( lsm_filled( $claim ) ) : ?>
						<?php /* Some claims break across two lines by design. */ ?>
						<h3 class="<?php echo esc_attr( $claim_class ); ?>"><?php echo implode( '<br>', array_map( 'esc_html', preg_split( '/\R/', $claim ) ) ); ?></h3>
					<?php endif; ?>
					<?php if ( lsm_filled( $copy ) ) : ?>
						<p class="<?php echo esc_attr( $copy_class ); ?>"><?php echo esc_html( $copy ); ?></p>
					<?php endif; ?>
				</article>
				<?php
			endforeach;
		endif;
		?>
	</div>
</section>

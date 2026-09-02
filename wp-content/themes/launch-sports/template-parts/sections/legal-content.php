<?php
/**
 * A legal page: a centred opening, then the policy beside its contents list.
 *
 * The contents column and the "Chapter 01" labels are derived from the <h2>s in
 * the editor's content - see inc/legal.php. An editor writes an ordinary
 * document and the numbering, the anchors and the list build themselves.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$label     = lsm_field( 'legal_label' );
$title     = lsm_field( 'legal_title', null, get_the_title() );
$accent    = lsm_field( 'legal_title_accent' );
$intro     = lsm_field( 'legal_intro' );
$updated   = lsm_field( 'legal_updated' );
$toc_title = lsm_field( 'legal_toc_title', null, __( 'Contents', 'launch-sports' ) );
$chapter   = lsm_field( 'legal_chapter_word', null, __( 'Chapter', 'launch-sports' ) );

/* An empty date line falls back to when the page was actually revised, which is
   a fact WordPress already knows and nobody has to remember to update. */
if ( ! lsm_filled( $updated ) ) {
	$updated = sprintf(
		/* translators: %s: date the page was last modified. */
		__( 'Last updated %s', 'launch-sports' ),
		get_the_modified_date( get_option( 'date_format' ) )
	);
}

$content = '';
while ( have_posts() ) {
	the_post();
	$content .= apply_filters( 'the_content', get_the_content() );
}

$parsed = lsm_legal_sections( $content, $chapter );
?>
<?php
/*
 * Two sections rather than one wrapper with two children, on purpose. The
 * responsive sheet clears the fixed mobile header with
 * `section:first-of-type > div { padding-top: 126px }`, which matches every
 * direct div of that section - so a single section gave the contents column
 * the header clearance too, and left 126px of nothing above it. Splitting them
 * means the rule lands on the opening only, which is what it was written for.
 */
?>
<section data-sec="legal-head" class="legal-band">
	<div class="legal-head">
		<?php if ( lsm_filled( $label ) ) : ?>
			<p class="legal-label" data-label><?php echo esc_html( $label ); ?></p>
		<?php endif; ?>

		<h1 class="legal-title"><?php echo lsm_accent_heading( $title, $accent, ' ' ); ?></h1>

		<?php if ( lsm_filled( $intro ) ) : ?>
			<p class="legal-intro"><?php echo esc_html( $intro ); ?></p>
		<?php endif; ?>

		<?php if ( lsm_filled( $updated ) ) : ?>
			<p class="legal-updated"><?php echo esc_html( $updated ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section data-sec="legal" class="legal-band">
	<div class="legal-shell">
		<?php if ( $parsed['toc'] ) : ?>
			<?php /* Sticky beside the policy on desktop; a plain list above it on phones. */ ?>
			<nav class="legal-toc" aria-label="<?php echo esc_attr( $toc_title ); ?>">
				<p class="legal-toc-title" data-label><?php echo esc_html( $toc_title ); ?></p>
				<ol class="legal-toc-list">
					<?php foreach ( $parsed['toc'] as $i => $entry ) : ?>
						<li>
							<a class="legal-toc-link" href="#<?php echo esc_attr( $entry['id'] ); ?>"<?php echo 0 === $i ? ' aria-current="true"' : ''; ?>>
								<span class="legal-toc-number"><?php echo esc_html( $entry['number'] ); ?></span>
								<?php echo esc_html( $entry['title'] ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ol>
			</nav>
		<?php endif; ?>

		<div class="legal-prose">
			<?php echo $parsed['html']; // phpcs:ignore WordPress.Security.EscapeOutput -- already through the_content filters. ?>
		</div>
	</div>
</section>

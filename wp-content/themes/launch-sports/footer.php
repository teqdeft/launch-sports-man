<?php
/**
 * Site footer and the close of the page shell.
 *
 * Values come from ACF Site settings, so the footer is
 * edited in one place and never per page.
 *
 * The giant [data-footmark] wordmark, the WebGL smoke and the "Pause motion"
 * control are all driven from motion.js, which finds them by attribute. Keep
 * data-footmark, data-credit-wrap, data-credit, data-motion-wrap and
 * data-motion-toggle exactly as they are - renaming any of them silently
 * disables that piece of the footer.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$footmark  = lsm_field( 'footer_wordmark', 'option' );
$copyright = lsm_field( 'footer_copyright', 'option' );
$credit    = lsm_field( 'footer_credit', 'option' );
?>

	<footer class="ftr-band">
		<?php if ( lsm_filled( $footmark ) ) : ?>
			<div data-footmark aria-hidden="true"><?php echo esc_html( $footmark ); ?></div>
		<?php endif; ?>

		<div class="ftr-shell">
			<div class="ftr-brand">
				<?php lsm_logo( 'footer', 'ftr-logo-mark' ); ?>

				<?php if ( lsm_filled( $copyright ) || lsm_filled( $credit ) ) : ?>
					<div data-credit-wrap>
						<?php if ( lsm_filled( $copyright ) ) : ?>
							<p class="ftr-credit" data-label><?php echo esc_html( $copyright ); ?></p>
						<?php endif; ?>
						<?php if ( lsm_filled( $credit ) ) : ?>
							<?php
							/*
							 * The design puts this on one line inside its own <p>, but a
							 * WYSIWYG field returns content already wrapped in <p> tags.
							 * Nesting them would be invalid, so unwrap a single outer
							 * paragraph and keep the inline markup (the heart <i> and the
							 * studio link) intact.
							 */
							$credit_inline = trim( $credit );
							if ( 1 === substr_count( $credit_inline, '<p>' ) && 0 === strpos( $credit_inline, '<p>' ) ) {
								$credit_inline = preg_replace( '#^<p>(.*)</p>$#s', '$1', $credit_inline );
							}
							?>
							<p class="ftr-credit" data-label data-credit><?php echo wp_kses_post( $credit_inline ); ?></p>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div data-motion-wrap>
				<?php if ( has_nav_menu( 'footer' ) ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Footer', 'launch-sports' ); ?>">
						<?php lsm_nav( 'footer', array( 'menu_class' => 'ftr-nav-list', 'items_wrap' => '<ul class="%2$s">%3$s</ul>' ) ); ?>
					</nav>
				<?php endif; ?>
				<?php /* WCAG 2.2.2. motion.js binds this and swaps the label; it must ship with the paused-off state. */ ?>
				<button type="button" data-motion-toggle aria-pressed="false"><?php esc_html_e( 'Pause motion', 'launch-sports' ); ?></button>
			</div>
		</div>
	</footer>
</div><?php // .lsm-shell ?>

<?php wp_footer(); ?>
</body>
</html>

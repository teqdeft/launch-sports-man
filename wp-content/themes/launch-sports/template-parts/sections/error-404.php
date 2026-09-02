<?php
/**
 * The 404 page's one section.
 *
 * There is no post behind a 404, so the wording is read from Site settings
 * rather than a page. Every part has a sensible default, so the page reads
 * properly even before anyone opens the settings screen.
 *
 * The shortcuts come from the primary menu rather than a second list to keep
 * in step - if a page is added to the navigation it appears here too.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$label  = lsm_field( 'error_label', 'option', '404' );
$title  = lsm_field( 'error_title', 'option', 'Page Not' );
$accent = lsm_field( 'error_title_accent', 'option', 'Found.' );
$lede   = lsm_field( 'error_lede', 'option', 'The page you were after has moved or never existed. Everything else is still where you left it.' );
$button = lsm_field( 'error_button', 'option' );

if ( ! is_array( $button ) ) {
	$button = array(
		'url'   => home_url( '/' ),
		'title' => 'Back to Home',
	);
}
?>
<section data-sec="error" class="err-band">
	<div class="err-shell">
		<?php if ( lsm_filled( $label ) ) : ?>
			<p class="err-label" data-label><?php echo esc_html( $label ); ?></p>
		<?php endif; ?>

		<?php if ( lsm_filled( $title ) || lsm_filled( $accent ) ) : ?>
			<h1 class="err-code"><?php echo lsm_accent_heading( $title, $accent, '<br>' ); ?></h1>
		<?php endif; ?>

		<?php if ( lsm_filled( $lede ) ) : ?>
			<p class="err-lede"><?php echo esc_html( $lede ); ?></p>
		<?php endif; ?>

		<?php lsm_button( $button, 'btn err-button' ); ?>

		<?php
		$shortcuts = wp_get_nav_menu_items( 'Primary' );
		if ( $shortcuts ) :
			?>
			<nav class="err-links" aria-label="<?php esc_attr_e( 'Elsewhere on this site', 'launch-sports' ); ?>">
				<?php foreach ( $shortcuts as $item ) : ?>
					<a class="err-link" href="<?php echo esc_url( $item->url ); ?>"><?php echo esc_html( $item->title ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</div>
</section>

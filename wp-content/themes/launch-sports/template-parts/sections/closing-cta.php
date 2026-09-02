<?php
/**
 * Closing call to action. Shared by every page; only the classes differ.
 *
 * [data-staartsectie] is required: from 1200px up the footer is pulled 450px
 * over this section, and that rule targets this attribute. Without it the
 * footer and this band stop overlapping and the page grows by 450px.
 *
 * @package LaunchSports
 *
 * @var array $args {
 *     @type string $sec          data-sec value ("closing" on Home, "closing-band" elsewhere).
 *     @type string $shell_class  Class for the inner wrapper.
 *     @type string $title_class  Class for the <h2>.
 *     @type string $button_class Class for the button.
 *     @type string $separator    ' ' or '<br>' between the heading and its gold part.
 * }
 */

defined( 'ABSPATH' ) || exit;

$sec          = isset( $args['sec'] ) ? $args['sec'] : 'closing';
$shell_class  = isset( $args['shell_class'] ) ? $args['shell_class'] : 'cta-shell';
$title_class  = isset( $args['title_class'] ) ? $args['title_class'] : 'cta-title-home';
$button_class = isset( $args['button_class'] ) ? $args['button_class'] : 'cta-button';
$separator    = isset( $args['separator'] ) ? $args['separator'] : ' ';

$title  = lsm_field( 'closing_title' );
$accent = lsm_field( 'closing_title_accent' );
$button = lsm_field( 'closing_button' );

if ( ! lsm_filled( $title ) && ! lsm_filled( $accent ) && ! is_array( $button ) ) {
	return;
}
?>
<section data-sec="<?php echo esc_attr( $sec ); ?>" class="cta-band" data-staartsectie>
	<div class="<?php echo esc_attr( $shell_class ); ?>">
		<?php if ( lsm_filled( $title ) || lsm_filled( $accent ) ) : ?>
			<h2 class="xl slot-kop <?php echo esc_attr( $title_class ); ?>"><?php echo lsm_accent_heading( $title, $accent, $separator ); ?></h2>
		<?php endif; ?>
		<?php lsm_button( $button, 'btn ' . $button_class ); ?>
	</div>
</section>

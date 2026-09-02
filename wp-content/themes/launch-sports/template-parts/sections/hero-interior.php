<?php
/**
 * Interior page hero - About, What We Do and Let's Talk share this shape.
 *
 * The classes differ per page (hero-title-about vs hero-title-what-we-do, and
 * so on) so they are passed in rather than branched on here.
 *
 * data-hero-kop is read by the motion layer; keep it on the heading.
 *
 * @package LaunchSports
 *
 * @var array $args {
 *     @type string $band_class   Class for the <section>.
 *     @type string $shell_class  Class for the inner wrapper.
 *     @type string $figure_class Class for the <figure>.
 *     @type string $title_class  Class for the <h1>.
 *     @type string $lede_class   Class for the standfirst.
 *     @type bool   $title_marker Emit data-hero-kop on the heading. Let's Talk does not.
 * }
 */

defined( 'ABSPATH' ) || exit;

$band_class   = isset( $args['band_class'] ) ? $args['band_class'] : 'hero-band-interior';
$shell_class  = isset( $args['shell_class'] ) ? $args['shell_class'] : 'hero-shell-interior';
$lede_class   = isset( $args['lede_class'] ) ? $args['lede_class'] : 'hero-lede-interior';
$title_marker = ! isset( $args['title_marker'] ) || $args['title_marker'];
$figure_class = isset( $args['figure_class'] ) ? $args['figure_class'] : '';
$title_class  = isset( $args["title_class"] ) ? $args["title_class"] : "";
/* What We Do sizes its hero figure through [data-hero-fig]; About does not have it. */
$figure_attr  = ! empty( $args["figure_attr"] ) ? " " . $args["figure_attr"] : "";

$image  = lsm_field( 'hero_image' );
$title  = lsm_field( 'hero_title' );
$accent = lsm_field( 'hero_title_accent' );
$lede   = lsm_field( 'hero_lede' );

if ( ! lsm_filled( $image ) && ! lsm_filled( $title ) && ! lsm_filled( $accent ) && ! lsm_filled( $lede ) ) {
	return;
}
?>
<section data-sec="hero" class="<?php echo esc_attr( $band_class ); ?>">
	<div class="<?php echo esc_attr( $shell_class ); ?>">
		<?php if ( lsm_filled( $image ) && $figure_class ) : ?>
			<figure<?php echo $figure_attr; // phpcs:ignore WordPress.Security.EscapeOutput -- fixed attribute name from the template. ?> class="fig-abs <?php echo esc_attr( $figure_class ); ?>">
				<?php lsm_image( $image, 'large', array( 'class' => 'u-frame-image', 'loading' => 'eager' ) ); ?>
			</figure>
		<?php endif; ?>

		<?php if ( lsm_filled( $title ) || lsm_filled( $accent ) ) : ?>
			<h1 class="<?php echo esc_attr( $title_class ); ?>"<?php echo $title_marker ? ' data-hero-kop' : ''; ?>><?php echo lsm_accent_heading( $title, $accent ); ?></h1>
		<?php endif; ?>

		<?php if ( lsm_filled( $lede ) ) : ?>
			<p class="<?php echo esc_attr( $lede_class ); ?>"><?php echo esc_html( $lede ); ?></p>
		<?php endif; ?>
	</div>
</section>

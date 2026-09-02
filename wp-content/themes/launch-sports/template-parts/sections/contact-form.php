<?php
/**
 * Let's Talk - the enquiry form and its side photograph.
 *
 * The form itself is a Contact Form 7 form, chosen on the page. CF7 owns the
 * fields, the validation, the messages and the mail; this template owns the
 * section around it. inc/contact.php normalises CF7's markup back to the
 * design's, so the classes below still describe what is rendered.
 *
 * Contracts the motion layer depends on, all required:
 *   data-sec="form-what-happens-next"  motion.js reads this to decide the page
 *                                      is Let's Talk. Renaming it silently
 *                                      turns every animation on the page off.
 *   the <form>'s direct children       animated as rows with a 0.07s stagger
 *   .field                             each one's underline draws left to right
 *   fieldset label                     the four role options land one by one
 *   button                             the submit
 *   [data-side-photo]                  the photograph
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$photo = lsm_field( 'form_side_photo' );
$form  = lsm_contact_form_shortcode();

if ( '' === $form && ! lsm_filled( $photo ) ) {
	return;
}
?>
<?php /* id="form" so a link can point straight at the form rather than the top of the hero. */ ?>
<section id="form" data-sec="form-what-happens-next" class="form-band">
<div class="form-layout">
	<?php
	/*
	 * CF7 wraps its form in a div of its own. That div would otherwise become a
	 * flex item of .form-layout in place of the form, so it is told to pass
	 * layout through to the form inside it - see .wpcf7 in desktop.css.
	 */
	echo do_shortcode( $form ); // phpcs:ignore WordPress.Security.EscapeOutput -- shortcode output is CF7's own escaped markup.
	?>

	<?php if ( lsm_filled( $photo ) ) : ?>
		<figure class="form-side-photo" data-side-photo><?php lsm_image( $photo, 'large', array( 'class' => 'form-side-photo-image' ) ); ?></figure>
	<?php endif; ?>
</div>
</section>

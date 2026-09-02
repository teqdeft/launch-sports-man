<?php
/**
 * Site header and the opening of the page shell.
 *
 * Two things here are load-bearing and easy to break:
 *
 * 1. The wrapper is .lsm-shell, not .page. WordPress's body_class() emits a
 *    literal "page" class on every Page request, and the stylesheets carry
 *    ~219 ".lsm-shell …" selectors. If this wrapper were still .page, all of
 *    them would also match <body> and the layout would shift.
 *
 * 2. The header markup differs between the home page and the three interior
 *    pages: home overlays a transparent header on the hero, the others sit in
 *    a solid black band. That is the only difference, so it is one class here
 *    rather than two templates.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php /* iOS turns strings like "01 / 04" into blue date links. Only visible on iOS, never in desktop Chrome. */ ?>
	<meta name="format-detection" content="telephone=no,date=no,address=no,email=no">
	<link rel="icon" href="<?php echo esc_url( lsm_asset( 'images/launch-logo-mark.svg' ) ); ?>">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="lsm-shell <?php echo is_front_page() ? 'sec-page-shell-home' : 'sec-page-shell'; ?>">
	<div class="u-grain" aria-hidden="true"></div>

	<?php
	/*
	 * Home overlays a transparent header on top of the hero photo; the three
	 * interior pages sit in a solid black band. These are the exact classes the
	 * static build uses - .hdr-overlay is styled in desktop.css, .u-band-black
	 * is the shared black-section utility. Do not invent a third.
	 */
	?>
	<header class="<?php echo is_front_page() ? 'hdr-overlay' : 'u-band-black'; ?>">
		<div class="hdr-inner">
			<a class="u-block" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( lsm_field( 'header_logo_label', 'option', get_bloginfo( 'name' ) . ', home' ) ); ?>"><?php lsm_logo( 'mark', 'hdr-logo-mark' ); ?></a>
			<div class="hdr-actions">
				<button class="burger" type="button" aria-label="<?php esc_attr_e( 'Menu', 'launch-sports' ); ?>" aria-expanded="false" aria-controls="menu"><i></i><i></i><i></i></button>
				<?php if ( has_nav_menu( 'primary' ) ) : ?>
					<nav aria-label="<?php esc_attr_e( 'Primary', 'launch-sports' ); ?>">
						<?php lsm_nav( 'primary', array( 'menu_class' => 'hdr-nav-list', 'items_wrap' => '<ul class="%2$s">%3$s</ul>' ) ); ?>
					</nav>
				<?php endif; ?>
				<?php
				$header_cta = lsm_field( 'header_cta', 'option' );
				if ( is_array( $header_cta ) ) {
					lsm_button( $header_cta, 'btn hdr-cta' );
				}
				?>
			</div>
		</div>
	</header>

	<?php if ( has_nav_menu( 'mobile' ) ) : ?>
		<nav id="menu" data-open="false" aria-label="<?php esc_attr_e( 'Mobile', 'launch-sports' ); ?>">
			<?php lsm_nav( 'mobile', array( 'walker' => new LSM_Mobile_Menu_Walker(), 'items_wrap' => '%3$s' ) ); ?>
		</nav>
	<?php endif; ?>

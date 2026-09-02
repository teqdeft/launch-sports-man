<?php
/**
 * About - the horizontally scrolled team panels.
 *
 * Structure the motion layer depends on, all of which must survive:
 *   [data-hscroll]  the section; also how motion.js identifies this page
 *   [data-viewport] the clipping window
 *   [data-track]    the strip that is translated
 *   [data-panel]    each card
 *   [data-count]    "01 / 04", updated as you scroll
 *   [data-progress] the fill bar
 *   [data-tail]     the small mark at the foot of each panel
 *
 * The panels are NOT uniform, and that is deliberate in the design:
 *   - panel 1 uses .panel-shell-lead, which is wider than the rest
 *   - the theme alternates dark, light, dark, light, and the number, bio and
 *     tail classes follow that theme
 *   - a panel without a photograph falls back to a drawn placeholder
 * All of it is derived from position here, so an editor cannot pick the wrong
 * variant. Reordering the team reorders the themes with them.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$chosen = lsm_field( 'team_members' );
$tail   = lsm_field( 'panel_tail' );
$hint   = lsm_field( 'panel_hint' );

$members = array();
if ( lsm_filled( $chosen ) ) {
	// Walk the chosen ids in order. Never assign to $post here: template parts
	// load in global scope and that would break every later section.
	foreach ( (array) $chosen as $mid ) {
		$member_post = get_post( (int) $mid );
		if ( $member_post && 'publish' === $member_post->post_status ) {
			$members[] = $member_post;
		}
	}
} else {
	$members = lsm_get_collection( 'lsm_member' );
}

if ( ! $members ) {
	return;
}

$total = count( $members );
$pad   = static function ( $n ) {
	return str_pad( (string) $n, 2, '0', STR_PAD_LEFT );
};
?>
<section data-sec="profiles" class="u-band-black" data-hscroll>
	<div class="panel-viewport" data-viewport>
		<div class="panel-track" data-track>
			<?php
			foreach ( $members as $i => $member ) :
				$n     = $i + 1;
				$theme = ( 0 === $i % 2 ) ? 'dark' : 'light';   // 1 dark, 2 light, 3 dark, 4 light
				$shell = ( 0 === $i ) ? 'panel-shell-lead' : 'panel-shell-' . $theme;

				$name    = get_the_title( $member );
				$role    = lsm_field( 'member_role', $member->ID );
				$bio     = lsm_field( 'member_bio', $member->ID );
				$caption = lsm_field( 'member_placeholder_caption', $member->ID );
				$mark    = lsm_field( 'member_placeholder_mark', $member->ID );
				$photo   = get_post_thumbnail_id( $member->ID );
				?>
				<article class="<?php echo esc_attr( $shell ); ?>" data-panel>
					<?php if ( $photo ) : ?>
						<figure class="panel-media">
							<?php lsm_image( $photo, 'lsm-panel', array( 'class' => 'u-frame-image', 'alt' => $name ) ); ?>
						</figure>
					<?php else : ?>
						<?php /* No photograph yet: the design draws a framed placeholder instead. */ ?>
						<figure class="panel-placeholder-<?php echo esc_attr( $theme ); ?>">
							<?php if ( lsm_filled( $caption ) ) : ?>
								<figcaption class="panel-caption-<?php echo esc_attr( $theme ); ?>" data-label><?php echo esc_html( $caption ); ?></figcaption>
							<?php endif; ?>
							<?php if ( $mark ) : ?>
								<div class="panel-placeholder-mark"></div>
							<?php endif; ?>
						</figure>
					<?php endif; ?>

					<div class="panel-copy">
						<p class="panel-number-<?php echo esc_attr( $theme ); ?>" data-label><?php echo esc_html( $pad( $n ) . ' / ' . $pad( $total ) ); ?></p>
						<?php if ( lsm_filled( $name ) ) : ?>
							<h2 class="xl panel-name"><?php echo esc_html( $name ); ?></h2>
						<?php endif; ?>
						<?php if ( lsm_filled( $role ) ) : ?>
							<p class="panel-role" data-label><?php echo esc_html( $role ); ?></p>
						<?php endif; ?>
						<?php if ( lsm_filled( $bio ) ) : ?>
							<p class="panel-bio-<?php echo esc_attr( $theme ); ?>"><?php echo esc_html( $bio ); ?></p>
						<?php endif; ?>
						<?php
						/*
						 * The mark at the foot of the panel. The wording is shared across
						 * every panel (About -> Team -> Panel footer mark); the link is set
						 * per person on the Team entry, so one member can point somewhere
						 * and the rest stay plain text.
						 *
						 * The <div> and its data-tail attribute stay put either way: the
						 * motion layer finds the mark by that attribute, and the styling
						 * hangs off .panel-tail-*. The anchor goes inside it, where the
						 * base reset already gives it inherited colour and no underline.
						 */
						$tail_link = lsm_field( 'member_tail_link', $member->ID );
						$tail_text = lsm_filled( $tail )
							? $tail
							: ( is_array( $tail_link ) && ! empty( $tail_link['title'] ) ? $tail_link['title'] : '' );

						if ( '' !== $tail_text ) :
							?>
							<div class="panel-tail-<?php echo esc_attr( $theme ); ?>" data-label data-tail><?php
								if ( is_array( $tail_link ) && ! empty( $tail_link['url'] ) ) {
									printf(
										'<a href="%s"%s>%s</a>',
										esc_url( $tail_link['url'] ),
										! empty( $tail_link['target'] ) ? ' target="' . esc_attr( $tail_link['target'] ) . '" rel="noopener"' : '',
										esc_html( $tail_text )
									);
								} else {
									echo esc_html( $tail_text );
								}
							?></div>
						<?php endif; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>

		<div class="panel-progress-bar">
			<?php /* motion.js rewrites this as you scroll; it ships showing the first panel. */ ?>
			<span class="panel-progress-count" data-label data-count><?php echo esc_html( $pad( 1 ) . ' / ' . $pad( $total ) ); ?></span>
			<span class="panel-progress-track">
				<i class="panel-progress-fill" data-progress></i>
			</span>
			<?php if ( lsm_filled( $hint ) ) : ?>
				<span class="panel-progress-hint" data-label><?php echo esc_html( $hint ); ?></span>
			<?php endif; ?>
		</div>
	</div>
</section>

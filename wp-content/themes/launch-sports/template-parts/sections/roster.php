<?php
/**
 * "Athletes We've Helped" - the player roster.
 *
 * Which players appear, and in what order, is chosen on the Home page under
 * the Players tab. Leaving that empty falls back to every published player, so
 * the section never silently empties itself because nobody made a selection.
 *
 * The counter beside the heading ("Roster &middot; 06") is derived from the
 * number of cards actually rendered rather than typed in, so it cannot drift
 * out of step with the roster.
 *
 * One card in the design wraps its media in a link to a clip; that is optional
 * per player, and a player without one renders the media block on its own,
 * exactly as the other five do.
 *
 * id="our-players" is the anchor the header and footer menus point at - it
 * must stay on this section.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

$title  = lsm_field( 'roster_title' );
$accent = lsm_field( 'roster_title_accent' );
$chosen = lsm_field( 'roster_players' );

$players = array();
if ( lsm_filled( $chosen ) ) {
	/*
	 * Walk the chosen ids in order; post__in on its own would not preserve it.
	 *
	 * Note the variable name. A template part is loaded in global scope, so
	 * assigning to $post here would clobber the global one and every later
	 * section would read its ACF fields off the last player instead of the
	 * page - which silently deleted the closing call-to-action.
	 */
	foreach ( (array) $chosen as $pid ) {
		$player_post = get_post( (int) $pid );
		if ( $player_post && 'publish' === $player_post->post_status ) {
			$players[] = $player_post;
		}
	}
} else {
	$players = lsm_get_collection( 'lsm_player' );
}

if ( ! lsm_filled( $title ) && ! lsm_filled( $accent ) && ! $players ) {
	return;
}

$count = count( $players );
?>
<section data-sec="our-players" class="roster-band" id="our-players">
	<div class="roster-shell">
		<?php if ( lsm_filled( $title ) || lsm_filled( $accent ) || $count ) : ?>
			<div class="roster-header">
				<?php if ( lsm_filled( $title ) || lsm_filled( $accent ) ) : ?>
					<h2 class="xl roster-title"><?php echo lsm_accent_heading( $title, $accent ); ?></h2>
				<?php endif; ?>
				<?php if ( $count ) : ?>
					<?php
					/*
					 * Counted, not typed. Zero padded to two digits to match the
					 * design's "Roster · 06".
					 */
					?>
					<p class="u-section-label" data-label><?php
						echo esc_html(
							sprintf(
								/* translators: %s: number of players, zero padded to two digits. */
								__( 'Roster · %s', 'launch-sports' ),
								str_pad( (string) $count, 2, '0', STR_PAD_LEFT )
							)
						);
					?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $players ) : ?>
			<?php /* fixed card width + wrap + flex-start: a last row of three reads as deliberate */ ?>
			<ul class="roster roster-grid">
				<?php
				foreach ( $players as $player ) :
					$name   = get_the_title( $player );
					$school = lsm_field( 'player_school', $player->ID );
					$clip   = lsm_field( 'player_clip', $player->ID );
					$thumb  = get_post_thumbnail_id( $player->ID );
					$alt    = $name . ( lsm_filled( $school ) ? ', ' . $school : '' );
					?>
					<li class="roster-card">
						<?php
						$media = static function () use ( $thumb, $alt ) {
							if ( ! $thumb ) {
								return;
							}
							echo '<div class="slot roster-card-media">';
							lsm_image( $thumb, 'lsm-roster', array( 'class' => 'roster-card-image', 'alt' => $alt ) );
							echo '</div>';
						};

						if ( is_array( $clip ) && ! empty( $clip['url'] ) ) :
							?>
							<a class="u-block" href="<?php echo esc_url( $clip['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( sprintf( 'Watch the clip of %s', $name ) ); ?>">
								<?php $media(); ?>
							</a>
							<?php
						else :
							$media();
						endif;
						?>
						<?php if ( lsm_filled( $name ) ) : ?>
							<p class="roster-card-name"><?php echo esc_html( $name ); ?></p>
						<?php endif; ?>
						<?php if ( lsm_filled( $school ) ) : ?>
							<p class="roster-card-meta" data-label><?php echo esc_html( $school ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>

<?php
/**
 * ACF field group: Home.
 *
 * Registered in PHP rather than hand-authored JSON so the field keys are
 * guaranteed to match what the templates read, and so the group ships with the
 * theme instead of living only in the database.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Small helpers to keep the field array readable.
 */
function lsm_f_text( $key, $label, $name, $instructions = '' ) {
	return array(
		'key'          => $key,
		'label'        => $label,
		'name'         => $name,
		'type'         => 'text',
		'instructions' => $instructions,
	);
}

function lsm_f_area( $key, $label, $name, $rows = 3, $instructions = '' ) {
	return array(
		'key'          => $key,
		'label'        => $label,
		'name'         => $name,
		'type'         => 'textarea',
		'rows'         => $rows,
		'new_lines'    => '',
		'instructions' => $instructions,
	);
}

function lsm_f_image( $key, $label, $name, $instructions = '' ) {
	return array(
		'key'           => $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'image',
		'return_format' => 'array',
		'preview_size'  => 'medium',
		'instructions'  => $instructions,
	);
}

function lsm_f_link( $key, $label, $name, $instructions = 'Leave empty to hide the button.' ) {
	return array(
		'key'           => $key,
		'label'         => $label,
		'name'          => $name,
		'type'          => 'link',
		'return_format' => 'array',
		'instructions'  => $instructions,
	);
}

function lsm_f_tab( $key, $label ) {
	return array(
		'key'   => $key,
		'label' => $label,
		'type'  => 'tab',
	);
}

/**
 * Register the Home field group.
 */
function lsm_acf_home_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'             => 'group_lsm_home',
			'title'           => 'Home page',
			'menu_order'      => 0,
			'position'        => 'normal',
			'style'           => 'default',
			'label_placement' => 'top',
			'active'          => true,
			'description'     => 'Front page content. Leave a field empty to hide that element - an empty section renders no markup at all.',
			'location'        => array(
				array(
					array(
						'param'    => 'page_type',
						'operator' => '==',
						'value'    => 'front_page',
					),
				),
			),
			'fields'          => array(

				// ---------------- HERO ----------------
				lsm_f_tab( 'field_lsm_home_tab_hero', 'Hero' ),
				lsm_f_image( 'field_lsm_hero_image', 'Background photograph', 'hero_image', 'Full-bleed behind the headline. Landscape, at least 1920px wide. Cropped to fill, so keep the subject away from the edges.' ),
				lsm_f_area( 'field_lsm_hero_title', 'Headline', 'hero_title', 2, 'Each new line becomes a line break. Two short lines is what the layout is built for.' ),
				lsm_f_text( 'field_lsm_hero_subhead', 'Gold subheading', 'hero_subhead' ),
				lsm_f_area( 'field_lsm_hero_intro', 'Lead paragraph', 'hero_intro', 3 ),
				lsm_f_area( 'field_lsm_hero_note', 'Supporting paragraph', 'hero_note', 3 ),
				lsm_f_link( 'field_lsm_hero_cta', 'Button', 'hero_cta' ),

				// ---------------- RUNNING BAND ----------------
				lsm_f_tab( 'field_lsm_home_tab_marquee', 'Running band' ),
				array(
					'key'          => 'field_lsm_marquee_items',
					'label'        => 'Band items',
					'name'         => 'marquee_items',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Add item',
					'instructions' => 'The scrolling gold band under the hero. Items repeat automatically to fill the screen; four or five short lines works best. Empty hides the band.',
					'sub_fields'   => array(
						lsm_f_text( 'field_lsm_marquee_item', 'Text', 'item' ),
					),
				),

				// ---------------- OPPORTUNITY ----------------
				lsm_f_tab( 'field_lsm_home_tab_why', 'Opportunity' ),
				lsm_f_image( 'field_lsm_why_ball', 'Basketball cut-out', 'why_ball_image', 'Transparent PNG, sits at the right edge of the dark band.' ),
				lsm_f_text( 'field_lsm_why_heading', 'Heading', 'why_heading' ),
				lsm_f_area( 'field_lsm_why_lead', 'Lead paragraph', 'why_lead', 2 ),
				lsm_f_area( 'field_lsm_why_body', 'Body paragraph', 'why_body', 4 ),
				array(
					'key'           => 'field_lsm_why_motif',
					'label'         => 'Show the animated diagram',
					'name'          => 'why_show_motif',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
					'instructions'  => 'The drawn timeline between the copy and the statement, where the pressures land on one line one after another. Drawn in code - nothing to upload.',
				),
				array(
					'key'               => 'field_lsm_motif_caption',
					'label'             => 'Diagram caption',
					'name'              => 'motif_caption',
					'type'              => 'text',
					'placeholder'       => 'ARRIVING AT ONCE',
					'instructions'      => 'Small mono label at the bottom left of the diagram. The counter on the right counts itself.',
					'conditional_logic' => array(
						array(
							array( 'field' => 'field_lsm_why_motif', 'operator' => '==', 'value' => '1' ),
						),
					),
				),
				array(
					'key'               => 'field_lsm_motif_words',
					'label'             => 'Diagram words',
					'name'              => 'motif_words',
					'type'              => 'repeater',
					'layout'            => 'table',
					'button_label'      => 'Add word',
					'instructions'      => 'The words that drop onto the line, in order. The last three are highlighted in gold, '
						. 'and the counter on the right ("12 / 12") follows the number of rows, so there is nothing to keep in sync. '
						. 'Leave the position blank to space them evenly; set it only where a word needs nudging.',
					'conditional_logic' => array(
						array(
							array( 'field' => 'field_lsm_why_motif', 'operator' => '==', 'value' => '1' ),
						),
					),
					'sub_fields'        => array(
						array(
							'key'   => 'field_lsm_motif_word',
							'label' => 'Word',
							'name'  => 'word',
							'type'  => 'text',
						),
						array(
							'key'           => 'field_lsm_motif_word_x',
							'label'         => 'Position (%)',
							'name'          => 'position',
							'type'          => 'number',
							'min'           => 0,
							'max'           => 100,
							'append'        => '%',
							'instructions'  => 'Optional. Distance along the line, 0 at the left, 100 at the right.',
						),
					),
				),
				lsm_f_area( 'field_lsm_why_statement', 'Statement', 'why_statement', 2, 'Large type. New lines become line breaks.' ),
				lsm_f_area( 'field_lsm_why_statement_accent', 'Statement - gold part', 'why_statement_accent', 2 ),
				lsm_f_area( 'field_lsm_why_note', 'Closing note', 'why_note', 3 ),
				lsm_f_image( 'field_lsm_why_figure', 'Wide photograph', 'why_figure', 'Full-bleed band closing the section.' ),

				// ---------------- THE LAUNCH APPROACH ----------------
				lsm_f_tab( 'field_lsm_home_tab_pillars', 'The Launch Approach' ),
				lsm_f_text( 'field_lsm_pillars_title', 'Heading', 'pillars_title' ),
				lsm_f_text( 'field_lsm_pillars_label', 'Small label', 'pillars_label' ),
				array(
					'key'          => 'field_lsm_pillars',
					'label'        => 'Pillars',
					'name'         => 'pillars',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add pillar',
					'max'          => 4,
					'instructions' => 'The layout is composed for exactly four, each with its own type size. Reordering changes which size a pillar gets.',
					'sub_fields'   => array(
						lsm_f_text( 'field_lsm_pillar_label', 'Label', 'label', 'For example: 01 &middot; The Player' ),
						lsm_f_area( 'field_lsm_pillar_claim', 'Headline', 'claim', 2, 'New lines become line breaks.' ),
						lsm_f_area( 'field_lsm_pillar_copy', 'Paragraph', 'copy', 4 ),
					),
				),

				// ---------------- PHILOSOPHY ----------------
				lsm_f_tab( 'field_lsm_home_tab_phil', 'Philosophy' ),
				lsm_f_image( 'field_lsm_phil_image', 'Square photograph', 'philosophy_image', 'Shown 1:1, bleeding off the left edge.' ),
				lsm_f_area( 'field_lsm_phil_title', 'Heading', 'philosophy_title', 2 ),
				lsm_f_text( 'field_lsm_phil_title_accent', 'Heading - gold part', 'philosophy_title_accent' ),
				lsm_f_area( 'field_lsm_phil_lead', 'Paragraph', 'philosophy_lead', 4 ),
				lsm_f_text( 'field_lsm_phil_word', 'Scrolling word', 'philosophy_marquee_word', 'One short phrase, repeated slowly across the band.' ),

				// ---------------- PLAYERS ----------------
				lsm_f_tab( 'field_lsm_home_tab_roster', 'Players' ),
				lsm_f_text( 'field_lsm_roster_title', 'Heading', 'roster_title' ),
				lsm_f_text( 'field_lsm_roster_title_accent', 'Heading - gold part', 'roster_title_accent' ),
				array(
					'key'           => 'field_lsm_roster_players',
					'label'         => 'Players shown',
					'name'          => 'roster_players',
					'type'          => 'relationship',
					'post_type'     => array( 'lsm_player' ),
					'filters'       => array( 'search' ),
					'return_format' => 'id',
					'min'           => 0,
					'max'           => 0,
					'instructions'  => 'Pick the players to show, and drag them into the order you want. '
						. 'The counter beside the heading updates itself from how many you pick, so there is nothing to keep in sync. '
						. 'Leave this empty to show every published player. Add or edit players under <strong>Players</strong> in the sidebar.',
				),

				// ---------------- CLOSING ----------------
				lsm_f_tab( 'field_lsm_home_tab_closing', 'Closing' ),
				lsm_f_area( 'field_lsm_closing_title', 'Heading', 'closing_title', 2 ),
				lsm_f_text( 'field_lsm_closing_title_accent', 'Heading - gold part', 'closing_title_accent' ),
				lsm_f_link( 'field_lsm_closing_button', 'Button', 'closing_button' ),
			),
		)
	);

	// ---------------- PLAYER (CPT) ----------------
	acf_add_local_field_group(
		array(
			'key'             => 'group_lsm_player',
			'title'           => 'Player details',
			'position'        => 'normal',
			'label_placement' => 'top',
			'active'          => true,
			'description'     => 'The player name is the post title; the photograph is the featured image (3:4).',
			'location'        => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'lsm_player',
					),
				),
			),
			'fields'          => array(
				lsm_f_text( 'field_lsm_player_school', 'School / team', 'player_school', 'Shown under the name, in mono caps.' ),
				lsm_f_link( 'field_lsm_player_clip', 'Clip link', 'player_clip', 'Optional. If set, the card photograph becomes a link that opens in a new tab.' ),
			),
		)
	);
}
add_action( 'acf/init', 'lsm_acf_home_fields' );

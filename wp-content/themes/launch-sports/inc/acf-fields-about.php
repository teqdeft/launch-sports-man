<?php
/**
 * ACF field groups: About Launch, and the Team member post type.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the About and Team member groups.
 */
function lsm_acf_about_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'             => 'group_lsm_about',
			'title'           => 'About page',
			'menu_order'      => 0,
			'position'        => 'normal',
			'style'           => 'default',
			'label_placement' => 'top',
			'active'          => true,
			'description'     => 'Leave a field empty to hide that element - an empty section renders no markup at all.',
			'location'        => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/page-about.php',
					),
				),
			),
			'fields'          => array(

				// ---------------- HERO ----------------
				lsm_f_tab( 'field_lsm_about_tab_hero', 'Hero' ),
				lsm_f_image( 'field_lsm_about_hero_image', 'Photograph', 'hero_image', 'Sits to the side of the heading, shown 4:5.' ),
				lsm_f_area( 'field_lsm_about_hero_title', 'Heading', 'hero_title', 2 ),
				lsm_f_text( 'field_lsm_about_hero_accent', 'Heading - gold part', 'hero_title_accent' ),
				lsm_f_area( 'field_lsm_about_hero_lede', 'Standfirst', 'hero_lede', 2 ),

				// ---------------- TEAM ----------------
				lsm_f_tab( 'field_lsm_about_tab_team', 'Team' ),
				array(
					'key'           => 'field_lsm_team_members',
					'label'         => 'Team members shown',
					'name'          => 'team_members',
					'type'          => 'relationship',
					'post_type'     => array( 'lsm_member' ),
					'filters'       => array( 'search' ),
					'return_format' => 'id',
					'instructions'  => 'Pick the people to show, and drag them into order. The panels scroll sideways. '
						. 'The "01 / 04" counters and the progress bar count themselves from this list. '
						. 'The first panel is the wide one, and the light/dark rhythm follows the order, so reordering changes which panel is which. '
						. 'Leave empty to show everyone. Add or edit people under <strong>Team</strong> in the sidebar.',
				),
				lsm_f_text( 'field_lsm_panel_tail', 'Panel footer mark', 'panel_tail', 'The small line at the foot of every panel.' ),
				lsm_f_text( 'field_lsm_panel_hint', 'Scroll hint', 'panel_hint', 'Sits to the right of the progress bar, e.g. "Keep scrolling".' ),

				// ---------------- CLOSING ----------------
				lsm_f_tab( 'field_lsm_about_tab_closing', 'Closing' ),
				lsm_f_area( 'field_lsm_about_closing_title', 'Heading', 'closing_title', 2 ),
				lsm_f_text( 'field_lsm_about_closing_accent', 'Heading - gold part', 'closing_title_accent' ),
				lsm_f_link( 'field_lsm_about_closing_button', 'Button', 'closing_button' ),
			),
		)
	);

	// ---------------- TEAM MEMBER (CPT) ----------------
	acf_add_local_field_group(
		array(
			'key'             => 'group_lsm_member',
			'title'           => 'Team member details',
			'position'        => 'normal',
			'label_placement' => 'top',
			'active'          => true,
			'description'     => 'The name is the post title; the photograph is the featured image, shown 4:5.',
			'location'        => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'lsm_member',
					),
				),
			),
			'fields'          => array(
				lsm_f_text( 'field_lsm_member_role', 'Role', 'member_role', 'Shown under the name in mono caps, e.g. "Founder".' ),
				lsm_f_area( 'field_lsm_member_bio', 'Biography', 'member_bio', 5 ),
				array(
					'key'           => 'field_lsm_member_tail_link',
					'label'         => 'Footer mark link',
					'name'          => 'member_tail_link',
					'type'          => 'link',
					'return_format' => 'array',
					'instructions'  => 'Optional. Turns the small mark at the foot of this panel (&ldquo;Launch Sports&rdquo;) into a link, '
						. 'for this person only. The wording comes from <strong>About &rarr; Team &rarr; Panel footer mark</strong>, '
						. 'so it stays consistent across panels; only the destination is set here. '
						. 'Leave empty and the mark stays plain text, exactly as it is now.',
				),

				array(
					'key'       => 'field_lsm_member_ph_msg',
					'label'     => 'When there is no photograph',
					'type'      => 'message',
					'message'   => 'Leave the featured image empty and the design draws a framed placeholder instead. '
						. 'The two fields below only apply in that case.',
					'esc_html'  => 0,
					'new_lines' => 'wpautop',
				),
				lsm_f_text(
					'field_lsm_member_ph_caption',
					'Placeholder caption',
					'member_placeholder_caption',
					'Small label inside the placeholder frame. Currently used for production notes such as "FIRM MARK &middot; NAME TBD &middot; 4:5" - visitors can read it, so clear it once a real photograph is in place.'
				),
				array(
					'key'           => 'field_lsm_member_ph_mark',
					'label'         => 'Show the diamond mark',
					'name'          => 'member_placeholder_mark',
					'type'          => 'true_false',
					'ui'            => 1,
					'default_value' => 1,
					'instructions'  => 'The rotated square drawn inside the placeholder. In the approved design the Legal Partner panel has it and the Ross Schraeder panel does not, so it is switchable per person rather than assumed.',
				),
			),
		)
	);
}
add_action( 'acf/init', 'lsm_acf_about_fields' );

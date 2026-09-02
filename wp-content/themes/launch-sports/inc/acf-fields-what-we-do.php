<?php
/**
 * ACF field groups: What We Do, plus the Process step and Register stage types.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the What We Do groups.
 */
function lsm_acf_what_we_do_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'             => 'group_lsm_wwd',
			'title'           => 'What We Do page',
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
						'value'    => 'templates/page-what-we-do.php',
					),
				),
			),
			'fields'          => array(

				// ---------------- HERO ----------------
				lsm_f_tab( 'field_lsm_wwd_tab_hero', 'Hero' ),
				lsm_f_image( 'field_lsm_wwd_hero_image', 'Photograph', 'hero_image', 'Sits to the side of the heading.' ),
				lsm_f_area( 'field_lsm_wwd_hero_title', 'Heading', 'hero_title', 2 ),
				lsm_f_text( 'field_lsm_wwd_hero_accent', 'Heading - gold part', 'hero_title_accent' ),
				lsm_f_area( 'field_lsm_wwd_hero_lede', 'Standfirst', 'hero_lede', 2 ),

				// ---------------- RUNNING BAND ----------------
				lsm_f_tab( 'field_lsm_wwd_tab_band', 'Running band' ),
				array(
					'key'          => 'field_lsm_wwd_marquee',
					'label'        => 'Band items',
					'name'         => 'marquee_items',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Add item',
					'instructions' => 'The gold band under the hero. On this page it carries the six moments. Empty hides the band.',
					'sub_fields'   => array(
						lsm_f_text( 'field_lsm_wwd_marquee_item', 'Text', 'item' ),
					),
				),

				// ---------------- THE PROCESS ----------------
				lsm_f_tab( 'field_lsm_wwd_tab_steps', 'The process' ),
				lsm_f_text( 'field_lsm_steps_label', 'Small label', 'steps_label', 'Above the step list, e.g. "The Launch Process".' ),
				lsm_f_image( 'field_lsm_steps_ball', 'Rolling ball', 'steps_ball_image', 'Transparent PNG. Rolls down the spine as you scroll. Leave empty to hide it.' ),
				array(
					'key'          => 'field_lsm_process_steps',
					'label'        => 'Steps',
					'name'         => 'process_steps',
					'type'         => 'repeater',
					'layout'       => 'block',
					'button_label' => 'Add step',
					'instructions' => 'Drag the rows to reorder. The 01-06 numbers follow the order, so there is nothing to renumber. '
						. 'Empty hides the whole section.',
					'sub_fields'   => array(
						lsm_f_text( 'field_lsm_step_name', 'Step name', 'step_name', 'The large word, e.g. "We Listen".' ),
						lsm_f_area( 'field_lsm_step_claim', 'Headline', 'step_claim', 2, 'The bold line beside the step name.' ),
						lsm_f_area( 'field_lsm_step_body', 'Paragraph', 'step_body', 4 ),
					),
				),

				// ---------------- THE LINE ----------------
				lsm_f_tab( 'field_lsm_wwd_tab_line', 'The line' ),
				lsm_f_image( 'field_lsm_line_image', 'Background photograph', 'closing_line_image', 'Full-bleed behind the three phrases, darkened automatically.' ),
				lsm_f_text( 'field_lsm_line_one', 'First phrase', 'closing_line_one' ),
				lsm_f_text( 'field_lsm_line_two', 'Second phrase', 'closing_line_two', 'Indented from the first.' ),
				lsm_f_text( 'field_lsm_line_three', 'Third phrase', 'closing_line_three', 'Indented furthest, in gold.' ),

				// ---------------- WHAT WE KEEP AN EYE ON ----------------
				lsm_f_tab( 'field_lsm_wwd_tab_register', 'What we keep an eye on' ),
				lsm_f_area( 'field_lsm_stage_heading', 'Heading', 'stage_heading', 2, 'New lines become line breaks.' ),
				lsm_f_image( 'field_lsm_stage_figure', 'Cut-out photograph', 'stage_figure', 'Stands between the two columns.' ),
				array(
					'key'          => 'field_lsm_register_stages',
					'label'        => 'Stages',
					'name'         => 'register_stages',
					'type'         => 'repeater',
					'layout'       => 'table',
					'button_label' => 'Add stage',
					'instructions' => 'Drag the rows to reorder. They alternate left and right of the photograph, and the 01-05 numbers '
						. 'follow the order. On phones they collapse into a single reading order automatically. Empty hides the stage columns.',
					'sub_fields'   => array(
						lsm_f_text( 'field_lsm_stage_name', 'Stage name', 'stage_name', 'e.g. "Her Game".' ),
					),
				),
				lsm_f_area( 'field_lsm_stage_note', 'Closing note', 'stage_note', 3, 'Sits under the right-hand column.' ),

				// ---------------- CLOSING ----------------
				lsm_f_tab( 'field_lsm_wwd_tab_closing', 'Closing' ),
				lsm_f_area( 'field_lsm_wwd_closing_title', 'Heading', 'closing_title', 2 ),
				lsm_f_text( 'field_lsm_wwd_closing_accent', 'Heading - gold part', 'closing_title_accent' ),
				lsm_f_link( 'field_lsm_wwd_closing_button', 'Button', 'closing_button' ),
			),
		)
	);

}
add_action( 'acf/init', 'lsm_acf_what_we_do_fields' );

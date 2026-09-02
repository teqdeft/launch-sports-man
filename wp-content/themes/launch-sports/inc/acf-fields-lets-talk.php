<?php
/**
 * ACF field group: Let's Talk.
 *
 * The page's own words are here. The form is not: it is a Contact Form 7 form,
 * edited under Contact, and this group only records which one to show.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the Let's Talk group.
 */
function lsm_acf_lets_talk_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'             => 'group_lsm_contact',
			'title'           => "Let's Talk page",
			'menu_order'      => 0,
			'position'        => 'normal',
			'style'           => 'default',
			'label_placement' => 'top',
			'active'          => true,
			'description'     => 'Leave a field empty to hide that element. The form is a Contact Form 7 form - pick it below and edit it under Contact.',
			'location'        => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/page-lets-talk.php',
					),
				),
			),
			'fields'          => array(

				// ---------------- HERO ----------------
				lsm_f_tab( 'field_lsm_contact_tab_hero', 'Hero' ),
				lsm_f_area( 'field_lsm_contact_hero_title', 'Heading', 'hero_title', 2 ),
				lsm_f_text( 'field_lsm_contact_hero_accent', 'Heading - gold part', 'hero_title_accent' ),
				lsm_f_area( 'field_lsm_contact_hero_lede', 'Standfirst', 'hero_lede', 2 ),

				// ---------------- FORM ----------------
				lsm_f_tab( 'field_lsm_contact_tab_form', 'Form' ),
				lsm_f_image( 'field_lsm_contact_photo', 'Photograph', 'form_side_photo', 'Stands beside the form.' ),
				array(
					'key'           => 'field_lsm_contact_form',
					'label'         => 'Form to show',
					'name'          => 'form_id',
					'type'          => 'post_object',
					'post_type'     => array( 'wpcf7_contact_form' ),
					'return_format' => 'id',
					'allow_null'    => 1,
					'ui'            => 1,
					'instructions'  => 'The fields, the wording, the messages and where enquiries are sent all live in '
						. '<strong>Contact</strong> in the sidebar. Leave empty to hide the form.',
				),

				// ---------------- CLOSING ----------------
				lsm_f_tab( 'field_lsm_contact_tab_closing', 'Closing' ),
				lsm_f_area( 'field_lsm_contact_closing_title', 'Heading', 'closing_title', 2 ),
				lsm_f_text( 'field_lsm_contact_closing_accent', 'Heading - gold part', 'closing_title_accent' ),
			),
		)
	);
}
add_action( 'acf/init', 'lsm_acf_lets_talk_fields' );

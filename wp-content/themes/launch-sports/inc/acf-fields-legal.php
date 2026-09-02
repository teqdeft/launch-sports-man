<?php
/**
 * ACF field group: legal pages (privacy policy, terms, cookie notice).
 *
 * Only the frame is here. The body of a legal page is the editor's content,
 * written in the Classic Editor, because a policy is revised prose and not a
 * fixed set of slots.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the legal page group.
 */
function lsm_acf_legal_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group(
		array(
			'key'             => 'group_lsm_legal',
			'title'           => 'Legal page',
			'menu_order'      => 0,
			'position'        => 'normal',
			'style'           => 'default',
			'label_placement' => 'top',
			'active'          => true,
			'description'     => 'The heading and date. The policy itself goes in the editor below, where headings, '
				. 'lists and links are all styled to match the site.',
			'location'        => array(
				array(
					array(
						'param'    => 'page_template',
						'operator' => '==',
						'value'    => 'templates/page-legal.php',
					),
				),
			),
			'fields'          => array(
				lsm_f_text( 'field_lsm_legal_label', 'Small label', 'legal_label', 'Above the heading, e.g. "Legal".' ),
				lsm_f_area( 'field_lsm_legal_title', 'Heading', 'legal_title', 2, 'Leave empty to use the page title.' ),
				lsm_f_text( 'field_lsm_legal_accent', 'Heading - gold part', 'legal_title_accent' ),
				lsm_f_area( 'field_lsm_legal_intro', 'Opening paragraph', 'legal_intro', 3, 'Set centred under the heading, before the policy begins.' ),
				lsm_f_text( 'field_lsm_legal_updated', 'Date line', 'legal_updated', 'e.g. "Last updated 2 September 2026". Leave empty and the date this page was last saved is used.' ),
				lsm_f_message(
					'field_lsm_legal_msg',
					'',
					'The contents list down the side builds itself from the <strong>Heading 2</strong> headings in the editor below, '
						. 'and numbers them in order. Add, rename or reorder a heading and the list follows - there is no second list to keep in step.'
				),
				lsm_f_text( 'field_lsm_legal_toc', 'Contents heading', 'legal_toc_title', 'Above the list. Defaults to "Contents".' ),
				lsm_f_text( 'field_lsm_legal_chapter', 'Word before each number', 'legal_chapter_word', 'Printed above each section as e.g. "Chapter 01". Defaults to "Chapter"; clear it to remove those labels.' ),
			),
		)
	);
}
add_action( 'acf/init', 'lsm_acf_legal_fields' );

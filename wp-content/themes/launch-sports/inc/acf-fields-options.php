<?php
/**
 * ACF field group: Site settings.
 *
 * Everything that appears on every page. These live in one place so the header
 * and footer are never edited per page.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * A read-only note rendered inside a field group.
 *
 * @param string $key     Field key.
 * @param string $label   Heading shown above the note.
 * @param string $message HTML message.
 * @return array
 */
function lsm_f_message( $key, $label, $message ) {
	return array(
		'key'       => $key,
		'label'     => $label,
		'type'      => 'message',
		'message'   => $message,
		'esc_html'  => 0,
		'new_lines' => 'wpautop',
	);
}

/**
 * Link to a registered menu location, or to the Menus screen if nothing is
 * assigned there yet.
 *
 * @param string $location Registered location slug.
 * @param string $label    Human label.
 * @return string HTML.
 */
function lsm_menu_edit_link( $location, $label ) {
	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations[ $location ] ) ? (int) $locations[ $location ] : 0;

	if ( $menu_id ) {
		$menu = wp_get_nav_menu_object( $menu_id );
		$url  = admin_url( 'nav-menus.php?action=edit&menu=' . $menu_id );
		return sprintf(
			'<a href="%s"><strong>%s</strong></a> &mdash; currently showing the &ldquo;%s&rdquo; menu',
			esc_url( $url ),
			esc_html( $label ),
			esc_html( $menu ? $menu->name : '?' )
		);
	}

	return sprintf(
		'<a href="%s"><strong>%s</strong></a> &mdash; <em>no menu assigned yet, so nothing renders here</em>',
		esc_url( admin_url( 'nav-menus.php?action=locations' ) ),
		esc_html( $label )
	);
}

/**
 * Register the Site settings field group.
 */
function lsm_acf_option_fields() {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$menus_url = admin_url( 'nav-menus.php?action=locations' );

	acf_add_local_field_group(
		array(
			'key'             => 'group_lsm_options',
			'title'           => 'Site settings',
			'menu_order'      => 0,
			'position'        => 'normal',
			'style'           => 'default',
			'label_placement' => 'top',
			'active'          => true,
			'location'        => array(
				array(
					array(
						'param'    => 'options_page',
						'operator' => '==',
						'value'    => 'lsm-site-settings',
					),
				),
			),
			'fields'          => array(

				// ------------------------------------------------ HEADER
				array(
					'key'   => 'field_lsm_opt_tab_header',
					'label' => 'Header',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_lsm_header_logo',
					'label'         => 'Header logo',
					'name'          => 'header_logo',
					'type'          => 'file',
					'return_format' => 'array',
					'mime_types'    => 'svg',
					'instructions'  => 'SVG only, and optional &mdash; leave empty to use the logo that ships with the theme. '
						. 'It must be an SVG because the mobile header sizes the logo with a rule that targets the '
						. '<code>&lt;svg&gt;</code> element itself, and recolours paths inside it. A PNG or JPG would not scale '
						. 'or recolour correctly on phones.',
				),
				array(
					'key'          => 'field_lsm_header_logo_label',
					'label'        => 'Logo link description',
					'name'         => 'header_logo_label',
					'type'         => 'text',
					'placeholder'  => 'Launch Sports Management, home',
					'instructions' => 'Read aloud by screen readers in place of the logo. Leave empty to use the site name.',
				),
				lsm_f_message(
					'field_lsm_header_menus',
					'Header menus',
					'<p>Menus are edited under <strong>Appearance &rarr; Menus</strong>, where you can add, reorder and '
					. 'relabel items and point them at any page. Assign a menu to a location and it appears immediately.</p>'
					. '<ul style="margin-left:1.2em;list-style:disc">'
					. '<li>' . lsm_menu_edit_link( 'primary', 'Primary (header)' ) . '</li>'
					. '<li>' . lsm_menu_edit_link( 'mobile', 'Mobile overlay' ) . '</li>'
					. '</ul>'
					. '<p><a href="' . esc_url( $menus_url ) . '">Manage menu locations &rarr;</a></p>'
				),
				array(
					'key'           => 'field_lsm_header_cta',
					'label'         => 'Header button',
					'name'          => 'header_cta',
					'type'          => 'link',
					'return_format' => 'array',
					'instructions'  => 'The gold button in the top right, on every page. Leave empty to hide it.',
				),

				// ------------------------------------------------ FOOTER
				array(
					'key'   => 'field_lsm_opt_tab_footer',
					'label' => 'Footer',
					'type'  => 'tab',
				),
				array(
					'key'           => 'field_lsm_footer_logo',
					'label'         => 'Footer logo',
					'name'          => 'footer_logo',
					'type'          => 'file',
					'return_format' => 'array',
					'mime_types'    => 'svg',
					'instructions'  => 'SVG only, and optional &mdash; leave empty to use the logo that ships with the theme. '
						. 'The footer lockup is larger than the header one and is a different drawing.',
				),
				lsm_f_message(
					'field_lsm_footer_menus',
					'Footer menu',
					'<ul style="margin-left:1.2em;list-style:disc"><li>' . lsm_menu_edit_link( 'footer', 'Footer' ) . '</li></ul>'
					. '<p><a href="' . esc_url( $menus_url ) . '">Manage menu locations &rarr;</a></p>'
				),
				array(
					'key'          => 'field_lsm_footer_wordmark',
					'label'        => 'Oversized wordmark',
					'name'         => 'footer_wordmark',
					'type'         => 'text',
					'instructions' => 'The giant outlined word across the bottom of every page. One short word; it is scaled to the full width of the viewport. Empty hides it.',
				),
				array(
					'key'   => 'field_lsm_footer_copyright',
					'label' => 'Copyright line',
					'name'  => 'footer_copyright',
					'type'  => 'text',
				),
				array(
					'key'          => 'field_lsm_footer_credit',
					'label'        => 'Credit line',
					'name'         => 'footer_credit',
					'type'         => 'wysiwyg',
					'tabs'         => 'visual',
					'toolbar'      => 'Launch inline',
					'media_upload' => 0,
					'delay'        => 0,
					'instructions' => 'Allows a link and inline emphasis. Rendered on one line under the copyright.',
				),

				// -------------------------------------------- PAGE NOT FOUND
				array(
					'key'   => 'field_lsm_opt_tab_error',
					'label' => 'Page not found',
					'type'  => 'tab',
				),
				lsm_f_message(
					'field_lsm_error_msg',
					'',
					'What someone sees when they follow a broken or out-of-date link. There is no page behind it, so the '
						. 'wording lives here. Every field has a sensible default - leave one empty and that default is used. '
						. 'The shortcuts underneath the button come from the <strong>Primary</strong> menu, so they keep '
						. 'themselves up to date.'
				),
				lsm_f_text( 'field_lsm_error_label', 'Small label', 'error_label', 'Above the heading. Defaults to "404".' ),
				lsm_f_area( 'field_lsm_error_title', 'Heading', 'error_title', 2 ),
				lsm_f_text( 'field_lsm_error_accent', 'Heading - gold part', 'error_title_accent', 'Set on its own line under the heading.' ),
				lsm_f_area( 'field_lsm_error_lede', 'Message', 'error_lede', 3 ),
				lsm_f_link( 'field_lsm_error_button', 'Button', 'error_button', 'Defaults to a link back to the home page.' ),
			),
		)
	);
}
add_action( 'acf/init', 'lsm_acf_option_fields' );

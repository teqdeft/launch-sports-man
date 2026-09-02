<?php
/**
 * Template helpers.
 *
 * Every template reads content through these rather than calling get_field()
 * directly, for three reasons:
 *   - the theme must not fatal if ACF is ever deactivated,
 *   - "empty" needs one consistent definition, so a section can decide not to
 *     render rather than emitting an empty shell,
 *   - asset URLs must never be hardcoded.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Read an ACF field without depending on ACF being active.
 *
 * @param string   $name    Field name.
 * @param int|null $post_id Defaults to the current post; pass 'option' for options pages.
 * @param mixed    $default Returned when ACF is missing or the field is empty.
 * @return mixed
 */
function lsm_field( $name, $post_id = null, $default = null ) {
	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}
	$value = get_field( $name, $post_id );
	return lsm_filled( $value ) ? $value : $default;
}

/**
 * One definition of "has content".
 *
 * ACF hands back '' for empty text, null for empty images, array() for empty
 * repeaters and false for unchecked truefalse. Treating all of those as empty
 * is what stops the templates printing hollow markup. Note 0 and '0' are
 * deliberately NOT empty: a stat of zero is still a stat.
 *
 * @param mixed $value Value to test.
 * @return bool
 */
function lsm_filled( $value ) {
	if ( is_null( $value ) || false === $value ) {
		return false;
	}
	if ( is_string( $value ) ) {
		return '' !== trim( $value );
	}
	if ( is_array( $value ) ) {
		return ! empty( $value );
	}
	return true;
}

/**
 * URL for a file inside the theme's assets directory.
 *
 * @param string $rel Path relative to assets/, e.g. 'images/home-hero.jpg'.
 * @return string
 */
function lsm_asset( $rel ) {
	return get_theme_file_uri( 'assets/' . ltrim( $rel, '/' ) );
}

/**
 * Echo an ACF image in the shape the design expects.
 *
 * Accepts an attachment ID, an array (ACF image array) or a URL string, so it
 * keeps working whichever return format a field group is configured with.
 *
 * @param mixed  $image ACF image value.
 * @param string $size  Registered image size.
 * @param array  $attr  Extra attributes, e.g. array( 'class' => 'hero-bg-image' ).
 */
function lsm_image( $image, $size = 'large', $attr = array() ) {
	if ( ! lsm_filled( $image ) ) {
		return;
	}

	$id  = 0;
	$url = '';
	$alt = '';

	if ( is_array( $image ) ) {
		$id  = isset( $image['ID'] ) ? (int) $image['ID'] : 0;
		$url = isset( $image['url'] ) ? $image['url'] : '';
		$alt = isset( $image['alt'] ) ? $image['alt'] : '';
	} elseif ( is_numeric( $image ) ) {
		$id = (int) $image;
	} else {
		$url = (string) $image;
	}

	if ( $id ) {
		$defaults = array( 'loading' => 'lazy', 'decoding' => 'async' );
		echo wp_get_attachment_image( $id, $size, false, array_merge( $defaults, $attr ) );
		return;
	}

	if ( '' === $url ) {
		return;
	}

	$out = '';
	foreach ( $attr as $k => $v ) {
		$out .= sprintf( ' %s="%s"', esc_attr( $k ), esc_attr( $v ) );
	}
	printf(
		'<img src="%s" alt="%s" loading="lazy" decoding="async"%s>',
		esc_url( $url ),
		esc_attr( $alt ),
		$out // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_attr above.
	);
}

/**
 * Render a button/CTA from an ACF link field.
 *
 * The design has two button treatments and they are not interchangeable: the
 * compact one in the header bar and the large one inside a hero or closing
 * section. $class picks which.
 *
 * @param array  $link  ACF link array (url, title, target).
 * @param string $class Class list for the anchor.
 * @param array  $attr  Extra attributes.
 */
function lsm_button( $link, $class = 'btn', $attr = array() ) {
	if ( ! is_array( $link ) || empty( $link['url'] ) || empty( $link['title'] ) ) {
		return;
	}

	$target = ! empty( $link['target'] ) ? $link['target'] : '';
	$extra  = '';
	foreach ( $attr as $k => $v ) {
		$extra .= sprintf( ' %s="%s"', esc_attr( $k ), esc_attr( $v ) );
	}

	printf(
		'<a class="%s" href="%s"%s%s>%s</a>',
		esc_attr( $class ),
		esc_url( $link['url'] ),
		$target ? ' target="' . esc_attr( $target ) . '" rel="noopener"' : '',
		$extra, // phpcs:ignore WordPress.Security.EscapeOutput -- built from esc_attr above.
		esc_html( $link['title'] )
	);
}

/**
 * A heading where the closing phrase is set in gold.
 *
 * The design uses this shape repeatedly - "Athletes <span>We've Helped</span>",
 * "Your Career Is Bigger Than <span>Your Next Contract.</span>". Splitting it
 * into two fields keeps editors out of raw HTML while preserving the exact
 * markup the CSS targets. Newlines in either part become <br>, because some of
 * these headings are deliberately broken across lines.
 *
 * Most of these run the gold phrase on from the previous words, separated by a
 * space. One does not: the "Opportunity is exciting. / It can also be
 * overwhelming." statement is set as two stacked lines, so it passes '<br>'.
 * Getting that wrong collapses the two lines into one wrapped paragraph.
 *
 * @param string $plain     Leading text.
 * @param string $accent    Text set in gold, or ''.
 * @param string $separator What goes between the two parts: ' ' or '<br>'.
 * @return string Escaped HTML.
 */
function lsm_accent_heading( $plain, $accent = '', $separator = ' ' ) {
	$br = static function ( $text ) {
		return implode( '<br>', array_map( 'esc_html', preg_split( '/\R/', (string) $text ) ) );
	};

	$out = lsm_filled( $plain ) ? $br( $plain ) : '';

	if ( lsm_filled( $accent ) ) {
		if ( '' !== $out ) {
			$out .= ( '<br>' === $separator ) ? '<br>' : ' ';
		}
		$out .= '<span class="u-accent-text">' . $br( $accent ) . '</span>';
	}

	return $out;
}

/**
 * Load a section part and hand it its data.
 *
 * Thin wrapper over get_template_part() so sections stay uniform and a section
 * that decides it has nothing to show can simply return.
 *
 * @param string $name Filename under template-parts/sections/, without .php.
 * @param array  $args Passed through as $args.
 */
function lsm_section( $name, $args = array() ) {
	get_template_part( 'template-parts/sections/' . $name, null, $args );
}

/**
 * The inline SVG wordmark, read from the theme rather than pasted per template.
 *
 * It is ~112KB and appears in both the header and the footer, so it lives in
 * one file and is echoed where needed. It has to be inline rather than an
 * <img>: the responsive sheet sizes it with element selectors, such as
 * `.lsm-shell > header svg { width: 100px !important }`, plus several fill
 * overrides that reach inside the artwork.
 *
 * @param string $which 'mark' for the header lockup, 'footer' for the footer.
 * @param string $class Class applied to the root <svg>.
 */
function lsm_logo( $which = 'mark', $class = '' ) {
	$svg = '';

	/*
	 * An SVG uploaded under Site settings wins; otherwise the one bundled with
	 * the theme. Read from disk rather than by URL so this never makes an HTTP
	 * request to render a header.
	 */
	$custom = lsm_field( ( 'footer' === $which ) ? 'footer_logo' : 'header_logo', 'option' );
	if ( is_array( $custom ) && ! empty( $custom['ID'] ) ) {
		$path = get_attached_file( (int) $custom['ID'] );
		if ( $path && file_exists( $path ) && 'svg' === strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) {
			$svg = (string) file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
	}

	if ( '' === $svg ) {
		$file = get_theme_file_path( 'assets/svg/logo-' . sanitize_file_name( $which ) . '.svg' );
		if ( ! file_exists( $file ) ) {
			return;
		}
		$svg = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions
	}

	// Printed inline, so it runs in this origin. Never trust an upload wholesale.
	$svg = preg_replace( '#<script\b.*?</script>#is', '', $svg );
	$svg = preg_replace( '#\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)#i', '', $svg );

	if ( $class ) {
		$svg = preg_replace( '/<svg\b/', '<svg class="' . esc_attr( $class ) . '"', $svg, 1 );
	}

	echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput -- theme file, or upload scrubbed above.
}

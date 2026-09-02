<?php
/**
 * Legal pages: contents list and chapter numbering.
 *
 * The layout has a contents column beside the policy. Making the editor keep
 * that list in step with the headings by hand would guarantee it drifts, so it
 * is derived: every <h2> in the content becomes a numbered entry, gets an id to
 * link to, and gets a "Chapter 01" label printed above it.
 *
 * That means an editor writes an ordinary document - headings, paragraphs,
 * lists - and the furniture assembles itself.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * Split rendered content into a contents list and the content itself.
 *
 * @param string $html   Content, already through the_content filters.
 * @param string $prefix Word before the number, e.g. 'Chapter'.
 * @return array{html:string,toc:array<int,array{id:string,title:string,number:string}>}
 */
function lsm_legal_sections( $html, $prefix = 'Chapter' ) {
	$empty = array( 'html' => $html, 'toc' => array() );

	if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
		return $empty;
	}

	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	// Declare UTF-8 or curly quotes and dashes in the policy come out mangled.
	$doc->loadHTML(
		'<?xml encoding="utf-8" ?><div id="lsm-legal-root">' . $html . '</div>',
		LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
	);
	libxml_clear_errors();

	$root = $doc->getElementById( 'lsm-legal-root' );
	if ( ! $root ) {
		return $empty;
	}

	$xp       = new DOMXPath( $doc );
	$headings = iterator_to_array( $xp->query( './/h2', $root ) );
	if ( ! $headings ) {
		return $empty;
	}

	$toc  = array();
	$seen = array();

	foreach ( $headings as $i => $h2 ) {
		$title  = trim( $h2->textContent );
		$number = str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT );

		// An id the editor did not have to think about, kept unique in case two
		// sections are given the same name.
		$id = $h2->getAttribute( 'id' );
		if ( '' === $id ) {
			$id   = sanitize_title( $title );
			$id   = '' !== $id ? $id : 'section-' . ( $i + 1 );
			$base = $id;
			$n    = 2;
			while ( isset( $seen[ $id ] ) ) {
				$id = $base . '-' . $n;
				$n++;
			}
			$h2->setAttribute( 'id', $id );
		}
		$seen[ $id ] = true;

		if ( '' !== $prefix ) {
			$label = $doc->createElement( 'p' );
			$label->setAttribute( 'class', 'legal-chapter' );
			$label->setAttribute( 'data-label', '' );
			$label->appendChild( $doc->createTextNode( $prefix . ' ' . $number ) );
			$h2->parentNode->insertBefore( $label, $h2 );
		}

		$toc[] = array(
			'id'     => $id,
			'title'  => $title,
			'number' => $number,
		);
	}

	$out = '';
	foreach ( $root->childNodes as $child ) {
		$out .= $doc->saveHTML( $child );
	}

	return array( 'html' => $out, 'toc' => $toc );
}

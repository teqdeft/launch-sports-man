<?php
/**
 * Contact Form 7 integration for the Let's Talk enquiry form.
 *
 * The design owns this form down to the class names, and CF7 generates markup
 * of its own: it wraps every control in a .wpcf7-form-control-wrap span, nests
 * radios and checkboxes three spans deep, and auto-paragraphs the template.
 * Left alone that produces a different document from the one that was signed
 * off, and the stylesheet - which targets .form-choice, .form-consent and
 * friends - stops reaching the elements it was written for.
 *
 * Rather than bolt a layer of CSS onto CF7's shapes, this file normalises CF7's
 * output back to the approved markup. The rewriting is done on a parsed tree,
 * not with regular expressions, so a change in CF7's whitespace or attribute
 * order cannot quietly break it.
 *
 * What still comes from CF7, and should: validation, the response message,
 * spam checks, the mail template and the record of what was sent.
 *
 * @package LaunchSports
 */

defined( 'ABSPATH' ) || exit;

/**
 * The template is HTML, not prose. CF7's auto-paragraphing would wrap the
 * design's divs in <p> tags and turn every newline into a <br>.
 */
add_filter( 'wpcf7_autop_or_not', '__return_false' );

/**
 * Put the design's class on CF7's <form>.
 *
 * @param string $class Existing class attribute.
 * @return string
 */
function lsm_cf7_form_class( $class ) {
	return $class . ' form-shell';
}
add_filter( 'wpcf7_form_class_attr', 'lsm_cf7_form_class' );

/**
 * Rewrite CF7's control markup into the markup the design expects.
 *
 * Three transformations, in order:
 *   1. unwrap CF7's grouping spans, so inputs sit directly inside the design's
 *      own .form-field / .form-choice-row containers,
 *   2. flatten the label text CF7 wraps in a span back to a text node,
 *   3. put .form-choice / .form-consent / .form-consent-box back on the radio
 *      and checkbox labels, which is where the stylesheet looks for them.
 *
 * @param string $html CF7's rendered form contents.
 * @return string
 */
function lsm_cf7_markup( $html ) {
	if ( '' === trim( $html ) || ! class_exists( 'DOMDocument' ) ) {
		return $html;
	}

	$doc = new DOMDocument();
	libxml_use_internal_errors( true );
	// The fragment is not a document; wrap it so the parser has a root, and
	// declare UTF-8 or the » and … in the design's copy come out mangled.
	$doc->loadHTML(
		'<?xml encoding="utf-8" ?><div id="lsm-cf7-root">' . $html . '</div>',
		LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED
	);
	libxml_clear_errors();

	$xp   = new DOMXPath( $doc );
	$root = $doc->getElementById( 'lsm-cf7-root' );
	if ( ! $root ) {
		return $html;
	}

	/** Replace a node with its own children. */
	$unwrap = static function ( DOMNode $node ) {
		while ( $node->firstChild ) {
			$node->parentNode->insertBefore( $node->firstChild, $node );
		}
		$node->parentNode->removeChild( $node );
	};

	/*
	 * 1. Unwrap only the spans CF7's own script never looks for.
	 *
	 * Three of them are load-bearing and must stay in the document:
	 *
	 *   .wpcf7-form-control-wrap  the anchor a validation message is appended to:
	 *
	 *   form.querySelectorAll('.wpcf7-form-control-wrap[data-name="NAME"]')
	 *     .forEach(el => el.appendChild(tip))
	 *
	 * Unwrapping it left that query with nothing to match, so "This field is
	 * required" was never painted next to the field it belonged to.
	 *
	 *   .wpcf7-radio and .wpcf7-acceptance  these spans ARE the form control for
	 *   a radio group and a consent box - the inputs inside them are not. CF7
	 *   re-validates a field on change only if the changed element can find a
	 *   .wpcf7-form-control ancestor, so with these unwrapped, choosing a role or
	 *   ticking consent left the error message sitting there until the next
	 *   submit. They also carry aria-describedby to the screen-reader message.
	 *
	 * All three stay in the document and are taken out of the layout with
	 * display:contents instead - see desktop.css. Only .wpcf7-list-item, which
	 * nothing in CF7 queries, is still unwrapped.
	 */
	$groups = $xp->query(
		".//span[contains(concat(' ', normalize-space(@class), ' '), ' wpcf7-list-item ')]",
		$root
	);
	// Iterate over a snapshot: unwrapping mutates the tree a live list follows.
	foreach ( iterator_to_array( $groups ) as $node ) {
		$unwrap( $node );
	}

	// 2. The option's own words, as a plain text node like the static build.
	foreach ( iterator_to_array( $xp->query( ".//span[contains(concat(' ', normalize-space(@class), ' '), ' wpcf7-list-item-label ')]", $root ) ) as $node ) {
		$unwrap( $node );
	}

	// 3. The classes the stylesheet actually targets.
	foreach ( iterator_to_array( $xp->query( './/label', $root ) ) as $label ) {
		if ( $label->getAttribute( 'class' ) ) {
			continue; // A label the design wrote itself, e.g. .form-label.
		}
		$radio = $xp->query( "./input[@type='radio']", $label );
		if ( $radio->length ) {
			$label->setAttribute( 'class', 'form-choice' );
			continue;
		}
		$check = $xp->query( "./input[@type='checkbox']", $label );
		if ( $check->length ) {
			$label->setAttribute( 'class', 'form-consent' );
			$box = $check->item( 0 );
			$box->setAttribute( 'class', trim( $box->getAttribute( 'class' ) . ' form-consent-box' ) );
		}
	}

	/*
	 * 4. CF7 submits with <input type="submit">; the design uses <button>, and
	 * so does motion.js, which looks the submit up with querySelector('button')
	 * and animates nothing at all when it is not there.
	 */
	foreach ( iterator_to_array( $xp->query( ".//input[@type='submit']", $root ) ) as $submit ) {
		$button = $doc->createElement( 'button' );
		foreach ( iterator_to_array( $submit->attributes ) as $attr ) {
			if ( 'value' === $attr->name ) {
				continue; // becomes the button's text
			}
			$button->setAttribute( $attr->name, $attr->value );
		}
		$button->setAttribute( 'type', 'submit' );
		$button->appendChild( $doc->createTextNode( $submit->getAttribute( 'value' ) ) );
		$submit->parentNode->replaceChild( $button, $submit );
	}

	$out = '';
	foreach ( $root->childNodes as $child ) {
		$out .= $doc->saveHTML( $child );
	}
	return $out;
}
add_filter( 'wpcf7_form_elements', 'lsm_cf7_markup' );

/**
 * The design's graduation-year field asks for a number, so phones should offer
 * a number pad. CF7 has no syntax for arbitrary attributes, so it is set here.
 *
 * @param string $html Rendered form contents.
 * @return string
 */
function lsm_cf7_gradyear_inputmode( $html ) {
	return str_replace( 'id="gradyear"', 'id="gradyear" inputmode="numeric"', $html );
}
add_filter( 'wpcf7_form_elements', 'lsm_cf7_gradyear_inputmode', 20 );

/**
 * The form the Let's Talk page is set to show, as a shortcode.
 *
 * @param int $page_id Page to read the setting from.
 * @return string Shortcode, or '' when no form is chosen.
 */
function lsm_contact_form_shortcode( $page_id = null ) {
	if ( ! shortcode_exists( 'contact-form-7' ) ) {
		return '';
	}

	$form = lsm_field( 'form_id', $page_id );
	$id   = 0;

	if ( is_object( $form ) && isset( $form->ID ) ) {
		$id = (int) $form->ID;
	} elseif ( is_numeric( $form ) ) {
		$id = (int) $form;
	}

	if ( ! $id || 'wpcf7_contact_form' !== get_post_type( $id ) ) {
		return '';
	}

	return sprintf( '[contact-form-7 id="%d"]', $id );
}

/**
 * Make an unticked consent box report itself like every other required field.
 *
 * CF7 handles acceptance separately from validation: an unticked box comes back
 * as "unaccepted" with only a form-level banner, so the one required field a
 * reader is most likely to skip is the only one that never says so beside
 * itself. CF7 has a setting that changes this - acceptance_as_validation - but
 * it lives in the form's own record in the database, and no deploy carries the
 * database. Setting it here means it travels with the theme and is true in
 * every environment, local and live alike.
 *
 * @param array            $props        Form properties.
 * @param WPCF7_ContactForm $contact_form The form.
 * @return array
 */
function lsm_cf7_acceptance_as_validation( $props, $contact_form ) {
	$settings = isset( $props['additional_settings'] ) ? (string) $props['additional_settings'] : '';

	if ( false === strpos( $settings, 'acceptance_as_validation' ) ) {
		$props['additional_settings'] = trim( $settings . "\nacceptance_as_validation: on" );
	}

	return $props;
}
add_filter( 'wpcf7_contact_form_properties', 'lsm_cf7_acceptance_as_validation', 10, 2 );

/**
 * Report the consent box as a plain Yes or No in the enquiry email.
 *
 * CF7 renders an acceptance mail tag as "Consented: <the wording on the form>",
 * which reads oddly on a line that already says "Consent:" and buries the
 * answer at the front of a sentence. Whoever reads these wants to see, at a
 * glance and in the same shape as every other field, whether the box was
 * ticked.
 *
 * Scoped to this one field by name: any other acceptance box added later keeps
 * CF7's own wording, which records what was agreed to and is the better default
 * when the wording matters more than the answer.
 *
 * @param string             $replaced  CF7's rendering.
 * @param string|array       $submitted The submitted value.
 * @param bool               $html      Whether the mail is HTML.
 * @param WPCF7_MailTag|null $mail_tag  The tag being replaced.
 * @return string
 */
function lsm_cf7_consent_yes_no( $replaced, $submitted, $html, $mail_tag ) {
	if ( ! $mail_tag || 'consent' !== $mail_tag->field_name() ) {
		return $replaced;
	}

	return empty( $submitted ) ? 'No' : 'Yes';
}
add_filter( 'wpcf7_mail_tag_replaced_acceptance', 'lsm_cf7_consent_yes_no', 20, 4 );

/**
 * Keep the consent box optional in every environment.
 *
 * "optional" is part of the form tag, and the form tag lives in the form's own
 * record in the database - which no deploy carries. Setting it here means a
 * server that has never had the edit made by hand still behaves the way this
 * theme expects, rather than silently demanding consent.
 *
 * The rewrite is idempotent: a form whose tag already says optional is left
 * alone, so making the same edit in wp-admin is safe and turns this into a
 * no-op. It matches the bare tag only, so changing the field in the admin to
 * anything else takes precedence over this.
 *
 * @param array             $props        Form properties.
 * @param WPCF7_ContactForm $contact_form The form.
 * @return array
 */
function lsm_cf7_consent_optional( $props, $contact_form ) {
	if ( isset( $props['form'] ) && false !== strpos( $props['form'], '[acceptance consent]' ) ) {
		$props['form'] = str_replace( '[acceptance consent]', '[acceptance consent optional]', $props['form'] );
	}

	return $props;
}
add_filter( 'wpcf7_contact_form_properties', 'lsm_cf7_consent_optional', 10, 2 );

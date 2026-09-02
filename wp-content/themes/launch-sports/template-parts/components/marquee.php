<?php
/**
 * Gold running band.
 *
 * Appears on Home (inside the hero band) and on What We Do, with different
 * wording each time, so the items come from a per-page repeater.
 *
 * Two identical .mq-set blocks are emitted on purpose. motion.js measures the
 * first set, removes every other one and then clones it to fill the viewport -
 * but before that runs, a single set leaves a visible gap on wide screens.
 * Shipping two matches the static build and covers the pre-JS frame.
 *
 * @package LaunchSports
 *
 * @var array $args {
 *     @type array  $items Repeater rows, each with an 'item' key.
 *     @type string $class Extra classes for the <section>.
 * }
 */

defined( 'ABSPATH' ) || exit;

$items = isset( $args['items'] ) ? $args['items'] : array();
if ( ! lsm_filled( $items ) ) {
	return;
}

$labels = array();
foreach ( $items as $row ) {
	$text = is_array( $row ) ? ( isset( $row['item'] ) ? $row['item'] : reset( $row ) ) : $row;
	if ( lsm_filled( $text ) ) {
		$labels[] = $text;
	}
}
if ( ! $labels ) {
	return;
}

$extra = isset( $args["class"] ) ? " " . $args["class"] : "";
$sec   = isset( $args["sec"] ) ? $args["sec"] : "";

/** One set of items; the star separator follows each item. */
$render_set = static function () use ( $labels ) {
	echo '<div class="mq-set" data-mq-set>';
	foreach ( $labels as $label ) {
		echo '<span class="mq-item">' . esc_html( $label ) . '</span>';
		echo '<svg class="mq-star" width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 0 L14.2 9.8 L24 12 L14.2 14.2 L12 24 L9.8 14.2 L0 12 L9.8 9.8 Z" fill="#F2F0EA"/></svg>';
	}
	echo '</div>';
};
?>
<section<?php echo $sec ? ' data-sec="' . esc_attr( $sec ) . '"' : ''; ?> class="mq-band<?php echo esc_attr( $extra ); ?>" data-marquee>
	<div class="mq-track" data-mq-track>
		<?php $render_set(); ?>
		<?php $render_set(); ?>
	</div>
</section>

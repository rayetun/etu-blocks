<?php
/**
 * Pricing Table block — server render.
 *
 * Plans are a role="list" of cards; each plan name is a heading, features are a
 * list with check / cross cells that carry "Included" / "Not included" text for
 * screen readers, and each CTA names its plan. The optional billing toggle is a
 * labelled group of aria-pressed buttons that swaps the monthly / yearly price
 * via the Interactivity store (prices work with JS off — monthly shows by default).
 *
 * @package Etublocks
 *
 * @var array    $attributes Block attributes.
 * @var string   $content    Unused (dynamic block).
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound -- Included within the block render callback's method scope; these are local, not global.

$plans = isset( $attributes['plans'] ) && is_array( $attributes['plans'] ) ? array_values( $attributes['plans'] ) : array();
$plans = array_values(
	array_filter(
		$plans,
		static function ( $p ) {
			return is_array( $p ) && ( '' !== trim( (string) ( $p['name'] ?? '' ) ) || '' !== trim( (string) ( $p['price'] ?? '' ) ) );
		}
	)
);
if ( empty( $plans ) ) {
	return;
}

$block_id = sanitize_html_class( (string) ( $attributes['blockId'] ?? '' ) );
if ( '' === $block_id ) {
	$block_id = 'x' . substr( md5( (string) wp_json_encode( $attributes ) ), 0, 10 );
}

$columns = (string) ( $attributes['columns'] ?? '3' );
if ( ! in_array( $columns, array( '2', '3', '4' ), true ) ) {
	$columns = '3';
}

$preset = (string) ( $attributes['preset'] ?? 'modern' );
if ( ! in_array( $preset, array( 'modern', 'outline', 'minimal' ), true ) ) {
	$preset = 'modern';
}

$billing       = ! empty( $attributes['enableBilling'] );
$monthly_label = trim( (string) ( $attributes['monthlyLabel'] ?? '' ) );
$yearly_label  = trim( (string) ( $attributes['yearlyLabel'] ?? '' ) );
$yearly_badge  = trim( (string) ( $attributes['yearlyBadge'] ?? '' ) );
if ( '' === $monthly_label ) {
	$monthly_label = __( 'Monthly', 'etu-blocks' );
}
if ( '' === $yearly_label ) {
	$yearly_label = __( 'Yearly', 'etu-blocks' );
}

$inline_allowed = array( 'strong' => array(), 'em' => array(), 'b' => array(), 'i' => array(), 'br' => array() );

$icon_yes = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M20 6 9 17l-5-5"/></svg>';
$icon_no  = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M6 6l12 12"/><path d="M18 6 6 18"/></svg>';

$scope         = '.etu-pricing-' . $block_id;
$generated_css = \Etublocks\CSS_Generator::for_block( 'etu/pricing-table', $attributes, $scope );

$classes = array(
	'etu-pricing',
	'etu-pricing--preset-' . $preset,
	'etu-pricing--cols-' . $columns,
	'etu-pricing-' . $block_id,
);

$wrapper_attributes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classes ) ) );

\Etublocks\CSS_Generator::inject( 'etu/pricing-table', $generated_css );

$open = '<div ' . $wrapper_attributes;
if ( $billing ) {
	$open .= ' data-wp-interactive="etu/pricing" ' . wp_interactivity_data_wp_context( array( 'yearly' => false ) );
}
$open .= '>';
echo wp_kses( $open, \Etublocks\Rendering::allowed() );

// Billing toggle.
if ( $billing ) {
	echo '<div class="etu-pricing__billing" role="group" aria-label="' . esc_attr__( 'Billing period', 'etu-blocks' ) . '">';
	echo '<button type="button" class="etu-pricing__billing-opt" data-wp-on--click="actions.setMonthly" data-wp-bind--aria-pressed="!context.yearly">' . esc_html( $monthly_label ) . '</button>';
	echo '<button type="button" class="etu-pricing__billing-opt" data-wp-on--click="actions.setYearly" data-wp-bind--aria-pressed="context.yearly">' . esc_html( $yearly_label );
	if ( '' !== $yearly_badge ) {
		echo ' <span class="etu-pricing__billing-badge">' . esc_html( $yearly_badge ) . '</span>';
	}
	echo '</button>';
	echo '</div>';
}

echo '<div class="etu-pricing__grid" role="list">';

foreach ( $plans as $plan ) {
	$is_high  = ! empty( $plan['highlighted'] );
	$name     = (string) ( $plan['name'] ?? '' );
	$badge    = trim( (string) ( $plan['badge'] ?? '' ) );
	$currency = (string) ( $plan['currency'] ?? '' );
	$price    = (string) ( $plan['price'] ?? '' );
	$price_y  = (string) ( $plan['priceYearly'] ?? $price );
	$period   = (string) ( $plan['period'] ?? '' );
	$period_y = (string) ( $plan['periodYearly'] ?? $period );
	$desc     = (string) ( $plan['description'] ?? '' );
	$features = isset( $plan['features'] ) && is_array( $plan['features'] ) ? $plan['features'] : array();
	$btn_text = trim( (string) ( $plan['buttonText'] ?? '' ) );
	$btn_url  = trim( (string) ( $plan['buttonUrl'] ?? '' ) );

	echo '<div class="etu-pricing__card' . ( $is_high ? ' etu-pricing__card--featured' : '' ) . '" role="listitem">';

	if ( '' !== $badge ) {
		echo '<span class="etu-pricing__badge">' . esc_html( $badge ) . '</span>';
	}

	echo '<h3 class="etu-pricing__name">' . wp_kses( $name, $inline_allowed ) . '</h3>';

	echo '<div class="etu-pricing__price">';
	if ( '' !== $currency ) {
		echo '<span class="etu-pricing__currency">' . esc_html( $currency ) . '</span>';
	}
	if ( $billing ) {
		echo '<span class="etu-pricing__amount" data-wp-bind--hidden="context.yearly">' . esc_html( $price ) . '</span>';
		echo '<span class="etu-pricing__amount" data-wp-bind--hidden="!context.yearly">' . esc_html( $price_y ) . '</span>';
		if ( '' !== $period || '' !== $period_y ) {
			echo '<span class="etu-pricing__period" data-wp-bind--hidden="context.yearly">' . esc_html( $period ) . '</span>';
			echo '<span class="etu-pricing__period" data-wp-bind--hidden="!context.yearly">' . esc_html( $period_y ) . '</span>';
		}
	} else {
		echo '<span class="etu-pricing__amount">' . esc_html( $price ) . '</span>';
		if ( '' !== $period ) {
			echo '<span class="etu-pricing__period">' . esc_html( $period ) . '</span>';
		}
	}
	echo '</div>';

	if ( '' !== trim( $desc ) ) {
		echo '<p class="etu-pricing__desc">' . wp_kses( $desc, $inline_allowed ) . '</p>';
	}

	if ( ! empty( $features ) ) {
		echo '<ul class="etu-pricing__features" role="list">';
		foreach ( $features as $feature ) {
			$ftext     = (string) ( is_array( $feature ) ? ( $feature['text'] ?? '' ) : $feature );
			$fincluded = ! is_array( $feature ) || ! empty( $feature['included'] );
			if ( '' === trim( $ftext ) ) {
				continue;
			}
			echo '<li class="etu-pricing__feature' . ( $fincluded ? '' : ' etu-pricing__feature--excluded' ) . '">';
			echo wp_kses( '<span class="etu-pricing__mark" aria-hidden="true">' . ( $fincluded ? $icon_yes : $icon_no ) . '</span>', \Etublocks\Rendering::allowed() );
			echo '<span class="screen-reader-text">' . ( $fincluded ? esc_html__( 'Included:', 'etu-blocks' ) : esc_html__( 'Not included:', 'etu-blocks' ) ) . '</span> ';
			echo '<span class="etu-pricing__feature-text">' . wp_kses( $ftext, $inline_allowed ) . '</span>';
			echo '</li>';
		}
		echo '</ul>';
	}

	if ( '' !== $btn_text ) {
		/* translators: 1: button text, 2: plan name. */
		$btn_aria = esc_attr( sprintf( __( '%1$s — %2$s plan', 'etu-blocks' ), wp_strip_all_tags( $btn_text ), wp_strip_all_tags( $name ) ) );
		$btn_class = 'etu-pricing__btn' . ( $is_high ? ' etu-pricing__btn--featured' : '' );
		if ( '' !== $btn_url ) {
			echo wp_kses( '<a class="' . $btn_class . '" href="' . esc_url( $btn_url ) . '" aria-label="' . $btn_aria . '">' . esc_html( $btn_text ) . '</a>', \Etublocks\Rendering::allowed() );
		} else {
			// No link yet — still a real, keyboard-focusable control.
			echo wp_kses( '<button type="button" class="' . $btn_class . '" aria-label="' . $btn_aria . '">' . esc_html( $btn_text ) . '</button>', \Etublocks\Rendering::allowed() );
		}
	}

	echo '</div>'; // card
}

echo '</div>'; // grid
echo '</div>'; // root

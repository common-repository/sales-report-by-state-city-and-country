<?php
/**
 * File Name: general-setting.php.
 * Description: Here we have some general setting.
 *
 * @package   sales-report-by-state-city-and-country.
 * @version   1.0.0.
 */

/**
 * WordPress check.
 *
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit;
}
add_settings_section(
	'devsoul_psbsp_general_settings_fields',
	'', // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'devsoul_psbsp_general_settings_sections'
);
add_settings_field(
	'devsoul_psbsp_product_per_page_for_product_sales',
	esc_html__('Product Per Page For Product Sale', ' product-sales-report'), // The label.
	array( $this, 'devsoul_psbsp_product_per_page_for_product_sales' ),
	'devsoul_psbsp_general_settings_sections', // The page on which this option will be displayed.
	'devsoul_psbsp_general_settings_fields'
);
register_setting(
	'devsoul_psbsp_general_settings_fields',
	'devsoul_psbsp_product_per_page_for_product_sales'
);
add_settings_field(
	'devsoul_psbsp_product_per_page_for_order_sales',
	esc_html__('Order Per Page', ' product-sales-report'), // The label.
	array( $this, 'devsoul_psbsp_product_per_page_for_order_sales' ),
	'devsoul_psbsp_general_settings_sections', // The page on which this option will be displayed.
	'devsoul_psbsp_general_settings_fields'
);
register_setting(
	'devsoul_psbsp_general_settings_fields',
	'devsoul_psbsp_product_per_page_for_order_sales'
);

$advance_filter = array( 'additional_fee', 'shipping_fee', 'tax_total', 'line_subtotal', 'order_total', 'order_subtotal', 'total_refunded_amount', 'total_discount', 'shipping_tax', 'shipping_method', 'payment_method', 'billing_name', 'billing_email', 'billing_phone', 'currency' );

foreach ($advance_filter as $current_filter_name) {

	add_settings_field(
		'devsoul_psbsp_' . $current_filter_name,
		esc_html__(' Enable ' . ucfirst(str_replace('_', ' ', $current_filter_name)), ' product-sales-report'),
		'devsoul_psbsp_' . $current_filter_name,
		'devsoul_psbsp_general_settings_sections',
		'devsoul_psbsp_general_settings_fields'
	);
	register_setting(
		'devsoul_psbsp_general_settings_fields',
		'devsoul_psbsp_' . $current_filter_name
	);
}


/**
 * Devsoul_psbsp_additional_fee decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_additional_fee')) {

	function devsoul_psbsp_additional_fee() {
		?>
		<input type="checkbox" name="devsoul_psbsp_additional_fee" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_additional_fee'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_shipping_fee decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_shipping_fee')) {

	function devsoul_psbsp_shipping_fee() {
		?>
		<input type="checkbox" name="devsoul_psbsp_shipping_fee" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_shipping_fee'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_tax_total decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_tax_total')) {

	function devsoul_psbsp_tax_total() {
		?>
		<input type="checkbox" name="devsoul_psbsp_tax_total" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_tax_total'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_line_subtotal decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_line_subtotal')) {

	function devsoul_psbsp_line_subtotal() {
		?>
		<input type="checkbox" name="devsoul_psbsp_line_subtotal" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_line_subtotal'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_order_total decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_order_total')) {

	function devsoul_psbsp_order_total() {
		?>
		<input type="checkbox" name="devsoul_psbsp_order_total" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_order_total'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_order_subtotal decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_order_subtotal')) {

	function devsoul_psbsp_order_subtotal() {
		?>
		<input type="checkbox" name="devsoul_psbsp_order_subtotal" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_order_subtotal'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_total_refunded_amount decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_total_refunded_amount')) {

	function devsoul_psbsp_total_refunded_amount() {
		?>
		<input type="checkbox" name="devsoul_psbsp_total_refunded_amount" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_total_refunded_amount'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_total_discount decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_total_discount')) {

	function devsoul_psbsp_total_discount() {
		?>
		<input type="checkbox" name="devsoul_psbsp_total_discount" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_total_discount'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_shipping_tax decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_shipping_tax')) {

	function devsoul_psbsp_shipping_tax() {
		?>
		<input type="checkbox" name="devsoul_psbsp_shipping_tax" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_shipping_tax'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_shipping_method decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_shipping_method')) {

	function devsoul_psbsp_shipping_method() {
		?>
		<input type="checkbox" name="devsoul_psbsp_shipping_method" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_shipping_method'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_payment_method decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_payment_method')) {

	function devsoul_psbsp_payment_method() {
		?>
		<input type="checkbox" name="devsoul_psbsp_payment_method" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_payment_method'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_billing_name decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_billing_name')) {

	function devsoul_psbsp_billing_name() {
		?>
		<input type="checkbox" name="devsoul_psbsp_billing_name" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_billing_name'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_billing_email decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_billing_email')) {

	function devsoul_psbsp_billing_email() {
		?>
		<input type="checkbox" name="devsoul_psbsp_billing_email" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_billing_email'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_billing_phone decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_billing_phone')) {

	function devsoul_psbsp_billing_phone() {
		?>
		<input type="checkbox" name="devsoul_psbsp_billing_phone" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_billing_phone'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}

/**
 * Devsoul_psbsp_currency decalaration function for setting  .
 *
 * @since 1.0.0
 */
if (!function_exists('devsoul_psbsp_currency')) {

	function devsoul_psbsp_currency() {
		?>
		<input type="checkbox" name="devsoul_psbsp_currency" value="yes" 
		<?php
		if (!empty(get_option('devsoul_psbsp_currency'))) :
			?>
			checked <?php endif ?>>
		<?php
	}

}
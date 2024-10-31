<?php
/**
 * File Name: class-devsoul-psbsp-general-functions.php.
 * Description: Here we have some general function without class.
 *
 * @package   sales-report-by-state-city-and-country.
 * @version   1.0.0.
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Get posts.
 *
 * @since 1.0.0
 *
 * @return array it will return post ids array.
 */
function devsoul_psbsp_all_prd_wit_var() {

	$args = array(
		'post_type' => array( 'product', 'product_variation' ),
		'post_status' => 'publish',
		'numberposts' => -1,
		'return' => 'ids',
		'type' => array( 'simple', 'variation' ),

	);
	$pros = wc_get_products($args);
	return $pros;
}

function devsoul_psbsp_get_tab_and_section() {
	$tab = array(
		'tab' => isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'graph',
		'section' => isset($_GET['section']) ? sanitize_text_field(wp_unslash($_GET['section'])) : 'all_prd',
		'admin_url' => admin_url('admin.php?page=devsoul_psbsp_setting'),
	);

	return $tab;
}

/**
 * This function will all order ids.
 *
 * @since 1.0.0
 *
 * @param array $order_dates_arr array of selected dates .
 * @return array it will return order ids.
 */
function devsoul_psbsp_all_orders( $order_dates_arr ) {
	$order_statuses = get_option('devsoul_psbsp_order_status_field') ? (array) get_option('devsoul_psbsp_order_status_field') : 'any';
	$data = new WP_Query(
		array(
			'post_type' => 'shop_order',
			'post_status' => $order_statuses,
			'posts_per_page' => -1,
			'fields' => 'ids',
			'orderby' => 'meta_value_num',
			'order' => 'ASC',
		)
	);
	$order_ids_arr = array();

	if ($data->have_posts()) {

		$total_order_id = $data->posts;

		$selected_field_id_arr = explode(',', get_option('devsoul_psbsp_apply_costs_on_a_selected_order_field'));

		foreach ($total_order_id as $current_order_id) {

			$order_obj = wc_get_order($current_order_id);

			$created_date = gmdate('Y-m-d', strtotime($order_obj->get_date_created()));

			if (strtotime($created_date) < strtotime($order_dates_arr['start_date']) || strtotime($created_date) > strtotime($order_dates_arr['end_date'])) {

				continue;

			}

			if (!empty(get_option('devsoul_psbsp_apply_costs_on_all_orders_field')) || !empty(get_post_meta((int) $current_order_id, 'devsoul_psbsp_plugin_is_activate', true))) {

				$order_ids_arr[] = $current_order_id;

			}
			if (empty(get_post_meta((int) $current_order_id, 'devsoul_psbsp_plugin_is_activate', true)) && in_array((string) $current_order_id, $selected_field_id_arr, true)) {

				$order_ids_arr[] = $current_order_id;
			}
		}
	}

	$order_ids_arr = array_unique($order_ids_arr);
	return $order_ids_arr;
}

/**
 * This function will return product ids of selected category.
 *
 * @since 1.0.0
 *
 * @param string $prod_categories_id array of selected dates .
 * @return array it will return all product ids of selected category id.
 */
function devsoul_psbsp_get_prd_of_selected_cat( $prod_categories_id ) {
	$data = new WP_Query(
		array(
			'post_type' => array( 'product', 'product_variation' ),
			'post_status' => 'any',
			'posts_per_page' => -1,
			'fields' => 'ids',
			'tax_query' => array(
				array(
					'taxonomy' => 'product_cat',
					'terms' => $prod_categories_id,
				),
			),
		)
	);
	return $data->posts;
}

/**
 * This function will return product ids of selected category.
 *
 * @since 1.0.0
 *
 * @param string $product_id selected specific product id .
 * @param array  $order_paramenters array of selected order parameter .
 * @return array it will return current product sales detail.
 */
function devsoul_psbsp_get_product_sales( $product_id, $order_paramenters ) {

	$date_query = array(
		array(
			'after' => $order_paramenters['start_date'] . ' 00:00:00',
			'before' => $order_paramenters['end_date'] . ' 23:59:59',
			'inclusive' => true,
		),
	);
	$args = array(
		'status' => $order_paramenters['order_status'],
		'limit' => -1,
		'return' => 'ids',
		'date_query' => $date_query,
	);

	$sales_detail = array(
		'purchase_qty' => 0,
		'sub_total' => 0,
		'total_tax' => 0,
		'total' => 0,
		'shipping_total' => 0,
		'shipping_tax' => 0,
	);
	if (isset($order_paramenters['country'])) {
		$args['billing_country'] = $order_paramenters['country'];
	}
	if (isset($order_paramenters['country'])) {
		$args['billing_country'] = $order_paramenters['country'];
	}
	if (isset($order_paramenters['customer'])) {
		$args['customer'] = $order_paramenters['customer'];
	}

	$all_orders = wc_get_orders($args);

	foreach ($all_orders as $key => $current_order_id) {
		$current_order = wc_get_order($current_order_id);

		if ($current_order) {

			if (!empty($order_paramenters['city']) && !str_contains(strtoupper($order_paramenters['city']), strtoupper($current_order->get_billing_city()))) {

				continue;
			}
			if (!empty($order_paramenters['postcode']) && !str_contains(strtoupper($order_paramenters['postcode']), strtoupper($current_order->get_billing_postcode()))) {

				continue;
			}


			foreach ($current_order->get_items() as $item_id => $item_object) {


				if ($product_id == $item_object->get_product_id() || $product_id == $item_object->get_variation_id()) {

					$total = $sales_detail['total'];
					$total_tax = $sales_detail['total_tax'];
					$purchase_qty = $sales_detail['purchase_qty'];
					$sub_total = $sales_detail['sub_total'];
					$shipping_total = $sales_detail['shipping_total'];
					$shipping_tax = $sales_detail['shipping_tax'];


					// set values.
					$sales_detail['total'] = $total + $item_object->get_total();
					$sales_detail['total_tax'] = $total_tax + $item_object->get_total_tax();
					$sales_detail['purchase_qty'] = $purchase_qty + $item_object->get_quantity();
					$sales_detail['sub_total'] = $sub_total + $item_object->get_subtotal();

				}
			}
			$sales_detail['shipping_total'] += $current_order->get_total_shipping();
			$sales_detail['shipping_tax'] += wc_format_decimal($current_order->get_shipping_tax(), 2);
		}

	}

	return $sales_detail;
}

function devsoul_psbsp_get_country_full_name( $country_code ) {

	$all_countries = new WC_Countries();
	$all_countries = (array) $all_countries->get_countries();

	return isset($all_countries[ $country_code ]) ? $all_countries[ $country_code ] : '';
}

function devsoul_psbsp_get_state_full_name( $country_code = '', $state_code = '' ) {

	$wc_countries = new WC_Countries();
	$states = $wc_countries->get_states($country_code);
	return isset($states[ $state_code ]) ? $states[ $state_code ] : '';
}
function devsoul_psbsp_custom_array_filter( $filters = array() ) {
	$filters = array_filter((array) $filters, function ( $current_value, $current_key ) {
		return ( '' !== $current_value && '' !== $current_key );
	}, ARRAY_FILTER_USE_BOTH);


	return $filters;
}

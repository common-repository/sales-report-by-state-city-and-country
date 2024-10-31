<?php
/**
 * File Name: class-devsoul-psbsp-ajax.php.
 * Description: Here we have some ajax functions.
 *
 * @package   sales-report-by-state-city-and-country.
 * @version   1.0.0.
 */

/**
 * WordPress Check.
 *
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}
class Devsoul_Psbsp_Ajax {



	public function __construct() {
		add_action('wp_ajax_update_value_exported', array( $this, 'update_value_exported' ));
		add_action('wp_ajax_devsoul_psbsp_product_search', array( $this, 'devsoul_psbsp_product_search' ));
		add_action('wp_ajax_devsoul_psbsp_category_search', array( $this, 'devsoul_psbsp_category_search' ));
		add_action('wp_ajax_devsoul_psbsp_customer_search', array( $this, 'devsoul_psbsp_customer_search' ));
		add_action('wp_ajax_devsoul_send_email_with_sales_detail', array( $this, 'devsoul_send_email_with_sales_detail' ));
		add_action('wp_ajax_change_order_status_of_selected_order', array( $this, 'change_order_status_of_selected_order' ));
		add_action('wp_ajax_ct_psbsp_show_graph', array( $this, 'ct_psbsp_show_graph' ));
	}
	public function update_value_exported() {
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : 0;

		if (!wp_verify_nonce($nonce, 'devsoul_psbsp_files_nonce')) {

			wp_die(esc_html__('Failed Security check!', 'product-sales-report'));
		}
		if (isset($_POST['order_ids_array'])) {
			$order_ids_array = sanitize_meta('', $_POST['order_ids_array'], '');
			$order_ids_array = devsoul_psbsp_custom_array_filter($order_ids_array);
			foreach ($order_ids_array as $order_id) {

				update_post_meta($order_id, 'already_exported', 'Exported');
			}
			wp_send_json(array( 'success' => true ));
		}
	}
	public function devsoul_psbsp_product_search() {
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : 0;

		if (!wp_verify_nonce($nonce, 'devsoul_psbsp_files_nonce')) {

			wp_die(esc_html__('Failed Security check!', 'product-sales-report'));
		}

		$pro = isset($_POST['q']) && '' !== $_POST['q'] ? sanitize_text_field(wp_unslash($_POST['q'])) : '';

		$data_array = array( array( '0', 'Select Products' ) );
		$args = array(
			'post_type' => array( 'product', 'product_variation' ),
			'post_status' => 'publish',
			'numberposts' => 100,
			's' => $pro,
			'type' => array( 'simple', 'variation' ),

		);
		$pros = wc_get_products($args);

		if (!empty($pros)) {
			foreach ($pros as $proo) {
				$title = ( mb_strlen($proo->get_name()) > 50 ) ? mb_substr($proo->get_name(), 0, 49) . '...' : $proo->get_name();
				$data_array[] = array( $proo->get_id(), $title ); // array( Post ID, Post Title ).
			}
		}
		echo wp_json_encode($data_array);
		die();
	}
	public function devsoul_psbsp_category_search() {
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : 0;

		if (!wp_verify_nonce($nonce, 'devsoul_psbsp_files_nonce')) {

			wp_die(esc_html__('Failed Security check!', 'product-sales-report'));
		}

		$pro = isset($_POST['q']) && '' !== $_POST['q'] ? sanitize_text_field(wp_unslash($_POST['q'])) : '';

		$data_array = array();
		$orderby = 'name';
		$order = 'asc';
		$hide_empty = false;
		$all_cat_args = array(
			'taxonomy' => 'product_cat',
			'orderby' => $orderby,
			'order' => $order,
			'hide_empty' => $hide_empty,
			'name__like' => $pro,
		);
		$product_categories = get_terms($all_cat_args);
		if (!empty($product_categories)) {
			foreach ($product_categories as $proo) {
				$pro_front_post = ( mb_strlen($proo->name) > 50 ) ? mb_substr($proo->name, 0, 49) . '...' : $proo->name;
				$data_array[] = array( $proo->term_id, $pro_front_post ); // array( Post ID, Post Title ).
			}
		}
		echo wp_json_encode($data_array);
		die();
	}
	public function devsoul_psbsp_customer_search() {
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : 0;

		if (!wp_verify_nonce($nonce, 'devsoul_psbsp_files_nonce')) {

			wp_die(esc_html__('Failed Security check!', 'product-sales-report'));
		}

		$pro = isset($_POST['q']) && !empty($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
		$all_users = get_users(
			array(
				'search' => '*' . $pro . '*',
				'search_columns' => array( 'user_login', 'user_email', 'display_name' ),
			)
		);
		foreach ($all_users as $user) {
			$pro_front_post = ( mb_strlen($user->display_name) > 50 ) ? mb_substr($user->display_name, 0, 49) . '...' : $user->display_name;
			$data_array[] = array( $user->ID, $pro_front_post ); // array( Post ID, Post Title ).
		}

		echo wp_json_encode($data_array);
		die();
	}
	public function change_order_status_of_selected_order() {
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : 0;

		if (!wp_verify_nonce($nonce, 'devsoul_psbsp_files_nonce')) {

			wp_die(esc_html__('Failed Security check!', 'product-sales-report'));
		}
	}
	public function ct_psbsp_show_graph() {
		$nonce = isset($_POST['nonce']) ? sanitize_text_field(wp_unslash($_POST['nonce'])) : 0;
		if (!wp_verify_nonce($nonce, 'devsoul_psbsp_files_nonce')) {

			wp_die(esc_html__('Failed Security check!', 'product-sales-report'));
		}

		$url = isset($_POST['url']) ? sanitize_url($_POST['url']) : '';

		$url_components = parse_url($url);

		$selected_data_for_graph = array();

		if (isset($url_components['query'])) {
			parse_str($url_components['query'], $selected_data_for_graph);
		}

		$selected_cities = isset($selected_data_for_graph['selected_cities']) && !empty($selected_data_for_graph['selected_cities']) ? trim(sanitize_text_field($selected_data_for_graph['selected_cities'])) : '';

		$postcode = isset($selected_data_for_graph['postcode']) && !empty($selected_data_for_graph['postcode']) ? sanitize_text_field($selected_data_for_graph['postcode']) : '';

		$selected_roles = isset($selected_data_for_graph['roles']) && !empty($selected_data_for_graph['roles']) ? explode(',', sanitize_text_field($selected_data_for_graph['roles'])) : array();
		$selected_roles = (array) $selected_roles;

		$selected_customer = isset($selected_data_for_graph['customer']) && !empty($selected_data_for_graph['customer']) ? explode(',', sanitize_text_field($selected_data_for_graph['customer'])) : array();
		$selected_customer = (array) $selected_customer;

		$order_statuses = isset($selected_data_for_graph['order_status']) && !empty($selected_data_for_graph['order_status']) ? explode(',', sanitize_text_field($selected_data_for_graph['order_status'])) : array( 'any' );
		$order_statuses = (array) $order_statuses;

		$selected_countries = isset($selected_data_for_graph['selected_countries']) && !empty($selected_data_for_graph['selected_countries']) ? explode(',', sanitize_text_field($selected_data_for_graph['selected_countries'])) : array();
		$selected_countries = (array) $selected_countries;

		$select_shipping_methods = isset($selected_data_for_graph['select_shipping_methods']) && !empty($selected_data_for_graph['select_shipping_methods']) ? explode(',', sanitize_text_field($selected_data_for_graph['select_shipping_methods'])) : array();
		$select_shipping_methods = (array) $select_shipping_methods;

		$select_payment_methods = isset($selected_data_for_graph['select_payment_methods']) && !empty($selected_data_for_graph['select_payment_methods']) ? explode(',', sanitize_text_field($selected_data_for_graph['select_payment_methods'])) : array();
		$select_payment_methods = (array) $select_payment_methods;

		$selected_state = isset($selected_data_for_graph['selected_state']) && !empty($selected_data_for_graph['selected_state']) ? explode(',', sanitize_text_field($selected_data_for_graph['selected_state'])) : array();
		$selected_state = (array) $selected_state;


		$date_type = isset($selected_data_for_graph['date_type']) && !empty($selected_data_for_graph['date_type']) ? sanitize_text_field($selected_data_for_graph['date_type']) : 'this_year';
		$selected_prod = isset($selected_data_for_graph['selected_product']) && !empty($selected_data_for_graph['selected_product']) ? sanitize_text_field($selected_data_for_graph['selected_product']) : 0;

		$start_date = gmdate('Y-01-01');
		$end_date = gmdate('Y-m-d');

		if ('last_month' == $date_type) {
			$start_date = gmdate('Y-m-d', strtotime('first day of previous month', strtotime(gmdate('Y-m-d'))));
			$end_date = gmdate('Y-m-d', strtotime('Last day of previous month', strtotime(gmdate('Y-m-d'))));

		}
		if ('this_month' == $date_type) {

			$start_date = gmdate('Y-m-d', strtotime('first day of this month', strtotime(gmdate('Y-m-d'))));
			$end_date = gmdate('Y-m-d');

		}

		if ('last_7_days' == $date_type) {
			$start_date = gmdate('Y-m-d', strtotime('- 7 days', strtotime(gmdate('Y-m-d'))));
			$end_date = gmdate('Y-m-d');

		}

		if ('custom_Date' == $date_type) {

			$start_date = isset($selected_data_for_graph['start_date']) && !empty($selected_data_for_graph['start_date']) ? sanitize_text_field($selected_data_for_graph['start_date']) : '2000-01-01';

			$end_date = isset($selected_data_for_graph['end_date']) && !empty($selected_data_for_graph['end_date']) ? sanitize_text_field($selected_data_for_graph['end_date']) : gmdate('Y-m-d');


		}
		$exported_or_not_exported = isset($selected_data_for_graph['ct-psbsp-export-type']) ? $selected_data_for_graph['ct-psbsp-export-type'] : 'All_Exported_and_not_Exported';
		$order_statuses = devsoul_psbsp_custom_array_filter($order_statuses);
		$selected_countries = devsoul_psbsp_custom_array_filter($selected_countries);
		$selected_state = devsoul_psbsp_custom_array_filter($selected_state);


		$date_query = array(
			array(
				'after' => $start_date . ' 00:00:00',
				'before' => $end_date . ' 23:59:59',
				'inclusive' => true,
			),
		);
		$all_roles_ids = new WP_User_Query(
			array(
				'role__in' => $selected_roles,
				'fields' => 'ID',
			)
		);

		$customer_ids = !empty($selected_roles) ? $all_roles_ids->results : array();

		$customer_ids = array_merge($customer_ids, $selected_customer);

		$args = array(
			'status' => $order_statuses,
			'limit' => -1,
			'return' => 'ids',
			'date_query' => $date_query,
			'order' => 'ASC',
		);


		if (isset($selected_countries) && count($selected_countries) >= 1) {
			$args['billing_country'] = $selected_countries;
		}
		if (isset($select_shipping_methods) && count($select_shipping_methods) >= 1) {
			$args['meta_query'] = array(
				'relation' => 'AND',
				array(
					'key' => '_shipping_method',
					'value' => $select_shipping_methods,
					'compare' => 'IN',
				),
			);
		}
		if (isset($select_payment_methods) && count($select_payment_methods) >= 1) {
			$args['payment_method'] = $select_payment_methods;
		}
		if (isset($selected_state) && count($selected_state) >= 1) {
			$args['billing_state'] = $selected_state;
		}
		if (isset($customer_ids) && count($customer_ids) >= 1) {
			$args['customer'] = $customer_ids;
		}

		if ('Already_Exported' == $exported_or_not_exported) {
			$args['meta_key'] = 'already_exported';
			$args['meta_value'] = 'Exported';
			$args['meta_compare'] = '=';

		}
		if ('Not_Exported' == $exported_or_not_exported) {
			$args['meta_key'] = 'already_exported';
			$args['meta_value'] = '';
			$args['meta_compare'] = '=';
		}
		$all_orders = wc_get_orders($args);

		$sale_by_cities = array();
		$sate_by_state = array();

		foreach ($all_orders as $order_id) {
			$order = wc_get_order($order_id);
			if ($order) {

				if (!empty($selected_cities) && ( !str_contains(strtoupper($selected_cities), strtoupper($order->get_billing_city())) && !in_array(strtoupper($order->get_billing_city()), explode(',', strtoupper($selected_cities))) )) {
					continue;
				}
				if (!empty($postcode) && !str_contains(strtoupper($postcode), strtoupper($order->get_billing_postcode()))) {
					continue;
				}
				if (count($selected_state) >= 1 && !in_array(strtoupper($order->get_billing_state()), $selected_state)) {
					continue;

				}
				$orders_item_ids = array();

				foreach ($order->get_items() as $order_meta) {
					$orders_item_ids[] = $order_meta->get_product_id();
					$orders_item_ids[] = $order_meta->get_variation_id();

				}
				$orders_item_ids = devsoul_psbsp_custom_array_filter($orders_item_ids);

				if (!empty($selected_prod) && !in_array($selected_prod, $orders_item_ids)) {
					continue;
				}


				$country = method_exists($order, 'get_billing_country') ? $order->get_billing_country() : '';
				$state = method_exists($order, 'get_billing_country') ? $order->get_billing_state() : '';

				$sale_by_cities[ $country ] = isset($sale_by_cities[ $country ]) ? (float) $sale_by_cities[ $country ] + $order->get_total() : $order->get_total();
				$sate_by_state[ $country . ',' . $state ] = !isset($sate_by_state[ $country . ',' . $state ]) ? (float) $sate_by_state[ $country . ',' . $state ] + $order->get_total() : $order->get_total();

			}
		}


		$countries_sales = array( array( 'Country', 'Sales' ) );
		foreach ($sale_by_cities as $country_key => $total) {
			$country_full_name = devsoul_psbsp_get_country_full_name($country_key);
			$countries_sales[] = array( $country_full_name, (float) $total );

		}
		$state_array = array( array( 'City ', 'Sales' ) );

		foreach ($sate_by_state as $country_and_state_key => $total) {

			$country_and_state_key_array = explode(',', $country_and_state_key);
			$country_key = current($country_and_state_key_array);
			$country_full_name = devsoul_psbsp_get_country_full_name(current($country_and_state_key_array));
			$state_full_name = devsoul_psbsp_get_state_full_name(current($country_and_state_key_array), end($country_and_state_key_array));
			$state_array[] = array( $state_full_name . ' ' . $country_full_name, (float) $total );

		}


		wp_send_json(
			array(
				'country_array' => $countries_sales,
				'state_array' => $state_array,
			)
		);
		wp_die();
	}
	public function devsoul_send_email_with_sales_detail() {
		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		if (isset($_POST['ct_export_sales_csv'])) {

			$whole_data = sanitize_meta('', $_POST['ct_export_sales_csv'], '');
			$file_name = isset($_POST['file_name']) ? sanitize_text_field($_POST['file_name']) : 0;
			$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

			if (!wp_verify_nonce($nonce, 'devsoul_psbsp_files_nonce')) {
				wp_die(esc_html__('Security Violated !', 'cloud_tech_psbsp'));
			}


			header('Pragma: public');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', false);
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment;filename="sales-report-by-order.csv";');
			header('Content-Transfer-Encoding: binary');

			$file = fopen(DEVS_PSBSCCP_PLUGIN_DIR . 'assets/export-csv/export.csv', 'w+');

			foreach ($whole_data as $current_row_detail) {
				if (!is_array($current_row_detail)) {
					continue;
				}
				$row_data = array();
				foreach ($current_row_detail as $value) {

					$row_data[] = $value;
				}

				fputcsv($file, $row_data);


			}

			echo wp_kses_post(file_get_contents(DEVS_PSBSCCP_PLUGIN_DIR . 'assets/export-csv/export.csv'));

			fclose($file);


			$array_data = (array) get_option('devsoul_psbsp_email_csv_sales_by_order');

			$recipient = isset($array_data['recipient']) && !empty($array_data['recipient']) ? explode(',', $array_data['recipient']) : array();

			$subject = isset($array_data['subject']) && !empty($array_data['subject']) ? $array_data['subject'] : 'Sale By Order';

			$heading = isset($array_data['heading']) && !empty($array_data['heading']) ? $array_data['heading'] : 'Sale By Order';

			$message = isset($array_data['additional_content']) && !empty($array_data['additional_content']) ? $array_data['additional_content'] : 'Sale By Order';

			if (isset($array_data['enable']) && !empty($array_data['enable']) && str_contains($file_name, 'order')) {

				$attachment = ( DEVS_PSBSCCP_PLUGIN_DIR . 'assets/export-csv/export.csv' );
				$success = wp_mail($recipient, $subject, $message, $headers, $attachment);
			}

			$array_data = (array) get_option('devsoul_psbsp_email_csv_sales_by_product');

			$recipient = isset($array_data['recipient']) && !empty($array_data['recipient']) ? explode(',', $array_data['recipient']) : array();

			$subject = isset($array_data['subject']) && !empty($array_data['subject']) ? $array_data['subject'] : 'Sale By Order';

			$heading = isset($array_data['heading']) && !empty($array_data['heading']) ? $array_data['heading'] : 'Sale By Order';

			$message = isset($array_data['additional_content']) && !empty($array_data['additional_content']) ? $array_data['additional_content'] : 'Sale By Order';

			if (isset($array_data['enable']) && !empty($array_data['enable']) && str_contains($file_name, 'product')) {

				$attachment = ( DEVS_PSBSCCP_PLUGIN_DIR . 'assets/export-csv/export.csv' );
				$success = wp_mail($recipient, $subject, $message, $headers, $attachment);
			}
			exit;

		}
	}
}

new Devsoul_Psbsp_Ajax();

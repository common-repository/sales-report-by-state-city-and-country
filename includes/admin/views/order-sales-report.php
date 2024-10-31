<?php
/**
 * File Name: order-sales-report.php.
 * Description: Here we have order sales detail.
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

$posts_per_page = !empty(get_option('ct_psbsp_product_per_page_for_product_sales')) ? get_option('ct_psbsp_product_per_page_for_product_sales') : 20;
$get_tab_info = devsoul_psbsp_get_tab_and_section();
$active_tab = $get_tab_info['tab'];
$active_section = $get_tab_info['section'];
$width = '75%';

$selected_cities = isset($_GET['selected_cities']) && !empty($_GET['selected_cities']) ? trim(sanitize_text_field($_GET['selected_cities'])) : '';
$postcode = isset($_GET['postcode']) && !empty($_GET['postcode']) ? sanitize_text_field($_GET['postcode']) : '';
$selected_roles = isset($_GET['roles']) && !empty($_GET['roles']) ? explode(',', sanitize_text_field($_GET['roles'])) : array();
$selected_customer = isset($_GET['customer']) && !empty($_GET['customer']) ? explode(',', sanitize_text_field($_GET['customer'])) : array();

$order_statuses = isset($_GET['order_status']) && !empty($_GET['order_status']) ? explode(',', sanitize_text_field($_GET['order_status'])) : array( 'any' );

$selected_cat = isset($_GET['selected_cat']) && !empty($_GET['selected_cat']) ? explode(',', sanitize_text_field($_GET['selected_cat'])) : array();

$selected_prod = isset($_GET['selected_product']) && !empty($_GET['selected_product']) ? explode(',', sanitize_text_field($_GET['selected_product'])) : array();

$selected_countries = isset($_GET['selected_countries']) && !empty($_GET['selected_countries']) ? explode(',', sanitize_text_field($_GET['selected_countries'])) : array();
$select_shipping_methods = isset($_GET['select_shipping_methods']) && !empty($_GET['select_shipping_methods']) ? explode(',', sanitize_text_field($_GET['select_shipping_methods'])) : array();
$select_payment_methods = isset($_GET['select_payment_methods']) && !empty($_GET['select_payment_methods']) ? explode(',', sanitize_text_field($_GET['select_payment_methods'])) : array();


$selected_state = isset($_GET['selected_state']) && !empty($_GET['selected_state']) ? explode(',', sanitize_text_field($_GET['selected_state'])) : array();


$date_type = isset($_GET['date_type']) && !empty($_GET['date_type']) ? sanitize_text_field($_GET['date_type']) : 'this_year';


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

	$start_date = isset($_GET['start_date']) && !empty($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : '2000-01-01';

	$end_date = isset($_GET['end_date']) && !empty($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : gmdate('Y-m-d');


}
$exported_or_not_exported = isset($_GET['ct-psbsp-export-type']) ? sanitize_text_field($_GET['ct-psbsp-export-type']) : 'All_Exported_and_not_Exported';


$order_statuses = devsoul_psbsp_custom_array_filter($order_statuses);
$selected_countries = devsoul_psbsp_custom_array_filter($selected_countries);
$selected_state = devsoul_psbsp_custom_array_filter($selected_state);
$current_page_num = isset($_GET['current_page_num']) && !empty($_GET['current_page_num']) ? sanitize_text_field($_GET['current_page_num']) : 1;

$orders_per_page = get_option('ct_psbsp_product_per_page_for_order_sales') ? get_option('ct_psbsp_product_per_page_for_order_sales') : 20;
$date_query = array(
	array(
		'after' => $start_date . ' 00:00:00',
		'before' => $end_date . ' 23:59:59',
		'inclusive' => true,
	),
);

$all_roles_ids = get_users(
	array(
		'role__in' => $selected_roles,
		'fields' => 'ID',
	)
);

$customer_ids = !empty($selected_roles) ? $all_roles_ids->results : array();


$customer_ids = array_merge($customer_ids, $selected_customer);

$args = array(
	'status' => $order_statuses,
	'limit' => 300,
	'return' => 'ids',
	'date_query' => $date_query,
	'order' => 'ASC',
	'paged' => $current_page_num,
);

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
if (isset($selected_countries) && count($selected_countries) >= 1) {
	$args['billing_country'] = $selected_countries;
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
$ignore_order_id = array();

$obj_countries = new WC_Countries();
wp_nonce_field('ct_psbsp_nonce', 'ct_psbsp_nonce');
?>
<div class="ct-psbsp-main-sale-product">
	<div class="ct-psbsp-tabs">
		<ul>
			<li>
				<font><input type="radio" name="ct_select_date" class="ct_select_date" value="this_year" 
				<?php
				if ('this_year' == $date_type) {
					?>
					checked <?php } ?>>
					<span>
						<?php echo esc_html__('Year', 'cloud_tech_psbsp'); ?>
					</span>
				</font>
			</li>
			<li>
				<font>
					<input type="radio" name="ct_select_date" class="ct_select_date" value="last_month" 
					<?php
					if ('last_month' == $date_type) {
						?>
						checked <?php } ?>>
					<span>
						<?php echo esc_html__('Last Month', 'cloud_tech_psbsp'); ?>
					</span>
				</font>
			</li>
			<li>
				<font>
					<input type="radio" name="ct_select_date" class="ct_select_date" value="this_month" 
					<?php
					if ('this_month' == $date_type) {
						?>
						checked <?php } ?>>
					<span>
						<?php echo esc_html__('This Month', 'cloud_tech_psbsp'); ?>
					</span>
				</font>
			</li>
			<li>
				<font>
					<input type="radio" name="ct_select_date" class="ct_select_date last_7_days" value="last_7_days"
						<?php
						if ('last_7_days' == $date_type) {
							?>
							checked <?php } ?>>
					<span>
						<?php echo esc_html__('Last 7 Days', 'cloud_tech_psbsp'); ?>
					</span>
				</font>
			</li>
			<li class="select_date_start_end_li">
				<font>
					<font>
						<input type="radio" name="ct_select_date" value="custom_Date"
							class="ct_select_date select_date_start_end" <?php if ('custom_Date' == $date_type) { ?>
								checked <?php } ?>>
						<span>
							<?php echo esc_html__('Custom Date', 'cloud_tech_psbsp'); ?>
						</span>
					</font>

				</font>
			</li>
			<li style="display:none;">
				<font>
					<div>
						<input type="date"
							value="<?php echo esc_attr(isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : ''); ?>"
							class="ct-psbsp-start-date">
						<font>
							<?php echo esc_html__('-', 'cloud_tech_psbsp'); ?>
						</font>
						<input type="date" name=""
							value="<?php echo esc_attr(isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : ''); ?>"
							class="ct-psbsp-end-date">
					</div>
				</font>
			</li>
			<li>
				<font>
					<i
						class="ct-psbsp-order-filter-btn ct-psbsp-filter-btn ct-psbsp-show button button-primary button-large">
						<?php echo esc_html__('Filter', 'cloud_tech_psbsp'); ?>
					</i>
				</font>
			</li>
			<li>
				<font>
					<input type="submit" name="ct_export_sales_csv" value="Export CSV"
						class=" button button-primary button-large">
				</font>
			</li>
			<li>
				<font>
					<span>
						<?php echo esc_html__('Change Order Status', 'cloud_tech_psbsp'); ?>
					</span>
					<select name="change_order_status" class="change_order_status">
						<?php
						foreach (wc_get_order_statuses() as $order_key => $order_label) {
							?>
							<option value="<?php echo esc_attr($order_key); ?>">
								<?php echo esc_attr($order_label); ?>
							</option>
						<?php } ?>
					</select>
					<i class="button button-primary button-large ct-psbsp-change-order-status">
						<?php echo esc_html__('Change', 'cloud_tech_psbsp'); ?>
					</i>
				</font>
			</li>
		</ul>
		<div class="ct-psbsp-export-btn-div">
			<a style="display:none" href="" class="ct-psbsp-export-btn button button-primary button-large">
				<?php echo esc_html__('Export CSV', 'cloud_tech_psbsp'); ?>
			</a>
		</div>
	</div>

	<div class="ct-psbsp-table-and-search-filed">
		<div class="ct-psbsp-main-table-data">
			<div class="ct-psbsp-form-field">
				<div>
					<h5>
						<?php echo esc_html__('Export Type', 'cloud_tech_psbsp'); ?>
					</h5>
					<select style="width: 100%;" class="ct-psbsp-export-type ct-psbsp-live-search">
						<?php foreach (array( 'All_Exported_and_not_Exported', 'Already_Exported', 'Not_Exported' ) as $value) : ?>
							<option value="<?php echo esc_attr($value); ?>" <?php selected($value, $exported_or_not_exported); ?>>
								<?php echo esc_attr(ucwords(str_replace('_', ' ', $value))); ?>
							</option>
						<?php endforeach ?>
					</select>

				</div>
			</div>
			<div class="ct-psbsp-form-field">
				<div>
					<h5>
						<?php
						echo esc_html__('Select Roles', 'cloud_tech_psbsp');
						global $wp_roles;
						?>
					</h5>
					<select style="width: 100%;" class="ct-psbsp-select-roles ct-psbsp-live-search" multiple>
						<?php foreach ($wp_roles->get_names() as $key => $value) : ?>
							<option value="<?php echo esc_attr($key); ?>" <?php if (in_array($key, $selected_roles)) : ?>
									selected <?php endif ?>>
								<?php echo esc_attr($value); ?>
							</option>
						<?php endforeach ?>
					</select>

				</div>
			</div>
			<div class="ct-psbsp-form-field">
				<div>
					<h5>
						<?php
						echo esc_html__('Select Cutomers', 'cloud_tech_psbsp');
						global $wp_roles;
						?>
					</h5>
					<select style="width: 100%;" class="ct-psbsp-select-user ct_psbsp_customer_search" multiple>
						<?php
						foreach ($selected_customer as $user_id) {
							if (!empty($user_id) && get_user_by('ID', $user_id)) {

								$user = get_user_by('ID', $user_id);
								?>
								<option value="<?php echo esc_attr($user->ID); ?>" selected>
									<?php echo esc_attr($user->display_name); ?>
								</option>
								<?php
							}

						}
						?>
					</select>
				</div>
			</div>

			<div class="ct-psbsp-form-field">
				<div>
					<h5>
						<?php echo esc_html__('Select Order Status', 'cloud_tech_psbsp'); ?>
					</h5>
					<select style="width: 100%;" class="ct-psbsp-select-order-status ct-psbsp-live-search" multiple>
						<?php foreach (wc_get_order_statuses() as $key => $value) : ?>
							<option value="<?php echo esc_attr($key); ?>" <?php if (in_array($key, $order_statuses)) : ?>
									selected <?php endif ?>>
								<?php echo esc_attr($value); ?>
							</option>
						<?php endforeach ?>
					</select>

				</div>
			</div>
			<?php if (class_exists('WC_Payment_Gateways')) { ?>
				<div class="ct-psbsp-form-field">
					<div>
						<h5>
							<?php echo esc_html__('Select Payment Method', 'cloud_tech_psbsp'); ?>
						</h5>
						<?php
						$payment_gateways = WC()->payment_gateways->payment_gateways();
						?>
						<select style="width: 100%;" class="ct-psbsp-select-payment-method ct-psbsp-live-search" multiple>
							<?php foreach ($payment_gateways as $gateway) : ?>
								<option value="<?php echo esc_attr($gateway->id); ?>" 
														  <?php
															if (in_array($gateway->id, $select_payment_methods)) :
																?>
									   selected <?php endif ?>>
									<?php echo esc_attr($gateway->get_title()); ?>
								</option>
							<?php endforeach ?>
						</select>

					</div>
				</div>
			<?php } ?>
			<div class="ct-psbsp-form-field">
				<div>
					<h5>
						<?php echo esc_html__('Select Shipping Method', 'cloud_tech_psbsp'); ?>
					</h5>
					<?php
					$shipping_gateways = WC()->shipping->get_shipping_methods();
					?>
					<select style="width: 100%;" class="ct-psbsp-select-shipping-method ct-psbsp-live-search" multiple>
						<?php foreach ($shipping_gateways as $method_id => $method) : ?>
							<option value="<?php echo esc_attr($method_id); ?>" 
													  <?php
														if (in_array($method_id, $select_shipping_methods)) :
															?>
								   selected <?php endif ?>>
								<?php echo esc_attr($method->get_method_title()); ?>
							</option>
						<?php endforeach ?>
					</select>

				</div>
			</div>

			<div class="ct-psbsp-form-field">
				<div>
					<h5>
						<?php echo esc_html__('Select Countries', 'cloud_tech_psbsp'); ?>
					</h5>
					<select style="width: 100%;" class="ct-psbsp-select-countries ct-psbsp-live-search" multiple>

						<?php foreach ($obj_countries->get_countries() as $key => $value) : ?>

							<option value="<?php echo esc_attr($key); ?>" 
													  <?php
														if (in_array($key, $selected_countries)) {
															?>
								   selected <?php } ?>>
								<?php echo esc_attr($value); ?>
							</option>

						<?php endforeach ?>

					</select>

				</div>
			</div>

			<?php if (count($selected_countries) >= 1) { ?>
				<div class="ct-psbsp-form-field">
					<div>
						<h5>
							<?php echo esc_html__('Select State', 'cloud_tech_psbsp'); ?>
						</h5>
						<select style="width: 100%;" class="ct-psbsp-select-state ct-psbsp-live-search" multiple>

							<?php
							foreach ($selected_countries as $current_country) {

								if (!is_array($obj_countries->get_states($current_country))) {
									continue;
								}

								foreach ($obj_countries->get_states($current_country) as $key => $value) {

									if (empty($value)) {
										continue;
									}
									?>
									<option value="<?php echo esc_attr($key); ?>" 
															  <?php
																if (in_array($key, $selected_state)) {
																	?>
											selected <?php } ?>>
										<?php echo esc_attr($value); ?>
									</option>
									<?php
								}
							}
							?>
						</select>

					</div>
				</div>
			<?php } ?>
			<?php if (!empty($selected_state)) { ?>
				<div class="ct-psbsp-form-field">
					<div>
						<h5>
							<?php echo esc_html__('Enter City', 'cloud_tech_psbsp'); ?>
						</h5>
						<textarea class="ct-psbsp-enter-city"><?php echo esc_attr($selected_cities); ?></textarea>
						<p>
							<?php echo esc_html__('Enter multiple cities with comma separated'); ?>
						</p>
					</div>
				</div>
			<?php } ?>
			<?php if (!empty($selected_state)) { ?>
				<div class="ct-psbsp-form-field">
					<div>
						<h5>
							<?php echo esc_html__('Enter Postcode', 'cloud_tech_psbsp'); ?>
						</h5>
						<textarea class="ct-psbsp-enter-post-code"><?php echo esc_attr($postcode); ?></textarea>
						<p>
							<?php echo esc_html__('Enter multiple cities with comma separated'); ?>
						</p>
					</div>
				</div>
			<?php } ?>

		</div>
		<div class="ct-psbsp-prouct-table" style="width: <?php echo esc_attr($width); ?>;">

			<?php

			$sum_of_subtotal = 0;
			$sum_of_tax_total = 0;
			$sum_of_shipping_tax = 0;
			$sum_of_shipping_total = 0;
			$sum_of_total = 0;
			$sum_of_refunded_amount = 0;
			$sum_of_all_coupon_amount = 0;

			$all_orders = devsoul_psbsp_custom_array_filter($all_orders);

			?>
			<table style="width: 100%;"
				class="wp-list-table widefat fixed striped table-view-list af-purchased-product-detail-table ct-psbsp-order-infor-table ct-psbsp-order-sales"
				data-file_name="order-sales-data<?php echo esc_attr(gmdate('F-j-Y') . current_time('mysql')); ?>.csv">
				<thead>
					<tr>
						<td>
							<input type="checkbox" class="select_all_order" />
						</td>
						<td>
							<?php echo esc_html__('Order Id', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Order Date', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Billing Name', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Billing Country', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Billing State', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Billing City', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Zip Code', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Subtotal', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Tax', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Shipping', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Refund', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Coupon', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Total', 'cloud_tech_psbsp'); ?>
						</td>
						<td>
							<?php echo esc_html__('Action', 'cloud_tech_psbsp'); ?>
						</td>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ($all_orders as $current_order_id) {

						$current_order = wc_get_order($current_order_id);

						if (!$current_order) {
							$ignore_order_id[] = $current_order_id;
							continue;
						}
						if (!empty($selected_cities) && ( !str_contains(strtoupper($selected_cities), strtoupper($current_order->get_billing_city())) && !in_array(strtoupper($current_order->get_billing_city()), explode(',', strtoupper($selected_cities))) )) {
							$ignore_order_id[] = $current_order_id;
							continue;

						}
						if (!empty($postcode) && !str_contains(strtoupper($postcode), strtoupper($current_order->get_billing_postcode()))) {
							$ignore_order_id[] = $current_order_id;
							continue;
						}

						$sum_of_subtotal += $current_order->get_subtotal();
						$sum_of_tax_total += (float) $current_order->get_total_tax();
						$sum_of_shipping_total += $current_order->get_shipping_total();
						$sum_of_total += $current_order->get_total();
						$country_full_name = method_exists($current_order, 'get_billing_country') ? devsoul_psbsp_get_country_full_name($current_order->get_billing_country()) : '';
						$state_full_name = method_exists($current_order, 'get_billing_country') ? devsoul_psbsp_get_state_full_name($current_order->get_billing_country(), $current_order->get_billing_state()) : '';
						$sum_of_refunded_amount += method_exists($current_order, 'get_total_refunded') ? $current_order->get_total_refunded() : 0;

						// Get applied coupons for the order
						$applied_coupons = $current_order->get_coupon_codes();

						// Initialize a variable to store the total coupon amount
						$total_coupon_amount = 0;

						// Loop through applied coupons and get their amounts
						foreach ($applied_coupons as $coupon_code) {
							// Get the coupon object
							$coupon = new WC_Coupon($coupon_code);

							// Get the coupon amount
							$coupon_amount = $coupon->get_amount();

							// Add the coupon amount to the total
							$total_coupon_amount += $coupon_amount;
						}
						$sum_of_all_coupon_amount += $total_coupon_amount;

						?>
						<tr>
							<td>
								<input type="checkbox" class="select_current_order"
									value="<?php echo esc_attr($current_order->get_id()); ?>">
							</td>
							<td>
								<?php echo esc_attr($current_order->get_id()); ?>
								<?php echo esc_attr(get_post_meta($current_order_id, 'already_exported', true)); ?>

							</td>
							<td>
								<?php echo esc_attr(gmdate('F j Y', strtotime($current_order->get_date_created()))); ?>
							</td>
							<td>
								<?php echo esc_attr(method_exists($current_order, 'get_billing_first_name') ? $current_order->get_billing_first_name() . ' ' . $current_order->get_billing_first_name() : ''); ?>
							</td>
							<td>
								<?php echo esc_attr($country_full_name); ?>
							</td>
							<td>
								<?php echo esc_attr($state_full_name); ?>
							</td>
							<td>
								<?php echo esc_attr(method_exists($current_order, 'get_billing_city') ? $current_order->get_billing_city() : ''); ?>
							</td>
							<td>
								<?php echo esc_attr(method_exists($current_order, 'get_billing_postcode') ? $current_order->get_billing_postcode() : ''); ?>
							</td>
							<td>
								<?php echo wp_kses_post(str_replace(',', '', wc_price(method_exists($current_order, 'get_subtotal') ? $current_order->get_subtotal() : 0))); ?>
							</td>
							<td>
								<?php echo wp_kses_post(str_replace(',', '', wc_price(method_exists($current_order, 'get_total_tax') ? $current_order->get_total_tax() : 0))); ?>
							</td>
							<td>
								<?php echo wp_kses_post(str_replace(',', '', wc_price(method_exists($current_order, 'get_shipping_total') ? $current_order->get_shipping_total() : 0))); ?>
							</td>
							<td>
								<?php echo wp_kses_post(str_replace(',', '', wc_price(method_exists($current_order, 'get_total_refunded') ? $current_order->get_total_refunded() : 0))); ?>
							</td>
							<td>
								<?php echo wp_kses_post(str_replace(',', '', wc_price($total_coupon_amount))); ?>
							</td>
							<td>
								<?php echo wp_kses_post(str_replace(',', '', wc_price(method_exists($current_order, 'get_total') ? $current_order->get_total() : 0))); ?>

								<?php
								// Get billing details
								$billing_first_name = method_exists($current_order, 'get_billing_first_name') ? $current_order->get_billing_first_name() : '';
								$billing_last_name = method_exists($current_order, 'get_billing_last_name') ? $current_order->get_billing_last_name() : '';
								$billing_email = method_exists($current_order, 'get_billing_email') ? $current_order->get_billing_email() : '';
								$billing_phone = method_exists($current_order, 'get_billing_phone') ? $current_order->get_billing_phone() : '';
								$billing_address_1 = method_exists($current_order, 'get_billing_address_1') ? $current_order->get_billing_address_1() : '';
								$billing_address_2 = method_exists($current_order, 'get_billing_address_2') ? $current_order->get_billing_address_2() : '';
								$billing_city = method_exists($current_order, 'get_billing_city') ? $current_order->get_billing_city() : '';
								$billing_country = method_exists($current_order, 'get_billing_country') ? devsoul_psbsp_get_country_full_name($current_order->get_billing_country()) : '';
								$billing_postcode = method_exists($current_order, 'get_billing_postcode') ? $current_order->get_billing_postcode() : '';
								$billing_method = method_exists($current_order, 'get_payment_method') ? $current_order->get_payment_method() : '';

								// Get shipping details
								$shipping_first_name = method_exists($current_order, 'get_shipping_first_name') ? $current_order->get_shipping_first_name() : '';
								$shipping_last_name = method_exists($current_order, 'get_shipping_last_name') ? $current_order->get_shipping_last_name() : '';
								$shipping_address_1 = method_exists($current_order, 'get_shipping_address_1') ? $current_order->get_shipping_address_1() : '';
								$shipping_address_2 = method_exists($current_order, 'get_shipping_address_2') ? $current_order->get_shipping_address_2() : '';
								$shipping_city = method_exists($current_order, 'get_shipping_city') ? $current_order->get_shipping_city() : '';
								$shipping_country = method_exists($current_order, 'get_shipping_country') ? devsoul_psbsp_get_country_full_name($current_order->get_shipping_country()) : '';
								$shipping_postcode = method_exists($current_order, 'get_shipping_postcode') ? $current_order->get_shipping_postcode() : '';

								?>
								<input type="hidden" class="ct-psbsp-order-complete-detail"
									data-coupon_amount="<?php echo esc_attr($total_coupon_amount); ?>"
									data-order_id="<?php echo esc_attr($current_order_id); ?>"
									data-shipping_method="<?php echo esc_attr($billing_first_name); ?>"
									data-billing_first_name="<?php echo esc_attr($billing_first_name); ?>"
									data-billing_last_name="<?php echo esc_attr($billing_last_name); ?>"
									data-billing_email="<?php echo esc_attr($billing_email); ?>"
									data-billing_phone="<?php echo esc_attr($billing_phone); ?>"
									data-billing_address_1="<?php echo esc_attr($billing_address_1); ?>"
									data-billing_address_2="<?php echo esc_attr($billing_address_2); ?>"
									data-billing_city="<?php echo esc_attr($billing_city); ?>"
									data-billing_country="<?php echo esc_attr($billing_country); ?>"
									data-billing_postcode="<?php echo esc_attr($billing_postcode); ?>"
									data-billing_method="<?php echo esc_attr($billing_method); ?>"
									data-shipping_first_name="<?php echo esc_attr($shipping_first_name); ?>"
									data-shipping_last_name="<?php echo esc_attr($shipping_last_name); ?>"
									data-shipping_address_1="<?php echo esc_attr($shipping_address_1); ?>"
									data-shipping_city="<?php echo esc_attr($shipping_city); ?>"
									data-shipping_country="<?php echo esc_attr($shipping_country); ?>"
									data-shipping_postcode="<?php echo esc_attr($shipping_postcode); ?>"
									data-refunded_amount="<?php echo esc_attr(method_exists($current_order, 'get_total_refunded') ? $current_order->get_total_refunded() : 0); ?>"
									data-subtotal="<?php echo esc_attr(method_exists($current_order, 'get_subtotal') ? $current_order->get_subtotal() : 0); ?>"
									data-tax_total="<?php echo esc_attr(method_exists($current_order, 'get_total_tax') ? $current_order->get_total_tax() : 0); ?>"
									data-shipping_total="<?php echo esc_attr(method_exists($current_order, 'get_shipping_total') ? $current_order->get_shipping_total() : 0); ?>"
									data-total="<?php echo esc_attr(method_exists($current_order, 'get_total') ? $current_order->get_total() : 0); ?>">
							</td>
							<td>
								<a href="<?php echo esc_url(get_edit_post_link($current_order_id)); ?>" class="af-tips"><i
										class="fa-solid fa fa-pencil"></i><span>
										<?php echo esc_html__('Edit Product', 'cloud_tech_psbsp'); ?>
									</span></a>
							</td>
						</tr>

					<?php } ?>
				</tbody>
			</table>

			<div class="ct-psbsp-total-of-selected-table">
				<table>

					<tr class="ct-psbsp-subtotal">
						<th>
							<?php echo esc_html__('Subtotal', 'cloud_tech_psbsp'); ?>
						</th>
						<td class="ct-psbsp-subtotal-td">
							<?php echo wp_kses_post(str_replace(',', '', wc_price($sum_of_subtotal))); ?>
						</td>
					</tr>
					<tr class="ct-psbsp-total-tax">
						<th>
							<?php echo esc_html__('Total Tax', 'cloud_tech_psbsp'); ?>
						</th>
						<td class="ct-psbsp-total-tax-td">
							<?php echo wp_kses_post(str_replace(',', '', wc_price($sum_of_tax_total))); ?>
						</td>
					</tr>
					<tr class="ct-psbsp-shipping-total">
						<th>
							<?php echo esc_html__('Order Shipping Total', 'cloud_tech_psbsp'); ?>
						</th>
						<td class="ct-psbsp-shipping-total-td">
							<?php echo wp_kses_post(str_replace(',', '', wc_price($sum_of_shipping_total))); ?>
						</td>
					</tr>
					<tr class="ct-psbsp-coupon-total">
						<th>
							<?php echo esc_html__('Order Coupon Total', 'cloud_tech_psbsp'); ?>
						</th>
						<td class="ct-psbsp-coupon-total-td">
							<?php echo wp_kses_post(str_replace(',', '', wc_price($sum_of_all_coupon_amount))); ?>
						</td>
					</tr>
					<tr class="ct-psbsp-refunded-total">
						<th>
							<?php echo esc_html__('Order Refund Total', 'cloud_tech_psbsp'); ?>
						</th>
						<td class="ct-psbsp-refunded-total-td">
							<?php echo wp_kses_post(str_replace(',', '', wc_price($sum_of_refunded_amount))); ?>
						</td>
					</tr>
					<tr class="ct-psbsp-total">
						<th>
							<?php echo esc_html__('Total', 'cloud_tech_psbsp'); ?>
						</th>
						<td class="ct-psbsp-total-td">
							<?php echo wp_kses_post(str_replace(',', '', wc_price($sum_of_total))); ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<?php

unset($args['paged']);
$args['limit'] = -1;
$args['exclude'] = $ignore_order_id;

$all_orders = wc_get_orders($args);
$total_pages = count($all_orders) / 300;
$check_float_number = $total_pages - (int) $total_pages;

if ($check_float_number >= 0.1 && $check_float_number <= 0.5) {
	$total_pages += .49;
}

$args = array(
	'total' => round($total_pages),
	'current' => $current_page_num,
	'end_size' => 1,
	'mid_size' => 2,
	'prev_next' => true,
	'prev_text' => __('« Previous'),
	'next_text' => __('Next »'),
	'type' => 'plain',
	'format' => '?current_page_num=%#%', // Use this line if pagination is for static front page
);

// Display the pagination links
echo wp_kses_post(paginate_links($args));
?>
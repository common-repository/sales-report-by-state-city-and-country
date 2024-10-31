<?php

// Add custom column to user table
function devsoul_modify_user_columns( $column_headers ) {
	$column_headers['total_order_amount'] = 'Total Spend Amount';
	return $column_headers;
}

add_filter('manage_users_columns', 'devsoul_modify_user_columns');
// Display total order amount in the custom column
function devsoul_show_user_total_order_amount( $value, $column_name, $user_id ) {
	if ('total_order_amount' == $column_name) {
		$user_orders = wc_get_orders(
			array(
				'customer' => $user_id,
				'return' => 'ids',
				'status' => array_keys(wc_get_order_statuses()),
			)
		); // Assuming you have a function to get user orders
		$total_amount = 0;

		foreach ($user_orders as $current_order_id) {
			$current_order = wc_get_order($current_order_id);
			$total_amount += floatval($current_order->get_total());
		}

		return wc_price($total_amount);
	}
	return $value;
}
add_filter('manage_users_custom_column', 'devsoul_show_user_total_order_amount', 10, 3);

// Add a custom column to the Products table.
add_filter('manage_edit-product_columns', 'devsoul_add_custom_product_column');
function devsoul_add_custom_product_column( $columns ) {
	$columns['total_profit'] = 'Total Sales / Total Quantity';
	return $columns;
}

// Populate the custom column with data.
add_action('manage_product_posts_custom_column', 'devsoul_custom_product_column_content', 10, 2);
function devsoul_custom_product_column_content( $column, $post_id ) {
	if ('total_profit' == $column) {

		devsoul_product_detail($post_id);
	}
}
add_action('woocommerce_product_options_general_product_data', 'devsoul_add_custom_general_fields');

function devsoul_add_custom_general_fields() {
	global $woocommerce, $post;

	?>
	<div class="options_group">
		<?php
		devsoul_product_detail($post->ID);
		?>
	</div>
	<?php
}
add_action('woocommerce_product_after_variable_attributes', 'devsoul_add_custom_variation_fields', 10, 3);

function devsoul_add_custom_variation_fields( $loop, $variation_data, $variation ) {
	?>
	<div class="options_group">
		<?php
		devsoul_product_detail($variation->ID);
		?>
	</div>
	<?php
}
function devsoul_product_detail( $post_id ) {
	global $wpdb;

	$results = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT *FROM {$wpdb->prefix}wc_order_product_lookup WHERE product_id = %d OR variation_id = %d",
			$post_id,
			$post_id
		)
	);
	$results = json_decode(json_encode($results), true);



	$new_total = 0;
	$total_qty = 0;

	foreach ($results as $current_order_detail) {
		if (isset($current_order_detail['product_net_revenue'])) {
			$new_total += (float) $current_order_detail['product_net_revenue'];
		}
		$total_qty += isset($current_order_detail['product_qty']) ? (float) $current_order_detail['product_qty'] : 0;
	}
	echo wp_kses(wc_price($new_total), wp_kses_allowed_html());
	echo esc_attr(' ( ' . $total_qty . ' ) ');
	?>
	<div class="devsoul-order-detail">
		<button id="showOrdersPopup"
			class="button"><?php echo esc_html__('Show Orders', 'dproduct-sales-report'); ?></button>
		<div id="ordersPopup" class="orders-popup-content" style="display:none;">
			<span class="orders-popup-close">&times;</span>
			<div class="orders-popup-content-table">
				<h4><?php echo esc_html__('Product Order Detail', 'dproduct-sales-report'); ?></h4>

				<table class="widefat fixed striped">
					<thead>
						<tr>
							<th><?php echo esc_html__('Order Id', 'dproduct-sales-report'); ?> </th>
							<th><?php echo esc_html__('Order Date', 'dproduct-sales-report'); ?> </th>
							<th><?php echo esc_html__('Order Status', 'dproduct-sales-report'); ?> </th>
							<th><?php echo esc_html__('Quantity', 'dproduct-sales-report'); ?> </th>
							<th><?php echo esc_html__('Total', 'dproduct-sales-report'); ?> </th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($results as $current_order_detail) {

							$order_id = isset($current_order_detail['order_id']) ? (float) $current_order_detail['order_id'] : 0;
							$date_created = isset($current_order_detail['date_created']) ? $current_order_detail['date_created'] : 0;
							$product_qty = isset($current_order_detail['product_qty']) ? (float) $current_order_detail['product_qty'] : 0;
							$product_net_revenue = isset($current_order_detail['product_net_revenue']) ? (float) $current_order_detail['product_net_revenue'] : 0;

							?>
							<tr>
								<td>
									<a href="<?php echo esc_url(get_edit_post_link($order_id)); ?>">
										<?php echo esc_attr($order_id); ?>
									</a>
								</td>
								<td><?php echo esc_attr(gmdate(' j-F-Y  H:m:s ', strtotime($date_created))); ?></td>
								<td><?php echo esc_attr(wc_get_order_status_name(get_post_status($order_id))); ?></td>
								<td><?php echo esc_attr($product_qty); ?></td>
								<td><?php echo wp_kses(wc_price($product_net_revenue), wp_kses_allowed_html()); ?></td>
							</tr>

							<?php

						}
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<?php
}

// Add custom section to the user edit page
add_action('edit_user_profile', 'devsoul_custom_user_orders_section');

function devsoul_custom_user_orders_section( $user ) {

	// Get all orders for the user
	$all_orders = wc_get_orders(
		array(
			'customer_id' => $user->ID,
			'status' => 'any',
			'limit' => -1,
		)
	);

	?>
	<h2><?php echo esc_html__('User Orders', 'dproduct-sales-report'); ?> </h2>
	<?php
	if (!$all_orders) {
		?>
		<p> <?php echo esc_html__('No orders found for this user.', 'dproduct-sales-report'); ?> </p>
		<?php
		return;
	}

	// Initialize sums
	$sum_subtotal = 0;
	$sum_tax = 0;
	$sum_total = 0;
	?>
	<table class="widefat fixed striped">
		<thead>
			<tr>
				<th><?php echo esc_html__('Order Date', 'dproduct-sales-report'); ?> </th>
				<th><?php echo esc_html__('Order Status', 'dproduct-sales-report'); ?> </th>
				<th><?php echo esc_html__('Payment Method', 'dproduct-sales-report'); ?> </th>
				<th><?php echo esc_html__('Extra Fee', 'dproduct-sales-report'); ?> </th>
				<th><?php echo esc_html__('Refund Fee', 'dproduct-sales-report'); ?> </th>
				<th><?php echo esc_html__('Tax', 'dproduct-sales-report'); ?> </th>
				<th><?php echo esc_html__('Coupon Amount', 'dproduct-sales-report'); ?> </th>
				<th><?php echo esc_html__('Total', 'dproduct-sales-report'); ?> </th>
			</tr>
		</thead>
		<tbody>

			<?php
			foreach ($all_orders as $current_order) {
				$current_order_id = $current_order->get_id();
				$current_order_date = $current_order->get_date_created() ? $current_order->get_date_created()->date('Y-m-d H:i:s') : esc_html__('N/A', 'dproduct-sales-report');
				$current_order_status = wc_get_order_status_name($current_order->get_status());
				$payment_method = $current_order->get_payment_method_title();
				$extra_fee = 0; // Calculate extra fees if any
				$refund_fee = $current_order->get_total_refunded();
				$tax = $current_order->get_total_tax();
				$coupon_amount = $current_order->get_discount_total();
				$total = $current_order->get_total();

				// Sum calculations
				$sum_subtotal += $current_order->get_subtotal();
				$sum_tax += $tax;
				$sum_total += $total;

				?>
				<tr>
					<td><?php echo esc_html($current_order_date); ?> </td>
					<td><?php echo esc_html($current_order_status); ?> </td>
					<td><?php echo esc_html($payment_method); ?> </td>
					<td><?php echo wp_kses(wc_price($extra_fee), wp_kses_allowed_html()); ?> </td>
					<td><?php echo wp_kses(wc_price($refund_fee), wp_kses_allowed_html()); ?> </td>
					<td><?php echo wp_kses(wc_price($tax), wp_kses_allowed_html()); ?> </td>
					<td><?php echo wp_kses(wc_price($coupon_amount), wp_kses_allowed_html()); ?> </td>
					<td><?php echo wp_kses(wc_price($total), wp_kses_allowed_html()); ?> </td>
				</tr>

				<?php
			}

			?>
		</tbody>
	</table>


	<h2><?php echo esc_html__('Order Sums', 'dproduct-sales-report'); ?></h2>
	<table class="widefat fixed striped">
		<thead>
			<tr>
				<th><?php echo esc_html__('Sum of Subtotals', 'dproduct-sales-report'); ?> </th>
				<th><?php echo esc_html__('Sum of Taxes', 'dproduct-sales-report'); ?> </th>
				<th><?php echo esc_html__('Sum of Totals', 'dproduct-sales-report'); ?> </th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><?php echo wp_kses(wc_price($sum_subtotal), wp_kses_allowed_html()); ?> </td>
				<td><?php echo wp_kses(wc_price($sum_tax), wp_kses_allowed_html()); ?> </td>
				<td><?php echo wp_kses(wc_price($sum_total), wp_kses_allowed_html()); ?> </td>
			</tr>
		</tbody>
	</table>
	<?php
}

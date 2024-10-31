<?php
/**
 * File Name: product-sales-report.php.
 * Description: Here we have single product sales detail.
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
$postcode = isset($_GET['postcode']) && !empty($_GET['postcode']) ? sanitize_text_field($_GET['postcode']) : '';

$selected_cities = isset($_GET['selected_cities']) && !empty($_GET['selected_cities']) ? sanitize_text_field($_GET['selected_cities']) : '';

$order_statuses = isset($_GET['order_status']) && !empty($_GET['order_status']) ? explode(',', sanitize_text_field($_GET['order_status'])) : array( 'any' );

$selected_cat = isset($_GET['selected_cat']) && !empty($_GET['selected_cat']) ? explode(',', sanitize_text_field($_GET['selected_cat'])) : array();

$selected_prod = isset($_GET['selected_product']) && !empty($_GET['selected_product']) ? explode(',', sanitize_text_field($_GET['selected_product'])) : array();

$selected_countries = isset($_GET['selected_countries']) && !empty($_GET['selected_countries']) ? explode(',', sanitize_text_field($_GET['selected_countries'])) : array();

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
$selected_roles = isset($_GET['roles']) && !empty($_GET['roles']) ? explode(',', sanitize_text_field($_GET['roles'])) : array();
$selected_customer = isset($_GET['customer']) && !empty($_GET['customer']) ? explode(',', sanitize_text_field($_GET['customer'])) : array();

$customer_ids = !empty($selected_roles) ? get_users(
	array(
		'role__in' => $selected_roles,
		'fields' => 'ID',
	)
) : array();
$customer_ids = array_merge($customer_ids, $selected_customer);

$order_statuses = devsoul_psbsp_custom_array_filter($order_statuses);
$selected_prod = devsoul_psbsp_custom_array_filter($selected_prod);
$selected_cat = devsoul_psbsp_custom_array_filter($selected_cat);
$selected_countries = devsoul_psbsp_custom_array_filter($selected_countries);
$selected_state = devsoul_psbsp_custom_array_filter($selected_state);


$order_paramenters = array(
	'start_date' => $start_date,
	'end_date' => $end_date,
	'order_status' => $order_statuses,
	'country' => $selected_countries,
	'state' => $selected_state,
	'city' => $selected_cities,
	'postcode' => $postcode,
);

if (!empty($customer_ids) && count($customer_ids) >= 1) {

	// Get customer orders
	$order_paramenters['customer'] = $customer_ids;


}


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
					<span><?php echo esc_html__('Year', 'cloud_tech_psbsp'); ?></span>
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
					<span><?php echo esc_html__('This Month', 'cloud_tech_psbsp'); ?></span>
				</font>
			</li>
			<li>
				<font>
					<input type="radio" name="ct_select_date" class="ct_select_date last_7_days" value="last_7_days"
						<?php
						if ('last_7_days' == $date_type) {
							?>
							checked <?php } ?>>
					<span><?php echo esc_html__('Last 7 Days', 'cloud_tech_psbsp'); ?></span>
				</font>
			</li>
			<li class="select_date_start_end_li">
				<font>
					<font>
						<input type="radio" name="ct_select_date" value="custom_Date"
							class="ct_select_date select_date_start_end" <?php if ('custom_Date' == $date_type) { ?>
								checked <?php } ?>>
						<span><?php echo esc_html__('Custom Date', 'cloud_tech_psbsp'); ?></span>
					</font>

				</font>
			</li>
			<li style="display:none;">
				<font>
					<div>
						<input type="date"
							value="<?php echo esc_attr(isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : ''); ?>"
							class="ct-psbsp-start-date">
						<font><?php echo esc_html__('-', 'cloud_tech_psbsp'); ?></font>
						<input type="date" name=""
							value="<?php echo esc_attr(isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : ''); ?>"
							class="ct-psbsp-end-date">
					</div>
				</font>
			</li>
			<li>
				<font>
					<i
						class="ct-psbsp-product-filter-btn ct-psbsp-filter-btn ct-psbsp-show button button-primary button-large"><?php echo esc_html__('Filter', 'cloud_tech_psbsp'); ?></i>
				</font>
			</li>
			<li>
				<font>
					<input type="submit" name="ct_export_sales_csv" value="Export CSV"
						class=" button button-primary button-large">
				</font>
			</li>
		</ul>
		<div class="ct-psbsp-export-btn-div">
			<a style="display:none" href=""
				class="ct-psbsp-export-btn button button-primary button-large"><?php echo esc_html__('Export CSV', 'cloud_tech_psbsp'); ?>
			</a>
		</div>
	</div>

	<div class="ct-psbsp-table-and-search-filed">
		<div class="ct-psbsp-main-table-data">
			<?php if ('selected_prd' == $active_section) : ?>
				<div class="ct-psbsp-form-field">
					<div>
						<h5><?php echo esc_html__('Product Search', 'cloud_tech_psbsp'); ?></h5>
						<select style="width: 100%;" data-prd_or_cat="product" class="ct_psbsp_product_live_search"
							name="ct_psbsp_product_exclusion_list[]" multiple>
							<?php if (count($selected_prod) >= 1) : ?>
								<?php foreach ($selected_prod as $prd_id) : ?>

									<option value="<?php echo esc_attr($prd_id); ?>" selected>
										<?php echo esc_attr(wc_get_product($prd_id)->get_name()); ?>
									</option>

								<?php endforeach ?>
							<?php endif ?>
						</select>

					</div>
				</div>
			<?php endif ?>
			<?php if ('selected_cat' == $active_section) : ?>
				<div class="ct-psbsp-form-field">
					<div>
						<h5><?php echo esc_html__('Search Category', 'cloud_tech_psbsp'); ?></h5>

						<select style="width: 100%;" data-prd_or_cat="category" class="ct_psbsp_category_live_search"
							name="af_a_nd_s_m_category_exclusion[]" multiple>
							<?php if (count($selected_cat) >= 1) : ?>
								<?php foreach ($selected_cat as $current_cat_id) : ?>

									<option value="<?php echo esc_attr($current_cat_id); ?>" selected>
										<?php echo esc_attr(get_term($current_cat_id)->name); ?>
									</option>

								<?php endforeach ?>
							<?php endif ?>
						</select>
					</div>
				</div>
			<?php endif ?>
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
					<h5><?php echo esc_html__('Select Order Status', 'cloud_tech_psbsp'); ?></h5>
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

			<div class="ct-psbsp-form-field">
				<div>
					<h5><?php echo esc_html__('Select Countries', 'cloud_tech_psbsp'); ?></h5>
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
						<h5><?php echo esc_html__('Select State', 'cloud_tech_psbsp'); ?></h5>
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
				<div class="ct-psbsp-form-field" style=" display : block !important; ">
					<div>
						<h5><?php echo esc_html__('Enter City', 'cloud_tech_psbsp'); ?></h5>
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
						<h5><?php echo esc_html__('Enter Postcode', 'cloud_tech_psbsp'); ?></h5>
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

			$current_page_num = isset($_GET['current_page_num']) && !empty($_GET['current_page_num']) ? sanitize_text_field($_GET['current_page_num']) : 1;

			$args = array(
				'status' => 'publish',
				'return' => 'ids',
				'limit' => -1,
				'order' => 'ASC',
				'type' => array( 'simple', 'grouped', 'external', 'variable', 'variation' ),
				'page' => $current_page_num,
			);

			if (count($selected_cat) >= 1) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_cat',
						'field' => 'term_id',
						'terms' => $selected_cat,
						'operator' => 'IN',
					),
				);


			}

			if (count($selected_prod) >= 1) {

				$args['include'] = $selected_prod;

			}

			$products = new WC_Product_Query($args);

			$sum_of_subtotal = 0;
			$sum_of_tax_total = 0;
			$sum_of_shipping_tax = 0;
			$sum_of_shipping_total = 0;
			$sum_of_total = 0;

			if (( 'selected_prd' == $active_section && count($selected_prod) < 1 ) || ( 'selected_cat' == $active_section && count($selected_cat) < 1 )) {
				return;
			}

			?>
			<table
				class="ct-psbsp-prouct-table wp-list-table widefat fixed striped table-view-list af-purchased-product-detail-table ct-psbsp-order-infor-table ct-psbsp-order-sales"
				data-file_name="product-sales-data<?php echo esc_attr(gmdate('F-j-Y') . current_time('mysql')); ?>.csv"
				style="width: 100%;">
				<thead>
					<tr>
						<td><?php echo esc_html__('Product', 'cloud_tech_psbsp'); ?></td>
						<td><?php echo esc_html__('Purchase Qty', 'cloud_tech_psbsp'); ?></td>
						<td><?php echo esc_html__('Price', 'cloud_tech_psbsp'); ?></td>
						<td><?php echo esc_html__('Total Tax', 'cloud_tech_psbsp'); ?></td>
						<td><?php echo esc_html__('Subtotal', 'cloud_tech_psbsp'); ?></td>
						<td><?php echo esc_html__('Total', 'cloud_tech_psbsp'); ?></td>
						<td><?php echo esc_html__('Action', 'cloud_tech_psbsp'); ?></td>
					</tr>
				</thead>
				<tbody>
					<?php

					foreach ($products->get_products() as $current_products_id) {

						$product = wc_get_product($current_products_id);

						$product_id_or_var_id = 'variation' == $product->get_type() ? $product->get_parent_id() : $product->get_id();

						$product_sales_detail = (array) devsoul_psbsp_get_product_sales($current_products_id, $order_paramenters);
						$total = isset($product_sales_detail['total']) ? (int) $product_sales_detail['total'] : 0;
						$total_tax = isset($product_sales_detail['total_tax']) ? (int) $product_sales_detail['total_tax'] : 0;
						$purchase_qty = isset($product_sales_detail['purchase_qty']) ? (int) $product_sales_detail['purchase_qty'] : 0;
						$sub_total = isset($product_sales_detail['sub_total']) ? (int) $product_sales_detail['sub_total'] : 0;

						$sum_of_subtotal += $sub_total;
						$sum_of_tax_total += $total_tax;
						$sum_of_total += $total;

						?>
						<tr>
							<td><?php echo esc_attr($product->get_name()); ?></td>
							<td><?php echo esc_attr($purchase_qty); ?></td>
							<td><?php echo wp_kses_post(str_replace(',', '', wc_price($product->get_price()))); ?></td>
							<td><?php echo wp_kses_post(str_replace(',', '', wc_price($total_tax))); ?></td>
							<td><?php echo wp_kses_post(str_replace(',', '', wc_price($sub_total))); ?></td>
							<td><?php echo wp_kses_post(str_replace(',', '', wc_price($total))); ?></td>
							<td>
								<input type="hidden" class="ct-psbsp-order-complete-detail"
									data-subtotal="<?php echo esc_attr($sub_total); ?>"
									data-tax_total="<?php echo esc_attr($total_tax); ?>" data-shipping_total="0"
									data-total="<?php echo esc_attr($total); ?>">
								<a href="<?php echo esc_url(get_edit_post_link($product_id_or_var_id)); ?>"
									class="af-tips"><i
										class="fa-solid fa fa-pencil"></i><span><?php echo esc_html__('Edit Product', 'cloud_tech_psbsp'); ?></span></a>
								<a href="<?php echo esc_url(get_post_permalink($product_id_or_var_id)); ?>"
									class="af-tips"><i
										class="fa fa-eye"></i><span><?php echo esc_html__('View Product', 'cloud_tech_psbsp'); ?></span></a>
							</td>
						</tr>

					<?php } ?>

				</tbody>
			</table>

			<div class="ct-psbsp-total-of-selected-table">
				<table>

					<tr class="ct-psbsp-subtotal">
						<th><?php echo esc_html__('Subtotal', 'cloud_tech_psbsp'); ?></th>
						<td class="ct-psbsp-subtotal-td">
							<?php echo wp_kses_post(str_replace(',', '', wc_price($sum_of_subtotal))); ?>
						</td>
					</tr>
					<tr class="ct-psbsp-total-tax">
						<th><?php echo esc_html__('Total Tax', 'cloud_tech_psbsp'); ?></th>
						<td class="ct-psbsp-total-tax-td">
							<?php echo wp_kses_post(str_replace(',', '', wc_price($sum_of_tax_total))); ?>
						</td>
					</tr>

					<tr class="ct-psbsp-total">
						<th><?php echo esc_html__('Total', 'cloud_tech_psbsp'); ?></th>
						<td class="ct-psbsp-total-td">
							<?php echo wp_kses_post(str_replace(',', '', wc_price($sum_of_total))); ?>
						</td>
					</tr>
				</table>
			</div>

		</div>
	</div>
</div>
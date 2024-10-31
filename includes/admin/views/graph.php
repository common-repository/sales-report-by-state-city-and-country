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
					<i class="ct-psbsp-show-graph ct-psbsp-show button button-primay">
						<?php echo esc_html__('Show Graph', 'cloud_tech_psbsp'); ?>
					</i>
				</font>
			</li>
		</ul>

	</div>

	<div class="ct-psbsp-table-and-search-filed">
		<div class="ct-psbsp-main-table-data">
			<div class="ct-psbsp-form-field">
				<div>
					<h5><?php echo esc_html__('Product Search', 'cloud_tech_psbsp'); ?></h5>
					<select style="width: 100%;" data-prd_or_cat="product" class="ct_psbsp_product_live_search"
						name="ct_psbsp_product_exclusion_list">
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

		</div>
		<div class="ct-psbsp-prouct-table" style="width: <?php echo esc_attr($width); ?>;">
			<div id="sale_report_by_country" style="width: 100%; height: 500px;"></div>
			<br>
			<div id="sale_report_by_state" style="width: 100%; height: 500px;"></div>

		</div>
	</div>
</div>
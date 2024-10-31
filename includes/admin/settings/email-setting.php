<?php
/**
 * File Name: email.php.
 * Description: Here we have email setting.
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
	'devsoul_psbsp_email_settings_fields',
	'', // Title to be displayed on the administration page.
	'', // Callback used to render the description of the section.
	'devsoul_psbsp_email_settings_sections'
);
add_settings_field(
	'devsoul_psbsp_email_csv_sales_by_product',
	esc_html__('Email CSV Sale by product ', 'product-sales-report'), // The label.
	'devsoul_psbsp_email_csv_sales_by_product',
	'devsoul_psbsp_email_settings_sections', // The page on which this option will be displayed.
	'devsoul_psbsp_email_settings_fields'
);
register_setting(
	'devsoul_psbsp_email_settings_fields',
	'devsoul_psbsp_email_csv_sales_by_product'
);
add_settings_field(
	'devsoul_psbsp_email_csv_sales_by_order',
	esc_html__('Email CSV Sale by Order', 'product-sales-report'), // The label.
	'devsoul_psbsp_email_csv_sales_by_order',
	'devsoul_psbsp_email_settings_sections', // The page on which this option will be displayed.
	'devsoul_psbsp_email_settings_fields'
);
register_setting(
	'devsoul_psbsp_email_settings_fields',
	'devsoul_psbsp_email_csv_sales_by_order'
);



function devsoul_psbsp_email_csv_sales_by_product() {
	$array_data = (array) get_option('devsoul_psbsp_email_csv_sales_by_product');
	$enable = isset($array_data['enable']) && !empty($array_data['enable']) ? $array_data['enable'] : '';
	$recipient = isset($array_data['recipient']) && !empty($array_data['recipient']) ? $array_data['recipient'] : '';
	$subject = isset($array_data['subject']) && !empty($array_data['subject']) ? $array_data['subject'] : 'Sale By Products';

	$additional_content = isset($array_data['additional_content']) && !empty($array_data['additional_content']) ? $array_data['additional_content'] : 'Sale By Products';


	?>
	<table>
		<tr>
			<th>
				<label><?php echo esc_html__(' Enable Checkbox', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<input type="checkbox" class="ct-psbsp-email-csv-sales-by-product"
					name="devsoul_psbsp_email_csv_sales_by_product[enable]" value="yes" <?php if (!empty($enable)) : ?>
						checked <?php endif ?>>
			</td>
		</tr>
		<tr>
			<th>
				<label><?php echo esc_html__('Recipient(s)', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<textarea
					name="devsoul_psbsp_email_csv_sales_by_product[recipient]"><?php echo esc_attr($recipient); ?></textarea>
				<p><?php echo esc_html__('Enter multiple email with comma separated.', 'cloud_tech_psbsp'); ?></p>
			</td>

		</tr>
		<tr>
			<th>
				<label><?php echo esc_html__('Subject', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<input type="text" name="devsoul_psbsp_email_csv_sales_by_product[subject]"
					value="<?php echo esc_attr($subject); ?>">
			</td>
		</tr>
		<tr>
			<th>

				<label><?php echo esc_html__('Additional Content', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<textarea
					name="devsoul_psbsp_email_csv_sales_by_product[additional_content]"><?php echo esc_attr($additional_content); ?></textarea>
			</td>
		</tr>
	</table>
	<?php
}

function devsoul_psbsp_email_csv_sales_by_order() {
	$array_data = (array) get_option('devsoul_psbsp_email_csv_sales_by_order');
	$enable = isset($array_data['enable']) && !empty($array_data['enable']) ? $array_data['enable'] : '';
	$recipient = isset($array_data['recipient']) && !empty($array_data['recipient']) ? $array_data['recipient'] : '';
	$subject = isset($array_data['subject']) && !empty($array_data['subject']) ? $array_data['subject'] : 'Sale By Order';

	$additional_content = isset($array_data['additional_content']) && !empty($array_data['additional_content']) ? $array_data['additional_content'] : 'Sale By Order';


	?>
	<table>
		<tr>
			<th>
				<label><?php echo esc_html__(' Enable Checkbox', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<input type="checkbox" name="devsoul_psbsp_email_csv_sales_by_order[enable]" value="yes" 
				<?php
				if (!empty($enable)) :
					?>
					checked <?php endif ?>>
			</td>
		</tr>
		<tr>
			<th>
				<label><?php echo esc_html__('Recipient(s)', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<textarea
					name="devsoul_psbsp_email_csv_sales_by_order[recipient]"><?php echo esc_attr($recipient); ?></textarea>
				<p><?php echo esc_html__('Enter multiple email with comma separated.', 'cloud_tech_psbsp'); ?></p>
			</td>

		</tr>
		<tr>
			<th>
				<label><?php echo esc_html__('Subject', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<input type="text" name="devsoul_psbsp_email_csv_sales_by_order[subject]"
					value="<?php echo esc_attr($subject); ?>">
			</td>
		</tr>
		<tr>
			<th>

				<label><?php echo esc_html__('Additional Content', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<textarea
					name="devsoul_psbsp_email_csv_sales_by_order[additional_content]"><?php echo esc_attr($additional_content); ?></textarea>

			</td>
		</tr>
	</table>
	<?php
}

add_settings_field(
	'devsoul_psbsp_auto_send_stats_on_mail',
	esc_html__('Auto Send Stats on Mail', 'product-sales-report'), // The label.
	'devsoul_psbsp_auto_send_stats_on_mail',
	'devsoul_psbsp_email_settings_sections', // The page on which this option will be displayed.
	'devsoul_psbsp_email_settings_fields'
);
register_setting(
	'devsoul_psbsp_email_settings_fields',
	'devsoul_psbsp_auto_send_stats_on_mail'
);

function devsoul_psbsp_auto_send_stats_on_mail() {
	$array_data = (array) get_option('devsoul_psbsp_auto_send_stats_on_mail');
	$enable = isset($array_data['enable']) && !empty($array_data['enable']) ? $array_data['enable'] : '';
	$duration = isset($array_data['duration']) && !empty($array_data['duration']) ? $array_data['duration'] : 'hours';
	$duration_number = isset($array_data['duration_number']) && !empty($array_data['duration_number']) ? $array_data['duration_number'] : '1';

	$selected_countries = isset($array_data['selected_countries']) && !empty($array_data['selected_countries']) ? (array) $array_data['selected_countries'] : array();

	$order_statuses = isset($array_data['order_status']) && !empty($array_data['order_status']) ? (array) $array_data['order_status'] : array();

	?>
	<table>
		<tr>
			<th>
				<label><?php echo esc_html__(' Enable Checkbox', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<input type="checkbox" name="devsoul_psbsp_auto_send_stats_on_mail[enable]" value="yes" 
				<?php
				if (!empty($enable)) :
					?>
					checked <?php endif ?>>
			</td>
		</tr>
		<tr>
			<th>
				<label><?php echo esc_html__('Select time duration', 'cloud_tech_psbsp'); ?></label>
			</th>

			<td>

				<i><?php echo esc_html__('Duration', 'cloud_tech_psbsp'); ?></i>

				<select name="devsoul_psbsp_auto_send_stats_on_mail[duration]">
					<option <?php selected($duration, 'hours'); ?> value="hours">
						<?php echo esc_html__('Hours', 'cloud_tech_psbsp'); ?>
					</option>
					<option <?php selected($duration, 'days'); ?> value="days">
						<?php echo esc_html__('Days', 'cloud_tech_psbsp'); ?>
					</option>
					<option <?php selected($duration, 'week'); ?> value="week">
						<?php echo esc_html__('Week', 'cloud_tech_psbsp'); ?>
					</option>
					<option <?php selected($duration, 'month'); ?> value="month">
						<?php echo esc_html__('Month', 'cloud_tech_psbsp'); ?>
					</option>
				</select>
				<br>
				<i><?php echo esc_html__('Time Duration', 'cloud_tech_psbsp'); ?></i>

				<input type="number" name="devsoul_psbsp_auto_send_stats_on_mail[duration_number]" min="1"
					value="<?php echo esc_attr($duration_number); ?>">

			</td>

		</tr>
		<tr>
			<th>

				<label><?php echo esc_html__('Order status', 'cloud_tech_psbsp'); ?></label>
			</th>
			<td>
				<select style="width:100%;" multiple name="devsoul_psbsp_auto_send_stats_on_mail[order_status][]"
					class="ct-psbsp-live-search">
					<?php
					foreach (wc_get_order_statuses() as $current_order_key => $order_label) {
						?>
						<option value="<?php echo esc_attr($current_order_key); ?>" 
												  <?php
													if (in_array($current_order_key, $order_statuses)) {
														?>
							   selected <?php } ?>>
							<?php echo esc_attr($order_label); ?>
						</option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>
				<?php echo esc_html__('Select Countries', 'cloud_tech_psbsp'); ?>
			</th>
			<td>
				<select style="width: 100%;" name="devsoul_psbsp_auto_send_stats_on_mail[selected_countries][]"
					class="ct-psbsp-select-countries ct-psbsp-live-search" multiple>

					<?php
					$obj_countries = new WC_Countries();
					foreach ($obj_countries->get_countries() as $key => $value) :
						?>

						<option value="<?php echo esc_attr($key); ?>" 
												  <?php
													if (in_array($key, $selected_countries)) {
														?>
								selected <?php } ?>>
							<?php echo esc_attr($value); ?>
						</option>

					<?php endforeach ?>

				</select>
			</td>
		</tr>
	</table>
	<?php
}
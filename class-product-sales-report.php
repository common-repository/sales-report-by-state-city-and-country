<?php
/**
 * Plugin Name: Sales Report By State , City and Country
 * Description: Get your whole store sales report with sales by country,city,state,,specific order status,specific customer ,specific product,specific category!.
 * Plugin URI: https://devsoul.store/product/sales-report-by-state-city-and-country/
 * Version: 1.0.0
 * Author: devsoul
 * Developed By: devsoul
 * Author URI: https://devsoul.store/
 * Support: https://devsoul.store/
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 * Text Domain: devsoul_psbscac_text_d
 * WC requires at least: 3.0.9
 * WC tested up to: 8.*.*
 *
 * @package product-sales-report
 */

/**
 * WordPress Check.
 *
 * @since 1.0.0
 */
if (!defined('ABSPATH')) {
	exit();
}

/**
 * Class exist Check.
 *
 * @since 1.0.0
 */
if (!class_exists('Product_Sales_Report')) {


	/**
	 * Main class
	 */
	class Product_Sales_Report {
	



		/**
		 * Class constructor starts
		 */
		public function __construct() {
			// Define Global Constants.
			$this->devsoul_psbsp_global_constents_vars();
			add_action('init', array( $this, 'devsoul_psbsp_include_file' ));
			add_action('plugins_loaded', array( $this, 'devsoul_psbsp_init' ));
			add_action('admin_head', array( $this, 'devsoul_psbsp_maybe_flush_w3tc_cache' ));
			add_action('before_woocommerce_init', array( $this, 'devsoul_psbsp_hops_compatibility' ));
		}

		public function devsoul_psbsp_maybe_flush_w3tc_cache() {
			wp_cache_flush();

			global $wp_object_cache;

			return $wp_object_cache->flush();
		}
		public function devsoul_psbsp_hops_compatibility() {
			if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
			}
		}

		/**
		 * Woocommerce enable check.
		 *
		 * @since 1.0.0
		 */
		public function devsoul_psbsp_init() {

			// Check the installation of WooCommerce module if it is not a multi site.
			if (!is_multisite() && !in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true)) {

				add_action('admin_notices', array( $this, 'devsoul_psbsp_check_wocommerce' ));
			}
		}

		/**
		 * Deactivate check.
		 *
		 * @since 1.0.0
		 */
		public function devsoul_psbsp_check_wocommerce() {
			// Deactivate the plugin.
			deactivate_plugins(__FILE__);
			?>
			<div id="message" class="error">
				<p>
					<strong>
						<?php esc_html_e('Sales Report By State , City and Country plugin is inactive. WooCommerce plugin must be active in order to activate it.', 'product-sales-report'); ?>
					</strong>
				</p>
			</div>
			<?php
		}

		/**
		 * Includes Files.
		 *
		 * @since 1.0.0
		 */
		public function devsoul_psbsp_include_file() {

			if (defined('WC_PLUGIN_FILE')) {

				add_action('wp_loaded', array( $this, 'devsoul_psbsp_register_text_domain' ));

				include DEVS_PSBSCCP_PLUGIN_DIR . 'includes/class-devsoul-psbsp-general-functions.php';

				include DEVS_PSBSCCP_PLUGIN_DIR . 'includes/class-devsoul-psbsp-ajax.php';

				if (is_admin()) {

					include DEVS_PSBSCCP_PLUGIN_DIR . 'includes/admin/class-devsoul-psbsp-admin.php';
					include DEVS_PSBSCCP_PLUGIN_DIR . 'includes/admin/views/column-in-table.php';

				}
			}
		}


		/**
		 * Define constant variables.
		 *
		 * @since 1.0.0
		 */
		public function devsoul_psbsp_global_constents_vars() {

			if (!defined('DEVS_PSBSCCP_URL')) {
				define('DEVS_PSBSCCP_URL', plugin_dir_url(__FILE__));
			}
			if (!defined('DEVS_SRBSCAC_BASENAME')) {
				define('DEVS_SRBSCAC_BASENAME', plugin_basename(__FILE__));
			}
			if (!defined('DEVS_PSBSCCP_PLUGIN_DIR')) {
				define('DEVS_PSBSCCP_PLUGIN_DIR', plugin_dir_path(__FILE__));
			}
		}

		/**
		 * Register text Domain.
		 *
		 * @since 1.0.0
		 */
		public function devsoul_psbsp_register_text_domain() {

			if (function_exists('load_plugin_textdomain')) {
				load_plugin_textdomain('product-sales-report', false, dirname(plugin_basename(__FILE__)) . '/languages/');
			}
		}
	}

	/**
	 * Class object
	 */
	new Product_Sales_Report();

}

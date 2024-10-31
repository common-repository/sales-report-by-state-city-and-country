<?php
/**
 * File Name: class-devsoul-psbsp-admin.php.
 * Description: Here we have admin class where we included file and admin side hooks.
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

class Ct_Devsoul_Psbsp_Admin {





	public function __construct() {

		add_action('admin_init', array( $this, 'devsoul_psbsp_regester_general_setting' ));
		add_action('admin_menu', array( $this, 'devsoul_psbsp_add_submenu' ), 10);
		add_action('admin_enqueue_scripts', array( $this, 'devsoul_psbsp_admin_enqueue_file' ));
	}
	public function devsoul_psbsp_admin_enqueue_file() {

		wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), '4.7.0', false);
		wp_enqueue_style('dataTables-style', 'https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css', array(), '1.11.5');
		wp_enqueue_script('dataTablesjs', 'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js', array( 'jquery' ), '1.11.5', false);
		wp_enqueue_script('ct-loader.js', 'https://www.gstatic.com/charts/loader.js', array( 'jquery' ), '1.0.0', false);

		wp_enqueue_style('Ct_Devsoul_Psbsp_Admin_side_style', DEVS_PSBSCCP_URL . '/assets/css/adminstyling.css', array(), '1.0.0', false);

		wp_enqueue_style('select2-css', plugins_url('assets/css/select2.css', WC_PLUGIN_FILE), array(), '5.7.2');

		wp_enqueue_script('select2-js', plugins_url('assets/js/select2/select2.min.js', WC_PLUGIN_FILE), array( 'jquery' ), '4.0.3', true);
		wp_enqueue_script('datatable', DEVS_PSBSCCP_URL . '/assets/js/data-table.js', array( 'jquery' ), '1.0.0', false);

		wp_enqueue_script('ct_psbsp_select_prod_nd_cat', DEVS_PSBSCCP_URL . '/assets/js/ct-psbsp-ajax-admin.js', array( 'jquery' ), '1.0.0', false);

		$addify_cs_ajax_data = array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('devsoul_psbsp_files_nonce'),
		);
		$addify_cs_ajax_data = array_merge($addify_cs_ajax_data, devsoul_psbsp_get_tab_and_section());

		wp_localize_script('ct_psbsp_select_prod_nd_cat', 'ct_psbsp_php_var', $addify_cs_ajax_data);
		wp_localize_script(
			'datatable',
			'ct_psbsp_php_var',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('devsoul_psbsp_files_nonce'),
			)
		);
	}
	public function devsoul_psbsp_add_submenu() {
		global $pagenow, $typenow;

		add_submenu_page(
			'woocommerce', // define post type.
			'Product Sale by State', // Page title.
			esc_html__('Product Sale by State Pro', 'cloud_tech_psbsp '), // Title.
			'manage_options', // Capability.
			'devsoul_psbsp_setting', // slug.
			array(
				$this,
				'ct_psbsp_tab_callback',
			) // callback
		);

		remove_submenu_page('woocommerce', 'devsoul_psbs_setting');
		remove_submenu_page('woocommerce', 'ct_psbs_setting');
	}
	public function devsoul_psbsp_regester_general_setting() {
		include DEVS_PSBSCCP_PLUGIN_DIR . 'includes/admin/settings/general-setting.php';
		include DEVS_PSBSCCP_PLUGIN_DIR . 'includes/admin/settings/email-setting.php';
	}
	public function ct_psbsp_product_per_page_for_product_sales() {
		?>
		<input type="number" min="1" name="ct_psbsp_product_per_page_for_product_sales"
			value="<?php echo esc_attr(!empty(get_option('ct_psbsp_product_per_page_for_product_sales')) ? get_option('ct_psbsp_product_per_page_for_product_sales') : 20); ?>">
		<?php
	}
	public function ct_psbsp_product_per_page_for_order_sales() {
		?>
		<input type="number" min="1" name="ct_psbsp_product_per_page_for_order_sales"
			value="<?php echo esc_attr(!empty(get_option('ct_psbsp_product_per_page_for_order_sales')) ? get_option('ct_psbsp_product_per_page_for_order_sales') : 20); ?>">
		<?php
	}


	public function ct_psbsp_tab_callback() {

		global $active_tab;
		$get_tab_info = devsoul_psbsp_get_tab_and_section();
		$active_tab = $get_tab_info['tab'];
		$active_section = $get_tab_info['section'];

		$section_url_Data = array(
			'all_prd' => 'All Products',
			'selected_prd' => 'Specific Products',
			'selected_cat' => 'Specific Category',
		);
		?>

		<!-- Title above Tabs  -->
		<h2> <?php echo esc_html__('Sales Analytics', 'country_selector'); ?></h2>

		<h2 class="nav-tab-wrapper">
			<?php settings_errors(); ?>

			<a href="<?php echo esc_url(admin_url('admin.php?page=devsoul_psbsp_setting&tab=graph')); ?>"
				class="nav-tab  <?php echo esc_attr($active_tab) === 'graph' ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e('Dashboard', 'country_selector'); ?> </a>

			<a href="<?php echo esc_url(admin_url('admin.php?page=devsoul_psbsp_setting&tab=email_settings')); ?>"
				class="nav-tab  <?php echo esc_attr($active_tab) === 'email_settings' ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e('Email Settings', 'country_selector'); ?> </a>

			<a href="<?php echo esc_url(admin_url('admin.php?page=devsoul_psbsp_setting&tab=sales_by_product&section=all_prd')); ?>"
				class="nav-tab  <?php echo esc_attr($active_tab) === 'sales_by_product' ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e('Sales By Products', 'country_selector'); ?> </a>

			<a href="<?php echo esc_url(admin_url('admin.php?page=devsoul_psbsp_setting&tab=sales_by_order&section=all_order')); ?>"
				class="nav-tab  <?php echo esc_attr($active_tab) === 'sales_by_order' ? ' nav-tab-active' : ''; ?>">
				<?php esc_html_e('Sales By Order', 'country_selector'); ?> </a>

			<div class="wrap">
		</h2>
		<div class="ct-psbsp-loading-icon-div" style="display:none">
			<div class="ct-psbsp-loading-icon-main-div">
				<img src="<?php echo esc_url(DEVS_PSBSCCP_URL . 'assets/loading-icons/loading-icon-2nd.gif'); ?>"
					class="ct-psbsp-loading-icon">
			</div>
		</div>
		<?php if ('sales_by_product' == $active_tab) { ?>




			<ul class="subsubsub">
				<?php
				foreach ($section_url_Data as $tab => $tab_name) {
					$url = admin_url('admin.php?page=devsoul_psbsp_setting&tab=sales_by_product&section=' . $tab);
					?>

					<li>
						<a href="<?php echo esc_url($url); ?>"
							class="  <?php echo esc_attr($active_section) === $tab ? 'current' : ''; ?>">
							<?php
							echo esc_attr($tab_name);
							echo esc_attr('selected_cat' == $tab ? '' : ' |');
							?>
						</a>
					</li>
				<?php } ?>
			</ul>
		<?php } ?>
		<br class="clear">

		<form method="post"
			action="<?php echo esc_url('general_setting' === $active_tab || 'email_settings' === $active_tab ? 'options.php' : ''); ?>"
			class="ct_psbsp_f_styling_form" id="ct_psbsp_f_styling_form">
			<?php

			if ('sales_by_product' === $active_tab) {
				include DEVS_PSBSCCP_PLUGIN_DIR . 'includes/admin/views/product-sales-report.php';
			}
			if ('sales_by_order' === $active_tab) {
				include DEVS_PSBSCCP_PLUGIN_DIR . 'includes/admin/views/order-sales-report.php';
			}

			if ('graph' === $active_tab) {
				include DEVS_PSBSCCP_PLUGIN_DIR . 'includes/admin/views/graph.php';
			}
			if ('email_settings' === $active_tab) {
				settings_fields('devsoul_psbsp_email_settings_fields');
				do_settings_sections('devsoul_psbsp_email_settings_sections');
				submit_button();
			}

			?>
		</form>
		<?php
	}
}
new Ct_Devsoul_Psbsp_Admin();
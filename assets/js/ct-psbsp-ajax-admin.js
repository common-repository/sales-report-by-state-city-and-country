jQuery('document').ready(
	function ($) {
		'use strict';
		var country_array = [['Countries', 'Sales Report']];
		var state_array = [['State', 'Country', 'Sales Report']];





		$('.ct-psbsp-live-search').select2();

		jQuery(document).on('click', '.ct-psbsp-filter-btn', function () {
			filter_for_sales();
		});
		jQuery('input[name="ct_select_date"]:checked').closest('li').addClass('active');

		select_date_type();
		jQuery(document).on('click', 'input[name="ct_select_date"]', function () {

			jQuery('input[name="ct_select_date"]').removeAttr('checked');

			jQuery('input[name="ct_select_date"]').each(function () {
				$(this).closest('li').removeClass('active');
				$(this).removeAttr('checked');
			});

			jQuery(this).prop('checked', true);

			$(this).closest('li').addClass('active');
			select_date_type();

		});

		function select_date_type() {

			var select_date_type = jQuery('input[name="ct_select_date"]:checked').val() ? jQuery('input[name="ct_select_date"]:checked').val() : 'this_year';

			jQuery('.ct-psbsp-start-date').closest('li').hide();
			if ('custom_Date' == select_date_type) {
				jQuery('.ct-psbsp-start-date').closest('li').show();

			}
		}


		function filter_for_sales() {
			window.location = get_fanilized_url();
		}
		function get_fanilized_url() {
			var select_date_type = jQuery('input[name="ct_select_date"]:checked').val() ? jQuery('input[name="ct_select_date"]:checked').val() : 'this_year';
			var url = ct_psbsp_php_var.admin_url + '&tab=' + ct_psbsp_php_var.tab + '&section=' + ct_psbsp_php_var.section + '&date_type=' + select_date_type;

			if (jQuery('.ct-psbsp-select-roles').val()) {
				url = url + '&roles=' + jQuery('.ct-psbsp-select-roles').val();
			}

			if (jQuery('.ct-psbsp-select-countries').val()) {
				url = url + '&selected_countries=' + jQuery('.ct-psbsp-select-countries').val();
			}
			

			return url;
		}

		jQuery('.ct_psbsp_product_live_search').select2({
			ajax: {
				url: ct_psbsp_php_var.ajax_url, // AJAX URL is predefined in WordPress admin.
				dataType: 'json',
				type: 'POST',
				delay: 20, // Delay in ms while typing when to perform a AJAX search.
				data: function (params) {
					return {
						q: params.term, // search query
						action: 'devsoul_psbsp_product_search', // AJAX action for admin-ajax.php.//aftaxsearchUsers(is function name which isused in adminn file)
						nonce: ct_psbsp_php_var.nonce // AJAX nonce for admin-ajax.php.
					};
				},
				processResults: function (data) {
					var options = [];
					if (data) {
						// data is the array of arrays, and each of them contains ID and the Label of the option.
						$.each(
							data,
							function (index, text) {
								// do not forget that "index" is just auto incremented value.
								options.push({ id: text[0], text: text[1] });
							}
						);
					}
					return {
						results: options
					};
				},
				cache: true
			},
			// multiple: true,
			placeholder: 'Choose Products',
			// minimumInputLength: 3 // the minimum of symbols to input before perform a search.
		});


		jQuery('.ct_psbsp_category_live_search').select2({
			ajax: {
				url: ct_psbsp_php_var.ajax_url, // AJAX URL is predefined in WordPress admin.
				dataType: 'json',
				type: 'POST',
				delay: 20, // Delay in ms while typing when to perform a AJAX search.
				data: function (params) {
					return {
						q: params.term, // search query
						action: 'devsoul_psbsp_category_search', // AJAX action for admin-ajax.php.//aftaxsearchUsers(is function name which isused in adminn file)
						nonce: ct_psbsp_php_var.nonce // AJAX nonce for admin-ajax.php.
					};
				},
				processResults: function (data) {
					var options = [];
					if (data) {
						// data is the array of arrays, and each of them contains ID and the Label of the option.
						$.each(
							data,
							function (index, text) {
								// do not forget that "index" is just auto incremented value.
								options.push({ id: text[0], text: text[1] });
							}
						);
					}
					return {
						results: options
					};
				},
				cache: true
			},
			multiple: true,
			placeholder: 'Choose category',
			// minimumInputLength: 3 // the minimum of symbols to input before perform a search.
		});

		jQuery('.ct_psbsp_customer_search').select2(
			{
				ajax: {
					url: ct_psbsp_php_var.ajax_url, // AJAX URL is predefined in WordPress admin.
					dataType: 'json',
					type: 'POST',
					// delay: 20, // Delay in ms while typing when to perform a AJAX search.
					data: function (params) {
						return {
							q: params.term, // search query
							action: 'devsoul_psbsp_customer_search', // AJAX action for admin-ajax.php.//aftaxsearchUsers(is function name which isused in adminn file)
							nonce: ct_psbsp_php_var.nonce // AJAX nonce for admin-ajax.php.
						};
					},
					processResults: function (data) {
						var options = [];
						if (data) {
							// data is the array of arrays, and each of them contains ID and the Label of the option.
							$.each(
								data,
								function (index, text) {
									// do not forget that "index" is just auto incremented value.
									options.push({ id: text[0], text: text[1] });
								}
							);
						}
						return {
							results: options
						};
					},
					cache: true
				},
				multiple: true,
				placeholder: 'Choose Customer',
				// minimumInputLength: 3 // the minimum of symbols to input before perform a search.
			}
		);

		if ($('.ct-psbsp-show-graph').length) {
			ct_psbsp_show_graph();
		}
		$(document).on('click', '.ct-psbsp-show-graph', ct_psbsp_show_graph);

		function ct_psbsp_show_graph() {
			$('.ct-psbsp-loading-icon-div').show('slow');
			jQuery.ajax({
				url: ct_psbsp_php_var.ajax_url,
				type: 'POST',
				data: {
					action: 'ct_psbsp_show_graph',
					nonce: ct_psbsp_php_var.nonce,
					url: get_fanilized_url(),
				},
				success: function (response) {
					console.log(response);
					$('.ct-psbsp-loading-icon-div').hide('slow');

					if (response.country_array) {
						country_array = response.country_array;
						google.charts.setOnLoadCallback(sales_graphp_of_countries);
					}
					if (response.state_array) {

						if (response.state_array) {
							state_array = response.state_array;
							google.charts.setOnLoadCallback(sales_graphp_of_state);

						}
					}
				},
			});

			history.pushState(null, null, get_fanilized_url());

		}
		google.charts.load('current', {
			'packages': ['geochart'],
			'mapsApiKey': 'AIzaSyA1fLiQdBevGEfscVKwS2XhnZs7AqIhBpo'
		});

		var all_color = [
			'AliceBlue',
			'AntiqueWhite',
			'Aqua',
			'Aquamarine',
			'Azure',
			'Beige',
			'Bisque',
			'Black',
			'BlanchedAlmond',
			'Blue',
			'BlueViolet',
			'Brown',
			'BurlyWood',
			'CadetBlue',
			'Chartreuse',
			'Chocolate',
			'Coral',
			'CornflowerBlue',
			'Cornsilk',
			'Crimson',
			'Cyan',
			'DarkBlue',
			'DarkCyan',
			'DarkGoldenRod',
			'DarkGray',
			'DarkGreen',
			'DarkKhaki',
			'DarkMagenta',
			'DarkOliveGreen',
			'DarkOrange'
		];
		function sales_graphp_of_countries() {

			var options = {
				displayMode: 'regions',
				colorAxis: { colors: all_color },
				sizeAxis: { minValue: 0, maxSize: 10 },
				region: 'world',
				magnifyingGlass: { enable: true, zoomFactor: 7.5 },
				// enableRegionInteractivity: true,
				tooltip: { isHtml: true },
			};

			var data = google.visualization.arrayToDataTable(country_array);

			var chart = new google.visualization.GeoChart(document.getElementById('sale_report_by_country'));

			chart.draw(data, options);
		}

		function sales_graphp_of_state() {
			console.log(state_array);


			var options = {
				displayMode: 'markers',
				colorAxis: { colors: all_color },
				sizeAxis: { minValue: 0, maxSize: 10 },
				region: 'world',
				magnifyingGlass: { enable: true, zoomFactor: 8.5 },
				// enableRegionInteractivity: true,
				tooltip: { isHtml: true },
			};
			var data = google.visualization.arrayToDataTable(state_array);


			var chart = new google.visualization.GeoChart(document.getElementById('sale_report_by_state'));
			chart.draw(data, options);
		}

	}
);

jQuery(document).ready(function ($) {

	// When the user clicks the button, open the modal.
	$(document).on('click', '#showOrdersPopup', function (e) {
		e.preventDefault();
		$('#ordersPopup').hide();
		$(this).closest('.devsoul-order-detail').find('#ordersPopup').css('display', 'block').css('opacity', '1');
	});

	// When the user clicks on <span> (x), close the modal.
	$(document).on('click', '.orders-popup-close', function () {

		$('#ordersPopup').css('opacity', '0');
		setTimeout(function () {
			$('#ordersPopup').hide('slow');
		}, 300); // Wait for the transition to complete.
	});

});

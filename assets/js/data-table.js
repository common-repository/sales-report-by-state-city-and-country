jQuery(document).ready(function ($) {

    jQuery('.ct-psbsp-order-infor-table').DataTable();

    for (let index = 1; index <= 100; index++) {
        if (index * 1000 % 10000 == 0) {

            let option = '<option value="' + index * 1000 + '">' + index * 1000 + '</option>';
            jQuery('.ct-psbsp-prouct-table select[name=DataTables_Table_0_length]').append(option);

        }
    }

    $(document).on('click', '.select_all_order', function () {
        let checked_or_not = $(this).is(':checked') ? true : false;
        $('.select_current_order').prop('checked', checked_or_not);
    });

    jQuery('.ct-psbsp-prouct-table .dataTables_length').css('width', '40%');
    jQuery('.ct-psbsp-prouct-table select[name=DataTables_Table_0_length]').css('width', '100%');

    $(document).on('click', 'input[name=ct_export_sales_csv]', function (e) {
        e.preventDefault();
        var csvContent = [];

        // Iterate through rows and cells to build CSV content

        var heading = [
            'Order id',
            'Order Total',
            'Billing first name',
            'Billing last name',
            'Billing email',
            'Billing phone',
            'Billing address 1',
            'Billing address 2',
            'Billing country',
            'Billing city',
            'Billing postcode',
            'Shipping method',
            'Shipping first name',
            'Shipping last name',
            'Shipping address 2',
            'Shipping country',
            'Shipping city',
            'Shipping postcode',
        ];
        csvContent.push(heading);

        let order_ids_array = [];

        let selecetd_checkbox = $('.select_current_order').is(':checked') ? $('.select_current_order:checked') : $('.select_current_order');
        selecetd_checkbox.closest('tr').find('.ct-psbsp-order-complete-detail').each(function () {
            var row = [
                $(this).data('order_id'),
                $(this).data('total'),
                $(this).data('billing_first_name'),
                $(this).data('billing_last_name'),
                $(this).data('billing_email'),
                $(this).data('billing_phone'),
                $(this).data('billing_address_1'),
                $(this).data('billing_address_2'),
                $(this).data('billing_country'),
                $(this).data('billing_city'),
                $(this).data('billing_postcode'),
                $(this).data('shipping_method'),
                $(this).data('shipping_first_name'),
                $(this).data('shipping_last_name'),
                $(this).data('shipping_email'),
                $(this).data('shipping_phone'),
                $(this).data('shipping_address_1'),
                $(this).data('shipping_address_2'),
                $(this).data('shipping_country'),
                $(this).data('shipping_city'),
                $(this).data('shipping_postcode'),
            ];

            csvContent.push(row);
            order_ids_array.push($(this).data('order_id'));
        });
        $('.ct-psbsp-total-of-selected-table table tr').each(function () {
            var row = [
                $(this).find('th').text(),
                $(this).find('td').text(),
            ];

            csvContent.push(row);
        });



        // console.log($('.select_current_order:checked').val());
        // console.log(csvContent);

        // return;


        if (csvContent.length <= 1) {
            return;
        }

        let file_name = $(".ct-psbsp-order-sales").data('file_name') ? $(".ct-psbsp-order-sales").data('file_name') : 'exported_data.csv';



        // Convert CSV content to a blob


        var csvBlob = new Blob([csvContent.join("\n")], { type: "text/csv;charset=utf-8;" });

        // Trigger a download link
        var link = $("<a></a>")
            .attr("href", window.URL.createObjectURL(csvBlob))
            .attr("download", file_name)
            .appendTo("body")[0];
        link.click();


        jQuery.ajax({
            url: ct_psbsp_php_var.ajax_url,
            type: 'POST',
            data: {
                action: 'update_value_exported',
                nonce: ct_psbsp_php_var.nonce,
                file_name: file_name,
                order_ids_array: order_ids_array,
            },
            success: function (response) {
                if (response['success']) {
                    window.location.reload(true);
                }
            },
        });
    });


    $(document).on('click', '.ct-psbsp-change-order-status', function (e) {
        e.preventDefault();

        let select_order = [];

        $('.select_current_order:checked').each(function () {
            select_order.push($(this).val());
        });

        if (select_order.length < 1) {
            return;
        }

        jQuery.ajax({
            url: ct_psbsp_php_var.ajax_url,
            type: 'POST',
            data: {
                action: 'change_order_status_of_selected_order',
                nonce: ct_psbsp_php_var.nonce,
                new_order_staus: $('.change_order_status').val(),
                select_order: select_order,
            },
            success: function (response) {

                $(document).ajaxComplete(function () {
                    window.location.reload(true);
                });
            },
        });

    });

    $(document).on('change , click , keyup , keydown , keypress', 'input, select, button, select[name=DataTables_Table_0_length] , input[type=search]', sale_detail_table);
    sale_detail_table();
    function sale_detail_table() {

        if ($('.ct-psbsp-order-complete-detail').length) {

            let subtotal = 0;
            let tax_total = 0;
            let refund_total = 0;
            let coupon_total = 0;
            let shipping_total = 0;
            let total = 0;
            let currency_symbol = $('.woocommerce-Price-currencySymbol').first().text();
            $('.ct-psbsp-order-complete-detail').each(function () {
                subtotal += parseFloat($(this).data('subtotal'));
                tax_total += parseFloat($(this).data('tax_total'));
                shipping_total += parseFloat($(this).data('shipping_total'));
                total += parseFloat($(this).data('total'));
                refund_total += parseFloat($(this).data('refunded_amount'));
                coupon_total += parseFloat($(this).data('coupon_amount'));


            });

            $('.ct-psbsp-subtotal-td').text(currency_symbol + subtotal);
            $('.ct-psbsp-total-tax-td').text(currency_symbol + tax_total);
            $('.ct-psbsp-shipping-total-td').text(currency_symbol + shipping_total);
            $('.ct-psbsp-refunded-total-td').text(currency_symbol + refund_total);
            $('.ct-psbsp-coupon-total-td').text(currency_symbol + coupon_total);
            $('.ct-psbsp-total-td').text(currency_symbol + total);
        }
    }

});
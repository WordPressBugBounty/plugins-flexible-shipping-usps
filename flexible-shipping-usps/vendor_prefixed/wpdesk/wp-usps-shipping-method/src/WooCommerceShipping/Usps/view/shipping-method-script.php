<script type="text/javascript">
    jQuery(document).ready(function(){
        let $custom_origin = jQuery('#woocommerce_flexible_shipping_usps_custom_origin');

        $custom_origin.change(function(){
            let $origin_address = jQuery('#woocommerce_flexible_shipping_usps_origin_address');
            let $origin_city = jQuery('#woocommerce_flexible_shipping_usps_origin_city');
            let $origin_postcode = jQuery('#woocommerce_flexible_shipping_usps_origin_postcode');
            let $origin_country = jQuery('#woocommerce_flexible_shipping_usps_origin_country');
            if ( jQuery(this).is(':checked') ) {
                $origin_address.closest('tr').show();
                $origin_address.attr('required',true);
                $origin_city.closest('tr').show();
                $origin_city.attr('required',true);
                $origin_postcode.closest('tr').show();
                $origin_postcode.attr('required',true);
                $origin_country.closest('tr').show();
                $origin_country.attr('required',true);
            }
            else {
                $origin_address.closest('tr').hide();
                $origin_address.attr('required',false);
                $origin_city.closest('tr').hide();
                $origin_city.attr('required',false);
                $origin_postcode.closest('tr').hide();
                $origin_postcode.attr('required',false);
                $origin_country.closest('tr').hide();
                $origin_country.attr('required',false);
            }
        });

        if ( $custom_origin.length ) {
            $custom_origin.change();
        }

		const $api_type = jQuery('#woocommerce_flexible_shipping_usps_api_type');

		function change_api_type() {
			const val = $api_type.val();
			const $web_api_fields = jQuery('.api-web').closest('tr');
			const $rest_api_fields = jQuery('.api-rest').closest('tr');
			const $web_api_inputs = $web_api_fields.find('input.api-web:not([type="checkbox"]),select.api-web');
			const $rest_api_inputs = $rest_api_fields.find('input.api-rest:not([type="checkbox"]),select.api-rest');
			if ( val === 'web_tools' ) {
				$rest_api_fields.hide();
				$web_api_fields.show();
				$rest_api_inputs.prop('required', false);
				$web_api_inputs.prop('required', true);
			} else if ( val === 'rest' ) {
				$web_api_fields.hide();
				$rest_api_fields.show();
				$web_api_inputs.prop('required', false);
				$rest_api_inputs.prop('required', true);
			}
		}

		$api_type.change(function(){
			change_api_type();
		})

		change_api_type();

		jQuery('#woocommerce_flexible_shipping_usps_origin_country').select2();

		const $change_overwrite_value_of_contents = jQuery('#woocommerce_flexible_shipping_usps_overwrite_value_of_contents');

		function change_overwrite_value_of_contents() {
			const checked = $change_overwrite_value_of_contents.is(':checked');
			jQuery('input[name="woocommerce_flexible_shipping_usps_value_of_contents"]').closest('tr').toggle(checked);
		}

		$change_overwrite_value_of_contents.change(function(){
			change_overwrite_value_of_contents();
		});

		change_overwrite_value_of_contents();

    });

</script>


<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\RateMethod\Fallback\FallbackRateMethod;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\WooCommerceNotInitializedException;
/**
 * A class that defines the basic settings for the shipping method.
 */
class UspsSettingsDefinition extends SettingsDefinition
{
    const DISABLE_REST_API = \true;
    const CUSTOM_SERVICES_CHECKBOX_CLASS = 'wpdesk_wc_shipping_custom_service_checkbox';
    const SHIPPING_METHOD_TITLE = 'shipping_method_title';
    const API_SETTINGS_TITLE = 'api_settings_title';
    const API_TYPE = 'api_type';
    const API_TYPE_WEB = 'web_tools';
    const API_TYPE_REST = 'rest';
    const REST_API_KEY = 'rest_api_key';
    const REST_API_SECRET_KEY = 'rest_api_secret_key';
    const USER_ID = 'user_id';
    const PASSWORD = 'password';
    const TESTING = 'testing';
    const ORIGIN_SETTINGS_TITLE = 'origin_settings_title';
    const CUSTOM_ORIGIN = 'custom_origin';
    const ORIGIN_ADDRESS = 'origin_address';
    const ORIGIN_CITY = 'origin_city';
    const ORIGIN_POSTCODE = 'origin_postcode';
    const ORIGIN_COUNTRY = 'origin_country';
    const ADVANCED_OPTIONS_TITLE = 'advanced_options_title';
    const DEBUG_MODE = 'debug_mode';
    const API_STATUS = 'api_status';
    const METHOD_SETTINGS_TITLE = 'method_settings_title';
    const TITLE = 'title';
    const FALLBACK = 'fallback';
    const CUSTOM_SERVICES = 'custom_services';
    const SERVICES_DOMESTIC = 'services_domestic';
    const SERVICES_INTERNATIONAL = 'services_international';
    const PACKAGE_SETTINGS_TITLE = 'package_settings_title';
    const SERVICE_SETTINGS_TITLE = 'service_settings_title';
    const PACKAGE_LENGTH = 'package_length';
    const PACKAGE_WIDTH = 'package_width';
    const PACKAGE_HEIGHT = 'package_height';
    const PACKAGE_WEIGHT = 'package_weight';
    const RATE_ADJUSTMENTS_TITLE = 'rate_adjustments_title';
    const INSURANCE = 'insurance';
    const FREE_SHIPPING = 'free_shipping';
    const COMMERCIAL_RATES = 'commercial_rates';
    const SKU_SERVICE_TYPE = 'sku_service_type';
    const SKU_SERVICE_SUB_TYPE = 'sku_service_sub_type';
    const SKU_SHAPE = 'sku_shape';
    const SKU_DELIVERY_TYPE = 'sku_delivery_type';
    const OVERWRITE_VALUE_OF_CONTENTS = 'overwrite_value_of_contents';
    const VALUE_OF_CONTENTS = 'value_of_contents';
    /**
     * Shop settings.
     *
     * @var ShopSettings
     */
    private $shop_settings;
    /**
     * UspsSettingsDefinition constructor.
     *
     * @param ShopSettings $shop_settings Shop settings.
     */
    public function __construct(ShopSettings $shop_settings)
    {
        $this->shop_settings = $shop_settings;
    }
    /**
     * Validate settings.
     *
     * @param SettingsValues $settings Settings.
     *
     * @return bool
     */
    public function validate_settings(SettingsValues $settings): bool
    {
        return \true;
    }
    /**
     * Prepare country state options.
     *
     * @return array
     */
    private function prepare_country_state_options(): array
    {
        try {
            $countries = $this->shop_settings->get_countries();
        } catch (WooCommerceNotInitializedException $e) {
            $countries = [];
        }
        $country_state_options = $countries;
        foreach ($country_state_options as $country_code => $country) {
            $states = $this->shop_settings->get_states($country_code);
            if ($states) {
                unset($country_state_options[$country_code]);
                foreach ($states as $state_code => $state_name) {
                    $country_state_options[$country_code . ':' . $state_code] = $country . ' &mdash; ' . $state_name;
                }
            }
        }
        return $country_state_options;
    }
    /**
     * Initialise Settings Form Fields.
     *
     * @return array
     */
    public function get_form_fields()
    {
        $services = new UspsServices();
        $docs_link = 'https://octol.io/usps-method-docs';
        $rest_api_description = sprintf(__('%1$sREST API%2$s: enter your REST API Application Consumer Key and Consumer Secret to establish the USPS REST API connection for the live rates to be calculated. Please mind that it usually consists of random numbers and characters and it is different than your standard USPS user account login you use to sign in at usps.com. If you do not have a REST API Application yet, %3$sregister for USPS Developers now%4$s or follow the instructions from our guide on %5$show to create the USPS REST API application &rarr;%6$s', 'flexible-shipping-usps'), '<strong>', '</strong>', '<a href="https://octol.io/usps-rest-api-site" target="_blank">', '</a>', '<a href="https://octol.io/usps-rest-api-application-docs" target="_blank">', '</a>');
        $web_tools_api_description = sprintf(__('%1$sWeb Tools API%2$s: enter your USPS Web Tools API USERID and PASSWORD to establish the USPS API connection for the live rates to be calculated. Please mind that it usually consists of random numbers and characters and it is different than your standard USPS user account login you use to sign in at usps.com. If you do not have a USERID yet, %3$sregister for USPS Web Tools now%4$s or follow the instructions from our guide on %5$show to obtain the USPS Web Tools API access &rarr;%6$s', 'flexible-shipping-usps'), '<strong>', '</strong>', '<a href="https://octol.io/usps-web-tools-api-site" target="_blank">', '</a>', '<a href="https://octol.io/usps-web-tools-api-access-docs" target="_blank">', '</a>');
        $connection_fields = [self::SHIPPING_METHOD_TITLE => ['title' => __('USPS', 'flexible-shipping-usps'), 'type' => 'title', 'description' => sprintf(
            // Translators: docs link.
            __('These are the USPS Live Rates plugin general settings. In order to learn more about its configuration please refer to its %1$sdedicated documentation →%2$s', 'flexible-shipping-usps'),
            '<a href="' . $docs_link . '" target="_blank">',
            '</a>'
        )], self::API_SETTINGS_TITLE => [
            'title' => __('API Settings', 'flexible-shipping-usps'),
            'type' => 'title',
            // Translators: link.
            'description' => self::DISABLE_REST_API ? $web_tools_api_description : $rest_api_description . '<br/><br/>' . $web_tools_api_description,
        ], self::API_TYPE => ['title' => __('API Type', 'fedex-shipping-service'), 'type' => 'select', 'class' => 'wc-enhanced-select', 'description' => __('After changing the API type from Web Tools API to REST API, you must reconfigure all USPS shipping methods in your shipping zones. You will be notified with a separate message if there is at least one active shipping method that has not been configured for the REST API.', 'fedex-shipping-service'), 'desc_tip' => __('Select API type.', 'fedex-shipping-service'), 'options' => [self::API_TYPE_REST => __('REST API', 'fedex-shipping-service'), self::API_TYPE_WEB => __('Web Tools API', 'fedex-shipping-service')]], self::REST_API_KEY => ['title' => __('Consumer Key', 'fedex-shipping-service'), 'type' => 'text', 'custom_attributes' => ['required' => 'required'], 'class' => 'api-rest'], self::REST_API_SECRET_KEY => ['title' => __('Consumer Secret', 'fedex-shipping-service'), 'type' => 'password', 'custom_attributes' => ['required' => 'required'], 'class' => 'api-rest'], self::USER_ID => ['title' => __('USPS Web Tools API USERID*', 'flexible-shipping-usps'), 'type' => 'text', 'custom_attributes' => ['required' => 'required'], 'description' => __('Enter your USPS USERID you acquired during the USPS Web Tools API registration process.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'default' => '', 'class' => 'api-web'], self::PASSWORD => ['title' => __('USPS Web Tools API PASSWORD*', 'flexible-shipping-usps'), 'type' => 'password', 'custom_attributes' => ['required' => 'required'], 'description' => __('Enter your USPS PASSWORD you acquired during the USPS Web Tools API registration process.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'default' => '', 'class' => 'api-web']];
        if (self::DISABLE_REST_API) {
            unset($connection_fields[self::API_TYPE]['options'][self::API_TYPE_REST]);
            unset($connection_fields[self::API_TYPE]['description']);
        }
        if ($this->shop_settings->is_testing()) {
            $connection_fields[self::TESTING] = ['title' => __('Test Credentials', 'fedex-shipping-service'), 'type' => 'checkbox', 'label' => __('Enable to use test credentials', 'fedex-shipping-service'), 'desc_tip' => \true, 'default' => 'no'];
        }
        $fields = [self::ADVANCED_OPTIONS_TITLE => ['title' => __('Advanced Options', 'flexible-shipping-usps'), 'type' => 'title'], self::DEBUG_MODE => ['title' => __('Debug Mode', 'flexible-shipping-usps'), 'label' => __('Enable debug mode', 'flexible-shipping-usps'), 'type' => 'checkbox', 'description' => __('Enable debug mode to display the additional tech information, incl. the data sent to USPS API, visible only for Admins and Shop Managers in the cart and checkout.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'default' => 'no']];
        $instance_fields = [self::METHOD_SETTINGS_TITLE => ['title' => __('Method Settings', 'flexible-shipping-usps'), 'description' => __('Manage the way how the USPS services are displayed in the cart and checkout.', 'flexible-shipping-usps'), 'type' => 'title'], self::TITLE => ['title' => __('Method Title', 'flexible-shipping-usps'), 'type' => 'text', 'description' => __('Define the USPS shipping method title which should be used in the cart/checkout when the Fallback option was triggered.', 'flexible-shipping-usps'), 'default' => __('USPS Live rates', 'flexible-shipping-usps'), 'desc_tip' => \true], self::FALLBACK => ['type' => FallbackRateMethod::FIELD_TYPE_FALLBACK, 'description' => __('Enable to offer flat rate cost for shipping so that the user can still checkout, if API for some reason returns no matching rates.', 'flexible-shipping-usps'), 'default' => ''], self::FREE_SHIPPING => ['title' => __('Free Shipping', 'flexible-shipping-usps'), 'type' => FreeShippingFields::FIELD_TYPE_FREE_SHIPPING, 'default' => ''], self::CUSTOM_SERVICES => ['title' => __('Services', 'flexible-shipping-usps'), 'label' => __('Enable the services\' custom settings', 'flexible-shipping-usps'), 'type' => 'checkbox', 'description' => __('Decide which services should be displayed and which not, change their names and order. Please mind that enabling a service does not guarantee it will be visible in the cart/checkout. It has to be available for the provided package weight, origin and destination in order to be displayed.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'class' => self::CUSTOM_SERVICES_CHECKBOX_CLASS . ' api-web', 'default' => 'no'], self::SERVICES_DOMESTIC => ['title' => __('Domestic Services Table', 'flexible-shipping-usps'), 'type' => 'services', 'default' => '', 'options' => $services->get_services_domestic()], self::SERVICES_INTERNATIONAL => ['title' => __('International Services Table', 'flexible-shipping-usps'), 'type' => 'services', 'default' => '', 'options' => $services->get_services_international()], self::SERVICE_SETTINGS_TITLE => ['title' => __('Services', 'flexible-shipping-usps'), 'type' => 'title', 'class' => 'api-rest'], self::SKU_SERVICE_TYPE => ['title' => __('Type', 'flexible-shipping-usps'), 'type' => 'multiselect', 'desc_tip' => \true, 'options' => ['P' => __('Priority Mail', 'flexible-shipping-usps'), 'E' => __('Priority Mail Express', 'flexible-shipping-usps'), 'M' => __('Media', 'flexible-shipping-usps'), 'F' => __('First-Class Mail', 'flexible-shipping-usps'), 'U' => __('USPS Ground Advantage*', 'flexible-shipping-usps'), 'L' => __('Library', 'flexible-shipping-usps')], 'class' => 'wc-enhanced-select api-rest', 'custom_attributes' => ['required' => 'required']], self::SKU_SERVICE_SUB_TYPE => ['title' => __('Sub type', 'flexible-shipping-usps'), 'type' => 'multiselect', 'desc_tip' => \true, 'options' => ['X' => __('None', 'flexible-shipping-usps'), 'A' => __('Automation', 'flexible-shipping-usps'), 'B' => __('Nonautomation', 'flexible-shipping-usps'), 'C' => __('Carrier Route', 'flexible-shipping-usps'), 'D' => __('Carrier Route Nonautomation', 'flexible-shipping-usps'), 'E' => __('Pending Periodicals', 'flexible-shipping-usps'), 'F' => __('Flat Rate', 'flexible-shipping-usps'), 'G' => __('USPS Connect Local', 'flexible-shipping-usps'), 'H' => __('USPS Connect Regional', 'flexible-shipping-usps'), 'I' => __('Irregular', 'flexible-shipping-usps'), 'K' => __('Share Mail', 'flexible-shipping-usps'), 'L' => __('Metered', 'flexible-shipping-usps'), 'M' => __('Machinable', 'flexible-shipping-usps'), 'N' => __('Nonmachinable', 'flexible-shipping-usps'), 'O' => __('USPS Connect Flat Rate', 'flexible-shipping-usps'), 'P' => __('Presorted', 'flexible-shipping-usps'), 'Q' => __('Automation Disc', 'flexible-shipping-usps'), 'R' => __('Regional Rate', 'flexible-shipping-usps'), 'S' => __('Simple Samples', 'flexible-shipping-usps'), 'T' => __('Permit Reply Mail', 'flexible-shipping-usps'), 'U' => __('Cubic', 'flexible-shipping-usps'), 'V' => __('Nonpresorted', 'flexible-shipping-usps'), 'W' => __('Permit Reply Mail', 'flexible-shipping-usps'), 'Y' => __('Nonautomation Disc', 'flexible-shipping-usps'), 'Z' => __('Customized', 'flexible-shipping-usps')], 'class' => 'wc-enhanced-select api-rest', 'custom_attributes' => ['required' => 'required']], self::SKU_SHAPE => ['title' => __('Shape', 'flexible-shipping-usps'), 'type' => 'multiselect', 'desc_tip' => \true, 'options' => ['X' => __('None', 'flexible-shipping-usps'), 'A' => __('Bag', 'flexible-shipping-usps'), 'B' => __('Box', 'flexible-shipping-usps'), 'C' => __('Postcards', 'flexible-shipping-usps'), 'E' => __('Envelope', 'flexible-shipping-usps'), 'F' => __('Flats or Large Envelope', 'flexible-shipping-usps'), 'H' => __('Half Tray', 'flexible-shipping-usps'), 'I' => __('Full Tray', 'flexible-shipping-usps'), 'J' => __('EMM Tray', 'flexible-shipping-usps'), 'K' => __('Tub', 'flexible-shipping-usps'), 'L' => __('Letters', 'flexible-shipping-usps'), 'M' => __('M-Bag', 'flexible-shipping-usps'), 'N' => __('Balloon', 'flexible-shipping-usps'), 'O' => __('Oversize', 'flexible-shipping-usps'), 'P' => __('Parcel or Package', 'flexible-shipping-usps'), 'Q' => __('Keys and IDs', 'flexible-shipping-usps'), 'R' => __('Dimensional Weight', 'flexible-shipping-usps'), 'U' => __('Pallet', 'flexible-shipping-usps'), 'V' => __('Half Pallet Box', 'flexible-shipping-usps'), 'W' => __('Full Pallet Box', 'flexible-shipping-usps')], 'class' => 'wc-enhanced-select api-rest', 'custom_attributes' => ['required' => 'required']], self::SKU_DELIVERY_TYPE => ['title' => __('Delivery type', 'flexible-shipping-usps'), 'type' => 'multiselect', 'desc_tip' => \true, 'options' => ['X' => __('None', 'flexible-shipping-usps'), 'H' => __('Hold for Pickup', 'flexible-shipping-usps'), 'S' => __('Sunday/Holiday', 'flexible-shipping-usps'), 'R' => __('Return', 'flexible-shipping-usps')], 'class' => 'wc-enhanced-select api-rest', 'custom_attributes' => ['required' => 'required']], self::PACKAGE_SETTINGS_TITLE => ['title' => __('Package Settings', 'flexible-shipping-usps'), 'description' => sprintf(__('Define the package details including its dimensions and weight which will be used as default for this shipping method.', 'flexible-shipping-usps')), 'type' => 'title', 'class' => 'api-rest'], self::PACKAGE_LENGTH => ['title' => __('Length [in] *', 'flexible-shipping-usps'), 'type' => 'number', 'description' => __('Enter only a numeric value without the metric symbol.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'custom_attributes' => ['min' => 0.1, 'step' => 0.1, 'required' => 'required'], 'class' => 'api-rest'], self::PACKAGE_WIDTH => ['title' => __('Width [in] *', 'flexible-shipping-usps'), 'type' => 'number', 'description' => __('Enter only a numeric value without the metric symbol.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'custom_attributes' => ['min' => 0.1, 'step' => 0.1, 'required' => 'required'], 'class' => 'api-rest'], self::PACKAGE_HEIGHT => ['title' => __('Height [in] *', 'flexible-shipping-usps'), 'type' => 'number', 'description' => __('Enter only a numeric value without the metric symbol.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'custom_attributes' => ['min' => 0.1, 'step' => 0.1, 'required' => 'required'], 'class' => 'api-rest'], self::PACKAGE_WEIGHT => ['title' => __('Default weight [lbs] *', 'flexible-shipping-usps'), 'type' => 'number', 'description' => __('Enter the package weight value which will be used as default if none of the products\' in the cart individual weight has been filled in or if the cart total weight equals 0 kg.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'custom_attributes' => ['min' => 0.001, 'step' => 0.001, 'required' => 'required'], 'class' => 'api-rest'], self::RATE_ADJUSTMENTS_TITLE => ['title' => __('Rates Adjustments', 'flexible-shipping-usps'), 'description' => sprintf(__('Adjust these settings to get more accurate rates. Read %swhat affects the USPS rates in USPS WooCommerce plugin →%s', 'flexible-shipping-usps'), sprintf('<a href="%s" target="_blank">', __('https://octol.io/usps-free-rates', 'flexible-shipping-usps')), '</a>'), 'type' => 'title'], self::COMMERCIAL_RATES => ['title' => __('Commercial Rates', 'flexible-shipping-usps'), 'label' => __('Use the USPS Commercial Pricing if available', 'flexible-shipping-usps'), 'type' => 'checkbox', 'description' => __('Tick this checkbox if you want to use and display the Commercial Pricing shipping rates to your customers instead of the standard ones.', 'flexible-shipping-usps'), 'desc_tip' => \true, 'default' => 'no'], self::OVERWRITE_VALUE_OF_CONTENTS => ['title' => __('Overwrite Value of contents', 'flexible-shipping-usps'), 'label' => __('Enable', 'flexible-shipping-usps'), 'type' => 'checkbox', 'description' => __('By default, the combined value of ordered products is sent within the "ValueOfContents" field in API requests. If you check this option, you can override this value with a custom one. Please note that for letter-shaped mail, the USPS requires this value to be set to 0.', 'flexible-shipping-usps'), 'desc_tip' => \false, 'default' => 'no'], self::VALUE_OF_CONTENTS => ['title' => __('Value of contents', 'flexible-shipping-usps'), 'type' => 'number', 'desc_tip' => \false, 'default' => '0'], self::INSURANCE => ['title' => __('Insurance', 'flexible-shipping-usps'), 'label' => __('Request insurance to be included in the USPS rates', 'flexible-shipping-usps'), 'type' => 'checkbox', 'description' => __('Enabling this option will increase time needed to return rates and may limit the number of available services.', 'flexible-shipping-usps'), 'desc_tip' => __('Enable if you want to include insurance in the USPS rates if possible.', 'flexible-shipping-usps'), 'default' => 'no']];
        return $connection_fields + $fields + $instance_fields;
    }
}

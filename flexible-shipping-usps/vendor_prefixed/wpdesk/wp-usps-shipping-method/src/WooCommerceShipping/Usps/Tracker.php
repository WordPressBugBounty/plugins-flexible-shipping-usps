<?php

/**
 * Tracker
 */
namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps;

use WC_Shipping_Method;
use WC_Shipping_Zone;
use WC_Shipping_Zones;
use WP_Screen;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSkuComponents;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\FreeShipping\FreeShippingFields;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod;
/**
 * Handles tracker actions.
 */
class Tracker implements Hookable
{
    const OPTION_VALUE_NO = 'no';
    const OPTION_VALUE_YES = 'yes';
    /**
     * Hooks.
     */
    public function hooks()
    {
        add_filter('wpdesk_tracker_data', [$this, 'append_tracker_data_usps'], 11);
    }
    /**
     * Prepare default plugin data.
     *
     * @param UspsShippingMethod $flexible_shipping_usps Shipping method.
     *
     * @return array
     */
    private function prepare_default_plugin_data(UspsShippingMethod $flexible_shipping_usps): array
    {
        if (self::OPTION_VALUE_YES === $flexible_shipping_usps->get_option(UspsSettingsDefinition::CUSTOM_ORIGIN, self::OPTION_VALUE_NO)) {
            $usps_origin_country = explode(':', $flexible_shipping_usps->get_option(UspsSettingsDefinition::ORIGIN_COUNTRY, ''));
        } else {
            $usps_origin_country = explode(':', get_option('woocommerce_default_country', ''));
        }
        if (!empty($usps_origin_country[0])) {
            $origin_country = $usps_origin_country[0];
        } else {
            $origin_country = 'not set';
        }
        $plugin_data = ['custom_origin' => $flexible_shipping_usps->get_option(UspsSettingsDefinition::CUSTOM_ORIGIN, self::OPTION_VALUE_NO), 'api_type' => $flexible_shipping_usps->get_option(UspsSettingsDefinition::API_TYPE, UspsSettingsDefinition::API_TYPE_WEB), 'shipping_methods' => 0, 'custom_services' => 0, 'insurance_option' => 0, 'fallback' => 0, 'free_shipping' => 0, 'sku_service_type' => [], 'sku_service_sub_type' => [], 'sku_shape' => [], 'sku_delivery_type' => [], 'sku_settings' => [], 'access_point' => 0, 'access_point_only' => 0, 'origin_country' => $origin_country, 'shipping_zones' => []];
        return $plugin_data;
    }
    /**
     * Append data for shipping method.
     *
     * @param array            custom_services$plugin_data     Plugin data.
     * @param WC_Shipping_Zone $zone Shipping zone.
     * @param ShippingMethod $shipping_method Shipping method.
     *
     * @return array
     */
    private function append_data_for_shipping_method(array $plugin_data, WC_Shipping_Zone $zone, UspsShippingMethod $shipping_method): array
    {
        $plugin_data['shipping_zones'][] = $zone->get_zone_name();
        $plugin_data['shipping_methods']++;
        if (self::OPTION_VALUE_YES === $shipping_method->get_instance_option('custom_services', self::OPTION_VALUE_NO)) {
            $plugin_data['custom_services']++;
        }
        if ($this->is_rest_api_enabled($shipping_method)) {
            $plugin_data = $this->append_sku_settings($plugin_data, $shipping_method);
        }
        if (self::OPTION_VALUE_YES === $shipping_method->get_instance_option(UspsSettingsDefinition::INSURANCE, self::OPTION_VALUE_NO)) {
            $plugin_data['insurance_option']++;
        }
        if (self::OPTION_VALUE_YES === $shipping_method->get_instance_option(UspsSettingsDefinition::FALLBACK, self::OPTION_VALUE_NO)) {
            $plugin_data['fallback']++;
        }
        //Count free shipping uses.
        if (self::OPTION_VALUE_YES === $shipping_method->get_instance_option(FreeShippingFields::FIELD_STATUS, self::OPTION_VALUE_NO)) {
            $plugin_data['free_shipping']++;
        }
        return $plugin_data;
    }
    /**
     * Append REST API SKU settings counts.
     *
     * @param array              $plugin_data Plugin data.
     * @param UspsShippingMethod $shipping_method Shipping method.
     *
     * @return array
     */
    private function append_sku_settings(array $plugin_data, UspsShippingMethod $shipping_method): array
    {
        $sku_components = new UspsSkuComponents();
        $sku_settings = [UspsSettingsDefinition::SKU_SERVICE_TYPE => $this->get_setting_values($shipping_method->get_instance_option(UspsSettingsDefinition::SKU_SERVICE_TYPE, []), $sku_components->get_default_service_types()), UspsSettingsDefinition::SKU_SERVICE_SUB_TYPE => $this->get_setting_values($shipping_method->get_instance_option(UspsSettingsDefinition::SKU_SERVICE_SUB_TYPE, []), $sku_components->get_default_service_sub_types()), UspsSettingsDefinition::SKU_SHAPE => $this->get_setting_values($shipping_method->get_instance_option(UspsSettingsDefinition::SKU_SHAPE, []), $sku_components->get_default_shapes()), UspsSettingsDefinition::SKU_DELIVERY_TYPE => $this->get_setting_values($shipping_method->get_instance_option(UspsSettingsDefinition::SKU_DELIVERY_TYPE, []), $sku_components->get_default_delivery_types())];
        $plugin_data['sku_service_type'] = $this->append_setting_values($plugin_data['sku_service_type'], $sku_settings[UspsSettingsDefinition::SKU_SERVICE_TYPE]);
        $plugin_data['sku_service_sub_type'] = $this->append_setting_values($plugin_data['sku_service_sub_type'], $sku_settings[UspsSettingsDefinition::SKU_SERVICE_SUB_TYPE]);
        $plugin_data['sku_shape'] = $this->append_setting_values($plugin_data['sku_shape'], $sku_settings[UspsSettingsDefinition::SKU_SHAPE]);
        $plugin_data['sku_delivery_type'] = $this->append_setting_values($plugin_data['sku_delivery_type'], $sku_settings[UspsSettingsDefinition::SKU_DELIVERY_TYPE]);
        $plugin_data['sku_settings'][] = $sku_settings;
        return $plugin_data;
    }
    /**
     * Append setting values counts.
     *
     * @param array $values_counts Setting values counts.
     * @param array $values Setting values.
     *
     * @return array
     */
    private function append_setting_values(array $values_counts, array $values): array
    {
        foreach ($values as $setting_value) {
            if (!isset($values_counts[$setting_value])) {
                $values_counts[$setting_value] = 0;
            }
            $values_counts[$setting_value]++;
        }
        return $values_counts;
    }
    /**
     * Get normalized setting values.
     *
     * @param mixed $setting_values Setting values from shipping method instance.
     * @param array $default_values Default values used by REST API.
     *
     * @return array
     */
    private function get_setting_values($setting_values, array $default_values): array
    {
        if (!is_array($setting_values) || empty($setting_values)) {
            $setting_values = $default_values;
        }
        $values = [];
        foreach ($setting_values as $setting_value) {
            if (!is_scalar($setting_value)) {
                continue;
            }
            $values[] = (string) $setting_value;
        }
        sort($values);
        return array_values(array_unique($values));
    }
    /**
     * Checks if REST API is enabled.
     *
     * @param UspsShippingMethod $shipping_method Shipping method.
     *
     * @return bool
     */
    private function is_rest_api_enabled(UspsShippingMethod $shipping_method): bool
    {
        return UspsSettingsDefinition::API_TYPE_REST === $shipping_method->get_option(UspsSettingsDefinition::API_TYPE, UspsSettingsDefinition::API_TYPE_WEB);
    }
    /**
     * Add plugin data tracker.
     *
     * @param array $data Data.
     *
     * @return array
     */
    public function append_tracker_data_usps(array $data): array
    {
        $shipping_methods = WC()->shipping()->get_shipping_methods();
        if (isset($shipping_methods['flexible_shipping_usps'])) {
            /**
             * IDE type hint.
             *
             * @var UspsShippingMethod $flexible_shipping_usps
             */
            $flexible_shipping_usps = $shipping_methods['flexible_shipping_usps'];
            $plugin_data = $this->prepare_default_plugin_data($flexible_shipping_usps);
            $shipping_zones = WC_Shipping_Zones::get_zones();
            $shipping_zones[0] = ['zone_id' => 0];
            /**
             * IDE type hint.
             *
             * @var WC_Shipping_Zone $zone_data
             */
            foreach ($shipping_zones as $zone_data) {
                $zone = new WC_Shipping_Zone($zone_data['zone_id']);
                $shipping_methods = $zone->get_shipping_methods(\true);
                /**
                 * IDE type hint.
                 *
                 * @var WC_Shipping_Method $shipping_method
                 */
                foreach ($shipping_methods as $shipping_method) {
                    if ($shipping_method instanceof UspsShippingMethod) {
                        $plugin_data = $this->append_data_for_shipping_method($plugin_data, $zone, $shipping_method);
                    }
                }
            }
            $data['flexible_shipping_usps'] = $plugin_data;
        }
        return $data;
    }
}

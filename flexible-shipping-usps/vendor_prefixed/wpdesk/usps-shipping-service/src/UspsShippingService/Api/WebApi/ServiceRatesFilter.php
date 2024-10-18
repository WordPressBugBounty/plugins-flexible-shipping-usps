<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\WebApi;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsServices;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
class ServiceRatesFilter
{
    /**
     * Filter&change rates according to settings.
     *
     * @param SettingsValues $settings Settings.
     * @param bool           $is_domestic Domestic rates.
     * @param SingleRate[]   $usps_rates Response.
     *
     * @return SingleRate[]
     */
    public function filter_service_rates(SettingsValues $settings, bool $is_domestic, array $usps_rates): array
    {
        $rates = [];
        if (!empty($usps_rates)) {
            $all_services = $this->get_services($is_domestic);
            $all_services_keys = array_keys($all_services);
            $services_settings = $this->get_services_settings($settings, $is_domestic);
            if ($this->is_custom_services_enable($settings)) {
                foreach ($usps_rates as $usps_single_rate) {
                    if (isset($usps_single_rate->service_type) && in_array($usps_single_rate->service_type, $all_services_keys) && !empty($services_settings[$usps_single_rate->service_type]['enabled'])) {
                        $usps_single_rate->service_name = $services_settings[$usps_single_rate->service_type]['name'];
                        $rates[$usps_single_rate->service_type] = $usps_single_rate;
                    }
                }
                $rates = $this->sort_services($rates, $services_settings);
            } else {
                foreach ($usps_rates as $usps_single_rate) {
                    if (isset($usps_single_rate->service_type) && in_array($usps_single_rate->service_type, $all_services_keys)) {
                        $usps_single_rate->service_name = $all_services[$usps_single_rate->service_type];
                        $rates[$usps_single_rate->service_type] = $usps_single_rate;
                    }
                }
            }
        }
        return $rates;
    }
    /**
     * Are customs service settings enabled.
     *
     * @param SettingsValues $settings Values.
     *
     * @return bool
     */
    private function is_custom_services_enable(SettingsValues $settings): bool
    {
        return $settings->has_value(UspsSettingsDefinition::CUSTOM_SERVICES) && 'yes' === $settings->get_value(UspsSettingsDefinition::CUSTOM_SERVICES);
    }
    /**
     * @param SettingsValues $settings Settings.
     * @param bool           $is_domestic Domestic rates.
     *
     * @return array
     */
    private function get_services_settings(SettingsValues $settings, bool $is_domestic): array
    {
        if ($is_domestic) {
            $services_settings = $settings->get_value(UspsSettingsDefinition::SERVICES_DOMESTIC, []);
        } else {
            $services_settings = $settings->get_value(UspsSettingsDefinition::SERVICES_INTERNATIONAL, []);
        }
        return is_array($services_settings) ? $services_settings : [];
    }
    /**
     * @param bool $is_domestic .
     *
     * @return array
     */
    private function get_services(bool $is_domestic): array
    {
        $usps_services = new UspsServices();
        if ($is_domestic) {
            return $usps_services->get_services_domestic();
        }
        return $usps_services->get_services_international();
    }
    /**
     * Sort rates according to order set in admin settings.
     *
     * @param SingleRate[] $rates           Rates.
     * @param array        $option_services Saved services to settings.
     *
     * @return SingleRate[]
     */
    private function sort_services(array $rates, array $option_services): array
    {
        if (!empty($option_services)) {
            $services = [];
            foreach ($option_services as $service_code => $service_name) {
                if (isset($rates[$service_code])) {
                    $services[] = $rates[$service_code];
                }
            }
            return $services;
        }
        return $rates;
    }
}

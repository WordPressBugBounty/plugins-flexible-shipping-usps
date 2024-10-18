<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RestApi;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
class ServiceRatesFilterRestApi
{
    private LoggerInterface $logger;
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    /**
     * Filter&change rates according to settings.
     *
     * @param SettingsValues $settings    Settings.
     * @param SingleRate[]   $usps_rates  Response.
     *
     * @return SingleRate[]
     */
    public function filter_service_rates(SettingsValues $settings, array $usps_rates): array
    {
        $service_type = $settings->get_value(UspsSettingsDefinition::SKU_SERVICE_TYPE, []);
        $service_sub_type = $settings->get_value(UspsSettingsDefinition::SKU_SERVICE_SUB_TYPE, []);
        $shape = $settings->get_value(UspsSettingsDefinition::SKU_SHAPE, []);
        $delivery_type = $settings->get_value(UspsSettingsDefinition::SKU_DELIVERY_TYPE, []);
        $rates = [];
        $context = ['Configuration' => ['Allowed service types' => $service_type, 'Allowed service sub types' => $service_sub_type, 'Allowed shapes' => $shape, 'Allowed delivery types' => $delivery_type]];
        foreach ($usps_rates as $usps_single_rate) {
            $sku = $usps_single_rate->service_type;
            if ($this->is_service_matched($sku, $service_type, $service_sub_type, $shape, $delivery_type, $context)) {
                $rates[$usps_single_rate->service_type] = $usps_single_rate;
            }
        }
        $this->logger->info('SKU matching results', $context);
        return $rates;
    }
    private function is_service_matched(string $sku, array $service_type, array $service_sub_type, array $shape, array $delivery_type, array &$context): bool
    {
        $sku_parts = str_split($sku);
        $matched = \true;
        $sku_context = ['SKU' => ['SKU' => $sku, 'Service type' => $sku_parts[1], 'Service sub type' => $sku_parts[2], 'Shape' => $sku_parts[3], 'Delivery type' => $sku_parts[5]]];
        if (in_array($sku_parts[1], $service_type, \true)) {
            $sku_context['Service type matched'] = 'yes';
        } else {
            $sku_context['Service type matched'] = 'no';
            $matched = \false;
        }
        if (in_array($sku_parts[2], $service_sub_type, \true)) {
            $sku_context['Service sub type matched'] = 'yes';
        } else {
            $sku_context['Service sub type matched'] = 'no';
            $matched = \false;
        }
        if (in_array($sku_parts[3], $shape, \true)) {
            $sku_context['Shape matched'] = 'yes';
        } else {
            $sku_context['Shape matched'] = 'no';
            $matched = \false;
        }
        if (in_array($sku_parts[5], $delivery_type, \true)) {
            $sku_context['Delivery type matched'] = 'yes';
        } else {
            $sku_context['Delivery type matched'] = 'no';
            $matched = \false;
        }
        $sku_context['SKU matched'] = $matched ? 'yes' : 'no';
        $context[$sku] = $sku_context;
        return $matched;
    }
}

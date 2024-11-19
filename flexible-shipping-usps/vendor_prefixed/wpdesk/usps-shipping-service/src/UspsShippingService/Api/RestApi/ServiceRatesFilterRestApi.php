<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\RestApi;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Rate\SingleRate;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSkuComponents;
class ServiceRatesFilterRestApi
{
    private const MATCHED_SK_US = 'Matched SKUs';
    private const UNMATCHED_SK_US = 'Unmatched SKUs';
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
        $sku_components = new UspsSkuComponents();
        $service_type = $settings->get_value(UspsSettingsDefinition::SKU_SERVICE_TYPE, []);
        $service_sub_type = $settings->get_value(UspsSettingsDefinition::SKU_SERVICE_SUB_TYPE, []);
        $shape = $settings->get_value(UspsSettingsDefinition::SKU_SHAPE, []);
        $delivery_type = $settings->get_value(UspsSettingsDefinition::SKU_DELIVERY_TYPE, []);
        $rates = [];
        $context = ['Configuration' => ['Allowed service types' => $sku_components->get_filtered_array_by_codes($sku_components->get_service_types(), $service_type), 'Allowed service sub types' => $sku_components->get_filtered_array_by_codes($sku_components->get_service_sub_types(), $service_sub_type), 'Allowed shapes' => $sku_components->get_filtered_array_by_codes($sku_components->get_shapes(), $shape), 'Allowed delivery types' => $sku_components->get_filtered_array_by_codes($sku_components->get_delivery_types(), $delivery_type)], self::MATCHED_SK_US => [], self::UNMATCHED_SK_US => []];
        foreach ($usps_rates as $usps_single_rate) {
            $sku = $usps_single_rate->service_type;
            $service_name = $usps_single_rate->service_name;
            if ($this->is_service_matched($sku_components, $sku, $service_name, $service_type, $service_sub_type, $shape, $delivery_type, $context)) {
                $rates[$usps_single_rate->service_type] = $usps_single_rate;
            }
        }
        $this->logger->info('SKU matching results', $context);
        return $rates;
    }
    private function is_service_matched(UspsSkuComponents $sku_components, string $sku, string $service_name, array $service_type, array $service_sub_type, array $shape, array $delivery_type, array &$context): bool
    {
        $sku_parts = str_split($sku);
        $matched = \true;
        $sku_context = ['SKU' => ['SKU' => $sku, 'Service name' => $service_name, 'Service type' => sprintf('%1$s (%2$s)', $sku_parts[1], $sku_components->get_service_types()[$sku_parts[1]] ?? 'Unknown'), 'Service sub type' => sprintf('%1$s (%2$s)', $sku_parts[2], $sku_components->get_service_sub_types()[$sku_parts[2]] ?? 'Unknown'), 'Shape' => sprintf('%1$s (%2$s)', $sku_parts[3], $sku_components->get_shapes()[$sku_parts[3]] ?? 'Unknown'), 'Delivery type' => sprintf('%1$s (%2$s)', $sku_parts[5], $sku_components->get_delivery_types()[$sku_parts[5]] ?? 'Unknown')]];
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
        if ($matched) {
            $context[self::MATCHED_SK_US][$sku] = $sku_context;
        } else {
            $context[self::UNMATCHED_SK_US][$sku] = $sku_context;
        }
        return $matched;
    }
}

<?php

/**
 * Capability: HasSettings class.
 *
 * @package WPDesk\AbstractShipping\ShippingServiceCapability
 */
namespace FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingServiceCapability;

use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsDefinition;
/**
 * Interface for get settings definition
 *
 * @package WPDesk\AbstractShipping\ShippingServiceCapability
 */
interface HasSettings
{
    /**
     * Get settings definition.
     *
     * @return SettingsDefinition
     */
    public function get_settings_definition();
}

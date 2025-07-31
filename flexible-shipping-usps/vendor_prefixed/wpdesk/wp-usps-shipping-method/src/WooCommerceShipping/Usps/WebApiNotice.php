<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps;

use FlexibleShippingUspsVendor\WPDesk\Notice\Notice;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
class WebApiNotice implements Hookable
{
    private array $usps_settings;
    public function __construct(array $usps_settings)
    {
        $this->usps_settings = $usps_settings;
    }
    public function hooks()
    {
        add_action('admin_notices', array($this, 'usps_xml_api_notice'));
    }
    public function usps_xml_api_notice()
    {
        if (UspsSettingsDefinition::API_TYPE_WEB === ($this->usps_settings[UspsSettingsDefinition::API_TYPE] ?? UspsSettingsDefinition::API_TYPE_REST)) {
            $settings_url = admin_url('admin.php?page=wc-settings&tab=shipping&section=flexible_shipping_usps');
            ob_start();
            include __DIR__ . '/view/web-api-notice.php';
            $content = ob_get_contents();
            ob_end_clean();
            new Notice($content, 'warning');
        }
    }
}

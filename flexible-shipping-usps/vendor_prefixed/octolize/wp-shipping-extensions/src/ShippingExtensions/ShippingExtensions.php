<?php

namespace FlexibleShippingUspsVendor\Octolize\ShippingExtensions;

use FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Tracker\Tracker;
use FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use FlexibleShippingUspsVendor\WPDesk_Plugin_Info;
/**
 * .
 */
class ShippingExtensions implements \FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable
{
    use HookableParent;
    private const VERSION = 2;
    private const OCTOLIZE_WP_SHIPPING_EXTENSIONS_INITIATED_FILTER = 'octolize/shipping-extensions/initiated';
    /**
     * @var WPDesk_Plugin_Info .
     */
    private $plugin_info;
    /**
     * @param WPDesk_Plugin_Info $plugin_info .
     */
    public function __construct(\FlexibleShippingUspsVendor\WPDesk_Plugin_Info $plugin_info)
    {
        $this->plugin_info = $plugin_info;
    }
    /**
     * @return void
     */
    public function hooks() : void
    {
        $this->add_hookable(new \FlexibleShippingUspsVendor\Octolize\ShippingExtensions\PluginLinks($this->plugin_info));
        if (\apply_filters(self::OCTOLIZE_WP_SHIPPING_EXTENSIONS_INITIATED_FILTER, \false) === \false) {
            \add_filter(self::OCTOLIZE_WP_SHIPPING_EXTENSIONS_INITIATED_FILTER, '__return_true');
            $tracker = new \FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Tracker\ViewPageTracker();
            $this->add_hookable(new \FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Page($this->get_assets_url(), $tracker));
            $this->add_hookable(new \FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Assets($this->get_assets_url(), self::VERSION));
            $this->add_hookable(new \FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Tracker\Tracker($tracker));
            $this->add_hookable(new \FlexibleShippingUspsVendor\Octolize\ShippingExtensions\PageViewTracker($tracker));
        }
        $this->hooks_on_hookable_objects();
    }
    /**
     * @return string
     */
    private function get_assets_url() : string
    {
        return \plugin_dir_url(__DIR__ . '/../../../') . 'assets/';
    }
}

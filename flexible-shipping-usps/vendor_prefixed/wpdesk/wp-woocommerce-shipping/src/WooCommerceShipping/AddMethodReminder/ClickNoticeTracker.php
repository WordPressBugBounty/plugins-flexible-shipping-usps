<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\AddMethodReminder;

use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FlexibleShippingUspsVendor\WPDesk\ShowDecision\GetStrategy;
/**
 * Can track click notice link.
 */
class ClickNoticeTracker implements Hookable
{
    use TrackerOption;
    private $method_id;
    /**
     * @param $method_id
     */
    public function __construct($method_id)
    {
        $this->method_id = $method_id;
    }
    public function hooks()
    {
        add_action('admin_init', [$this, 'track_click']);
    }
    public function track_click()
    {
        $display_strategy = new GetStrategy([['page' => 'wc-settings', 'tab' => 'shipping']]);
        if ($display_strategy->shouldDisplay() && isset($_GET['track_click_notice_method']) && wp_verify_nonce($_GET['track_click_notice_method'], $this->method_id)) {
            $this->increase_tracker_option_value($this->method_id);
        }
    }
}

<?php

/**
 * @package WPDesk\WooCommerceShipping
 */
namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingService;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
/**
 * Can provide plugin/service depending decisions for ShippingMethod.
 */
class PluginShippingDecisions
{
    /** @var ShippingService */
    private $service;
    /** @var LoggerInterface */
    private $logger;
    /**
     * AJAX handler for API status field.
     * API Status is checked asynchronously.
     * AJAX handler must be created in Plugin - it adds hooks.
     * Must be passed to ShippingMethod - it is used for nonce creation.
     *
     * @var FieldApiStatusAjax
     */
    private $field_api_status_ajax;
    /**
     * .
     *
     * @param ShippingService $service .
     * @param LoggerInterface $logger .
     *
     */
    public function __construct(ShippingService $service, LoggerInterface $logger)
    {
        $this->service = $service;
        $this->logger = $logger;
    }
    /**
     * @return ShippingService
     */
    public function get_shipping_service()
    {
        return $this->service;
    }
    /**
     * @return LoggerInterface
     */
    public function get_logger()
    {
        return $this->logger;
    }
    /**
     * @param FieldApiStatusAjax $field_api_status_ajax
     */
    public function set_field_api_status_ajax(FieldApiStatusAjax $field_api_status_ajax)
    {
        $this->field_api_status_ajax = $field_api_status_ajax;
    }
    /**
     * @return FieldApiStatusAjax
     */
    public function get_field_api_status_ajax()
    {
        return $this->field_api_status_ajax;
    }
}

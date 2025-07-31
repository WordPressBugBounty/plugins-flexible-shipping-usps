<?php

/**
 * Trait with ShippingService static injection
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\Traits
 */
namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\Traits;

use FlexibleShippingUspsVendor\Monolog\Handler\FilterHandler;
use FlexibleShippingUspsVendor\Monolog\Handler\PsrHandler;
use FlexibleShippingUspsVendor\Monolog\Logger;
use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\Psr\Log\LogLevel;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\ShippingService;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Logger\DisplayNoticeLogger;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod;
/**
 * Facilitates access to ShippingService abstract class with rates.
 *
 * @package WPDesk\WooCommerceShipping\ShippingMethod\Traits
 */
trait ShippingServiceTrait
{
    /**
     * @var LoggerInterface
     */
    private $service_logger;
    /**
     * @param ShippingMethod $shipping_method
     *
     * @return ShippingService
     */
    private function get_shipping_service(ShippingMethod $shipping_method)
    {
        return $shipping_method->get_plugin_shipping_decisions()->get_shipping_service();
    }
    /**
     * Initializes and injects logger into service.
     *
     * @param ShippingService $service
     *
     * @return LoggerInterface
     */
    private function inject_logger_into(ShippingService $service)
    {
        $logger = $this->get_service_logger($service);
        $service->setLogger($logger);
        return $logger;
    }
    /**
     * @param ShippingService $service
     *
     * @return LoggerInterface
     */
    private function get_service_logger(ShippingService $service)
    {
        if (null === $this->service_logger) {
            if ($this->can_see_error_notices()) {
                $logger = new Logger('notice_logger_handler_' . $service->get_unique_id());
                $notice_logger = new PsrHandler(new DisplayNoticeLogger($service->get_name(), $this->instance_id));
                if ($this->can_see_debug_notices()) {
                    $logger->pushHandler($notice_logger);
                } else {
                    $logger->pushHandler(new FilterHandler($notice_logger, LogLevel::ERROR));
                }
                $logger->pushHandler(new PsrHandler($this->get_logger($this)));
                $this->service_logger = $logger;
            } else {
                $this->service_logger = $this->get_logger($this);
            }
        }
        return $this->service_logger;
    }
}

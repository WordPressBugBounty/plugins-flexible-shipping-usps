<?php

namespace FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Api\WebApi;

use FlexibleShippingUspsVendor\Psr\Log\LoggerInterface;
use FlexibleShippingUspsVendor\USPS\RatePackage;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValues;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\Exception\ConnectionCheckerException;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
/**
 * Can check connection.
 */
class ConnectionChecker
{
    /**
     * Settings.
     *
     * @var SettingsValuesAsArray
     */
    private $settings;
    /**
     * Logger.
     *
     * @var LoggerInterface
     */
    private $logger;
    /**
     * ConnectionChecker constructor.
     *
     * @param SettingsValues      $settings .
     * @param LoggerInterface     $logger .
     */
    public function __construct(SettingsValues $settings, LoggerInterface $logger)
    {
        $this->settings = $settings;
        $this->logger = $logger;
    }
    /**
     * Pings API.
     *
     * @throws \Exception .
     */
    public function check_connection()
    {
        $user_id = $this->settings->get_value(UspsSettingsDefinition::USER_ID, '');
        if ('' === $user_id) {
            throw new ConnectionCheckerException(__('Please enter USPS User ID.', 'flexible-shipping-usps'));
        }
        $password = $this->settings->get_value(UspsSettingsDefinition::PASSWORD, '');
        $rate = new Rate($user_id, $password);
        $package = new RatePackage();
        $package->setService(RatePackage::SERVICE_ONLINE);
        $package->setFirstClassMailType(RatePackage::MAIL_TYPE_LETTER);
        $package->setZipOrigination(91601);
        $package->setZipDestination(91730);
        $package->setPounds(0);
        $package->setOunces(3.5);
        $package->setContainer('');
        $package->setSize(RatePackage::SIZE_REGULAR);
        $package->setField('Machinable', \true);
        $rate->addPackage($package);
        $this->logger->debug('Connection checker');
        $this->logger->debug($rate->getXmlRequest());
        $this->logger->debug($rate->getRate());
        if (!$rate->isSuccess()) {
            throw new ConnectionCheckerException($rate->getErrorMessage());
        }
    }
}

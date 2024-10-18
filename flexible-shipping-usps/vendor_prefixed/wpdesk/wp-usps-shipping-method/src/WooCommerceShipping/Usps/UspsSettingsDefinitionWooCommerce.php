<?php

namespace FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps;

use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
/**
 * Can handle global and instance settings for WooCommerce shipping method.
 */
class UspsSettingsDefinitionWooCommerce
{
    private $global_method_fields = [UspsSettingsDefinition::SHIPPING_METHOD_TITLE, UspsSettingsDefinition::API_SETTINGS_TITLE, UspsSettingsDefinition::API_TYPE, UspsSettingsDefinition::REST_API_KEY, UspsSettingsDefinition::REST_API_SECRET_KEY, UspsSettingsDefinition::USER_ID, UspsSettingsDefinition::PASSWORD, UspsSettingsDefinition::TESTING, UspsSettingsDefinition::ORIGIN_SETTINGS_TITLE, UspsSettingsDefinition::CUSTOM_ORIGIN, UspsSettingsDefinition::ORIGIN_ADDRESS, UspsSettingsDefinition::ORIGIN_CITY, UspsSettingsDefinition::ORIGIN_POSTCODE, UspsSettingsDefinition::ORIGIN_COUNTRY, UspsSettingsDefinition::ADVANCED_OPTIONS_TITLE, UspsSettingsDefinition::DEBUG_MODE, UspsSettingsDefinition::API_STATUS];
    /**
     * Form fields.
     *
     * @var array
     */
    private $form_fields;
    /**
     * @var string
     */
    private $current_api_type;
    /**
     * UspsSettingsDefinitionWooCommerce constructor.
     *
     * @param array $form_fields Form fields.
     */
    public function __construct(array $form_fields, $default_api_type_web_api = \true, $current_api_type = UspsSettingsDefinition::API_TYPE_WEB)
    {
        $this->form_fields = $form_fields;
        if ($default_api_type_web_api) {
            $this->form_fields[UspsSettingsDefinition::API_TYPE]['default'] = UspsSettingsDefinition::API_TYPE_WEB;
        }
        if ($current_api_type === UspsSettingsDefinition::API_TYPE_REST) {
            unset($this->form_fields[UspsSettingsDefinition::API_TYPE]['description']);
        }
        $this->current_api_type = $current_api_type;
    }
    /**
     * Get form fields.
     *
     * @return array
     */
    public function get_form_fields()
    {
        return $this->filter_instance_fields($this->form_fields, \false);
    }
    /**
     * Get instance form fields.
     *
     * @return array
     */
    public function get_instance_form_fields()
    {
        return $this->filter_instance_fields($this->form_fields, \true);
    }
    /**
     * Get global method fields.
     *
     * @return array
     */
    protected function get_global_method_fields()
    {
        return $this->global_method_fields;
    }
    /**
     * Filter instance form fields.
     *
     * @param array $all_fields .
     * @param bool  $instance_fields .
     *
     * @return array
     */
    private function filter_instance_fields(array $all_fields, $instance_fields)
    {
        $fields = array();
        foreach ($all_fields as $key => $field) {
            $is_instance_field = !in_array($key, $this->get_global_method_fields(), \true);
            if ($instance_fields && !$is_instance_field || !$instance_fields && $is_instance_field) {
                continue;
            }
            if ($instance_fields && $this->current_api_type !== UspsSettingsDefinition::API_TYPE_REST && strpos($field['class'] ?? 'api-web', 'api-rest') !== \false) {
                continue;
            }
            if ($instance_fields && $this->current_api_type !== UspsSettingsDefinition::API_TYPE_WEB && strpos($field['class'] ?? 'api-rest', 'api-web') !== \false) {
                continue;
            }
            $fields[$key] = $field;
        }
        return $fields;
    }
}

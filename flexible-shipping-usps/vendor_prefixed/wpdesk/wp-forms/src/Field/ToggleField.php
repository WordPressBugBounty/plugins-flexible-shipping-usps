<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Field;

class ToggleField extends CheckboxField
{
    public function __construct()
    {
        $this->add_class('wpd-toggle-field');
    }
    public function get_template_name(): string
    {
        return 'input-toggle';
    }
}

<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Field;

use FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer;
use FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer;
class InputTextField extends \FlexibleShippingUspsVendor\WPDesk\Forms\Field\BasicField
{
    public function get_sanitizer() : \FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer
    {
        return new \FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer\TextFieldSanitizer();
    }
    public function get_template_name() : string
    {
        return 'input-text';
    }
}
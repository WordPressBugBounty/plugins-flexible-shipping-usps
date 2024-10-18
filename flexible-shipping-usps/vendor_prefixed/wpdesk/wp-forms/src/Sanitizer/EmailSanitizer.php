<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer;

use FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer;
class EmailSanitizer implements Sanitizer
{
    public function sanitize($value): string
    {
        return sanitize_email($value);
    }
}

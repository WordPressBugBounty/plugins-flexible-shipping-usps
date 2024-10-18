<?php

namespace FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer;

use FlexibleShippingUspsVendor\WPDesk\Forms\Sanitizer;
class CallableSanitizer implements Sanitizer
{
    /** @var callable */
    private $callable;
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }
    public function sanitize($value): string
    {
        return call_user_func($this->callable, $value);
    }
}

<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

class ResponseErrorException extends \RuntimeException
{
    /**
     * ResponseErrorException constructor.
     *
     * @param mixed $response
     */
    public function __construct($response)
    {
        $message = __('Unknown exception!', 'flexible-shipping-usps');
        if (is_array($response)) {
            if (isset($response['error_description'])) {
                $message = $response['error_description'];
            }
        }
        parent::__construct($message);
    }
}

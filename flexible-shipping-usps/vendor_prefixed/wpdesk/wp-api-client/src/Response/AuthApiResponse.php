<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Response;

use FlexibleShippingUspsVendor\WPDesk\ApiClient\Response\Traits\AuthApiResponseDecorator;
class AuthApiResponse implements ApiResponse
{
    const RESPONSE_CODE_BAD_CREDENTIALS = 401;
    const RESPONSE_CODE_NOT_EXISTS = 404;
    use AuthApiResponseDecorator;
}

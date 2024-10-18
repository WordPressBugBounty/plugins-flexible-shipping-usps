<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

use FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer\Serializer;
/**
 * Can serialize content.
 */
class JsonContentSerializer implements Serializer
{
    /**
     * @param array $data .
     *
     * @return string
     */
    public function serialize($data)
    {
        return json_encode($data);
    }
    /**
     * @param string $data .
     *
     * @return array
     */
    public function unserialize($data)
    {
        return json_decode($data, \true);
    }
    /**
     * @return string
     */
    public function getMime()
    {
        return 'application/x-www-form-urlencoded';
    }
}

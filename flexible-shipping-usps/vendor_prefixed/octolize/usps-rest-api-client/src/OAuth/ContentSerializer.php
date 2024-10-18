<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps\OAuth;

use FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer\Serializer;
/**
 * Can serialize content.
 */
class ContentSerializer implements Serializer
{
    /**
     * @param array $data .
     *
     * @return string
     */
    public function serialize($data)
    {
        $query = '';
        foreach ($data as $key => $value) {
            $query = add_query_arg(rawurldecode($key), rawurldecode($value), $query);
        }
        return trim($query, '?');
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

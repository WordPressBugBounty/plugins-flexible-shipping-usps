<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer;

use FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer\Exception\CannotUnserializeException;
class JsonSerializer implements Serializer
{
    /**
     * Convert data to string
     *
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        return json_encode($data, \JSON_FORCE_OBJECT);
    }
    /**
     * Convert string to php data
     *
     * @param string $data
     * @return mixed
     */
    public function unserialize($data)
    {
        $unserializedResult = json_decode($data, \true);
        if ($unserializedResult === null) {
            throw new CannotUnserializeException("Cannot unserialize data: {$data}");
        }
        return $unserializedResult;
    }
    /**
     * @return string
     */
    public function getMime()
    {
        return 'application/json';
    }
}

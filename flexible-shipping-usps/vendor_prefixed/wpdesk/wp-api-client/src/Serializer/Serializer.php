<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer;

interface Serializer
{
    /**
     * @param mixed $data
     * @return string
     */
    public function serialize($data);
    /**
     * @param string $data
     * @return mixed
     */
    public function unserialize($data);
    /**
     * @return string
     */
    public function getMime();
}

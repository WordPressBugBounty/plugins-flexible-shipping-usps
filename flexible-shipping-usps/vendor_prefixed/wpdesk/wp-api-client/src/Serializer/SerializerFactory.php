<?php

namespace FlexibleShippingUspsVendor\WPDesk\ApiClient\Serializer;

class SerializerFactory
{
    /**
     * @param SerializerOptions $options
     * @return Serializer
     */
    public function createSerializer(SerializerOptions $options)
    {
        $className = $options->getSerializerClass();
        return new $className();
    }
}

<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps\DomesticPrices\Model;

use FlexibleShippingUspsVendor\Octolize\Usps\AbstractSearchParameters;
use FlexibleShippingUspsVendor\Octolize\Usps\CommonSearchParameters;
class DomesticPricesSearchParameters extends AbstractSearchParameters implements \JsonSerializable, CommonSearchParameters
{
    private string $destination_zip_code;
    public function __construct(string $origin_zip_code, string $destination_zip_code)
    {
        $this->origin_zip_code = $origin_zip_code;
        $this->destination_zip_code = $destination_zip_code;
    }
    public function set_destination_zip_code(string $destination_zip_code): DomesticPricesSearchParameters
    {
        $this->destination_zip_code = $destination_zip_code;
        return $this;
    }
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['destinationZIPCode'] = $this->destination_zip_code;
        return $data;
    }
}

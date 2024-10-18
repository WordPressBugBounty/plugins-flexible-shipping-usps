<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps\InternationalPrices\Model;

use FlexibleShippingUspsVendor\Octolize\Usps\AbstractSearchParameters;
use FlexibleShippingUspsVendor\Octolize\Usps\CommonSearchParameters;
class InternationalSearchParameters extends AbstractSearchParameters implements \JsonSerializable, CommonSearchParameters
{
    private string $foreign_postal_code;
    private string $destination_country_code;
    public function __construct(string $origin_zip_code, string $foreign_postal_code, string $destination_country_code)
    {
        $this->origin_zip_code = $origin_zip_code;
        $this->foreign_postal_code = $foreign_postal_code;
        $this->destination_country_code = $destination_country_code;
    }
    public function set_foreign_postal_code(string $foreign_postal_code): InternationalSearchParameters
    {
        $this->foreign_postal_code = $foreign_postal_code;
        return $this;
    }
    public function set_destination_country_code(string $destination_country_code): InternationalSearchParameters
    {
        $this->destination_country_code = $destination_country_code;
        return $this;
    }
    public function jsonSerialize(): array
    {
        $data = parent::jsonSerialize();
        $data['foreignPostalCode'] = $this->foreign_postal_code;
        $data['destinationCountryCode'] = $this->destination_country_code;
        return $data;
    }
}

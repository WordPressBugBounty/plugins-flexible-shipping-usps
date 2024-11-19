<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

interface CommonSearchParameters
{
    public function set_origin_zip_code(string $origin_zip_code): self;
    public function set_weight(float $weight): self;
    public function set_length(float $length): self;
    public function set_width(float $width): self;
    public function set_height(float $height): self;
    public function set_mail_class(string $mail_class): self;
    public function set_price_type(string $price_type): self;
    public function set_mailing_date(\DateTime $mailing_date): self;
    public function set_account_type(string $account_type): self;
    public function set_account_number(string $account_number): self;
    public function set_item_value(float $item_value): self;
    public function set_extra_services(array $extra_services): self;
}

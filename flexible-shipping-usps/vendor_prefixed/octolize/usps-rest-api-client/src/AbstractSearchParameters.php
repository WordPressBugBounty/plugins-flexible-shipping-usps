<?php

namespace FlexibleShippingUspsVendor\Octolize\Usps;

class AbstractSearchParameters implements CommonSearchParameters, \JsonSerializable
{
    protected string $origin_zip_code;
    protected float $weight = 0.0;
    protected float $length = 0.0;
    protected float $width = 0.0;
    protected float $height = 0.0;
    protected string $mail_class = '';
    protected string $price_type = '';
    protected \DateTime $mailing_date;
    protected string $account_type = '';
    protected string $account_number = '';
    protected float $item_value = 0.0;
    protected array $extra_services = [];
    public function set_origin_zip_code(string $origin_zip_code): self
    {
        $this->origin_zip_code = $origin_zip_code;
        return $this;
    }
    public function set_weight(float $weight): self
    {
        $this->weight = $weight;
        return $this;
    }
    public function set_length(float $length): self
    {
        $this->length = $length;
        return $this;
    }
    public function set_width(float $width): self
    {
        $this->width = $width;
        return $this;
    }
    public function set_height(float $height): self
    {
        $this->height = $height;
        return $this;
    }
    public function set_mail_class(string $mail_class): self
    {
        $this->mail_class = $mail_class;
        return $this;
    }
    public function set_price_type(string $price_type): self
    {
        $this->price_type = $price_type;
        return $this;
    }
    public function set_mailing_date(\DateTime $mailing_date): self
    {
        $this->mailing_date = $mailing_date;
        return $this;
    }
    public function set_account_type(string $account_type): self
    {
        $this->account_type = $account_type;
        return $this;
    }
    public function set_account_number(string $account_number): self
    {
        $this->account_number = $account_number;
        return $this;
    }
    public function set_item_value(float $item_value): self
    {
        $this->item_value = $item_value;
        return $this;
    }
    public function set_extra_services(array $extra_services): self
    {
        $this->extra_services = $extra_services;
        return $this;
    }
    public function jsonSerialize(): array
    {
        $data = ['originZIPCode' => $this->origin_zip_code];
        if (!empty($this->extra_services)) {
            $data['extraServices'] = $this->extra_services;
        }
        if ($this->weight) {
            $data['weight'] = $this->weight;
        }
        if ($this->length) {
            $data['length'] = $this->length;
        }
        if ($this->width) {
            $data['width'] = $this->width;
        }
        if ($this->height) {
            $data['height'] = $this->height;
        }
        if ($this->mail_class) {
            $data['mailClass'] = $this->mail_class;
        }
        if ($this->price_type) {
            $data['priceType'] = $this->price_type;
        }
        if (!empty($this->mailing_date)) {
            $data['mailingDate'] = $this->mailing_date->format('Y-m-d');
        }
        if ($this->account_type) {
            $data['accountType'] = $this->account_type;
        }
        if ($this->account_number) {
            $data['accountNumber'] = $this->account_number;
        }
        if ($this->item_value) {
            $data['itemValue'] = $this->item_value;
        }
        return $data;
    }
}

<?php

namespace FlexibleShippingUspsVendor\WPDesk\RepositoryRating\PopupPetition;

use FlexibleShippingUspsVendor\WPDesk\RepositoryRating\PetitionText;
class RepositoryPopupPetitionText implements PetitionText
{
    private string $plugin_title;
    public function __construct(string $plugin_title)
    {
        $this->plugin_title = $plugin_title;
    }
    /**
     * @inheritDoc
     */
    public function get_petition_text(): string
    {
        return sprintf(__('How\'s %1$s so far?', 'flexible-shipping-usps'), $this->plugin_title);
    }
}

<?php

/**
 * BoxesWithUnit interface.
 *
 * @package WPDesk\Packer\BoxFactory
 */
namespace FlexibleShippingUspsVendor\WPDesk\Packer\BoxFactory;

/**
 * Boxes array.
 */
interface BoxesWithUnit extends Boxes
{
    /**
     * Returns true when metric units are used in boxes.
     *
     * @return bool
     */
    public function is_metric();
}

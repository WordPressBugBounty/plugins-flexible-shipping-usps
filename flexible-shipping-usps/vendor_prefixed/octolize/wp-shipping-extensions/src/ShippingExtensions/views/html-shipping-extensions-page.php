<?php

namespace FlexibleShippingUspsVendor;

/**
 * @var string $assets_url .
 * @var Plugin[] $plugins .
 * @var string[] $categories .
 */
use FlexibleShippingUspsVendor\Octolize\ShippingExtensions\Plugin\Plugin;
\defined('ABSPATH') || exit;
?>

<div id="shipping-extensions"
     data-assets_url="<?php 
echo \esc_url($assets_url);
?>"
     data-admin_page_title="<?php 
echo \esc_attr(\get_admin_page_title());
?>"
     data-header_title="<?php 
\esc_attr_e('Shipping Extensions by', 'flexible-shipping-usps');
?>"
     data-header_description="<?php 
\esc_attr_e('Dive into a system of Octolize ecommerce shipping plugins for WooCommerce. Don’t lose your customers, time and money. Let our plugins secure your sales!', 'flexible-shipping-usps');
?>"
     data-default_category="<?php 
\esc_attr_e('All', 'flexible-shipping-usps');
?>"
     data-text_filter="<?php 
\esc_attr_e('Filter plugins:', 'flexible-shipping-usps');
?>"
     data-categories="<?php 
echo \esc_attr(\wp_json_encode($categories));
?>"
     data-plugins="<?php 
echo \esc_attr(\wp_json_encode($plugins));
?>"
>
</div>
<?php 

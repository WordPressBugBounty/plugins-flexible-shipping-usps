<?php
/**
 * Plugin Name: Shipping Live Rates for USPS for WooCommerce
 * Plugin URI: https://wordpress.org/plugins/flexible-shipping-usps/
 * Description: Shipping Live Rates for USPS WooCommerce shipping methods with real-time calculated shipping rates based on the established USPS API connection.
 * Version: 3.0.4
 * Author: Octolize
 * Author URI: https://octol.io/usps-author
 * Text Domain: flexible-shipping-usps
 * Domain Path: /lang/
 * Requires at least: 6.4
 * Tested up to: 6.7
 * WC requires at least: 9.4
 * WC tested up to: 9.8
 * Requires PHP: 7.4
 * ​
 * Copyright 2019 WP Desk Ltd.
 * ​
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * ​
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * ​
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @package WPDesk\FlexibleShippingDhl
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/* THIS VARIABLE CAN BE CHANGED AUTOMATICALLY */
$plugin_version = '3.0.4';

$plugin_name        = 'Live rates for USPS and WooCommerce by Flexible Shipping';
$plugin_class_name  = '\WPDesk\FlexibleShippingUsps\Plugin';
$plugin_text_domain = 'flexible-shipping-usps';
$product_id         = 'Flexible Shipping USPS';
$plugin_file        = __FILE__;
$plugin_dir         = __DIR__;

define( $plugin_class_name, $plugin_version );
define( 'FLEXIBLE_SHIPPING_USPS_VERSION', $plugin_version );

$requirements = [
	'php'          => '7.4',
	'wp'           => '5.7',
	'repo_plugins' => [
		[
			'name'      => 'woocommerce/woocommerce.php',
			'nice_name' => 'WooCommerce',
			'version'   => '6.6',
		],
	],
];

require __DIR__ . '/vendor_prefixed/wpdesk/wp-plugin-flow-common/src/plugin-init-php52-free.php';

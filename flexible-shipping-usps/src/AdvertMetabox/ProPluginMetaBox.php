<?php

namespace WPDesk\FlexibleShippingUsps\AdvertMetabox;

use FlexibleShippingUspsVendor\Octolize\Brand\Assets\AdminAssets;
use FlexibleShippingUspsVendor\Octolize\Brand\UpsellingBox\SettingsSidebar;
use FlexibleShippingUspsVendor\Octolize\Brand\UpsellingBox\ShippingMethodInstanceShouldShowStrategy;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use FlexibleShippingUspsVendor\WPDesk\ShowDecision\OrStrategy;
use FlexibleShippingUspsVendor\WPDesk\ShowDecision\WooCommerce\ShippingMethodStrategy;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsShippingService;

class ProPluginMetaBox implements Hookable, HookableCollection {

	use HookableParent;

	private string $assets_url;

	public function __construct( string $assets_url ) {
		$this->assets_url = $assets_url;
	}

	public function hooks() {
		$should_show_strategy = new OrStrategy( new ShippingMethodStrategy( UspsShippingService::UNIQUE_ID ) );
		$should_show_strategy->addCondition( new ShippingMethodInstanceShouldShowStrategy( new \WC_Shipping_Zones(), UspsShippingService::UNIQUE_ID ) );
		$this->add_hookable( new AdminAssets( $this->assets_url, 'ups', $should_show_strategy ) );

		add_action(
			'admin_init',
			function () use ( $should_show_strategy ) {
				$settings_sidebar = new SettingsSidebar(
					'flexible_shipping_usps_settings_sidebar',
					$should_show_strategy,
					__( 'Get USPS WooCommerce Live Rates PRO!', 'flexible-shipping-usps' ),
					[
						__( 'Handling Fees', 'flexible-shipping-usps' ),
						__( 'Automatic Box Packing', 'flexible-shipping-usps' ),
						__( 'Premium Support', 'flexible-shipping-usps' ),
						__( 'Multicurrency Support', 'flexible-shipping-usps' ),
					],
					'https://octol.io/usps-up-box',
					__( 'Upgrade Now', 'flexible-shipping-usps' ),
					1320,
					20,
					'#mainform h2:first,#mainform h3:first'
				);
				( $settings_sidebar )->hooks();
			}
		);

		$this->hooks_on_hookable_objects();
	}

}

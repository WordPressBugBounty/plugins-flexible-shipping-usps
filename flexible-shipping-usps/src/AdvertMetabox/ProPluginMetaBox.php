<?php

namespace WPDesk\FlexibleShippingUsps\AdvertMetabox;

use FlexibleShippingUspsVendor\Octolize\Brand\Assets\AdminAssets;
use FlexibleShippingUspsVendor\Octolize\Brand\UpgradeBox\SettingsSidebarBox;
use FlexibleShippingUspsVendor\Octolize\Brand\UpsellingBox\ShippingMethodInstanceShouldShowStrategy;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use FlexibleShippingUspsVendor\WPDesk\ShowDecision\OrStrategy;
use FlexibleShippingUspsVendor\WPDesk\ShowDecision\WooCommerce\ShippingMethodStrategy;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsShippingService;

/**
 * Displays the USPS PRO offer beside shipping settings.
 */
class ProPluginMetaBox implements Hookable, HookableCollection {

	use HookableParent;

	private string $assets_url;

	public function __construct( string $assets_url ) {
		$this->assets_url = $assets_url;
	}

	public function hooks(): void {
		$should_show_strategy = new OrStrategy( new ShippingMethodStrategy( UspsShippingService::UNIQUE_ID ) );
		$should_show_strategy->addCondition( new ShippingMethodInstanceShouldShowStrategy( new \WC_Shipping_Zones(), UspsShippingService::UNIQUE_ID ) );
		$this->add_hookable( new AdminAssets( $this->assets_url, 'usps', $should_show_strategy ) );

		add_action(
			'admin_init',
			function () use ( $should_show_strategy ): void {
				$settings_sidebar = new SettingsSidebarBox(
					'flexible_shipping_usps_settings_sidebar',
					$should_show_strategy,
					( new UspsProOffer() )->create( 'https://octol.io/usps-up-box' ),
					[
						'min_width'            => 1200,
						'position_right'       => 20,
						'align_top_to_element' => '#mainform h2:first,#mainform h3:first',
					]
				);
				$settings_sidebar->hooks();
			}
		);

		$this->hooks_on_hookable_objects();
	}
}

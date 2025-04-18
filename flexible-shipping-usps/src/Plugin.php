<?php
/**
 * Plugin main class.
 *
 * @package WPDesk\FlexibleShippingUsps
 */

namespace WPDesk\FlexibleShippingUsps;

use FlexibleShippingUspsVendor\Octolize\Onboarding\PluginUpgrade\MessageFactory\LiveRatesFsRulesTable;
use FlexibleShippingUspsVendor\Octolize\Onboarding\PluginUpgrade\PluginUpgradeMessage;
use FlexibleShippingUspsVendor\Octolize\Onboarding\PluginUpgrade\PluginUpgradeOnboardingFactory;
use FlexibleShippingUspsVendor\Octolize\ShippingExtensions\ShippingExtensions;
use FlexibleShippingUspsVendor\Octolize\Tracker\DeactivationTracker\OctolizeReasonsFactory;
use FlexibleShippingUspsVendor\Octolize\Tracker\TrackerInitializer;
use FlexibleShippingUspsVendor\Octolize\Usps\RestApiFactory;
use FlexibleShippingUspsVendor\WPDesk\AbstractShipping\Settings\SettingsValuesAsArray;
use FlexibleShippingUspsVendor\WPDesk\Logger\Processor\SensitiveDataProcessor;
use FlexibleShippingUspsVendor\WPDesk\Logger\SimpleLoggerFactory;
use FlexibleShippingUspsVendor\WPDesk\Notice\AjaxHandler;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\AbstractPlugin;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\HookableCollection;
use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\HookableParent;
use FlexibleShippingUspsVendor\WPDesk\RepositoryRating\DisplayStrategy\ShippingMethodDisplayDecision;
use FlexibleShippingUspsVendor\WPDesk\RepositoryRating\RatingPetitionNotice;
use FlexibleShippingUspsVendor\WPDesk\RepositoryRating\RepositoryRatingPetitionText;
use FlexibleShippingUspsVendor\WPDesk\RepositoryRating\TextPetitionDisplayer;
use FlexibleShippingUspsVendor\WPDesk\RepositoryRating\TimeWatcher\ShippingMethodGlobalSettingsWatcher;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsShippingService;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\CustomFields\ApiStatus\FieldApiStatusAjax;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\OrderMetaData\AdminOrderMetaDataDisplay;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\OrderMetaData\FrontOrderMetaDataDisplay;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\OrderMetaData\SingleAdminOrderMetaDataInterpreterImplementation;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\PluginShippingDecisions;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingBuilder\WooCommerceShippingMetaDataBuilder;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShippingMethod\RulesTableAdv;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\ShopSettings;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\FallbackAdminMetaDataInterpreter;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Ups\MetaDataInterpreters\PackedPackagesAdminMetaDataInterpreter;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\Api\Rest\OauthApiCached;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\ShippingMethodsChecker;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\Tracker;
use FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Usps\UspsShippingMethod;
use FlexibleShippingUspsVendor\WPDesk_Plugin_Info;
use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareInterface;
use FlexibleShippingUspsVendor\Psr\Log\LoggerAwareTrait;
use FlexibleShippingUspsVendor\Psr\Log\NullLogger;
use Octolize\Brand\UpsellingBox\SettingsSidebar;
use WPDesk\FlexibleShippingUsps\AdvertMetabox\ProPluginMetaBox;

/**
 * Main plugin class. The most important flow decisions are made here.
 *
 * @package WPDesk\FlexibleShippingUsps
 */
class Plugin extends AbstractPlugin implements LoggerAwareInterface, HookableCollection {

	use LoggerAwareTrait;
	use HookableParent;

	/**
	 * Scripts version.
	 *
	 * @var string
	 */
	private $scripts_version = '1';

	/**
	 * Plugin constructor.
	 *
	 * @param WPDesk_Plugin_Info $plugin_info Plugin info.
	 */
	public function __construct( WPDesk_Plugin_Info $plugin_info ) {
		if ( defined( 'FLEXIBLE_SHIPPING_USPS_VERSION' ) ) {
			$this->scripts_version = FLEXIBLE_SHIPPING_USPS_VERSION . '.' . $this->scripts_version;
		}
		parent::__construct( $plugin_info );
		$this->setLogger( $this->is_debug_mode() ? ( new SimpleLoggerFactory( 'usps' ) )->getLogger()->pushProcessor( new SensitiveDataProcessor(
			[
				'USERID="' . $this->get_global_usps_settings()[UspsSettingsDefinition::USER_ID] . '"' => 'USERID="***"',
				'PASSWORD="' . $this->get_global_usps_settings()[UspsSettingsDefinition::PASSWORD] . '"' => 'PASSWORD="***"',
			]
		) ) : new NullLogger() );

		$this->plugin_url       = $this->plugin_info->get_plugin_url();
		$this->plugin_namespace = $this->plugin_info->get_text_domain();
	}

	/**
	 * Returns true when debug mode is on.
	 *
	 * @return bool
	 */
	private function is_debug_mode() {
		$global_usps_settings = $this->get_global_usps_settings();

		return isset( $global_usps_settings['debug_mode'] ) && 'yes' === $global_usps_settings['debug_mode'];
	}


	/**
	 * Get global USPS settings.
	 *
	 * @return string[]
	 */
	private function get_global_usps_settings() {
		// @phpstan-ignore-next-line.
		return get_option( 'woocommerce_' . UspsShippingService::UNIQUE_ID . '_settings', [] );
	}

	/**
	 * Init plugin
	 *
	 * @return void
	 */
	public function init() {
		$global_usps_settings = new SettingsValuesAsArray( $this->get_global_usps_settings() );

		$origin_country = $this->get_origin_country_code( $global_usps_settings );

		//
		$customer_id     = $global_usps_settings->get_value( UspsSettingsDefinition::REST_API_KEY, '' );
		$customer_secret = $global_usps_settings->get_value( UspsSettingsDefinition::REST_API_SECRET_KEY, '' );
		$rest_api        = ( new RestApiFactory() )->create(
			$customer_id,
			$customer_secret,
			$this->logger,
			OauthApiCached::class
		);

		// @phpstan-ignore-next-line.
		$usps_service = apply_filters(
			'flexible_shipping_usps_shipping_service',
			new UspsShippingService(
				$this->logger,
				new ShopSettings( UspsShippingService::UNIQUE_ID ),
				$origin_country,
				$rest_api
			)
		);

		$this->add_hookable(
			new \FlexibleShippingUspsVendor\WPDesk\WooCommerceShipping\Assets(
				$this->get_plugin_url().'vendor_prefixed/wpdesk/wp-woocommerce-shipping/assets', 'usps'
			)
		);
		add_action( 'init', [ $this, 'init_repository_rating' ] );

		add_action(
			'init',
			function () {
				$admin_meta_data_interpreter = new AdminOrderMetaDataDisplay( UspsShippingService::UNIQUE_ID );
				$admin_meta_data_interpreter->add_interpreter(
					new SingleAdminOrderMetaDataInterpreterImplementation(
						WooCommerceShippingMetaDataBuilder::SERVICE_TYPE,
						__( 'Service Code', 'flexible-shipping-usps' )
					)
				);
				$admin_meta_data_interpreter->add_interpreter( new FallbackAdminMetaDataInterpreter() );
				$admin_meta_data_interpreter->add_hidden_order_item_meta_key(
					WooCommerceShippingMetaDataBuilder::COLLECTION_POINT
				);
				$admin_meta_data_interpreter->add_interpreter( new PackedPackagesAdminMetaDataInterpreter() );
				( $admin_meta_data_interpreter )->hooks();

				$meta_data_interpreter = new FrontOrderMetaDataDisplay( UspsShippingService::UNIQUE_ID );
				( $meta_data_interpreter )->hooks();
			}
		);

		/**
		 * Handles API Status AJAX requests.
		 *
		 * @var FieldApiStatusAjax $api_ajax_status_handler .
		 */
		// @phpstan-ignore-next-line.
		$api_ajax_status_handler = new FieldApiStatusAjax( $usps_service, $global_usps_settings, $this->logger );
		$this->add_hookable( $api_ajax_status_handler );

		$this->add_hookable( new PluginLinks( $this->plugin_info->get_plugin_file_name() ) );

		// @phpstan-ignore-next-line.
		$plugin_shipping_decisions = new PluginShippingDecisions( $usps_service, $this->logger );
		$plugin_shipping_decisions->set_field_api_status_ajax( $api_ajax_status_handler );

		UspsShippingMethod::set_plugin_shipping_decisions( $plugin_shipping_decisions );

		add_action( 'init', [ $this, 'init_tracker' ] );
		add_action( 'init', [ $this, 'init_upgrade_onboarding' ] );

		parent::init();
	}

	public function init_upgrade_onboarding() {
		$upgrade_onboarding = new PluginUpgradeOnboardingFactory(
			$this->plugin_info->get_plugin_name(),
			$this->plugin_info->get_version(),
			$this->plugin_info->get_plugin_file_name()
		);
		$upgrade_onboarding->add_upgrade_message(
			new PluginUpgradeMessage(
				'1.9.0',
				$this->plugin_info->get_plugin_url() . '/assets/images/icon-package-add.svg',
				__( 'We added USPS API Password field', 'flexible-shipping-usps' ),
				__( 'On January 31, 2024, USPS will be changing the API used to present live rates to your customers. We have made the necessary improvements to ensure that our plugin is ready for this update. However, in order for USPS live rates to be visible in your checkout, you will need to provide a password in the plugin settings.', 'flexible-shipping-usps' ),
				'',
				''
			)
		);
		$upgrade_onboarding->add_upgrade_message( ( new LiveRatesFsRulesTable() )->create_message( '2.0.0', $this->plugin_info->get_plugin_url() ) );
		$upgrade_onboarding->create_onboarding();
	}

	/**
	 * @return void
	 */
	public function init_tracker(): void {
		(
			TrackerInitializer::create_from_plugin_info_for_shipping_method(
				$this->plugin_info,
				UspsShippingService::UNIQUE_ID,
				new OctolizeReasonsFactory(
					'https://octol.io/usps-docs-exit-pop-up',
					'https://octol.io/usps-support-forum-exit-pop-up',
					__( 'Flexible Shipping USPS PRO', 'flexible-shipping-usps' ),
					'https://octol.io/usps-contact-exit-pop-up'
				)
			)
		)->hooks();

		( new Tracker() )->hooks();
	}

	/**
	 * Show repository rating notice when time comes.
	 *
	 * @return void
	 */
	public function init_repository_rating() {
		( new AjaxHandler( trailingslashit( $this->get_plugin_url() ) . 'vendor_prefixed/wpdesk/wp-notice/assets' ) )->hooks();

		$time_tracker = new ShippingMethodGlobalSettingsWatcher( UspsShippingService::UNIQUE_ID );
		( $time_tracker )->hooks();
		(
			new RatingPetitionNotice(
				$time_tracker,
				UspsShippingService::UNIQUE_ID,
				$this->plugin_info->get_plugin_name(),
				'https://octol.io/rate-usps'
			)
		)->hooks();

		(
			new TextPetitionDisplayer(
				'woocommerce_after_settings_shipping',
				new ShippingMethodDisplayDecision( new \WC_Shipping_Zones(), 'flexible_shipping_usps' ),
				new RepositoryRatingPetitionText(
					'Flexible Shipping',
					__( 'Live rates for USPS and WooCommerce', 'flexible-shipping-usps' ),
					'https://octol.io/rate-usps',
					'center'
				)
			)
		)->hooks();
	}

	/**
	 * Init hooks.
	 *
	 * @return void
	 */
	public function hooks() {
		parent::hooks();

		add_filter( 'woocommerce_shipping_methods', [ $this, 'woocommerce_shipping_methods_filter' ], 20, 1 );

		$this->add_hookable( new ProPluginMetaBox( $this->get_plugin_assets_url() . '../vendor_prefixed/octolize/wp-octolize-brand-assets/assets/' ) );

		$this->add_hookable( new ShippingExtensions( $this->plugin_info ) );

		$this->add_hookable( new ShippingMethodsChecker() );

		$this->hooks_on_hookable_objects();
	}

	/**
	 * Adds shipping method to Woocommerce.
	 *
	 * @param string[] $methods Methods.
	 *
	 * @return string[]
	 */
	public function woocommerce_shipping_methods_filter( $methods ) {
		$methods[ UspsShippingService::UNIQUE_ID ] = UspsShippingMethod::class;

		return $methods;
	}

	/**
	 * Quick links on plugins page.
	 *
	 * @param string[] $links .
	 *
	 * @return string[]
	 */
	public function links_filter( $links ) {
		$settings_url = \admin_url( 'admin.php?page=wc-settings&tab=shipping&section=flexible_shipping_usps' );

		$plugin_links = [
			'<a href="' . esc_url( $settings_url ) . '">' . __( 'Settings', 'flexible-shipping-usps' ) . '</a>',
		];

		if ( ! defined( 'FLEXIBLE_SHIPPING_USPS_PRO_VERSION' ) ) {
			$upgrade_link   = 'https://octol.io/usps-upgrade';
			$plugin_links[] = '<a target="_blank" href="' . $upgrade_link . '" style="color:#d64e07;font-weight:bold;">' . __( 'Buy PRO', 'flexible-shipping-usps' ) . '</a>';
		}

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Get origin country code.
	 *
	 * @param SettingsValuesAsArray $global_usps_settings .
	 *
	 * @return string
	 */
	private function get_origin_country_code( $global_usps_settings ) {
		if ( 'yes' === $global_usps_settings->get_value( UspsSettingsDefinition::CUSTOM_ORIGIN, 'no' ) ) {
			$origin_country_code_with_state = $global_usps_settings->get_value( UspsSettingsDefinition::ORIGIN_COUNTRY, '' );
		} else {
			$origin_country_code_with_state = get_option( 'woocommerce_default_country', '' );
		}

		// @phpstan-ignore-next-line.
		[ $origin_country ] = explode( ':', $origin_country_code_with_state );

		return $origin_country;
	}

}

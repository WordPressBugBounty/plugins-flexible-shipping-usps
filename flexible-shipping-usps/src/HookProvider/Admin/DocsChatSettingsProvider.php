<?php

declare( strict_types=1 );

namespace WPDesk\FlexibleShippingUsps\HookProvider\Admin;

use FlexibleShippingUspsVendor\Octolize\Docs\Chat\ChatSettings;
use FlexibleShippingUspsVendor\Octolize\Docs\Chat\ChatSettingsProvider as ChatSettingsProviderInterface;
use FlexibleShippingUspsVendor\Octolize\Docs\Chat\ConsentSettings;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsSettingsDefinition;
use FlexibleShippingUspsVendor\WPDesk\UspsShippingService\UspsShippingService;

/**
 * Provides USPS settings and context for the docs chat.
 */
final class DocsChatSettingsProvider implements ChatSettingsProviderInterface {

	private const MASKED_VALUE = '********';

	private string $plugin_name;

	public function __construct( string $plugin_name ) {
		$this->plugin_name = $plugin_name;
	}

	/**
	 * @param array<string, mixed> $settings     Current chat settings.
	 * @param array<string, mixed> $request_data Request data.
	 *
	 * @return array<string, mixed>
	 */
	public function prepare_settings( array $settings, array $request_data ): array {
		$chat_settings = new ChatSettings();
		$chat_settings->set_consent_settings( $this->prepare_consent_settings() );
		$chat_settings->set_plugin( $this->plugin_name );
		$chat_settings->set_plugin_settings( $this->get_masked_plugin_settings() );

		$instance_id = $request_data['instance_id'] ?? '';
		$chat_settings->set_current_page( $instance_id ? 'Shipping method' : 'Plugin Settings' );

		if ( $instance_id ) {
			$shipping_method = \WC_Shipping_Zones::get_shipping_method( (int) $instance_id );
			if ( $shipping_method instanceof \WC_Shipping_Method ) {
				$chat_settings->set_shipping_method_settings( $shipping_method->instance_settings );
			}
		}

		return array_merge( $settings, $chat_settings->get_settings() );
	}

	/** @return array<string, mixed> */
	private function get_masked_plugin_settings(): array {
		$settings = get_option( 'woocommerce_' . UspsShippingService::UNIQUE_ID . '_settings', [] );
		if ( ! is_array( $settings ) ) {
			return [];
		}

		foreach ( $this->sensitive_settings_keys() as $key ) {
			if ( array_key_exists( $key, $settings ) ) {
				$settings[ $key ] = self::MASKED_VALUE;
			}
		}

		return $settings;
	}

	/** @return string[] */
	private function sensitive_settings_keys(): array {
		return [
			UspsSettingsDefinition::REST_API_KEY,
			UspsSettingsDefinition::REST_API_SECRET_KEY,
			UspsSettingsDefinition::USER_ID,
			UspsSettingsDefinition::PASSWORD,
		];
	}

	private function prepare_consent_settings(): ConsentSettings {
		$consent_settings = new ConsentSettings();
		$consent_settings->set_title( __( 'Do you consent to sending data to the chat?', 'flexible-shipping-usps' ) );
		$consent_settings->set_message( __( 'To start the chat, we need to send your inputs and technical data to the chat service.', 'flexible-shipping-usps' ) );
		$consent_settings->set_accept( __( 'Accept', 'flexible-shipping-usps' ) );
		$consent_settings->set_decline( __( 'Decline', 'flexible-shipping-usps' ) );
		$consent_settings->set_privacy_policy_url( ' ' );
		$consent_settings->set_privacy_link_label( ' ' );
		$consent_settings->set_support_url( 'https://wordpress.org/support/plugin/flexible-shipping-usps/' );
		$consent_settings->set_support_text( __( 'If you don’t want to give consent, you can contact our support using this: ', 'flexible-shipping-usps' ) );
		$consent_settings->set_support_link_label( __( 'support forum', 'flexible-shipping-usps' ) );

		return $consent_settings;
	}
}

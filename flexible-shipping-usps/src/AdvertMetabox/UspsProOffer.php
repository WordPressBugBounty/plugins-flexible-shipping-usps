<?php
/**
 * USPS PRO offer.
 *
 * @package WPDesk\FlexibleShippingUsps\AdvertMetabox
 */

declare( strict_types=1 );

namespace WPDesk\FlexibleShippingUsps\AdvertMetabox;

/**
 * Provides the USPS PRO offer for the free plugin.
 */
final class UspsProOffer {

	/**
	 * Creates the localized offer data.
	 *
	 * @param string $url Upgrade URL.
	 *
	 * @return array<string, mixed>
	 */
	public function create( string $url ): array {
		return [
			'eyebrow'      => __( 'USPS PRO', 'flexible-shipping-usps' ),
			'headline'     => __( 'Ship from the address that fits the order', 'flexible-shipping-usps' ),
			'description'  => __( 'The free version calculates live USPS rates. PRO lets you ship from a different origin address and adds a precise handling fee.', 'flexible-shipping-usps' ),
			'current'      => [
				'label' => __( 'One origin address', 'flexible-shipping-usps' ),
				'badge' => __( 'NOW', 'flexible-shipping-usps' ),
			],
			'upgrade'      => [
				'label' => __( 'Custom origin address', 'flexible-shipping-usps' ),
				'badge' => __( 'PRO', 'flexible-shipping-usps' ),
			],
			'benefits'     => [
				[
					'icon' => 'pin',
					'text' => __( 'Ship from an origin address different from your WooCommerce default', 'flexible-shipping-usps' ),
				],
				[
					'icon' => 'box',
					'text' => __( 'Combine several products into one carton instead of paying for separate shipments', 'flexible-shipping-usps' ),
				],
				[
					'icon' => 'percent',
					'text' => __( 'Add fixed or percentage handling fees to USPS rates', 'flexible-shipping-usps' ),
				],
			],
			'social_proof' => __( 'Trusted by 250,000+ WooCommerce stores', 'flexible-shipping-usps' ),
			'guarantee'    => __( '30-day money-back guarantee - risk-free', 'flexible-shipping-usps' ),
			'cta_label'    => __( 'Unlock address', 'flexible-shipping-usps' ),
			'cta_url'      => $url,
		];
	}
}

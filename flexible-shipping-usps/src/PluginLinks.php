<?php

namespace WPDesk\FlexibleShippingUsps;

use FlexibleShippingUspsVendor\WPDesk\PluginBuilder\Plugin\Hookable;

class PluginLinks implements Hookable {

	private string $plugin_file;

	public function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	public function hooks(): void {
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 4 );
	}

	/**
	 * Add links to plugin row meta.
	 *
	 * @param array  $plugin_meta Plugin meta.
	 * @param string $plugin_file Plugin file.
	 * @param array  $plugin_data Plugin data.
	 * @param string $status      Plugin status.
	 *
	 * @return array
	 */
	public function plugin_row_meta( $plugin_meta, $plugin_file, $plugin_data, $status ) {
		if ( $plugin_file === $this->plugin_file ) {
			$docs_link    = 'https://octol.io/usps-docs';
			$support_link = 'https://octol.io/usps-support';

			$external_attributes = ' target="_blank" ';

			$plugin_links = [
				'<a href="' . esc_url( $docs_link ) . '"' . $external_attributes . '>' . __( 'Docs', 'flexible-shipping-usps' ) . '</a>',
				'<a href="' . esc_url( $support_link ) . '"' . $external_attributes . '>' . __( 'Support', 'flexible-shipping-usps' ) . '</a>',
			];

			return array_merge( $plugin_meta, $plugin_links );
		}

		return $plugin_meta;
	}

}

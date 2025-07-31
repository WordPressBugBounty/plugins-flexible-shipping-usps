<?php

namespace FlexibleShippingUspsVendor;

/**
 * @var string $settings_url
 */
?>
<p><strong><?php 
echo \esc_html(\__('USPS Web Tools API is being retired — action required'));
?></strong></p>
<p><?php 
echo \wp_kses_post(\sprintf(\__('USPS will retire the legacy Web Tools API platform on %1$sJanuary 25, 2026%2$s. Our plugin already supports the new USPS API, but to avoid disruptions, you need to update your settings.', 'flexible-shipping-usps'), '<strong>', '</strong>'));
?></p>

<p><?php 
\esc_html_e('What you need to do:', 'flexible-shipping-usps');
?><br/>
	<?php 
echo \wp_kses_post(\sprintf(\__(' - Go to %1$splugin settings%2$s,', 'flexible-shipping-usps'), '<a href="' . $settings_url . '">', '</a>'));
?><br/>
	<?php 
echo \wp_kses_post(\sprintf(\__(' - Follow %1$sthis guide%2$s to get your new USPS API credentials,', 'flexible-shipping-usps'), '<a href="https://octolize.com/docs/article/usps-how-to-get-acces-to-usps-rest-api/" target="_blank">', '</a>'));
?><br/>
	<?php 
echo \wp_kses_post(\sprintf(\__(' - Reconfigure your USPS shipping methods as explained %1$shere%2$s,', 'flexible-shipping-usps'), '<a href="https://octolize.com/docs/article/usps-usps-rest-api-services/" target="_blank">', '</a>'));
?><br/>
	<?php 
\esc_html_e(' - Save the changes.', 'flexible-shipping-usps');
?><br/>
	<?php 
echo \wp_kses_post(\sprintf(\__(' - Make sure to complete the update before %1$sJanuary 25, 2026%2$s to keep showing USPS live rates at checkout.', 'flexible-shipping-usps'), '<strong>', '</strong>'));
?>
</p>

<p><?php 
\esc_html_e('Thank you for using USPS Live Rates!', 'flexible-shipping-usps');
?></p>
<?php 

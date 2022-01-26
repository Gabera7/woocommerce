<?php

/**
 *
 * Tracking Number content template
 *
 * The file is prone to modifications after plugin upgrade or alike; customizations are advised via hooks/filters
 *
 */

if (!defined('ABSPATH')) {
	exit;
}
/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email); ?>
<p><?php _e('Sua encomenda foi enviada!', 'woocommerce'); ?></p>
<?php
//Get the field that needs to be sent on this email and add it to the template 
$custom_field = $order->get_meta('dlinfo');
echo '<strong> Número de rastreio: </strong>' . $custom_field;
?>
<p><?php _e('', 'woocommerce'); ?></p>
<p><?php _e('Abaixo estão os detalhes de sua encomenda.') ?></p>
<?php




if ($order && $billing_first_name && $billing_last_name) : ?>
	<p><?php printf($opening_paragraph, $billing_first_name . ' ' . $billing_last_name); ?></p>
<?php endif; ?>
<?php
/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Emails::order_schema_markup() Adds Schema.org markup.
 * @since 2.5.0
 */
do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);
/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);
/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action('woocommerce_email_footer', $email);

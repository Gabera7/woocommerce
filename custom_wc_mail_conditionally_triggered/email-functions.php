<?php
add_filter('woocommerce_email_classes', 'custom_tracking_email');
function custom_tracking_email($email_classes)
{
	//* Custom email class
	$upload_dir = wp_upload_dir();

	require_once($upload_dir['basedir'] . '/custom-emails/class-tracking-number-email.php');
	$email_classes['WC_Email_Customer_Tracking_Number'] = new WC_Email_Customer_Tracking_Number(); // add to the list of email classes that WooCommerce loads
	return $email_classes;
}

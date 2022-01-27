<?php

$upload_dir = wp_upload_dir();
include_once( $upload_dir['basedir'] . '/custom-emails/email-functions.php' );


//Create new order status
add_action( 'init', 'register_shipped_order_status' );

function register_shipped_order_status() {
register_post_status( 'wc-shipped', array(
'label' => 'Shipped',
'public' => true,
'show_in_admin_status_list' => true,
'show_in_admin_all_list' => true,
'exclude_from_search' => false,
'label_count' => _n_noop( 'Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>' )
) );
}



// Add order status after specific option (processing)
add_filter( 'wc_order_statuses', 'add_shipped_to_order_statuses' );
function add_shipped_to_order_statuses( $order_statuses ) {
$new_order_statuses = array();
foreach ( $order_statuses as $key => $status ) {
$new_order_statuses[ $key ] = $status;
if ( 'wc-processing' === $key ) {
$new_order_statuses['wc-shipped'] = 'Shipped';
}
}
return $new_order_statuses;
}


//Trigger specific email class

add_action('woocommerce_order_status_changed', 'custom_shipped_notification', 10, 4);
function custom_shipped_notification( $order_id, $from_status, $to_status, $order ) {
global $woocommerce;
$order = new WC_Order( $order_id );
if( $order->has_status( 'shipped' )) {

// Getting all WC_emails objects
$email_notifications = WC()->mailer()->get_emails();

// Sending the customized email class previously created
$email_notifications['WC_Email_Customer_Tracking_Number']->trigger( $order_id );
}

}


add_filter( 'cron_schedules', 'custom_cron_10min' );
function custom_cron_10min( $schedules ) { 
    $schedules['ten_minutes'] = array(
        'interval' => 600,
        'display'  => esc_html__( 'Every Ten Minutes' ), );
    return $schedules;
}
add_action( 'trigger_cron', 'trigger_cron_exec' );
//Schedule the action hook using the WP Cron we setup above.

if ( ! wp_next_scheduled( 'trigger_cron' ) ) {
    wp_schedule_event( time(), 'ten_minutes', 'trigger_cron' );
}
// Create the function that is called in your hook.

function trigger_cron_exec() {
	
	
	$orders = wc_get_orders( array('numberposts' => -1) );

	// Loop through each WC_Order object
	foreach( $orders as $order ){
  	$id = $order->get_id(); 
	// update_post_meta ( $id, 'dlinfo', '321' ); - used for testing
	$custom_field = get_post_meta($id, 'dlinfo', true);
	$order_status  = $order->get_status();
	//var_dump($custom_field);
	if ($custom_field != '' || $custom_field != null && $order_status == 'processing' ) {
    $order->update_status( 'shipped' );
			
	}
}
	
	foreach( $orders as $order ){
 	 
		$id = $order->get_id(); 
		$order_status  = $order->get_status();
		$mail_sent = get_post_meta($id, '_tracking_email_sent', true);
		if (  $order_status == 'shipped' && $mail_sent == 1  ) {
			$order->update_status( 'processing' );
		}
	}
}


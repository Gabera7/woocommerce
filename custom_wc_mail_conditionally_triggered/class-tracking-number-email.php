<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Customer Tracking Number Email class used to send out tracking emails to customers
 *
 * @extends \WC_Email
 */
class WC_Email_Customer_Tracking_Number extends WC_Email
{

	/**
	 * Set email defaults
	 */
	public function __construct()
	{
		// Unique ID for custom email
		$this->id = 'tracking_email';
		// Is a customer email
		$this->customer_email = true;

		// Default title field in WooCommerce Email settings
		$this->title = __('Tracking Number Email', 'woocommerce');
		// Default description field in WooCommerce email settings
		$this->description = __('Tracking number email', 'woocommerce');
		// Default heading and subject lines in WooCommerce email settings
		$this->subject = apply_filters('tracking_email_default_subject', __('Número de Rastreio da Encomenda {order_number}', 'woocommerce'));
		$this->heading = apply_filters('tracking_email_default_heading', __('Número de Rastreio da Encomenda {order_number}', 'woocommerce'));

		// these define the locations of the templates that this email should use
		$upload_dir = wp_upload_dir();

		$this->template_base  = $upload_dir['basedir'] . '/custom-emails/';	// Fix the template base lookup for use on admin screen template path display
		$this->template_html  = 'emails/tracking-email.php';
		$this->template_plain = 'custom-emails/emails/tracking-email.php';


		// Trigger email with default woocommerce order_from_to triggers *https://wp-kama.com/plugin/woocommerce/hook/woocommerce_order_status_(from)_to_(to) []
		//add_action('woocommerce_order_status_processing_to_cancelled_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_on-hold_to_cancelled_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_pending_to_on-hold_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_failed_to_on-hold_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_cancelled_to_on-hold_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_cancelled_to_processing_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_failed_to_processing_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_on-hold_to_processing_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_pending_to_processing_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_on-hold_to_failed_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_pending_to_failed_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_pending_to_processing_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_pending_to_completed_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_pending_to_on-hold_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_failed_to_processing_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_failed_to_completed_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_failed_to_on-hold_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_cancelled_to_processing_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_cancelled_to_completed_notification', array($this, 'trigger'), 10, 2);
		//add_action('woocommerce_order_status_cancelled_to_on-hold_notification', array($this, 'trigger'), 10, 2);



		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();
	}
	/**
	 * Prepares email content and triggers the email
	 *
	 * @param int $order_id
	 */
	public function trigger($order_id)
	{
		// Bail if no order ID is present
		if (!$order_id)
			return;

		// Send  email only once and not on every order status change		
		if (!get_post_meta($order_id, '_tracking_email_sent', true)) {

			// setup order object
			$this->object = new WC_Order($order_id);

			// get order items as array
			$order_items = $this->object->get_items();

			//* Include an conditional check to make sure that the email will be sent on the right time 

			// Block email if meta field is empty
			$custom_field = get_post_meta($this->object->id, 'dlinfo', true);
			if ($custom_field = '' || $custom_field = null || $custom_field == false) {
				return;
			}

			// Block email if order status is processing 
			//$custom_field =  $order->get_status();
			//if ($custom_field == 'processing' ) {
			//	return;
			//}

			/* Proceed with sending email */

			$this->recipient = $this->object->billing_email;
			// replace variables in the subject/headings
			$this->find[] = '{order_date}';
			$this->replace[] = date_i18n(woocommerce_date_format(), strtotime($this->object->order_date));
			$this->find[] = '{order_number}';
			$this->replace[] = $this->object->get_order_number();
			if (!$this->is_enabled() || !$this->get_recipient()) {
				return;
			}
			// All well, send the email
			$this->send($this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments());

			// add order note about the same
			$this->object->add_order_note(sprintf(__('%s email sent to the customer.', 'woocommerce'), $this->title));
			// Set order meta to indicate that the welcome email was sent
			update_post_meta($this->object->id, '_tracking_email_sent', 1);
		}
	}

	/**
	 * get_content_html function.
	 *
	 * @return string
	 */
	public function get_content_html()
	{
		return wc_get_template_html($this->template_html, array(
			'order'			=> $this->object,
			'email_heading'		=> $this->email_heading,
			'sent_to_admin'		=> false,
			'plain_text'		=> false,
			'email'			=> $this
		));
	}
	/**
	 * get_content_plain function.
	 *
	 * @return string
	 */
	public function get_content_plain()
	{
		return wc_get_template_html($this->template_plain, array(
			'order'			=> $this->object,
			'email_heading'		=> $this->email_heading,
			'sent_to_admin'		=> false,
			'plain_text'		=> true,
			'email'			=> $this
		));
	}
	/**
	 * Initialize settings form fields
	 */
	public function init_form_fields()
	{
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __('Enable/Disable', 'woocommerce'),
				'type'    => 'checkbox',
				'label'   => 'Enable this email notification',
				'default' => 'yes'
			),
			'subject'    => array(
				'title'       => __('Subject', 'woocommerce'),
				'type'        => 'text',
				'description' => sprintf('This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => __('Email Heading', 'woocommerce'),
				'type'        => 'text',
				'description' => sprintf(__('This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.'), $this->heading),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __('Email type', 'woocommerce'),
				'type'        => 'select',
				'description' => __('Choose which format of email to send.', 'woocommerce'),
				'default'       => 'html',
				'class'         => 'email_type wc-enhanced-select',
				'options'     => array(
					'plain'	    => __('Plain text', 'woocommerce'),
					'html' 	    => __('HTML', 'woocommerce'),
					'multipart' => __('Multipart', 'woocommerce'),
				)
			)
		);
	}
}

<?php

if ( ! defined( 'ABSPATH' ) ) {  
	exit; // Exit if accessed directly
}

/**
 * WooCommerce FILO_AJAX -> REPAIR of class-wc-ajax.php
 * 
 * @package     Filogy/Classes
 * @subpackage 	Framework
 * @author      WebshopLogic - Peter Rath
 * @author 		WooThemes (original file)
 * @category    Abstract Class
 * 
 * @based_on	class-filo-ajax.php file in WooCommerce plugin by WooThemes 
 */
class FILO_AJAX {
//class FILO_AJAX extends WC_AJAX {
			
	/**
	 * Hook in methods
	 */
	public static function init() {

		wsl_log(null, 'class-filo-ajax.php init ');

		//New ajax events in FILO
		// filo_EVENT => nopriv
		$ajax_events = array(
		
			//it is needed for Filogy Mini too, because it is necessary for select seller user on settings page.
			'get_formatted_seller_data'                        => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			////add_action( 'wp_ajax_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			add_action( 'wp_ajax_filo_' . $ajax_event, array( __CLASS__, $ajax_event ) ); // MODIFY RaPe -> action name prefix is wp_ajax_filo, in js files actions: filo_..... 

			if ( $nopriv ) {
				////add_action( 'wp_ajax_nopriv_woocommerce_' . $ajax_event, array( __CLASS__, $ajax_event ) );
				add_action( 'wp_ajax_nopriv_filo_' . $ajax_event, array( __CLASS__, $ajax_event ) ); // MODIFY RaPe -> action name prefix is wp_ajax_filo, in js files acrions: filo_.....
			}
		}

	}

	/**
	 * Get Formatted Seller Data for displaying it after chosing a user (e.g. seller on doc settings pafe)
	 */
	public static function get_formatted_seller_data() {

		check_ajax_referer( 'get_formatted_seller_data', 'security' );

		$seller_user_id = wc_clean( $_POST['seller_user_id'] ); //+wc_clean
		
		$address = array(
			'first_name'    => get_user_meta( $seller_user_id, 'billing_first_name', true ), //$this->billing_first_name,
			'last_name'     => get_user_meta( $seller_user_id, 'billing_last_name', true ), //$this->billing_last_name,
			'company'       => get_user_meta( $seller_user_id, 'billing_company', true ), //$this->billing_company,
			'address_1'     => get_user_meta( $seller_user_id, 'billing_address_1', true ), //$this->billing_address_1,
			'address_2'     => get_user_meta( $seller_user_id, 'billing_address_2', true ), //$this->billing_address_2,
			'city'          => get_user_meta( $seller_user_id, 'billing_city', true ), //$this->billing_city,
			'state'         => get_user_meta( $seller_user_id, 'billing_state', true ), //$this->billing_state,
			'postcode'      => get_user_meta( $seller_user_id, 'billing_postcode', true ), //$this->billing_postcode,
			'country'       => get_user_meta( $seller_user_id, 'billing_country', true ), //$this->billing_country
		);

		$formatted_billing_address = WC()->countries->get_formatted_address( $address );

		echo '<p id="seller_formatted_data"><strong>' . __( 'Seller', 'filofw_text' ) . ': </strong>' . wp_kses( $formatted_billing_address, array( 'br' => array() ) ) . '</p>';
		
		die();

	}

}

FILO_AJAX::init();

<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FILO_Order_Factory -> MODIFICATION of class-wc-order-factory.php
 * 
 * @package     Filogy/Classes
 * @subpackage 	Financials
 * @author      WebshopLogic - Peter Rath
 * @author 		WooThemes (original file)
 * @category    Class
 * 
 * @based_on	class-wc-order.php file in WooCommerce plugin by WooThemes 
 * 
 */
class FILO_Order_Factory {

	public function __construct() {

		wsl_log(null, 'class-filo-order-factory.php __construct 0: ' . wsl_vartotext(''));				
		add_filter( 'woocommerce_order_class', array($this, 'woocommerce_order_class' ), 10, 4 );
		
	}

	public function get_order( $the_order = false ) {
		
		//wsl_log(null, 'class-filo-order-factory.php get_order $the_order 0: ' . wsl_vartotext($the_order));
		//call original WC order factory
		return wc_get_order( $the_order );
		
	}
	
	public function woocommerce_order_class( $classname, $post_type, $order_id, $the_order = false ) {

		//wsl_log(null, 'class-filo-order-factory.php woocommerce_order_class $classname 0: ' . wsl_vartotext($classname));
		
		//wsl_log(null, 'class-filo-order-factory.php woocommerce_order_class debug_backtrace: ' . wsl_vartotext( debug_backtrace() ));

		if ( ! $classname || ! class_exists( $classname ) ) {
			$classname = 'FILO_Financial_Document';
		}
		
		// Replace WC_Order to FILO_FinaDoc_Shop_Order to the extended financial functionality can be used for orders.
		if ($classname == 'WC_Order') {
			$classname = 'FILO_FinaDoc_Shop_Order';
		}
		
		//wsl_log(null, 'class-filo-order-factory.php woocommerce_order_class $classname 9: ' . wsl_vartotext($classname));
		
		return $classname;
		
	}	
		
}

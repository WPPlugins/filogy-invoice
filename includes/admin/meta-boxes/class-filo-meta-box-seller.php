<?php
if ( !defined('ABSPATH') ) exit;

/**
 * Seller Metabox
 *
 * @package     Filogy/Admin/Metabox
 * @subpackage 	Financials
 * @category    Admin/Metabox
 */
class FILO_Meta_Box_Seller {

	/**
	 * output
	 */
	public static function output( $post ) {
		global $theorder;

		//we have to get always the default seller (from the option), if seller is not saved in the order. 		
		$formatted_seller_address = $theorder->get_formatted_seller_address( $return_default_if = 'always' );
		echo '<p id="seller_formatted_data"><strong>' . __( 'Seller', 'filo_text' ) . ': </strong>' . wp_kses( $formatted_seller_address, array( 'br' => array() ) ) . '</p>';

	}

}

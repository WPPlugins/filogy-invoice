<?php
/**
 * Abstract Order -> REPAIR and expand abstract-wc-order.php
 * 
 * @package     Filogy/Abstracts
 * @subpackage 	Framework
 * @author      WebshopLogic - Peter Rath
 * @author 		WooThemes (original file)
 * @category    Abstract Class
 * 
 * @based_on	abstract-wc-order.php file in WooCommerce plugin by WooThemes
 *  
 */
//abstract class FILO_Abstract_Order extends WC_Abstract_Order {
abstract class FILO_Abstract_Order extends WC_Order { //it is extends WC_Order, whichi is extends WC_Abstract_Order

	/**
	 * Add coupon code to the order
	 *
	 * @param string $code
	 * @param integer $discount_amount
	 * @return int|bool Item ID or false
	 * 
	 * @based_on WC_Abstract_Order->add_coupon WC_v2.4.10
	 * 
	 */
	//public function add_coupon( $code, $discount_amount = 0 ) { // REPAIR of abstract-filo-order
	//DELETED

	/**
	 * Add a fee to the order
	 *
	 * @param object $fee
	 * @return int|bool Item ID or false
	 * 
	 * @based_on WC_Abstract_Order->add_fee WC_v2.4.10
	 * 
	 */
	//public function add_fee( $fee ) { // REPAIR of abstract-filo-order
	//DELETED


	/**
	 * Calculate taxes for all line items and shipping, and store the totals and tax rows.
	 * Will use the base country unless customer addresses are set.
	 *
	 * @return bool success or fail
	 * 
	 * @based_on WC_Abstract_Order->calculate_taxes WC_v2.4.10
	 */
	//public function calculate_taxes() { // REPAIR of abstract-wc-order
	//DELETED


	/**
	 * Get totals for display on pages and in emails.
	 *
	 * @return array
	 */
	public function get_order_item_subtotals() { //ADD RaPe

		$total_rows = array();

		$total_fee = 0;
		if ( $fees = $this->get_fees() )
			foreach( $fees as $id => $fee ) {
				$total_fee += $fee['line_total'];
			}

		$subtotal = $this->get_subtotal() + $this->get_total_shipping() + $total_fee;
		$total = $this->get_total();		
		//get_subtotal_to_display + $this->get_shipping_to_display()

		/*if ( $this->get_cart_discount() > 0 ) {
			$total_rows['cart_discount'] = array(
				'label' => __( 'Items Discount:', 'filofw_text' ),
				'value'	=> '-' . wc_price( $this->get_cart_discount(), array('currency' => $this->get_currency()) )
			);
		}
		*/
				 
		if ( $subtotal ) {
			$total_rows['cart_subtotal'] = array(
				'label' => __( 'Order Subtotal:', 'filofw_text' ),
				'value'	=> wc_price( $subtotal - $this->get_cart_discount(), array('currency' => $this->get_currency()) )
			);
		}


		return apply_filters( 'filo_get_order_item_subtotals', $total_rows, $this );
	}

	/**
	 * Gets an order from the database by one of the order items id.
	 *
	 * @param int $item_id
	 * @return bool
	 */
	public static function get_order_by_item_id( $item_id ) { //ADD RaPe
		global $wpdb;

		if ( ! $item_id ) {
			return false;
		}
		
		$result_order = false;

		$order_id = $wpdb->get_var(
			$wpdb->prepare( "
					select order_id
					from {$wpdb->prefix}woocommerce_order_items
					where order_item_id = %s;
				",
				$item_id
			)
		);

		if ( ! empty($order_id) ) {
			$result_order = wc_get_order( $order_id );
		}

		return $result_order;
	}

	/**
	 * Get shipping lines to display as invoice sum part.
	 *
	 * @return array
	 */

	public function get_doc_sum_lines( $doc_financial_data, $types = array('shipping','fee') ) { //ADD RaPe
		//$type: shipping, fee
		
		$lines = array();
		
		foreach ( $doc_financial_data['lines'] as $item_id => $item ) {
			
			if ( in_array( $item['item_type'], $types ) ) {
	
				if ($doc_financial_data['lines'][$item_id]['line_total_net'] != 0 ) {
					
					$lines[$item_id] = array(
							'label' => 
								$item['item_name'] . ' ' . 
								implode( ", ", $item['tax_labels'] ),
							'value'	=> $item['line_total_net'],
							'class' => 'line_total_net'
							);
							
				}
					
			}
				
		}
		
		//wsl_log(null, 'abstract-filo-order.php get_doc_sum_lines $lines: ' . wsl_vartotext($lines));
		
		return apply_filters( 'filo_get_doc_sum_lines', $lines, $this );
		
	}
	

	/**
	 * Get totals for display on pages and in emails.
	 * 
	 * @param string $tax_display excl/incl (exclude or include tax)
	 * @param boolean if tax lines should be displayed
	 * @param $display_discount_and_total_lines if discount and total lines should be displayed
	 * @param boolean $format_numbers if numbers should be format 
	 *
	 * @return array
	 */
	public function get_doc_item_taxtotal( $tax_display = '', $display_tax_lines = true, $display_discount_and_total_lines = true, $format_numbers = true ) { //ADD RaPe

		//if ( ! $tax_display ) {
		//	$tax_display = $this->tax_display_cart;
		//}

		$total_rows = array();


		//wsl_log(null, 'abstract-filo-order.phptract-filo-order.php $tax_display: ' . wsl_vartotext($tax_display));
		wsl_log(null, 'abstract-filo-order.phptract-filo-order.php $display_tax_lines: ' . wsl_vartotext($display_tax_lines));
		wsl_log(null, 'abstract-filo-order.phptract-filo-order.php woocommerce_tax_total_display: ' . wsl_vartotext(get_option( 'woocommerce_tax_total_display' )));
		// Tax for tax exclusive prices
		//if ( 'excl' == $tax_display && $display_tax_lines ) {
		if ( $display_tax_lines ) {			

			if ( get_option( 'woocommerce_tax_total_display' ) == 'itemized' ) {
					
				wsl_log(null, 'abstract-filo-order.phptract-filo-order.php $this->get_tax_totals(): ' . wsl_vartotext($this->get_tax_totals()));
				
				foreach ( $this->get_tax_totals() as $code => $tax ) {

					$total_rows[ sanitize_title( $code ) ] = array(
						'label' => $tax->label . ':',
						'value'	=> $format_numbers ? $tax->formatted_amount : $tax->amount,
						'class' => 'tax_label',
					);
				}

			} else {

				$total_rows['tax'] = array(
					'label' => WC()->countries->tax_or_vat() . ':',
					'value'	=> $format_numbers ? wc_price( $this->get_total_tax(), array('currency' => $this->get_currency()) ) : $this->get_total_tax(),
					'class' => 'total_tax',
				);
			}
		}

		if ($display_discount_and_total_lines ) {

			//if ( $this->order_discount > 0 ) {
			if ( $this->get_total_discount() > 0 ) {
				
				$total_rows['order_discount'] = array(
					'label' => __( 'Discount:', 'woocommerce' ),
					//'value'	=> '-' . ($format_numbers ? $this->get_order_discount_to_display() : $this->order_discount),
					'value'	=> '-' . ($format_numbers ? $this->get_order_discount_to_display() : $this->get_total_discount()),
					'class' => 'order_discount',
				);
			}
			
	
			/*if ( $this->get_total() > 0 ) {
				$total_rows['payment_method'] = array(
					'label' => __( 'Payment Method:', 'woocommerce' ),
					'value' => $this->get_payment_method_title(),
			 		'class' => 'payment_method_title',
				);
			}
			*/
	
			$total_rows['order_total'] = array(
				'label' => __( 'Total:', 'woocommerce' ),
				'value'	=> $format_numbers ? $this->get_formatted_order_total() : $this->get_total(),
				'class' => 'order_total',
			);

		}

		// Tax for inclusive prices
		/*
		if ( 'yes' == get_option( 'woocommerce_calc_taxes' ) && 'incl' == $tax_display ) {

			$tax_string_array = array();

			if ( 'itemized' == get_option( 'woocommerce_tax_total_display' ) ) {

				foreach ( $this->get_tax_totals() as $code => $tax ) {
					$tax_string_array[] = sprintf( '%s %s', $tax->formatted_amount, $tax->label );
				}

			} else {
				$tax_string_array[] = sprintf( '%s %s', wc_price( $this->get_total_tax(), array('currency' => $this->get_currency()) ), WC()->countries->tax_or_vat() );
			}

			if ( ! empty( $tax_string_array ) ) {
				$total_rows['order_total']['value'] .= ' ' . sprintf( __( '(Includes %s)', 'woocommerce' ), implode( ', ', $tax_string_array ) );
			}
		}
		*/
		wsl_log(null, 'abstract-filo-order.phptract-filo-order.php $total_rows: ' . wsl_vartotext($total_rows));
				
		return apply_filters( 'filo_get_order_item_taxtotals', $total_rows, $this );
	}

	public function get_seller_first_name() {
		return get_post_meta( $this->id, '_seller_first_name', true );
	}
	public function get_seller_last_name() {
		return get_post_meta( $this->id, '_seller_last_name', true );
	}
	public function get_seller_company() {
		return get_post_meta( $this->id, '_seller_company', true );
	}
	public function get_seller_address_1() {
		return get_post_meta( $this->id, '_seller_address_1', true );
	}
	public function get_seller_address_2() {
		return get_post_meta( $this->id, '_seller_address_2', true );
	}
	public function get_seller_city() {
		return get_post_meta( $this->id, '_seller_city', true );
	}
	public function get_seller_state() {
		return get_post_meta( $this->id, '_seller_state', true );
	}
	public function get_seller_postcode() {
		return get_post_meta( $this->id, '_seller_postcode', true );
	}
	public function get_seller_country() {
		return get_post_meta( $this->id, '_seller_country', true );
	}
	public function get_seller_email() {
		return get_post_meta( $this->id, '_seller_email', true );
	}
	public function get_seller_phone() {
		return get_post_meta( $this->id, '_seller_phone', true );
	}

	/**
	 * Get a formatted billing address for the order.
	 *
	 * @return string
	 * 
	 * @param string $return_default_if - never, before_filo_start_order, always 
	 * Caller can deside if he needs seller address, if no seller address stored for the order.
	 */
	public function get_formatted_seller_address( $return_default_if = 'never') { //ADD RaPe
		
		//wsl_log(null, 'abstract-filo-order.php get_formatted_seller_address $this: ' . wsl_vartotext($this));
		
		//if ( ! $this->formatted_seller_address ) {

			//check that it is an old order, created before filo was installed
			//the old doc numbers does not has _numbering_sequence_id meta field, we will check it
			$numbering_sequence_id = get_post_meta( $this->id, '_numbering_sequence_id', true );
			$is_old = false;
			if ( empty( $numbering_sequence_id ) ) { //This is an old document
				$is_old = true;
			}
		
			$order_seller_address = $this->get_seller_address('array', true);
			
			//if seller address has not been set yet in the order, or it is empty, then write default seller according to settings 
			if (	(isset( $order_seller_address ) && is_array( $order_seller_address ) && $order_seller_address['first_name'] == '' && $order_seller_address['last_name'] == '' && $order_seller_address['company'] == '' ) 
					or !isset( $order_seller_address )	) {

				//if the caller need default always, or the caller need default in case of old documents and our document is old (made before FILO was started)
				if ( $return_default_if == 'always' 
					or ( $return_default_if == 'before_filo_start_order' and $is_old ) ) {
			
					$seller_user_id = get_option('filo_document_seller_user');
		
					// Formatted Addresses
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
	
					wsl_log(null, 'abstract-filo-order.php get_formatted_seller_address DEFAULT $address: ' . wsl_vartotext($address));
				}
				
			} else { //otherwise display seller stored in the order
	
				// Formatted Addresses
				// fields are get through magic __get() function of abstract-wc-order.php
				$address = apply_filters( 'filo_order_formatted_seller_address', array(
					'first_name'    => $this->get_seller_first_name(),
					'last_name'     => $this->get_seller_last_name(),
					'company'       => $this->get_seller_company(),
					'address_1'     => $this->get_seller_address_1(),
					'address_2'     => $this->get_seller_address_2(),
					'city'          => $this->get_seller_city(),
					'state'         => $this->get_seller_state(),
					'postcode'      => $this->get_seller_postcode(),
					'country'       => $this->get_seller_country()
				), $this );


	
				wsl_log(null, 'abstract-filo-order.php get_formatted_seller_address ORDER $address: ' . wsl_vartotext($address));
				
			}
			
			//$this->formatted_seller_address = WC()->countries->get_formatted_address( $address );
			$formatted_seller_address = WC()->countries->get_formatted_address( $address );

		//}

		//wsl_log(null, 'abstract-filo-order.php get_formatted_seller_address order $this->formatted_seller_address: ' . wsl_vartotext($this->formatted_seller_address));

		//return $this->formatted_seller_address;
		return $formatted_seller_address;
				
	}

	/**
	 * Get the billing address in an array.
	 *
	 * $format: joined / array
	 * 
	 * @return string
	 */
	public function get_seller_address( $format = 'joined', $with_names = false) { //ADD RaPe
	
		//wsl_log(null, 'abstract-filo-order.php get_seller_address $seller_address: ' . wsl_vartotext($seller_address));

		//if ( ! $seller_address ) { //we could not use this previously calculated value, because results are depend on the parameters of the function call

			$seller_address_array = array();
			
			if ( $with_names ) {
				$seller_address_array['first_name'] = $this->get_seller_first_name();
				$seller_address_array['last_name'] = $this->get_seller_last_name();
				$seller_address_array['company'] = $this->get_seller_company();
			}

			$seller_address_array['address_1'] 	= $this->get_seller_address_1();
			$seller_address_array['address_2'] 	= $this->get_seller_address_2();
			$seller_address_array['city'] 		= $this->get_seller_city();
			$seller_address_array['state'] 		= $this->get_seller_state();
			$seller_address_array['postcode'] 	= $this->get_seller_postcode();
			$seller_address_array['country'] 		= $this->get_seller_country();

			$joined_address = array();

			foreach ( $seller_address_array as $part ) {

				if ( ! empty( $part ) ) {
					$joined_address[] = $part;
				}
			}

			$seller_address = implode( ', ', $joined_address );
			
		//}

		if ( $format == 'joined' ) 
			$ret = $seller_address;
		else
			$ret = $seller_address_array;

		return $ret;
	}

	/**
	 * Get order item meta for display (e.g variaton data).
	 * from html-order-item.php
	 * 
	 * @return string
	 */
	//NOT USED
	/*
	public function get_order_item_display_metas( $item_id ) { //ADD RaPe	
		global $wpdb;
		$order = $this;
		$display_metas = array();

		if ( $metadata = $order->has_meta( $item_id ) ) {
			//echo '<table cellspacing="0" class="display_meta">';
			foreach ( $metadata as $meta ) {

				// Skip hidden core fields
				if ( in_array( $meta['meta_key'], apply_filters( 'filo_hidden_order_itemmeta', array(
					'_qty',
					'_tax_class',
					'_product_id',
					'_variation_id',
					'_line_subtotal',
					'_line_subtotal_tax',
					'_line_total',
					'_line_tax',
				) ) ) ) {
					continue;
				}

				// Skip serialised meta
				if ( is_serialized( $meta['meta_value'] ) ) {
					continue;
				}

				// Get attribute data
				if ( taxonomy_exists( $meta['meta_key'] ) ) {
					$term           = get_term_by( 'slug', $meta['meta_value'], $meta['meta_key'] );
					$attribute_name = str_replace( 'pa_', '', wc_clean( $meta['meta_key'] ) );
					$attribute      = $wpdb->get_var(
						$wpdb->prepare( "
								SELECT attribute_label
								FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
								WHERE attribute_name = %s;
							",
							$attribute_name
						)
					);

					$meta['meta_key']   = ( ! is_wp_error( $attribute ) && $attribute ) ? $attribute : $attribute_name;
					$meta['meta_value'] = ( isset( $term->name ) ) ? $term->name : $meta['meta_value'];
				}


				$display_metas[$meta['meta_key']] = $meta['meta_value']; 
				//echo '<tr><th>' . wp_kses_post( urldecode( $meta['meta_key'] ) ) . ':</th><td>' . wp_kses_post( wpautop( urldecode( $meta['meta_value'] ) ) ) . '</td></tr>';
			}
			//echo '</table>';
		}
		return $display_metas;
	}*/

	/**
	 * get_document_number
	 * 
	 * @return string
	 */
	function get_document_number() { //Add RaPe
		
		return '#'. $this->id;
		
	}

}

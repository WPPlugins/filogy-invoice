<?php
/**
 * General part of financial document handling (orders, good receipts, invoices, ...)
 * 
 * @package     Filogy/FinancialDocuments/Classes
 * @subpackage 	Financials
 * @category    Class
 * 
 */
//class FILO_Financial_Document_FW extends FILO_Order {
class FILO_Financial_Document_FW extends FILO_Abstract_Order {

	/**
	 * get registration data of this dist finadoc post type, or post subtype (this contains e.g. label of post type or post subtype)
	 *
	 */ 
	public function get_doc_type_registration_data( $doc_type = '', $doc_subtype = '' ) {
				
		if ( $doc_type == '' ) {				
			$doc_type = $this->get_doc_type();
			$doc_subtype = $this->get_doc_subtype();
		}

		$post_type_registration_data = self::get_doc_type_registration_data_static( $doc_type, $doc_subtype );
		
		return $post_type_registration_data;
	}			

	/**
	 * get registration data of a given post type and/or subtype in parameters, independent on any concrete finadoc (this contains e.g. label of post type or post subtype)
	 *
	 */ 
	public static function get_doc_type_registration_data_static( $doc_type, $doc_subtype ) {
		global $wp_post_types; //this is an array containing post_type registration data
		global $filo_original_post_types; //for saving original content of $wp_post_types, because $wp_post_types will be changed

		//if $filo_original_post_types is set then use it, else use $wp_post_types
		$post_types = ( isset($filo_original_post_types) and $filo_original_post_types !='' ) ? $filo_original_post_types : $wp_post_types;

		//wsl_log(null, 'class-filo-financial-document.php get_doc_type_registration_data $post_types: ' . wsl_vartotext($post_types));
		//wsl_log(null, 'class-filo-financial-document.php get_doc_type_registration_data $doc_type: ' . wsl_vartotext($doc_type));
		//wsl_log(null, 'class-filo-financial-document.php get_doc_type_registration_data $doc_subtype: ' . wsl_vartotext($doc_subtype));

		if ( $doc_subtype == '' ){
			$post_type_registration_data = (array) $post_types[$doc_type];
		} else {
			$post_type_registration_data = $post_types[$doc_type]->subtypes[$doc_subtype];
		}
		
		//wsl_log(null, 'class-filo-financial-document.php get_doc_type_registration_data $post_type_registration_data: ' . wsl_vartotext($post_type_registration_data));
		
		return $post_type_registration_data;
	}			


	public function get_doc_username( $with_link = true ) {
		$the_order = $this;
	
		if ( $the_order->get_user_id() ) {
			$user_info = get_userdata( $the_order->get_user_id() );
		}

		//wsl_log(null, 'class-wc-admin-post-types.php render_shop_order_columns $the_order->get_user_id(): ' . wsl_vartotext( $the_order->get_user_id() ));
		//wsl_log(null, 'class-wc-admin-post-types.php render_shop_order_columns $user_info: ' . wsl_vartotext( $user_info ));

		if ( ! empty( $user_info ) ) {

			$username = '';
			
			if ( $with_link ) {
				$username .= '<a href="user-edit.php?user_id=' . absint( $user_info->ID ) . '">';
			}

			if ( $user_info->first_name || $user_info->last_name ) {
				$username .= esc_html( ucfirst( $user_info->first_name ) . ' ' . ucfirst( $user_info->last_name ) );
			} else {
				$username .= esc_html( ucfirst( $user_info->display_name ) );
			}

			if ( $with_link ) {
				$username .= '</a>';
			}

		} else {
			
			
			//wsl_log(null, 'class-wc-admin-post-types.php render_shop_order_columns $the_order->billing_first_name: ' . wsl_vartotext( $the_order->billing_first_name ));
			//wsl_log(null, 'class-wc-admin-post-types.php render_shop_order_columns $the_order->billing_last_name: ' . wsl_vartotext( $the_order->billing_last_name ));
			
			$billing_first_name = $the_order->get_billing_first_name();
			$billing_last_name = $the_order->get_billing_last_name();
			
			if ( $billing_first_name || $billing_last_name ) {
				$username = trim( $billing_first_name . ' ' . $billing_last_name );
			} else {
				$username = __( 'Guest', 'woocommerce' );
			}
		}
		
		return $username; 
		
	}

	/**
	 * get_doc_title_user_address (list_table style title and address)
	 * 
	 * @based_on class-wc-admin-post-types.php -> render_shop_order_columns() order_title column
	 *
	 */ 
	public function get_doc_title_user_address() {
		
		
				$the_order = $this;
				
				$customer_tip = array();

				if ( $address = $the_order->get_formatted_billing_address() ) {
					$customer_tip[] = __( 'Billing:', 'woocommerce' ) . ' ' . $address . '<br/><br/>';
				}
				
				//wsl_log(null, 'class-wc-admin-post-types.php render_shop_order_columns $the_order: ' . wsl_vartotext( $the_order ));
				//wsl_log(null, 'class-wc-admin-post-types.php render_shop_order_columns $address: ' . wsl_vartotext( $address ));

				if ( $the_order->get_billing_phone() ) {
					$customer_tip[] = __( 'Tel:', 'woocommerce' ) . ' ' . $the_order->get_billing_phone();
				}

				$username = $the_order->get_doc_username();

				//wsl_log(null, 'class-wc-admin-post-types.php render_shop_order_columns $username: ' . wsl_vartotext( $username ));

				//printf( _x( '%s by %s', 'Order number by X', 'woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $post->ID ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $the_order->get_order_number() ) . '</strong></a>', $username );
				$ret = sprintf( _x( '%s', 'Order number by X', 'woocommerce' ), $username ); //MODIFY RaPe

				if ( $the_order->get_billing_email() ) {
					$ret .= '<small class="meta email"><a href="' . esc_url( 'mailto:' . $the_order->get_billing_email() ) . '">' . esc_html( $the_order->get_billing_email() ) . '</a></small>'; //MODIFY RaPe
				}

				//$ret .= '<button type="button" class="toggle-row"><span class="screen-reader-text">' . __( 'Show more details', 'woocommerce' ) . '</span></button>'; //MODIFY RaPe
				
				//wsl_log(null, 'class-wc-admin-post-types.php render_shop_order_columns $ret: ' . wsl_vartotext( $ret ));				
				
				return $ret; //MODIFY RaPe
		
	}
	
	/**
	 * get_doc_title_doc_number (list_table style title number)
	 *
	 */ 
	public function get_doc_title_doc_number() {
		
		$the_order = $this;
		
		//printf( _x( '%s by %s', 'Order number by X', 'woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $post->ID ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $the_order->get_order_number() ) . '</strong></a>', $username );
		$ret = sprintf( _x( '%s', 'Order number by X', 'woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $the_order->get_id() ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $the_order->get_order_number() ) . '</strong></a>' );
		
		return $ret;
		
	}
	
	/**
	 * get_doc_title (list_table style title)
	 *
	 */ 
	public function get_doc_title() {

		$doc_title_user_address = $this->get_doc_title_user_address();
		$doc_title_doc_number = $this->get_doc_title_doc_number();
		
		//printf( _x( '%s by %s', 'Order number by X', 'woocommerce' ), '<a href="' . admin_url( 'post.php?post=' . absint( $post->ID ) . '&action=edit' ) . '" class="row-title"><strong>#' . esc_attr( $the_order->get_order_number() ) . '</strong></a>', $username );
		$ret = sprintf( _x( '%s by %s', 'Order number by X', 'woocommerce' ), $doc_title_doc_number, $doc_title_user_address );
		return $ret; 
		
		
	}
	
	/**
	 * Return an item within this order.
	 * 
	 * It is not needed, the normal get_item function is used instead.
	 */
	/*
	public static function get_item_specfilo( $item_id ) {
		global $wpdb;

		
		$sql = $wpdb->prepare( "SELECT order_item_id, order_item_name, order_item_type FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d ", $item_id );
		$item_row = $wpdb->get_row( $sql );
		
		$finadoc_id = FILO_Financial_Document::get_order_id_by_item_id( $item_id );
		$finadoc = wc_get_order( $finadoc_id );



		wsl_log(null, 'class-filo-financial-document-fw.php get_item $sql: ' . wsl_vartotext($sql));
		wsl_log(null, 'class-filo-financial-document-fw.php get_item $item_row: ' . wsl_vartotext($item_row));



		$item            		= $finadoc->get_item( $item_row->order_item_id );
		
		//-------------------------------
		
		$item_meta_data			= $item->get_meta_data();

		////wsl_log(null, 'class-filo-financial-document-fw.php get_item $finadoc->get_item_meta( $item_row->order_item_id ): ' . wsl_vartotext($finadoc->get_item_meta( $item_row->order_item_id )));
		//wsl_log(null, 'class-filo-financial-document-fw.php get_item wc_get_order_item_meta( $item_row->order_item_id, .. ): ' . wsl_vartotext(wc_get_order_item_meta( $item_row->order_item_id, '' )));

		////wsl_log(null, 'class-filo-financial-document-fw.php get_item $finadoc->get_item_meta_array( $item_row->order_item_id ): ' . wsl_vartotext($finadoc->get_item_meta_array( $item_row->order_item_id )));
		//wsl_log(null, 'class-filo-financial-document-fw.php get_item $item_meta_data: ' . wsl_vartotext($item_meta_data));

		$item['name']            = $item_row->order_item_name;
		$item['type']            = $item_row->order_item_type;
		$item['item_meta']       = wc_get_order_item_meta( $item_row->order_item_id, '' ); //$finadoc->get_item_meta( $item_row->order_item_id ); //get all itemmeta
		$item['item_meta_array'] = $item_meta_data; //$finadoc->get_item_meta_array( $item_row->order_item_id );
		$item['id']              = $item_id;
		
		//$item2                    = $finadoc->expand_item_meta( $item );  //expand_item_meta is deprecated
		
		//wsl_log(null, 'class-filo-financial-document-fw.php get_item $item 1: ' . wsl_vartotext($item));
		//wsl_log(null, 'class-filo-financial-document-fw.php get_item $item2 1: ' . wsl_vartotext($item));
		
		//$item = array_merge($item, $item2);
		//--------------------------------
			
		wsl_log(null, 'class-filo-financial-document-fw.php get_item $item: ' . wsl_vartotext($item));
		

		return apply_filters( 'filo_order_get_item', $item, $item_id, $finadoc );
	}
	*/
	

}

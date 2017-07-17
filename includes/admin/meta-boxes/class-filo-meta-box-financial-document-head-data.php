<?php
if ( !defined('ABSPATH') ) exit; 

/**
 * Financial Document Head Data Metabox
 *
 * @package     Filogy/Admin/Metabox
 * @subpackage 	Financials
 * @category    Admin/Metabox
 */
class FILO_Meta_Box_Financial_Document_Head_Data 
	extends WC_Meta_Box_Order_Data {

	protected static $billing_fields = array(); //inherit
	protected static $shipping_fields = array(); //inherit
	protected static $seller_fields = array();
	protected static $form_fields;
	protected static $sequence_options;
	protected static $customer_user_options;

	/**
	 * output
	 */
	public static function output( $post ) {
		parent::output( $post );
			
	}
	
	/**
	 * init_form_fields
	 */
	private static function init_form_fields( $order ) {
			
		//wsl_log(null, 'class-filo-meta-box-sales-invoice-data.php $order: ' . wsl_vartotext($order));
		//wsl_log(null, 'class-filo-meta-box-sales-invoice-data.php $order->post: ' . wsl_vartotext($order->post));
		//wsl_log(null, 'class-filo-meta-box-sales-invoice-data.php $order->post->post_type: ' . wsl_vartotext($order->post->post_type));
		//wsl_log(null, 'class-filo-meta-box-sales-invoice-data.php $order->order_type: ' . wsl_vartotext($order->order_type));
		//wsl_log(null, 'class-filo-meta-box-sales-invoice-data.php get_post_type( $order->get_id() ): ' . wsl_vartotext(get_post_type( $order->get_id() )));
		//wsl_log(null, 'class-filo-meta-box-sales-invoice-data.php $order->get_doc_type(): ' . wsl_vartotext($order->get_doc_type()));
		
		
		
		//wsl_log(null, 'class-filo-meta-box-sales-invoice-data.php $order->get_customer_user(): ' . wsl_vartotext($order->get_customer_user()));

		//*** numbering_sequence_option ***
		//create $sequence_options array, keys and values contains the sequence names
		//wsl_log(null, 'class-filo-meta-box-sales-invoice-data.php init_form_fields $order->post->post_type . _sequences: ' . wsl_vartotext($order->post->post_type . '_sequences'));
		//$sequences = get_option($order->post->post_type . '_sequences'); //e.g: filo_sales_invoice_sequences
		$sequences = get_option($order->get_doc_type() . '_sequences'); //e.g: filo_sales_invoice_sequences
		self::$sequence_options = array();
	
		if (isset( $sequences ) && is_array( $sequences ) ) {
		
			foreach ($sequences as $sequence) {
				//self::$sequence_options[$sequence['sequence_id']] = $sequence['sequence_name'] . ' [ ' . __('next','filo_text') . ': ' . $order->generate_document_number($sequence['sequence_id'], false) . ' ]';
				self::$sequence_options[$sequence['sequence_id']] = $sequence['sequence_name'];
			}		
		
		}
		
		//wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php $order: ' . wsl_vartotext($order)); //big
		////wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php $order->post->post_date: ' . wsl_vartotext($order->post->post_date));
		////wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php $order->post->post_type: ' . wsl_vartotext($order->post->post_type));
		wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php $order->get_doc_type(): ' . wsl_vartotext($order->get_doc_type()));

		$numbering_sequence_id = $order->get_numbering_sequence_id();

		//if order is editable now show options or already has saved sequence id, then also show options to display the selected option
		if ( $order->is_editable() or ! empty( $numbering_sequence_id) ) {
			$numbering_sequence_options	= self::$sequence_options;
		} else { //otherwise this may be an old order, that is not editable and does not have sequence number, then we give an empty list, for not to display the first existing option (we need an empty field) 
			$numbering_sequence_options = array();
		}	
		
		$order_type_object = get_post_type_object( $order->get_doc_type() );
		$doc_type_name = $order_type_object->labels->singular_name;

		//woocommerce_wp_text_input array (not only text): wc-meta-box-functions.php
		//details in filo-meta-box-functions.php filo_output_field function
		self::$form_fields = apply_filters('filo_meta_box_' . $order->get_doc_type() . '_data_fields', array( //e.g: filo_meta_box_sales_invoice_data_fields
			array(
				'type' 			=> 'select',
				'id'			=> '_numbering_sequence_id',				
				'label'			=> __( 'Sequence Name', 'filo_text' ) . ':',
				'description'	=> (( empty(self::$sequence_options) ) ? __('THIS LIST IS EMPTY! Add items before use this function.', 'filo_text') . ' ' : __('Determines sequence from which invoice number is generated.', 'filo_text')) .  
									sprintf(__( 'Go to WooCommerce Settings / Documents / %s to create or edit sequences, displayed on this list.', 'filo_text' ), $doc_type_name),
				'desc_tip'		=>  true,
				'desc_tip_in_label' => true,
				'value'		  	=> $order->get_numbering_sequence_id(),
				//'class'    		=> 'chosen_select', //set advanced select
				'options'  		=> $numbering_sequence_options, //array of keys and values
				'form_field_class' => 'form-field form-field-wide filo-sequence-field',
				//'is_wrapper'    => false,
				//'custom_attributes' => array(
				//	'style'			=> 'min-width:300px;',	
				//	'required'		=> '',			
				//),
				'field_order'	=> 5,
			), 
						
			array(
				'type' 			=> 'hidden',
				'id'			=> '_numbering_prefix',				
				'value'		  	=> $order->get_numbering_prefix(),
				'field_order'	=> 30,
			),
			array(
				'type' 			=> 'hidden',
				'id'			=> '_numbering_suffix',				
				'value'		  	=> $order->get_numbering_suffix(),
				'field_order'	=> 40,
			),
			array(
				'type' 			=> 'hidden',
				'id'			=> '_numbering_year',				
				'value'		  	=> $order->get_numbering_year(),
				'field_order'	=> 50,
			),			 		 
			
			
					
		), $order );
		
		self::$form_fields = wsl_array_column_sort(self::$form_fields, "field_order", SORT_ASC); //sort by "field_order" field 		
		
		//wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php self::$form_fields: ' . wsl_vartotext(self::$form_fields)); //big
		
		return self::$form_fields;
	}

	/**
	 * init_address_fields
	 * 
	 * boolean $all_fields - if true, then set billing, shipping and seller fields, else if false just FILO seller_fields
	 */
	public static function init_address_fields( $all_fields = true ) { 

		if ( $all_fields )
			parent::init_address_fields();

		//ADD RaPe
		self::$seller_fields = apply_filters( 'filo_admin_seller_fields', array(
			'first_name' => array(
				'label' => __( 'First Name', 'woocommerce' ),
				'show'  => false
			),
			'last_name' => array(
				'label' => __( 'Last Name', 'woocommerce' ),
				'show'  => false
			),
			'company' => array(
				'label' => __( 'Company', 'woocommerce' ),
				'show'  => false
			),
			'address_1' => array(
				'label' => __( 'Address 1', 'woocommerce' ),
				'show'  => false
			),
			'address_2' => array(
				'label' => __( 'Address 2', 'woocommerce' ),
				'show'  => false
			),
			'city' => array(
				'label' => __( 'City', 'woocommerce' ),
				'show'  => false
			),
			'postcode' => array(
				'label' => __( 'Postcode', 'woocommerce' ),
				'show'  => false
			),
			'country' => array(
				'label'   => __( 'Country', 'woocommerce' ),
				'show'    => false,
				'type'    => 'select',
				'class'   => 'js_field-country select short',	//ADD at (WC v2.4.10)				
				'options' => array( '' => __( 'Select a country&hellip;', 'woocommerce' ) ) + WC()->countries->get_allowed_countries()
			),
			'state' => array(
				'label' => __( 'State/County', 'woocommerce' ),
				'class'   => 'js_field-state select short',		//ADD at (WC v2.4.10)
				'show'  => false
			),
			'email' => array(
				'label' => __( 'Email', 'woocommerce' ),
			),
			'phone' => array(
				'label' => __( 'Phone', 'woocommerce' ),
			),
		) );
		
		//wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php init_address_fields self::$seller_fields: ' . wsl_vartotext(self::$seller_fields)); //big
		
	}
	
	/**
	 * woocommerce_admin_order_data_after_order_details
	 */
	public static function woocommerce_admin_order_data_after_order_details ( $order ) {
		wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php woocommerce_admin_order_data_after_order_details $order: ' . wsl_vartotext($order));
		//$order_id = $order->id;
		$order_id = $order->get_id();
		
		$theorder = wc_get_order( $order_id ); //filo_get_order //MODIFY RaPe
		
		//ADD RaPe BEGIN
		self::init_form_fields($order); //ADD RaPe

		$document_options = get_option( 'woocommerce_document_' . $order->get_doc_type() . '_settings'); //ADD RaPe
		
		if ( $order->is_editable() ) {
			$readonly = false;
			$readonly_text = '';
		} else {
			$readonly = true;
			$readonly_text = 'readonly';
		}

		//do_action( 'filo_admin_financial_document_head_data_before', $order );
		//do_action( 'filo_admin_' . $order->post->post_type . '_head_data_before', $order );

		$order_type_object = get_post_type_object( $order->get_doc_type() ); //$order_type_object in WC v2.4.10
		$doc_type_name = $order_type_object->labels->singular_name;
		//ADD RaPe END
				
		//Display fields that defined in init_form_fields()
		filo_output_fields( self::$form_fields, $readonly );
						
		wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php woocommerce_admin_order_data_after_order_details $order->get_doc_type(): ' . wsl_vartotext($order->get_doc_type()));
		do_action( 'filo_admin_financial_document_head_data_before', $order );
		do_action( 'filo_admin_' . $order->get_doc_type() . '_head_data_before', $order );
		
	}	

	/**
	 * save
	 */
	//public static function save( $post_id, $post ) {
	public static function save( $post_id ) {
		global $wpdb;

		try {

			$order = wc_get_order( $post_id ); //filo_get_order
			
			if ( $order->is_editable() or FILO_TYPE == 'filo_invoice_type' ) {
					
				//wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php save $post_id: ' . wsl_vartotext($post_id) . '; $_POST: ' . wsl_vartotext($_POST)); //big
				
				self::init_form_fields($order);
				
				self::init_address_fields();
				
				do_action( 'filo_admin_' . $order->get_doc_type() . '_head_data_before_save', $order ); //e.g. filo_admin_shop_order_head_data_save, filo_admin_sales_invoice_head_data_save
				
				$post = get_post($post_id);
				parent::save( $post_id, $post );
				
				//wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php save self::$seller_fields: ' . wsl_vartotext(self::$seller_fields)); //big
				
				if ( FILO_TYPE == 'filo_invoice_type' ) {
					self::save_seller( $post_id );
				}				
		
				//Save fields that defined in init_form_fields() 
				foreach (self::$form_fields as $form_field) {
		
					//if $form_field['save'] is given, then save with this name, otherwise save name is the field id
					$form_field_id = isset($form_field['id']) ? $form_field['id'] : null;
					$form_field_save = isset($form_field['save']) ?  $form_field['save'] : $form_field_id; //if save is not given then saved fileld name id equal to the id 
					wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php $form_field_id / $form_field_save: ' . wsl_vartotext($form_field_id) . '; ' . wsl_vartotext($form_field_save));			
					
					if ( isset( $_POST[ $form_field_id ] ) ) {
						update_post_meta( $post_id, $form_field_save, wc_clean( $_POST[ $form_field_id ] ) );
					}
					
				}
				
				do_action( 'filo_admin_financial_document_head_data_save', $order );
				do_action( 'filo_admin_' . $order->get_doc_type() . '_head_data_save', $order ); //e.g. filo_admin_shop_order_head_data_save, filo_admin_sales_invoice_head_data_save

				//Earlier PRO!		
				
				//there can be errors that does not block saving the document (does not throw exception), but validation is not possible (e.g payment method is empty)
				//these are not "saved" error messages
				$filo_errors = FILO_Admin_Meta_Boxes::$meta_box_errors;
				
				//wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php $_POST 2: ' . wsl_vartotext($_POST)); //big								
				wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php $filo_errors 2: ' . wsl_vartotext($filo_errors));
				
				$validated_now = false;  

				
				if ( ( (isset($_POST['save_valid_financial_document']) and $_POST['save_valid_financial_document']) or FILO_TYPE == 'filo_invoice_type') and empty( $filo_errors ) ) {
					
					$validated_now = true;  
	
					wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php make_valid: ' . wsl_vartotext(''));

					//Earlier PRO!		
					
					// set it valid, generate_document_number, do accounting and inventory posting (account_and_inventory_postings)
					$order->make_valid();

					//Earlier PRO!		  
					
				} else {
					
					wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php save_draft: ' . wsl_vartotext(''));
	
					$is_already_valid = get_post_meta( $post_id, '_doc_validated', true );
					$existing_document_number = get_post_meta( $post_id, '_document_number', true );

					if ( ! $is_already_valid ) {

						//Earlier PRO!		
					
						//if it is not valid yet, and have not got existing doc number then generate a draft number
						if ( $existing_document_number == null ) { 
		
							//it is draft, generate and save draft doc num:
							$is_draft = true;
							$order->generate_document_number( null, true, $is_draft );
							
						}

						//Earlier PRO!		
						
					}	
					
				}
				
				//Earlier PRO!		
					
				wc_delete_shop_order_transients( $post_id );

			}

		} catch (FILO_Validation_Exception $ve) {
			
			//catch these:  throw new FILO_Validation_Exception( $message, $code );
			//and add FILO error message
			//class-filo-admin-meta-boxes.php-> end_transaction will be rollback it if there were error
			
			//WC_Admin_Meta_Boxes::add_error( __( 'Financial document saving ERROR', 'filo_text' ) . ' - ' . $ve->getMessage() ); //RaPe +F
			FILO_Admin_Meta_Boxes::add_error( __( 'Financial document saving ERROR', 'filo_text' ) . ' - ' . $ve->getMessage() ); //RaPe +F
			

			//$wpdb->query('ROLLBACK'); 
			wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php save ERROR: ' . wsl_vartotext(''));
			
		}

	}

	/**
	 * save_seller
	 */
	public static function save_seller( $post_id ) {


		wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php save_seller self::$seller_fields: ' . wsl_vartotext( self::$seller_fields ));
		
		//if ( self::$seller_fields ) {
		if ( ! empty( self::$seller_fields ) ) {

			$seller_user_id = get_option('filo_document_seller_user');

			foreach ( self::$seller_fields as $key => $field ) {
				//update_post_meta( $post_id, '_seller_' . $key, wc_clean( get_user_meta( $seller_user_id, 'billing_' . $key, true ) ) );

				if ( ! isset( $field['id'] ) ){
					$field['id'] = '_seller_' . $key;
				}

				wsl_log(null, 'class-filo-meta-box-financial-document-head-data.php save_seller wc_clean( get_user_meta( $seller_user_id, billing_ . $key, true ) ): ' . wsl_vartotext(wc_clean( get_user_meta( $seller_user_id, 'billing_' . $key, true ) )));
				
				//update_post_meta( $post_id, $field['id'], wc_clean( $_POST[ $field['id'] ] ) );
				update_post_meta( $post_id, $field['id'], wc_clean( get_user_meta( $seller_user_id, 'billing_' . $key, true ) ) );
				
			}
			
		}

	}

}

<?php

/**
 * General part of financial document handling (orders, good receipts, invoices, ...)
 * 
 * @package     Filogy/FinancialDocuments/Classes
 * @subpackage 	Financials
 * @category    Class
 * 
 */
class FILO_Financial_Document extends FILO_Financial_Document_FW {

	/**
	 * construct
	 *
	 * @param int|object $order order can be any financial document, because all finadoc is handled as "order" technically
	 */
	public function __construct( $order = '' ) {
		global $is_filo_settings_ok;
		//wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php __construct $is_filo_settings_ok: ' . wsl_vartotext($is_filo_settings_ok));
		
		parent::__construct($order);
		
		//if none all settings are ok, then do nothing 
		if ( ! $is_filo_settings_ok ) {
			return false;
		}
		
		add_action( 'woocommerce_api_create_order', array( $this, 'wc_api_create_doc_seller' ), 20, 3); //RaPe 2->3

		//add_shortcode( 'filogy_doc', 'FILO_Financial_Document::filogy_doc_shortcode' ); //moved to class-filo-initial-functions.php
		//add_shortcode( 'filogy_doc_show_if', 'FILO_Financial_Document::filogy_doc_shortcode' ); //Show or hide content enclosed short code tags. It is needed because filogy_doc short code cannot include another filogy_doc shortcode, but filogy_doc_show_if can include filogy_doc. //moved to class-filo-initial-functions.php
				
	}

	public function get_creation_date( $pseudo_doc_type = '' ) {
		if ( empty($pseudo_doc_type) ) {
			return get_the_date( 'Y-m-d', $this->id );
		} else {
			$pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
			return get_post_meta( $this->id, $pseudo_doc_type . '_creation_date', true );
		} 
		//return get_post_meta( $this->id, '_creation_date', true );
	}	

	public function get_doc_type() {
		return get_post_type($this->id); //$this->post->post_type
	}


	public function get_doc_subtype() {
		return get_post_meta( $this->id, '_filo_doc_subtype', true );
	}

	public function get_transaction_type() {
		return get_post_meta( $this->id, '_filo_transaction_type', true );
	}
	
	public function get_doc_validated() {
		return get_post_meta( $this->id, '_doc_validated', true );
	}


	public function get_doc_post() {
		$my_post = get_post( $this->get_id() );
		return $my_post;
	}

	public function get_doc_post_status() {
		$my_post = get_post( $this->get_id() );
		return $my_post->post_status;
	}
		 	 
	public function get_numbering_sequence_id( $pseudo_doc_type = '' ) {
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix 
		return get_post_meta( $this->id, $pseudo_doc_type . '_numbering_sequence_id', true );
	}

	public function get_numbering_prefix( $pseudo_doc_type = '' ) {
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
		return get_post_meta( $this->id, $pseudo_doc_type . '_numbering_prefix', true );
	}
	
	public function get_numbering_suffix( $pseudo_doc_type = '' ) {
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
		return get_post_meta( $this->id, $pseudo_doc_type . '_numbering_suffix', true ); 
	}
	
	public function get_numbering_year_handling( $pseudo_doc_type = '' ) {
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
		// @todo maybe it will not be used
		return get_post_meta( $this->id, $pseudo_doc_type . '_numbering_year_handling', true ); 
	}
	
	public function get_numbering_year( $pseudo_doc_type = '' ) {
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
		return get_post_meta( $this->id, $pseudo_doc_type . '_numbering_year', true );
	}

	public function get_numbering_sequential_number( $pseudo_doc_type = '' ) {
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
		return get_post_meta( $this->id, $pseudo_doc_type . '_numbering_sequential_number', true );
	}
	
	public function get_completion_date( $pseudo_doc_type = '' ) {
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
		return get_post_meta( $this->id, $pseudo_doc_type . '_completion_date', true );
	}

	public function get_due_date( $pseudo_doc_type = '' ) {
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
		return get_post_meta( $this->id, $pseudo_doc_type . '_due_date', true );
	}

	/*public function get_formatted_creation_date() {
		return $this->get_date_created()->date_i18n( apply_filters( 'woocommerce_admin_order_date_format', __( 'Y-m-d', 'woocommerce' ) ) );
	}*/

	//WWWQQQ
	public function get_payment_method_data_html() {
		return get_post_meta( $this->id, '_payment_method_data_html', true );
	}
		
	

	//also see: function get_creation_date( $pseudo_doc_type = '' ) {
		
	// comments of pseudo docs are net handled ad normal WP post notes, but handled in a metafields of the order, named $pseudo_doc_type . '_pseudo_doc_comment' 
	public function get_pseudo_doc_comment( $pseudo_doc_type = '' ) {
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
		return get_post_meta( $this->id, $pseudo_doc_type . '_pseudo_doc_comment', true );
	}

	public function get_has_pseudo_docs() {
		return get_post_meta( $this->id, '_has_pseudo_docs', true ); 
	}
	
	public function is_pseudo_doc_valid( $pseudo_doc_type ) {
		
		$pseudo_doc_type = '_' . $pseudo_doc_type; //add _ prefix
		$saved_creation_date = get_post_meta( $this->id, $pseudo_doc_type . '_creation_date', true );
		wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php save $saved_creation_date: ' . wsl_vartotext($saved_creation_date));
		if ( ! empty($saved_creation_date) ) {
			return true; //valid
		} else {
			return false; //valid
		}
		
	}

	public function get_seller_vat_number() {
		 	
		$doc_seller_vat_number = get_post_meta( $this->id, '_seller_vat_number', true );
		 
		if ( empty($doc_seller_vat_number) ) {
			$doc_seller_vat_number = get_option('filo_seller_vat_number');
		}
		
		wsl_log(null, 'class-filo-financial-document.php get_seller_vat_number $doc_seller_vat_number: ' .  wsl_vartotext($doc_seller_vat_number)); 
		return $doc_seller_vat_number;
	}

	public function get_seller_domestic_vat_number() {
		 	
		$doc_seller_domestic_vat_number = get_post_meta( $this->id, '_seller_domestic_vat_number', true );
		 
		if ( empty($doc_seller_domestic_vat_number) ) {
			$doc_seller_domestic_vat_number = get_option('filo_seller_domestic_vat_number');
		}

		wsl_log(null, 'class-filo-financial-document.php get_seller_domestic_vat_number $doc_seller_domestic_vat_number: ' .  wsl_vartotext($doc_seller_domestic_vat_number));		 
		return $doc_seller_domestic_vat_number;
	}


	//We do not have _filo_is_vat_exempt, because we have a new _is_vat_exempt feature in WC
	/*public function get_filo_is_vat_exempt() {
		return get_post_meta( $this->id, '_filo_is_vat_exempt', true );
	}*/
	
	public function get_is_vat_exempt() {
		return get_post_meta( $this->id, '_is_vat_exempt', true );
	}
		

	/**
	 * Returns if tax was enabled at the time of document creation
	 */
	public function get_filo_is_tax_enabled() {
		return get_post_meta( $this->id, '_filo_is_tax_enabled', true );
	}
				
	/**
	 * get_document_number
	 * 
	 * according to $filo_order_number_display_format
	 *
	 * @return string
	 */
	public function get_document_number( $pseudo_doc_type = '' ) {
		
		$doc_num = $this->change_wc_order_number(null, null, $pseudo_doc_type);
		
		wsl_log(null, 'class-filo-financial-document.php get_filo_document_number $doc_num: ' . wsl_vartotext($doc_num));
		
		return $doc_num;
		
	}
				
	/**
	 * get_filo_document_number
	 * 
	 * always the sequential number regardless of $filo_order_number_display_format
	 *
	 * @return string
	 */
	public function get_filo_document_number( $pseudo_doc_type = '' ) {
		
		if ( ! empty($pseudo_doc_type) ) $pseudo_doc_type = '_' . $pseudo_doc_type; //if $pseudo_doc_type is given, then add _ prefix
		
		$doc_num = get_post_meta( $this->id, $pseudo_doc_type . '_document_number', true );
		
		if ($doc_num == '') {
			
			//check that it is an old order, created before filo was installed
			//the old doc numbers does not has _numbering_sequence_id meta field, we will check it
			$numbering_sequence_id = get_post_meta( $this->id, $pseudo_doc_type . '_numbering_sequence_id', true );
			if ( empty( $numbering_sequence_id ) and empty($pseudo_doc_type) ) { //This is an old document
				$doc_num = $this->id;
			} else { //This is a document made by filo
				$doc_num = __('Draft', 'filo_text') . '-' . $this->id;
			}
		}
		
		wsl_log(null, 'class-filo-financial-document.php get_document_number $doc_num: ' . wsl_vartotext($doc_num));
		
		return $doc_num;
	}	

	/**
	 * Get get_order_number
	 * 
	 * Overwritten original WC get_order_number() function, that returns a real document number
	 *
	 * @return string
	 */
	public function get_order_number() {
		return $this->get_document_number();
	}	

	/**
	 * get_order_id_by_item_id
	 */
	public static function get_order_id_by_item_id( $item_id ) {
		global $wpdb;
		
		$sql = $wpdb->prepare( "
			select order_id 
			from {$wpdb->prefix}woocommerce_order_items 
			where order_item_id = %s
			",
			$item_id
		);
		
		$order_id = $wpdb->get_var( $sql );

		wsl_log(null, 'class-filo-financial-document.php get_order_id_by_item_id $sql: ' . wsl_vartotext($sql));		
		wsl_log(null, 'class-filo-financial-document.php get_order_id_by_item_id $order_id: ' . wsl_vartotext($order_id));
		
		return $order_id;
	}	

	public static function change_wc_order_number_static( $wc_order_number = null, $order = null, $pseudo_doc_type = null ) {
		$finadoc = new FILO_Financial_Document();
		$finadoc->change_wc_order_number( $wc_order_number, $order, $pseudo_doc_type );
	}

	/**
	 * Add FILO document number when an order number is displayed on front-end or on the back-end
	 * 
	 * can be called by woocommerce_order_number WooCommerce filter (in this case parameters are given, otherwise not)
	 *
	 * @return string
	 */
	public function change_wc_order_number( $wc_order_number = null, $order = null, $pseudo_doc_type = null ) {
		//NOT STATIC
		
		wsl_log(null, 'class-filo-financial-document.php change_wc_order_number $order: ' . wsl_vartotext($order));
		//wsl_log(null, 'class-filo-financial-document.php change_wc_order_number $this: ' . wsl_vartotext($this));

		if ( ! empty($order) ) {
			$order_finadoc = wc_get_order( $order->get_id() ); //filo_get_order
		} else {
			$order_finadoc = $this;
		}
		
		//get filo document number (always the sequential number regardless of $filo_order_number_display_format)
		$order_finadoc_document_number = $order_finadoc->get_filo_document_number($pseudo_doc_type);
		
		if ( empty($wc_order_number) ) {
			$wc_order_number = $order_finadoc->id;
		}

		//filo_order_number_display_format option is valid only for shop_orders, all of the other document types filo_only settings should be applied, regardless of filo_order_number_display_format setting
		if ( $order_finadoc->get_doc_type() == 'shop_order' and empty($pseudo_doc_type) ) {
			$filo_order_number_display_format = get_option('filo_order_number_display_format');
		} else {
			$filo_order_number_display_format = 'filo_only';
		}
		wsl_log(null, 'class-filo-financial-document.php change_wc_order_number $filo_order_number_display_format: ' . wsl_vartotext($filo_order_number_display_format));
		//filo_only - Filogy order format only e.g. SO00010
		//wc_only  - WooCommerce original order format only e.g. #1234
		//filo_and_wc - WC Financials order format plus WC original order format in brakets e.g. SO00010 (#1234) 
		
		switch ($filo_order_number_display_format) {

			case 'filo_only':
				$displayed_order_number = $order_finadoc_document_number;
				break;

			case 'filo_and_wc':
				$displayed_order_number = $order_finadoc_document_number . ' (' . $wc_order_number . ')';
				break;

			default:
				$displayed_order_number = $wc_order_number;
				break;
		}
	 
	 	$displayed_order_number = apply_filters('filo_order_number_display_format', $displayed_order_number, $filo_order_number_display_format, $wc_order_number, $order_finadoc_document_number, $order_finadoc, $pseudo_doc_type );
		
	 	return $displayed_order_number;
	}

	public function get_customer_user() {
		return get_post_meta( $this->id, '_customer_user', true );
	}			

	/**
	 * get registration data of this post type, or post subtype (this contains e.g. label of post type or post subtype)
	 *
	 */
	//MOVED TO FW 
	/*public function get_doc_type_registration_data( $doc_type = '', $doc_subtype = '' ) {
		global $wp_post_types; //this is an array containing post_type registration data
		global $filo_original_post_types; //for saving original content of $wp_post_types, because $wp_post_types will be changed

		//if $filo_original_post_types is set then use it, else use $wp_post_types
		$post_types = ( isset($filo_original_post_types) and $filo_original_post_types !='' ) ? $filo_original_post_types : $wp_post_types;
				
		if ( $doc_type == '' ) {				
			$doc_type = $this->get_doc_type();
			$doc_subtype = $this->get_doc_subtype();
		}

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
	*/
	
	/**
	 * get document type or subtype labael name
	 *
	 */ 
	public function get_doc_type_label_name() {
		$data = $this->get_doc_type_registration_data();

		if(isset($data['original_labels']))
			return $data['original_labels']->name;
		else
			return $data['labels']->name;

	}

	/**
	 * get document type or subtype labael singular_name
	 *
	 */ 
	public function get_doc_type_label_singular_name() {
		$data = $this->get_doc_type_registration_data();

		if(isset($data['original_labels']))
			return $data['original_labels']->singular_name;
		else
			return $data['labels']->singular_name;

	}

	/**
	 * get document type or subtype labael short_name
	 *
	 */ 
	public function get_doc_type_label_short_name() {
		$data = $this->get_doc_type_registration_data();
		
		if(isset($data['original_labels'])) {
			return $data['original_labels']->short_name == '' ? $data['original_labels']->name : $data['original_labels']->short_name;  
		} else {
			return $data['labels']->short_name == '' ? $data['labels']->name : $data['labels']->short_name;
		}
		
	}

	/**
	 * get document type or subtype labael singular_short_name
	 *
	 */ 
	public function get_doc_type_label_singular_short_name() {
		$data = $this->get_doc_type_registration_data();
		
		//wsl_log(null, 'class-filo-financial-document.php get_doc_type_label_singular_short_name $data: ' . wsl_vartotext($data));
		
		if(isset($data['original_labels'])) {
			return $data['original_labels']->singular_short_name == '' ? $data['original_labels']->singular_name : $data['original_labels']->singular_short_name;  
		} else {
			return $data['labels']->singular_short_name == '' ? $data['labels']->singular_name : $data['labels']->singular_short_name;
		}
		
	}
	
	/**
	 * get_played_role - returns if the shop or partner is in seller or customer role in this order
	 * 
	 * @param string $role_of_who - 'shop' or 'partner'; set that if the own shop's, or the partner's role is returned
	 * @param string $format - 'normal' or 'translated'; set that the returned role is translated or not
	 * @return strimg $played_role - seller or customer
	 */
	function get_played_role( $role_of_who ) {
		global $filo_sales_types_financial_documents, $filo_purchase_types_financial_documents;

		// diagnose the what is the role of the shop and the customer of this order					
		if ( in_array( $this->get_doc_type(), $filo_sales_types_financial_documents ) ) {
			$shop_role = 'seller';
			$partner_role = 'customer';
		} elseif ( in_array( $this->get_doc_type(), $filo_purchase_types_financial_documents ) ) {
			$shop_role = 'customer';
			$partner_role = 'seller';
		}
		
		// generate return value depending on if the caller need the shop's or the partner's role 
		if ( $role_of_who == 'shop' ) {
			$played_role = $shop_role;
		} elseif ( $role_of_who == 'partner' ) {
			$played_role = $partner_role;
		}
		
		return apply_filters( 'filo_get_played_role', $played_role, $this, $role_of_who );
	}

	/**
	 * get_played_role_display_name - returns if the shop or partner is in seller or customer role in this order
	 * 
	 * @see get_played_role 
	 */
	function get_played_role_display_name( $role_of_who ) {
		
		$played_role = $this->get_played_role( $role_of_who );
		
		if ( $played_role == 'customer' ) {
			$played_role_display = _x( 'Customer', 'filo_doc', 'filo_text');
		} elseif ( $played_role == 'seller' ) {			
			$played_role_display = _x( 'Seller', 'filo_doc', 'filo_text');
		}
		
		return $played_role_display;
		
	}
	
	/**
	 * Collect and give back financial data of a document in a unified structure
	 * 
	 * Calculate 4 different groups of items:
	 * 1. lines: financial data for each line
	 * 2. lines_total: totals of all lines of financial document
	 * 3. line_types_total: totals groupd by line types: line_item, shipping, fee
	 * 4. doc_total: totals of the document
	 * 5. tax_summary: tax lines groupd by tax rates (if $type parameter is empty)
	 *  
	 * 2 and 4 can be different, because line_totals (2) is the summary of each row, and some rounding difference
	 * can be than doc_total (4).
	 *
	 * @return array
	 */

	public function get_doc_financial_data( $type = '', $calculate_footer_lines = false, $pseudo_doc_type = null ) {
		global $wpdb, $doc_financial_data;
		
		if ( empty($pseudo_doc_type) and isset($_GET['filo_pseudo_doc_type'])) {
			$pseudo_doc_type = wc_clean( $_GET['filo_pseudo_doc_type'] ); //+wc_clean
		}
		
		$finadoc_modified_time = get_post_modified_time( 'G', true, $this->id );
		
		//if $doc_financial_data has already set, and the id of it is equal the doc_id of our actual object, and all parameters are the same then return the earlyer calculated data  
		if ( isset($doc_financial_data['document_data']['document_id']) and $doc_financial_data['document_data']['document_id'] == $this->id 
			and isset($doc_financial_data['run_param']) and $doc_financial_data['run_param']['type'] == $type and $doc_financial_data['run_param']['calculate_footer_lines'] == $calculate_footer_lines 
			and isset($doc_financial_data['run_param']['finadoc_modified_time']) and $finadoc_modified_time == $doc_financial_data['run_param']['finadoc_modified_time'] ) { //finadoc has not changed since last call

			return $doc_financial_data; 
		} 
			
		$doc_financial_data['run_param']['type'] = $type;
		$doc_financial_data['run_param']['calculate_footer_lines'] = $calculate_footer_lines;
		$doc_financial_data['run_param']['finadoc_modified_time'] = $finadoc_modified_time;
			
		
		$dispay_round_precision = wc_get_price_decimals();
		
		//subtotal level rounding, and not per line rounding
		/*if ( get_option( 'woocommerce_tax_round_at_subtotal' ) == 'yes' )
			$line_rounding = false; //subtotal rounding, so lines are rounded after subtotals have already calculated
		else 
			$line_rounding = true; //line roubding bedor, so lines are rounded before subtotals calculated
		
		if ($line_rounding) {

			$line_round_precision = $dispay_round_precision; 
			$total_round_precision = $dispay_round_precision;
						
		} else {

			$line_round_precision = WC_ROUNDING_PRECISION; //do not round lines to display precision, only to WC_ROUNDING_PRECISION, which means 4 digits  
			$total_round_precision = $dispay_round_precision;			
		}
		*/
		
		//We have to always use that round precision that is set as WooCommerce "Number of Decimals" settings ($dispay_round_precision).

		//We don't have to deal with wether tax_round_at_subtotal or item level here 
		//(this is also a WC settings: Round tax at subtotal level, instead of rounding per line). 
		//The reason of it that WC calculates subtotals according to this settings.
		//Lines always displayed rounded. It the total calculated by the none rounded line prices (this is a WC calculation) 
		//differs from the separatly rounded lines total, then a rounding difference will be calculated, but this is independent 
		//of tax_round_at_subtotal option.
		//(So one side of rounding difference is the WC calculated total (this uses tax_round_at_subtotal option), 
		//the other side is always the summary of displayed lines (separately rounded lines), that is calculated by this function. 
		//That is why dhis function have to always use separatly rounded lines.)
		
		$line_round_precision = $dispay_round_precision; 
		$total_round_precision = $dispay_round_precision;
		
		$doc_financial_data = array();

		if ( ! empty($pseudo_doc_type) ) {
			$post_type = $pseudo_doc_type;
		} else {
			$post_type = $this->get_doc_type();
		}
		$post_type_obj = get_post_type_object( $post_type );
		$post_type_short_name = $post_type_obj->labels->singular_short_name == '' ? $post_type_obj->labels->singular_name : $post_type_obj->labels->singular_short_name;
		$post_type_name = $post_type_obj->labels->singular_name;
		
		//get tax types (tax rates) used in an order e.g. arrays of GB-20% VAT, GB-3% VAT, ...
		$order_taxes = $this->get_taxes();
		//wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $order_taxes: ' . wsl_vartotext($order_taxes)); //big
			
		
		//gather item data into an array in own data format of item types
		//DISTRX
		//The order of types is important, we rely on this order at fee distributeion of purchase documents
		$item_types = array();
		if ( $type == 'line_item' or $type== '' )
			$item_types['line_item']= $this->get_items( 'line_item' );
		
		if ( $type == 'shipping' or $type == '' )
			$item_types['shipping']	= $this->get_items( 'shipping' );
		
		if ( $type == 'fee' or $type == '' )
			$item_types['fee'] 		= $this->get_items( 'fee' );
		
		//wsl_log(null, 'class-filo-financial-document.php $item_types: ' . wsl_vartotext($item_types));
			
		//reset doc totals
		//$lines_total['unit_subtotal_net'] 	= 0;
		//$lines_total['unit_total_net'] 		= 0;
		$lines_total['line_qty']			= 0;
		$lines_total['line_subtotal_net']	= 0;
		$lines_total['line_total_net']		= 0;
		$lines_total['line_subtotal_tax'] 	= 0;
		$lines_total['line_total_tax'] 		= 0;
		$lines_total['line_subtotal_gross']	= 0;
		$lines_total['line_total_gross']	= 0;
		
		//wsl_log(null, 'class-filo-financial-document.php $order $this: ' . wsl_vartotext($this)); //big
		
		$recalculated_tax_rate_percents = array();
		
		//convert data of each item type to a common data format 
		//loop through every given order item type
		foreach ( $item_types as $item_type => $items ) {

			//reset type totals
			//$line_types_total[$item_type]['unit_subtotal_net'] 	= 0;
			//$line_types_total[$item_type]['unit_total_net'] 	= 0;
			$line_types_total[$item_type]['line_qty']			= 0;
			$line_types_total[$item_type]['line_subtotal_net']	= 0;
			$line_types_total[$item_type]['line_total_net']		= 0;
			$line_types_total[$item_type]['line_subtotal_tax'] 	= 0;
			$line_types_total[$item_type]['line_total_tax'] 	= 0;
			$line_types_total[$item_type]['line_subtotal_gross']= 0;
			$line_types_total[$item_type]['line_total_gross']	= 0;

			//wsl_log(null, 'class-filo-financial-document.php $items: ' . wsl_vartotext($items));
			
			//loop through every item of the order item type
			if (isset( $items ) && is_array( $items ) )			
			foreach ( $items as $item_id => $item ) {
	
				$_product     = apply_filters( 'woocommerce_order_item_product', $this->get_product_from_item( $item ), $item );
			
				//$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product ); //display() is depricated
				
				// Item Name
				//$item_name	= apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
				$item_name		= $item->get_name(); //display() is depricated; use $item->get_name() instead
	
				// SKU
				$show_sku = true;
				if ( isset($show_sku) && $show_sku && is_object( $_product ) && $_product->get_sku() ) {
					$item_name =  '#' . $_product->get_sku() . ' - ' . $item_name;
				}
	
				// Variation
				/*if ( $item_meta->meta ) {
					$item_name .= ' [ ' . nl2br( $item_meta->display( true, true ) ) . ' ]';
				}*/
				
				$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product ); 

				// Financial data				
				//wsl_log(null, 'document-order-items.php get_doc_financial_data $item: ' . wsl_vartotext($item));
				//wsl_log(null, 'document-order-items.php get_doc_financial_data $_product: ' . wsl_vartotext($_product));
				//wsl_log(null, 'document-order-items.php get_doc_financial_data $item_meta: ' . wsl_vartotext($item_meta));
				
				////wsl_log(null, 'document-order-items.php get_doc_financial_data $item_meta->meta[_line_tax_data][0]: ' . wsl_vartotext($item_meta->meta['_line_tax_data'][0]));
				////wsl_log(null, 'document-order-items.php get_doc_financial_data $item_meta->meta[taxes][0]: ' . wsl_vartotext($item_meta->meta['taxes'][0]));
				
				
				//get an array of total and subtotal rate_id-s / amounts e.g [subtotal][4] => 626.77, [subtotal][8] => 94.01
				//in case of normal items and fees '_line_tax_data'; in case of shipping costs 'taxes' is given
				$item_taxes = isset($item_meta->meta['taxes'][0]) ? $item_meta->meta['taxes'][0] : null;
				$tax_data_serialized = isset( $item_meta->meta['_line_tax_data'][0] ) ? $item_meta->meta['_line_tax_data'][0] : $item_taxes ;
				$tax_data = maybe_unserialize( $tax_data_serialized );
			
				wsl_log(null, 'document-order-items.php $tax_data: ' . wsl_vartotext($tax_data));
				//wsl_log(null, 'document-order-items.php $this->get_taxes(): ' . wsl_vartotext($this->get_taxes())); //big

								
				switch ($item_type) {
					case 'line_item':
						
						//If taxdata contains subtotal and total parts (normal items), then we need subtotal part
						$tax_data = $tax_data['total'];
						
						$line_total_net = $item['line_total']; //$this->get_line_total( $item ); //$net_price
						$line_subtotal_net = $item['line_subtotal'];
						$unit_total_net = $this->get_item_total($item, $p_inc_tax = false, $p_round = false); //not rounded to eliminate rounding differences
						$unit_subtotal_net = $this->get_item_subtotal($item, $p_inc_tax = false, $p_round = false); //not rounded to eliminate rounding differences
						$line_total_tax = $item['line_tax']; //$this->get_line_tax($item); //$line_tax
						$line_subtotal_tax = $item['line_subtotal_tax']; 
						
						$qty = $item['qty'];

						If ( $item['variation_id'] != 0 and $item['variation_id'] != null ) {
							$product_or_variation_id = $item['variation_id'];
						} else {
							$product_or_variation_id = $item['product_id'];
						} 
						
						$product_or_variation = wc_get_product( $product_or_variation_id );
						
						if ( ! empty($product_or_variation) ) { //QQQ

							//wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $product_or_variation_id: ' . wsl_vartotext($product_or_variation_id));
							//wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $product_or_variation: ' . wsl_vartotext($product_or_variation));
							
							//++			
							//wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $item: ' . wsl_vartotext($item));
							//wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $item: ' . wsl_vartotext($item));
							//$doc_financial_data['lines'][$item_id]['product_id'] 	= $item->product_id;
							//$doc_financial_data['lines'][$item_id]['variation_id'] 	= $item->variation_id;
							$doc_financial_data['lines'][$item_id]['product_id'] 		= $item['product_id'];
							$doc_financial_data['lines'][$item_id]['variation_id'] 		= ($item['variation_id'] == 0) ? '' : $item['variation_id']; // convert 0 to null, because in inventory transactions sometimes 0 sometimes null value stored, and this is wrong using group by there
							//$manage_stock = get_post_meta ( $item['product_id'], '_manage_stock', true );
							
							
							wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data get_option(woocommerce_manage_stock): ' . wsl_vartotext(get_option('woocommerce_manage_stock')));
							wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $product_or_variation->managing_stock(): ' . wsl_vartotext($product_or_variation->managing_stock()));
							
							if (( get_option('woocommerce_manage_stock') == 'yes' and $product_or_variation->managing_stock() )) {
								$manage_stock = 'yes';
							} else {
								$manage_stock = 'no';
							} 
							
							//$doc_financial_data['lines'][$item_id]['manage_stock'] 		= ($manage_stock == '') ? 'no' : $manage_stock; 
							$doc_financial_data['lines'][$item_id]['manage_stock'] 		= $manage_stock;
							
							//XXXXXXXXXXXXXXXXXXXXXXX!!!!! get and set open_qty data
							
							//select openess order meta data
							$sql = $wpdb->prepare( "
								select meta_key, meta_value
								from {$wpdb->prefix}woocommerce_order_itemmeta as order_itemmeta
								where meta_key like '_open_%%' and order_itemmeta.order_item_id = %d 
								"
								,$item_id 
							);
									
							$openess_values = $wpdb->get_results($sql);
							
							//e.g.
							/*	Array
								(
									[0] => stdClass Object
										(
											[meta_key] => _open_qty_standard
											[meta_value] => 1
										)
								
									[1] => stdClass Object
										(
											[meta_key] => _open_qty_validated_standard
											[meta_value] => 1
										)
								
								)
							 */		
				
							if ( isset($openess_values) and is_array($openess_values) )
							foreach ( $openess_values as $openess_value ) {
								$openess_key = $openess_value->meta_key;
								$openess_value = $openess_value->meta_value;
								
								$doc_financial_data['lines'][$item_id][$openess_key] = $openess_value; //e.g. _open_qty_standard / _open_qty_validated_standard 
							}						
																			
						}
						
						//Meta data of line items (e.g. ordered attributes of product variation) 
						//if ( $item_meta->meta ) {
							//$item_meta_text = nl2br( $item_meta->display( true, true ) ); //display() is depricated
						//}
													
						
						break;
			
					case 'shipping':
					
						wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data shipping $tax_data: ' . wsl_vartotext($tax_data));
						//wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data  shipping $this: ' . wsl_vartotext($this)); //big
					
						$line_total_net = $item['cost']; //$this->get_line_total( $item ); //$net_price
							$line_subtotal_net = $line_total_net;
							$unit_total_net = $line_total_net;
							$unit_subtotal_net = $line_total_net;
						if ( is_array( $tax_data) ) {	
							$line_total_tax = array_sum ( $tax_data ); //$line_tax
								$line_subtotal_tax = $line_total_tax;
						} 
						$qty = 1;
						
						$doc_financial_data['lines'][$item_id]['shipping_method_id'] = $item['method_id'];
						
						break;
						
					case 'fee':

						//If taxdata contains subtotal and total parts (normal items), then we need subtotal part
						$tax_data = $tax_data['total'];

						wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data fee $tax_data: ' . wsl_vartotext($tax_data));
						wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data fee $this: ' . wsl_vartotext($this));

						$line_total_net = $item['line_total']; //$this->get_line_total( $item ); //$net_price
							$line_subtotal_net = $line_total_net;
							$unit_total_net = $line_total_net;
							$unit_subtotal_net = $line_total_net;
						if ( is_array($tax_data) ) {							
							$line_total_tax = array_sum ( $tax_data ); //$line_tax
							$line_subtotal_tax = $line_total_tax;
						} 
						$qty = 1;
						
						break;
						
				}			

				/*
				$tax_data example for a line with to matching tax classes, 77 and 88 are the tax rate id-s, 1350 and 500 are the $ values.
 		
				$tax_data: Array
					(
			            [77] => 1350
			            [88] => 500
					)				
				*/

				//collect tax rate ids and net totals for the particular tax_rate, and the recalculated percent of the tax rate (because percentages is not stored by WC)
				//we use it for tax summary data
				if (isset($tax_data) && is_array($tax_data) )
				foreach ($tax_data as $tax_rate_id => $tax_value ) {

					//cumulate tax $ values						
					if ( isset($tax_rate_net_total[$tax_rate_id]) ) {
						$tax_rate_net_totals[$tax_rate_id] = $line_total_net;
					} else {
						$tax_rate_net_totals[$tax_rate_id] .= $line_total_net;
					}

					//if $recalculated_tax_rate_percents has not calculated yet, and can be calculated now, the do this					
					if ( ! isset($recalculated_tax_rate_percents[$tax_rate_id]) and isset($line_total_net) and $line_total_net !=0 ) {
							
						//tax_percent = line_total_tax / line_total_net * 100
						$recalculated_tax_rate_percents[$tax_rate_id] = $tax_value / $line_total_net * 100;
						
					} 
					
				}
				
				//collect tax rates names / labels into this array used in the current item:
				$order_item_tax_labels = array();
			
				foreach ( $order_taxes as $tax_item ) {
					$tax_item_id = $tax_item['rate_id'];
					if ( isset( $tax_data[ $tax_item_id ] ) and !empty( $tax_data[ $tax_item_id ] ) ) {
						$order_item_tax_labels[$tax_item['name']] = $tax_item['item_meta']['label'][0];
					}
				}
				
				// make <br> separated string from tax_labels array
				
				if (isset( $order_item_tax_labels ) && is_array( $order_item_tax_labels ) )
						$disp_order_item_tax_labels = implode( "<br>", $order_item_tax_labels );
	
	
				//++
				$doc_financial_data['lines'][$item_id]['item_type'] 			= $item_type;
				$doc_financial_data['lines'][$item_id]['item_name'] 			= $item_name;
				//$doc_financial_data['lines'][$item_id]['item_meta'] 			= $item_meta_text; //display() is depricated
				
				$doc_financial_data['lines'][$item_id]['tax_labels'] 			= $order_item_tax_labels;
				$doc_financial_data['lines'][$item_id]['display_tax_labels'] 	= $disp_order_item_tax_labels;
				
				if ( !empty($line_total_net) and $line_total_net != 0 ) {
					
					//if not vat exempted and and tax is enabled, or there is calculated tax that not 0, we can calculate the tax_percent, that is not stored explicitly
					//if ( ( $this->is_vat_exempt !== 'yes' and $this->get_filo_is_tax_enabled() ) or $line_total_tax != 0 ) {
					//if ( ( ! $this->get_filo_is_vat_exempt() and $this->get_filo_is_tax_enabled() ) or $line_total_tax != 0 ) {
					if ( ( $this->get_is_vat_exempt() !== 'yes' and $this->get_filo_is_tax_enabled() and isset( $line_total_tax) ) or ( isset( $line_total_tax) and $line_total_tax != 0 ) ) {						
						$recalculated_tax_percent = $line_total_tax / $line_total_net * 100;						
					} else {
						$recalculated_tax_percent = ''; //in case of vat exempt ot tax_is_not_enabled, percent is empty (so not 0, but empty)
					}
					 
					$doc_financial_data['lines'][$item_id]['recalculated_tax_percent'] = $recalculated_tax_percent;
				}
				 
				$doc_financial_data['lines'][$item_id]['line_qty'] 				= $qty;
				
				$doc_financial_data['lines'][$item_id]['unit_subtotal_net'] 	= round( $unit_subtotal_net, $line_round_precision );
				$doc_financial_data['lines'][$item_id]['unit_total_net'] 		= round( $unit_total_net, $line_round_precision );
				$doc_financial_data['lines'][$item_id]['line_subtotal_net']		= round( $line_subtotal_net, $line_round_precision );
				
				$doc_financial_data['lines'][$item_id]['line_total_net']		= round( $line_total_net, $line_round_precision );
				$doc_financial_data['lines'][$item_id]['line_subtotal_tax'] 	= isset($line_subtotal_tax) ? round( $line_subtotal_tax, $line_round_precision ) : null;
				$doc_financial_data['lines'][$item_id]['line_total_tax'] 		= isset($line_total_tax) ? round( $line_total_tax, $line_round_precision ) : null;
				$doc_financial_data['lines'][$item_id]['line_subtotal_gross']	= round( $line_subtotal_net, $line_round_precision ) + $doc_financial_data['lines'][$item_id]['line_subtotal_tax'];
				$doc_financial_data['lines'][$item_id]['line_total_gross']		= round( $line_total_net, $line_round_precision ) + $doc_financial_data['lines'][$item_id]['line_total_tax'];
	
				$doc_financial_data['lines'][$item_id]['base_item_id']		= $item['base_item_id'];
	
				//Set Type Totals
				//$line_types_total[$item_type]['unit_subtotal_net'] 	+= $unit_subtotal_net;
				//$line_types_total[$item_type]['unit_total_net'] 	+= $unit_total_net;
				$line_types_total[$item_type]['line_qty']			+= $qty;
				$line_types_total[$item_type]['line_subtotal_net']	+= $doc_financial_data['lines'][$item_id]['line_subtotal_net']; //round( $line_subtotal_net, $total_round_precision );
				$line_types_total[$item_type]['line_total_net']		+= $doc_financial_data['lines'][$item_id]['line_total_net']; //round( $line_total_net, $total_round_precision );
				$line_types_total[$item_type]['line_subtotal_tax'] 	+= $doc_financial_data['lines'][$item_id]['line_subtotal_tax']; //round( $line_subtotal_tax, $total_round_precision );
				$line_types_total[$item_type]['line_total_tax'] 	+= $doc_financial_data['lines'][$item_id]['line_total_tax']; //round( $line_total_tax, $total_round_precision );
				$line_types_total[$item_type]['line_subtotal_gross']+= $doc_financial_data['lines'][$item_id]['line_subtotal_gross']; //round( $line_subtotal_net, $total_round_precision ) + round( $line_subtotal_tax, $total_round_precision );
				$line_types_total[$item_type]['line_total_gross']	+= $doc_financial_data['lines'][$item_id]['line_total_gross']; //round( $line_total_net, $total_round_precision ) + round( $line_total_tax, $total_round_precision );

				//Set Doc Totals	
				//$lines_total['unit_subtotal_net'] 	+= $unit_subtotal_net;
				//$lines_total['unit_total_net'] 		+= $unit_total_net;
				$lines_total['line_qty']			+= $qty;
				$lines_total['line_subtotal_net']	+= $doc_financial_data['lines'][$item_id]['line_subtotal_net']; //round( $line_subtotal_net, $total_round_precision );
				$lines_total['line_total_net']		+= $doc_financial_data['lines'][$item_id]['line_total_net']; //round( $line_total_net, $total_round_precision );
				$lines_total['line_subtotal_tax'] 	+= $doc_financial_data['lines'][$item_id]['line_subtotal_tax']; //round( $line_subtotal_tax, $total_round_precision );
				$lines_total['line_total_tax'] 		+= $doc_financial_data['lines'][$item_id]['line_total_tax']; //round( $line_total_tax, $total_round_precision );
				$lines_total['line_subtotal_gross']	+= $doc_financial_data['lines'][$item_id]['line_subtotal_gross']; //round( $line_subtotal_net, $total_round_precision ) + round( $line_subtotal_tax, $total_round_precision );
				$lines_total['line_total_gross']	+= $doc_financial_data['lines'][$item_id]['line_total_gross']; //round( $line_total_net, $total_round_precision ) + round( $line_total_tax, $total_round_precision );
				
			}
			
		}

		$doc_financial_data['line_types_total'] = $line_types_total;
		$doc_financial_data['lines_total'] = $lines_total;
		
		//calculate doc total
		if ( $type == '' ) {
			
			//$order_discount 					= $this->get_order_discount(); //depricated WooCommerce function
			$order_discount 					= $this->get_total_discount(); //order_discount;
			
			$doc_total_gross  					= $this->get_total() + $order_discount;
			$doc_total_tax 						= $this->get_total_tax();
			$doc_total_net						= $doc_total_gross - $doc_total_tax;
			$doc_total_gross_with_cartdiscount  = $this->get_total();
			
			$doc_financial_data['doc_total']['total_net']				= round( $doc_total_net, $total_round_precision );
			$doc_financial_data['doc_total']['total_tax'] 				= round( $doc_total_tax, $total_round_precision );
			$doc_financial_data['doc_total']['total_gross']				= round( $doc_total_gross, $total_round_precision );					
			//$doc_financial_data['doc_total']['total_gross']				= round( $doc_total_gross, $total_round_precision );
			$doc_financial_data['doc_total']['order_discount']			= round( -$order_discount, $total_round_precision );
			$doc_financial_data['doc_total']['total_gross_discounted']	= round( $doc_total_gross_with_cartdiscount, $total_round_precision );
		}

		wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $doc_financial_data[doc_total]: ' . wsl_vartotext($doc_financial_data['doc_total']));

		//AFTER CALCULATION ROUNDINGS
		//In case of total level rounding, lines have not rounded yet, but the totals have calculated from the none rounded lines
		//so we can round the lines now
		/* //$line_rounding set is commented out above, thus it can be commented out too:
		if ( ! $line_rounding ) {
			
			if ( isset($doc_financial_data['lines']) and is_array($doc_financial_data['lines']) )
			foreach ( $doc_financial_data['lines'] as $item_id => $line_content ) {
				
				$doc_financial_data['lines'][$item_id]['unit_subtotal_net'] 	= round( $line_content['unit_subtotal_net'], $dispay_round_precision);
				$doc_financial_data['lines'][$item_id]['unit_total_net'] 		= round( $line_content['unit_total_net'], $dispay_round_precision);
				$doc_financial_data['lines'][$item_id]['line_subtotal_net']		= round( $line_content['line_subtotal_net'], $dispay_round_precision);
				$doc_financial_data['lines'][$item_id]['line_total_net']		= round( $line_content['line_total_net'], $dispay_round_precision);
				$doc_financial_data['lines'][$item_id]['line_subtotal_tax'] 	= round( $line_content['line_subtotal_tax'], $dispay_round_precision);
				$doc_financial_data['lines'][$item_id]['line_total_tax'] 		= round( $line_content['line_total_tax'], $dispay_round_precision);
				$doc_financial_data['lines'][$item_id]['line_subtotal_gross']	= round( $line_content['line_subtotal_gross'], $dispay_round_precision);
				$doc_financial_data['lines'][$item_id]['line_total_gross']		= round( $line_content['line_total_gross'], $dispay_round_precision);
				
			}

			if ( isset($doc_financial_data['line_types_total']) and is_array($doc_financial_data['line_types_total']) )
			foreach ( $doc_financial_data['line_types_total'] as $line_type => $the_line_type_total ) {
				$doc_financial_data['line_types_total'][$line_type]['line_subtotal_net'] 	= round( $the_line_type_total['line_subtotal_net'], $dispay_round_precision);
				$doc_financial_data['line_types_total'][$line_type]['line_total_net'] 		= round( $the_line_type_total['line_total_net'], $dispay_round_precision);
				$doc_financial_data['line_types_total'][$line_type]['line_subtotal_tax'] 	= round( $the_line_type_total['line_subtotal_tax'], $dispay_round_precision);
				$doc_financial_data['line_types_total'][$line_type]['line_total_tax'] 		= round( $the_line_type_total['line_total_tax'], $dispay_round_precision);
				$doc_financial_data['line_types_total'][$line_type]['line_subtotal_gross'] 	= round( $the_line_type_total['line_subtotal_gross'], $dispay_round_precision);
				$doc_financial_data['line_types_total'][$line_type]['line_total_gross'] 	= round( $the_line_type_total['line_total_gross'], $dispay_round_precision);
			}
				
			$doc_financial_data['lines_total']['line_subtotal_net'] 		= round( $doc_financial_data['lines_total']['line_subtotal_net'], $dispay_round_precision);
			$doc_financial_data['lines_total']['line_total_net'] 			= round( $doc_financial_data['lines_total']['line_total_net'], $dispay_round_precision);
			$doc_financial_data['lines_total']['line_subtotal_tax'] 		= round( $doc_financial_data['lines_total']['line_subtotal_tax'], $dispay_round_precision);
			$doc_financial_data['lines_total']['line_total_tax'] 			= round( $doc_financial_data['lines_total']['line_total_tax'], $dispay_round_precision);
			$doc_financial_data['lines_total']['line_subtotal_gross'] 		= round( $doc_financial_data['lines_total']['line_subtotal_gross'], $dispay_round_precision);
			$doc_financial_data['lines_total']['line_total_gross'] 			= round( $doc_financial_data['lines_total']['line_total_gross'], $dispay_round_precision);
			$doc_financial_data['lines_total']['total_tax_of_tax_summary'] 	= round( $doc_financial_data['lines_total']['total_tax_of_tax_summary'], $dispay_round_precision);

			//doc total was rounded above

		}*/
		

		//calculate rounding difference, tax summary and Tax summary rounding difference, and partner data
		if ( $type == '' ) {

			//set rounding difference (in case only of all line_type query)
			
			//This is not accountable (if doc totals is accounted)
			//$rounding_difference_total_net		= round( $doc_total_net, WC_ROUNDING_PRECISION )   - $lines_total['line_total_net'];
			//$rounding_difference_total_tax 		= round( $doc_total_tax, WC_ROUNDING_PRECISION )   - $lines_total['line_total_tax'];
			//$rounding_difference_total_gross	= round( $doc_total_gross, WC_ROUNDING_PRECISION ) - $lines_total['line_total_gross'];
			
			$rounding_difference_total_net		= $doc_financial_data['doc_total']['total_net']   - $doc_financial_data['lines_total']['line_total_net'];		//the values have already been rounded
			$rounding_difference_total_tax 		= $doc_financial_data['doc_total']['total_tax']   - $doc_financial_data['lines_total']['line_total_tax'];		//the values have already been rounded
			$rounding_difference_total_gross	= $doc_financial_data['doc_total']['total_gross'] - $doc_financial_data['lines_total']['line_total_gross'];	//the values have already been rounded

			wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $rounding_difference_total_net: ' . wsl_vartotext($rounding_difference_total_net));
			wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $rounding_difference_total_tax: ' . wsl_vartotext($rounding_difference_total_tax));
			wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $rounding_difference_total_gross: ' . wsl_vartotext($rounding_difference_total_gross));
			
			if ( $rounding_difference_total_net != 0 or $rounding_difference_total_tax != 0 or $rounding_difference_total_gross != 0 ) {
				$doc_financial_data['rounding_difference']['total_net']		= $rounding_difference_total_net;
				$doc_financial_data['rounding_difference']['total_tax'] 	= $rounding_difference_total_tax;
				$doc_financial_data['rounding_difference']['total_gross']	= $rounding_difference_total_gross;
			}
			
			//Tax summary			
			$tax_summary = array();
			$total_tax_of_tax_summary = 0;
			foreach ($order_taxes as $order_tax_line_id => $order_tax_row) {
				
				//we cannot use this, bacause tax rates can be deleted
				//$tax_rate = FILO_Tax::get_tax_rate( $order_tax_row['rate_id'] );
				//wsl_log(null, 'document-order-items.php get_doc_financial_data $tax_rate: ' . wsl_vartotext($tax_rate));
				
				$rate_id = $order_tax_row['rate_id'];

				if ( isset($recalculated_tax_rate_percents[$rate_id]) ) {
					$tax_rate_percent = $recalculated_tax_rate_percents[$rate_id];
				} else {
					$tax_rate_percent = null;
				}
				
				if ( isset($tax_rate_net_totals[$rate_id]) ) {
					$tax_rate_net_total = $tax_rate_net_totals[$rate_id];
				} else {
					$tax_rate_net_total = null;
				}
				
				/*if ( ! empty($tax_rate['tax_rate_percent']) and $tax_rate['tax_rate_percent'] != 0) {
					$recalculated_net_total_not_round = ( $order_tax_row['tax_amount'] + $order_tax_row['shipping_tax_amount'] ) / ( $tax_rate['tax_rate_percent'] / 100 );
					$recalculated_gross_total_not_round = $recalculated_net_total_not_round + $order_tax_row['tax_amount'] + $order_tax_row['shipping_tax_amount'];
				} else {
					$recalculated_net_total_not_round = null;
					$recalculated_gross_total_not_round = null;
				}*/
				
				$tax_summary[$order_tax_line_id] = array (
					'rate_id' => $rate_id,
					//'tax_rate_country' => $tax_rate['tax_rate_country'],
					'tax_rate_percent' => $tax_rate_percent,//$tax_rate['tax_rate_percent']
					//'tax_rate_state' => $tax_rate['tax_rate_state'],
					'item_type' => $order_tax_row['type'],
					'item_name' => $order_tax_row['label'], //$tax_rate['tax_rate_name'],
					'item_code' => $order_tax_row['name'],
					'line_total_tax' => round( $order_tax_row['tax_amount'] + $order_tax_row['shipping_tax_amount'], $line_round_precision ), //we use $line_round_precision rounding becouse in case of total level rounding we can calculate tax types without rounding, and only tha total tax will be rounded
					'line_and_fee_tax_not_round' => $order_tax_row['tax_amount']+0,
					'shipping_tax_amount_not_round' => $order_tax_row['shipping_tax_amount']+0,
					
					'recalculated_net_total_not_round' => $tax_rate_net_total,
					'recalculated_gross_total_not_round' => $tax_rate_net_total + $order_tax_row['tax_amount'] + $order_tax_row['shipping_tax_amount'],
					'recalculated_net_total' => round( $tax_rate_net_total ),
					'recalculated_gross_total' => round( $tax_rate_net_total + $order_tax_row['tax_amount'] + $order_tax_row['shipping_tax_amount'] ),
					
				);
				
				$total_tax_of_tax_summary += $tax_summary[$order_tax_line_id]['line_total_tax'];
				
			}

			//?
			//$doc_financial_data['line_total_gross']['total_tax'] = round( $doc_financial_data['line_total_gross']['total_tax'], $dispay_round_precision);
			
			$doc_financial_data['tax_summary'] = $tax_summary;
			$doc_financial_data['lines_total']['total_tax_of_tax_summary'] = round( $total_tax_of_tax_summary, $total_round_precision ); //total tax calculated by summary of tax total rows (sum of tax types amount)

			//AFTER TAX SUMMARY CALCULATION ROUNDINGS
			//In case of total level rounding, TAX SUMMARY lines have not rounded yet, but the totals have calculated from the none rounded lines
			//so we can round the lines now
			/* //$line_rounding set is commented out above, thus it can be commented out too:
			if ( ! $line_rounding ) {

				if ( isset($doc_financial_data['tax_summary']) and is_array($doc_financial_data['tax_summary']) )
				foreach ( $doc_financial_data['tax_summary'] as $order_tax_line_id => $tax_summary_line ) {
					
					$doc_financial_data['tax_summary'][$order_tax_line_id]['line_total_tax'] = round( $doc_financial_data['tax_summary'][$order_tax_line_id]['line_total_tax'], $dispay_round_precision);
				
				}

			}*/

			$payment_method_data_html = $this->get_payment_method_data_html();

			//remove html tags			
			$payment_method_data_html_clear = $payment_method_data_html;
			$payment_method_data_html_clear = str_replace('<h2>', '<div class="filo_headline_2">', $payment_method_data_html_clear); //older WC versions
			$payment_method_data_html_clear = str_replace('<h2 class="wc-bacs-bank-details-heading">', '<div class="filo_headline_2">', $payment_method_data_html_clear); 
			$payment_method_data_html_clear = str_replace('</h2>', '</div>', $payment_method_data_html_clear);
			$payment_method_data_html_clear = str_replace('<h3>', '<div class="filo_headline_3">', $payment_method_data_html_clear);
			$payment_method_data_html_clear = str_replace('</h3>', '</div>', $payment_method_data_html_clear);
			$payment_method_data_html_clear = str_replace('<strong>', '<div class="filo_value">', $payment_method_data_html_clear);
			$payment_method_data_html_clear = str_replace('</strong>', '</div>', $payment_method_data_html_clear);

			$notes = $this->get_customer_order_notes();
			krsort($notes); //sort notes descending by key, it will be the chronological order

			//create documenter and then document for handling replacement of find / replace {} tags defined in trigger() functions of documents.
			//first create classname because document can be find about this
			//document means documenter documents (not financial documents)
			$doctype_obj = get_post_type_object( $this->get_doc_type() );
			$doctype_classname = $doctype_obj->class_name; //e.g: FILO_FinaDoc_Sa_Invoice
			$documenter = FILO()->documenter();
			$document = $documenter->get_document( $doctype_classname );
			
			$all_notes = '';	
			
			if ( empty($pseudo_doc_type) ) { // not pseudo doc
				
				foreach ($notes as $note) {
	
					$all_notes .= '<div class="filo_note">';
				
					//<br> tags are cleaned by format_string function unnecessarily
					//to prevent this, we replace <br> to [br] and after formatting, replace back from [br] to <br>
					$note_text = $note->comment_content;
					
					$note_text = str_replace( 
						array('<br>','<p>','</p>',"\n"), 
						array('[br]','[p]','[/p]','[br]'),
						$note_text);
					$note_text =  strip_tags( $document->format_string( $note_text ) ); //remove html elements (for example links for base document)
					$note_text = str_replace( 
						array('[br]','[p]','[/p]'),
						array('<br>','<p>','</p>'),							
						$note_text);
						
					$all_notes .= $note_text;
					
					$all_notes .= '</div>';
				
				}
			
			} else { // not pseudo doc
				
				$all_notes = $this->get_pseudo_doc_comment($pseudo_doc_type);
				
			}

						
			//Partner Data
			$doc_financial_data['partner_data']['partner_id']                       = $this->get_customer_user();
			$doc_financial_data['partner_data']['partner_name']                     = trim($this->get_formatted_billing_full_name() . ' ' . $this->get_billing_company()); //trim($this->billing_first_name . ' ' . $this->billing_last_name . ' ' . $this->billing_company); 
			$doc_financial_data['partner_data']['billing_address']['first_name']    = $this->get_billing_first_name(); //$this->billing_first_name;
			$doc_financial_data['partner_data']['billing_address']['last_name']     = $this->get_billing_last_name(); //$this->billing_last_name;
			$doc_financial_data['partner_data']['billing_address']['company']       = $this->get_billing_company(); //$this->billing_company;
			$doc_financial_data['partner_data']['billing_address']['address_1']     = $this->get_billing_address_1(); //$this->billing_address_1;
			$doc_financial_data['partner_data']['billing_address']['address_2']     = $this->get_billing_address_2(); //$this->billing_address_2;
			$doc_financial_data['partner_data']['billing_address']['city']          = $this->get_billing_city(); //$this->billing_city;
			$doc_financial_data['partner_data']['billing_address']['state']         = $this->get_billing_state(); //$this->billing_state;
			$doc_financial_data['partner_data']['billing_address']['postcode']      = $this->get_billing_postcode(); //$this->billing_postcode;
			$doc_financial_data['partner_data']['billing_address']['country']       = $this->get_billing_country(); //$this->billing_country;
			
			$doc_financial_data['partner_data']['shipping_address']['first_name']    = $this->get_shipping_first_name(); //$this->shipping_first_name;
			$doc_financial_data['partner_data']['shipping_address']['last_name']     = $this->get_shipping_last_name(); //$this->shipping_last_name;
			$doc_financial_data['partner_data']['shipping_address']['company']       = $this->get_shipping_company(); //$this->shipping_company;
			$doc_financial_data['partner_data']['shipping_address']['address_1']     = $this->get_shipping_address_1(); //$this->shipping_address_1;
			$doc_financial_data['partner_data']['shipping_address']['address_2']     = $this->get_shipping_address_2(); //$this->shipping_address_2;
			$doc_financial_data['partner_data']['shipping_address']['city']          = $this->get_shipping_city(); //$this->shipping_city;
			$doc_financial_data['partner_data']['shipping_address']['state']         = $this->get_shipping_state(); //$this->shipping_state;
			$doc_financial_data['partner_data']['shipping_address']['postcode']      = $this->get_shipping_postcode(); //$this->shipping_postcode;
			$doc_financial_data['partner_data']['shipping_address']['country']       = $this->get_shipping_country(); //$this->shipping_country;

			//Seller Data
			$doc_financial_data['seller_address']['first_name']    = $this->get_seller_first_name(); //$this->seller_first_name;
			$doc_financial_data['seller_address']['last_name']     = $this->get_seller_last_name(); //$this->seller_last_name;
			$doc_financial_data['seller_address']['company']       = $this->get_seller_company(); //$this->seller_company;
			$doc_financial_data['seller_address']['address_1']     = $this->get_seller_address_1(); //$this->seller_address_1;
			$doc_financial_data['seller_address']['address_2']     = $this->get_seller_address_2(); //$this->seller_address_2;
			$doc_financial_data['seller_address']['city']          = $this->get_seller_city(); //$this->seller_city;
			$doc_financial_data['seller_address']['state']         = $this->get_seller_state(); //$this->seller_state;
			$doc_financial_data['seller_address']['postcode']      = $this->get_seller_postcode(); //$this->seller_postcode;
			$doc_financial_data['seller_address']['country']       = $this->get_seller_country(); //$this->seller_country;
			$doc_financial_data['seller_address']['seller_vat_number'] = $this->get_seller_vat_number();
			$doc_financial_data['seller_address']['seller_domestic_vat_number'] = $this->get_seller_domestic_vat_number();			
			
			//Document Data
			$doc_financial_data['document_data']['document_type']                    = $this->get_doc_type();
			$doc_financial_data['document_data']['document_subtype']                 = $this->get_doc_subtype();
			$doc_financial_data['document_data']['transaction_type']                 = $this->get_transaction_type();
			$doc_financial_data['document_data']['document_type_name']               = $post_type_name;
			$doc_financial_data['document_data']['document_type_short_name']         = $post_type_short_name == '' ? $post_type_name : $post_type_short_name;
			
			//$doc_financial_data['document_data']['creation_date']                    = $this->post->post_date;
			$doc_financial_data['document_data']['document_number']                  = $this->get_document_number($pseudo_doc_type);
			$doc_financial_data['document_data']['document_id']                      = $this->id;
			//$doc_financial_data['document_data']['is_cancel_type']                   = self::get_doc_type_registration_value( $this->post->post_type, $this->get_doc_subtype(), 'cancel' );
			$doc_financial_data['document_data']['is_cancel_type']                   = self::get_doc_type_registration_value( $this->get_doc_type(), $this->get_doc_subtype(), 'cancel' );
			
			//$doc_financial_data['document_data']['creation_date']                    = $this->get_creation_date(); //pstdt
			$crd = $this->get_date_created();
			wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $crd: ' . wsl_vartotext($crd));
			$doc_financial_data['document_data']['creation_date']                    = empty($crd) ? null: $crd->date; //$this->post->post_date; //pstdt
			$doc_financial_data['document_data']['creation_date_short']              = date_i18n( 'Y-m-d', strtotime( $this->get_creation_date() ) );
			$doc_financial_data['document_data']['due_date']                         = $this->get_due_date($pseudo_doc_type);
			$doc_financial_data['document_data']['completion_date']                  = $this->get_completion_date($pseudo_doc_type);
			
			$doc_financial_data['document_data']['payment_method']                   = $this->get_payment_method();
			$doc_financial_data['document_data']['payment_method_title']             = $this->get_payment_method_title();
			$doc_financial_data['document_data']['payment_method_data_html']         = $payment_method_data_html;
			$doc_financial_data['document_data']['payment_method_data_html_clear']   = $payment_method_data_html_clear;
			$doc_financial_data['document_data']['notes']                            = $all_notes;
			
			//$doc_financial_data['document_data']['filo_is_vat_exempt']               = $this->get_filo_is_vat_exempt();
			$doc_financial_data['document_data']['is_vat_exempt']                    = $this->get_is_vat_exempt(); //$this->is_vat_exempt;
			$doc_financial_data['document_data']['filo_is_tax_enabled']              = $this->get_filo_is_tax_enabled();
			$doc_financial_data['document_data']['currency']                         = $this->get_currency();
			$doc_financial_data['document_data']['shop_role']                        = $this->get_played_role( 'shop' );  
			$doc_financial_data['document_data']['partner_role']                     = $this->get_played_role( 'partner' );
			$doc_financial_data['document_data']['shop_role_display_name']           = $this->get_played_role_display_name( 'shop' );
			$doc_financial_data['document_data']['partner_role_display_name']        = $this->get_played_role_display_name( 'partner' );
			
			$document_settings = get_option('woocommerce_document_' . $this->get_doc_type() . '_settings');
			//$doc_customizer_root_settings = FILO_Customize_Manager::get_root_value();  //use it e.g. $doc_customizer_settings['']['filo_doc_template_custom_settings']['pdf_gen_doc_format']
			$doc_customizer_root_settings = FILO_Customize_Manager::get_root_value( $default = null, $enable_cleaning = false, $is_simple = true );  //use it e.g. $doc_customizer_settings['']['filo_doc_template_custom_settings']['pdf_gen_doc_format']

			
			//wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $doc_customizer_root_settings: ' . wsl_vartotext($doc_customizer_root_settings)); //big
			
			if ( isset($doc_customizer_root_settings['']['filo_doc_template_custom_settings']) ) {
				$doc_customizer_template_settings = $doc_customizer_root_settings['']['filo_doc_template_custom_settings'];
			}

			if ( isset($doc_customizer_root_settings['']['Document-General']['css_document_general_selector']['filo_logo']) ) {
				$doc_financial_data['document_data']['filo_logo_url'] 				= $doc_customizer_root_settings['']['Document-General']['css_document_general_selector']['filo_logo'];
			} else {
				$doc_financial_data['document_data']['filo_logo_url'] 				= null;
			}
			
			$doc_financial_data['document_data']['pdf_gen_doc_format']              = $doc_customizer_template_settings['pdf_gen_doc_format'];

			//TEST RaPe
			//if ( $calculate_footer_lines ) {
			if ( true ) {
								
				$footer_id = 0;			
				
				/*
	            [label] => Total:
	            [value] => 23
	            [class] => order_total				
				
				$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['label']
				$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['class']
				$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_net']
				$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_tax']
				$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_gross']
				*/

				//1. Footer lines: 	Subtotal with rounding:
				//	- Rounding diff (if set) (if detailed)
				//	- Subtotal
	
				wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $doc_customizer_template_settings: ' . wsl_vartotext($doc_customizer_template_settings));
	
				// CALCULATE 1.
				if ( in_array( $doc_customizer_template_settings['pdf_gen_doc_format'], array('extra_lines', 'detailed') ) ) { //pdf_shipping_fees_place == shipping_fees_in_item_lines
					
					$disp_line_total_net_without_rounding_dif = $doc_financial_data['lines_total']['line_total_net'];
					$disp_line_total_tax_without_rounding_dif = $doc_financial_data['lines_total']['line_total_tax'];
					$disp_line_total_gross_without_rounding_dif = $doc_financial_data['lines_total']['line_total_gross'];
					
					if (isset($doc_financial_data['rounding_difference'])) {
						
						$disp_rounding_difference_total_net = $doc_financial_data['rounding_difference']['total_net'];
						$disp_rounding_difference_total_tax = $doc_financial_data['rounding_difference']['total_tax'];
						$disp_rounding_difference_total_gross = $doc_financial_data['rounding_difference']['total_gross'];
						
						$disp_line_total_net = $disp_line_total_net_without_rounding_dif + $disp_rounding_difference_total_net;
						$disp_line_total_tax = $disp_line_total_tax_without_rounding_dif + $disp_rounding_difference_total_tax;
						$disp_line_total_gross = $disp_line_total_gross_without_rounding_dif + $disp_rounding_difference_total_gross;
						
					} else {

						$disp_line_total_net = $disp_line_total_net_without_rounding_dif;
						$disp_line_total_tax = $disp_line_total_tax_without_rounding_dif;
						$disp_line_total_gross = $disp_line_total_gross_without_rounding_dif;
						
					}
		
					
				} elseif ( $doc_customizer_template_settings['pdf_gen_doc_format'] == 'classic' ) { //pdf_shipping_fees_place == shipping_fees_in_summarys_lines
		
					$disp_line_total_net = $doc_financial_data['line_types_total']['line_item']['line_total_net'];
					$disp_line_total_tax = $doc_financial_data['line_types_total']['line_item']['line_total_tax'];
					$disp_line_total_gross = $doc_financial_data['line_types_total']['line_item']['line_total_gross'];
					
				}
		
				// SET RESULT 1.		
				if ( $doc_customizer_template_settings['pdf_gen_doc_format'] != 'detailed' ) { //pdf_item_complexity == items_without_tax_values
	
					$footer_id ++;
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['label'] = _x('Subtotal', 'filo_doc', 'filo_text');
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['class'] = 'order_subtotal';
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_net'] = $disp_line_total_net;
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_tax'] = '';
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_gross'] = '';
				
				} elseif ( $doc_customizer_template_settings['pdf_gen_doc_format'] == 'detailed' ) { //pdf_item_complexity == items_contains_tax_values 
		
					if ( isset( $doc_financial_data['rounding_difference'])) {
						$footer_id ++;
						$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['label'] = _x('Rounding difference', 'filo_doc', 'filo_text');
						$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['class'] = 'order_difference';
						$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_net'] = $disp_rounding_difference_total_net;
						$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_tax'] = $disp_rounding_difference_total_tax;
						$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_gross'] = $disp_rounding_difference_total_gross;
					}
	
					$footer_id ++;
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['label'] = _x('Subtotal', 'filo_doc', 'filo_text');
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['class'] = 'order_subtotal';
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_net'] = $disp_line_total_net;
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_tax'] = $disp_line_total_tax;
					$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_gross'] = $disp_line_total_gross;
			
				} 			
	
				//2. Footer lines: tax and other lines ( get_doc_item_taxtotal (Sum lines) )
				//	- 2a. Shipping, fee (if classic)
				//	- 2b. Tax and other Lines (if classic or extra_lined - sonot detailed)
				//		- tax: 
				//			- n if itemized (tax_label)
				//			- or 1 if NOT itemozed (total_tax)
				//		- order_discount (if set)
				//		- order_total
				
				// CALCULATE 2.	
				//2a. in classic case, shipping and fee linse are added to item summary lines
				if ( $doc_customizer_template_settings['pdf_gen_doc_format'] == 'classic' ) {
					$doc_sum_lines = $this->get_doc_sum_lines( $doc_financial_data, array('shipping','fee') );
				} else {
					$doc_sum_lines = array();
				}
	
				wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $doc_sum_lines: ' . wsl_vartotext($doc_sum_lines));
	
				// 2b. Display tax lines in case of classic and extra lines format
				// and display discount and total 
				$display_tax_lines = $doc_customizer_template_settings['pdf_gen_doc_format'] == 'detailed' ? false : true; 
				$display_discont_and_total_lines = true; 
				$format_numbers = false;
				$doc_item_taxtotal = $this->get_doc_item_taxtotal('', $display_tax_lines, $display_discont_and_total_lines, $format_numbers ); //'pdf_item_complexity == items_without_tax_values
				
				wsl_log(null, 'class-filo-financial-document.php get_doc_financial_data $doc_item_taxtotal: ' . wsl_vartotext($doc_item_taxtotal));			
				
				// SET RESULT 2.
				$totals = array_merge($doc_sum_lines, $doc_item_taxtotal);
				
				if (isset( $totals ) && is_array( $totals ) ) {
	
					foreach ( $totals as $total ) {
	
						//values goes to gross column into detailed mode, otherwise into net column
						if ( $doc_customizer_template_settings['pdf_gen_doc_format'] == 'detailed' ) {
							$footer_id ++;
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['label'] = $total['label'];
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['class'] = $total['class'];
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_net'] = '';
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_tax'] = '';
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_gross'] = $total['value']; //end total is in gross column 
						} else {
							$footer_id ++;
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['label'] = $total['label'];
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['class'] = $total['class'];
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_net'] = $total['value'];  //end total is in net column
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_tax'] = '';
							$doc_financial_data['footer_lines'][$this->format_footer_id($footer_id)]['line_total_gross'] = '';
							
						}
						
						
					}
				}
				
				/*
				// these dummy lines is used by shortcodes, when a normally empty call have to be returned (empty_line line_id should be used)
				$doc_financial_data['lines']['empty_line']['unit_subtotal_net'] 	= '';
				$doc_financial_data['lines']['empty_line']['unit_total_net'] 		= '';
				$doc_financial_data['lines']['empty_line']['line_subtotal_net']		= '';
				$doc_financial_data['lines']['empty_line']['line_total_net']		= '';
				$doc_financial_data['lines']['empty_line']['line_subtotal_tax'] 	= '';
				$doc_financial_data['lines']['empty_line']['line_total_tax'] 		= '';
				$doc_financial_data['lines']['empty_line']['line_subtotal_gross']	= '';
				$doc_financial_data['lines']['empty_line']['line_total_gross']		= '';
				*/
						
			}

			//More custom fields can be added using filo_initialize_custom_field_values filter, e.g.:
			//_vat_number, _eu_vat_checked

			$doc_financial_data = apply_filters('filo_copy_initialize_custom_field_values', $doc_financial_data, $this);
			
		}		

		//wsl_log(null, 'document-order-items.php get_doc_financial_data $doc_financial_data: ' . wsl_vartotext($doc_financial_data)); //big

		return apply_filters( 'filo_get_doc_financial_data', $doc_financial_data, $this );
	}

	/**
	 * format_footer_id
	 * 
	 * apply F pewfix and 0 padding, e.g. F000004
	 */ 
	public static function format_footer_id($id) {
		return 'F' . str_pad($id, 6, '0', STR_PAD_LEFT); 
	} 
	
	/**
	 * Shortcode to display post_meta
	 * [filogy_doc ...]
	 * 		"aaa" "bbb" (parameters without key names are the dimensions of the data) - they are standard fields from doc_financial_data array
	 * 		"ccc" (special parameters without key name, e.g. formatted_seller_address; they are not in doc_financial_data, but handled by this function individually):
	 * 			- formatted_seller_address
	 * 
	 * 		br=true - linebrake if contained data
	 * 		seller_address
	 * e.g. [filogy_doc doc_id="2944" "document_data" "currency" br=true]
	 * 
	 * [filogy_doc_show_if ...]
	 * 		....
	 * [/filogy_doc_show_if]
	 * "filogy_doc_show_if" shows or hides content of enclosed short code tags. It is needed because filogy_doc short code cannot include another filogy_doc shortcode, but filogy_doc_show_if can inclusde filogy_doc.
	 */
	public static function filogy_doc_shortcode( $atts, $content, $short_code_tag ) { // ADD RaPe
		global $post, $filo_post_types_financial_documents;
		
		//ADD RaPe begin
		//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $post: ' . wsl_vartotext($post));
		wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $atts: ' . wsl_vartotext($atts));
		wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $content: ' . wsl_vartotext($content));
		wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $short_code_tag: ' . wsl_vartotext($short_code_tag));
		if ( is_array($atts) ) { 
			extract( $atts );
		}

		//get doc_id
		if ( isset($doc_id) ) {
			//if we have doc_id attribute (there is extracted $doc_id var), then use this
			$doc_id = $doc_id; // :-)
		} else {
			$doc_id = self::get_doc_id_invbld();
		}
		
		/*
		} elseif ( isset($_GET['doc_id']) ) {
			//if doc_id http parameter is set, then use it 
			$doc_id = wc_clean( $_GET['doc_id'] ); //+wc_clean  
		} elseif ( in_array($post->post_type, $filo_post_types_financial_documents) ) {
			//if the actual post is a filo financial document, then use the global $post->ID 
			$doc_id = $post->ID;
		} else {
			return "Error: unknown document id";
		}*/
		
		wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $doc_id: ' . wsl_vartotext($doc_id));
		
		$finadoc = wc_get_order( $doc_id ); //filo_get_order
		if ( !empty($finadoc) ) {
			$my_doc_financial_data = $finadoc->get_doc_financial_data();

			$result = $my_doc_financial_data;
			$error = null;
			$used_dimensions = '';
			//a normal shortcode looks like this: [filogy_doc doc_id=999 document_data currency]
			//attributes of the above example $atts = array( 'doc_id' => 999, 1 => 'document_data', 2 => 'currency' )
			//we use the no name attributes for defining the dimensions of doc_financial_data to be returned
			//"parse" the shortcode attributes that has no name (e.g. document_data, currency), and narrow the financial_data according to the parsed dimensions.
			if ( isset($atts) and is_array($atts) )
			foreach ( $atts as $atts_key => $atts_value) {
				
				//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $atts X: ' . wsl_vartotext($atts));
				//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $atts_key: ' . wsl_vartotext($atts_key));
				//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $atts_value: ' . wsl_vartotext($atts_value));
				//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $result X: ' . wsl_vartotext($result));
				
				//$is_standard_financial_data_dimension = false;
				if ( is_numeric($atts_key) ) {
					
					//concatenate the dimensions in order
					$used_dimensions .= (empty($used_dimensions)? '' : ' -> ') . $atts_value;

					wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $used_dimensions: ' . wsl_vartotext($used_dimensions));
					
					//special fields called by functions (return immadiately, break the loop)

					if ( $atts_value == "formatted_billing_address" ) {
						
						//put name parts of addresses into div, class = filo_address_name
						add_filter('woocommerce_localisation_address_formats', array(FILOFW()->countries, 'filo_localisation_address_formats_namediv') ,1); //priority 0 is important, to be the first this procedure in the filter, becouse it overwrites the formats, and the following registered functions can modify it. (If it would run later, then it would overwrite others!)
						
						$result = $finadoc->get_formatted_billing_address();
						break;

					} elseif ( $atts_value == "formatted_shipping_address" ) {
						
						//put name parts of addresses into div, class = filo_address_name
						add_filter('woocommerce_localisation_address_formats', array(FILOFW()->countries, 'filo_localisation_address_formats_namediv') ,1); //priority 0 is important, to be the first this procedure in the filter, becouse it overwrites the formats, and the following registered functions can modify it. (If it would run later, then it would overwrite others!)

						if ( ! wc_ship_to_billing_address_only() && $finadoc->needs_shipping_address() && ( $shipping = $finadoc->get_formatted_shipping_address() ) ) {
							$result = '<div class="filo_value">' . $shipping . '</div>';				
						} else {
							$result = '';
						}
						
						break;

					} elseif ( $atts_value == "formatted_seller_address" ) {
								
						//put name parts of addresses into div, class = filo_address_name
						add_filter('woocommerce_localisation_address_formats', array(FILOFW()->countries, 'filo_localisation_address_formats_namediv') ,1); //priority 0 is important, to be the first this procedure in the filter, becouse it overwrites the formats, and the following registered functions can modify it. (If it would run later, then it would overwrite others!)
						
						$result = $finadoc->get_formatted_seller_address( 'before_filo_start_order' );
						break;

					} elseif ( $atts_value == "display_logo" ) {
						
						//$filo_document_logo = get_option('filo_document_logo');
						$filo_document_logo = $my_doc_financial_data['document_data']['filo_logo_url']; 
						
						if ( ! empty( $filo_document_logo ) ) {
							$result  = '<div id="filo_logo">';
							$result .= '<img src="' . $filo_document_logo . '" alt="">';
							$result .= '</div>';
						} else {
							$result  = '<div id="filo_logo">';
							$result .= '<img src="" alt="">';
							$result .= '</div>';
						}

						break;
					
					} elseif ( $atts_value == "line_item_ids" ) {
							
						$item_ids = array();
						if ( isset($my_doc_financial_data['lines']) and is_array($my_doc_financial_data['lines']) )
						foreach ($my_doc_financial_data['lines'] as $item_id => $item_value) {
							$item_ids[] = $item_id;
						}		
						
						//convert to comma separated list
						$result = implode(', ', $item_ids);
						
						break;

					} elseif ( $atts_value == "footer_line_ids" ) {
							
						$item_ids = array();
						if ( isset($my_doc_financial_data['footer_lines']) and is_array($my_doc_financial_data['footer_lines']) )
						foreach ($my_doc_financial_data['footer_lines'] as $item_id => $item_value) {
							$item_ids[] = $item_id;
						}		
						
						//convert to comma separated list
						$result = implode(', ', $item_ids);
						
						break;

					} elseif ( $atts_value == "has_base_order" ) { // true / false
						//if our doc is a sales document after the order in logical chronological order, then dispay the order number
						
						$result = in_array( $my_doc_financial_data['document_data']['document_type'], array('filo_sa_deliv_note', 'filo_sa_invoice') );
						break;							

					} elseif ( $atts_value == "is_invoice" ) { // true / false
						wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode is_invoice $my_doc_financial_data[document_data][document_type]: ' . wsl_vartotext($my_doc_financial_data['document_data']['document_type']));
						$result = in_array( $my_doc_financial_data['document_data']['document_type'], array('filo_sa_invoice', 'filo_pu_invoice', ) );
						break;							
							
					} elseif (isset($result[$atts_value])) {
						
						
					//standard fields from doc_financial_data array						
						$result = $result[$atts_value];

						//wsl_log(null, 'STANDARD class-filo-financial-document.php filogy_doc_shortcode $atts_key: ' . wsl_vartotext($atts_key));
						//wsl_log(null, 'STANDARD class-filo-financial-document.php filogy_doc_shortcode $atts_value: ' . wsl_vartotext($atts_value));
						//wsl_log(null, 'STANDARD class-filo-financial-document.php filogy_doc_shortcode $result: ' . wsl_vartotext($result));

						if ( ! is_array($result) ) {
							//$is_standard_financial_data_dimension = true;
							$last_standard_financial_data_dimension_key = $atts_value;
						}
					} elseif ( in_array($atts_value, $optional_atts = array('item_meta') )) { //REGISTER OPTIONAL FIELDS not to return error mesage
						// We know that the actual attribute is not set here, because the previous elseif has already examined it and could not find,
						// but this is an optional attribute, thus we have to return an empty value, and must not return any error message.
						$result = '';
					} else {
						$error .= (empty($error)? '' : '\n') . 'Warning, the data keys defined in shortcode does not exist. Key: ' . $used_dimensions; 
					}
				}
					
			}

			//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $is_standard_financial_data_dimension: ' . wsl_vartotext($is_standard_financial_data_dimension));
			//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $last_standard_financial_data_dimension_key: ' . wsl_vartotext($last_standard_financial_data_dimension_key));

			//line_total_tax and line_subtotal_gross columns can be used only in detailed mode 
			//if the short code contains a standard financial data dimension, and it is line_total_tax, line_subtotal_gross
			//then empty the result text and set an error message
			if ( isset($last_standard_financial_data_dimension_key) and in_array($last_standard_financial_data_dimension_key, array('line_total_tax', 'line_total_gross' ) ) ) {
								
				//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $last_standard_financial_data_dimension_key A: ' . wsl_vartotext($last_standard_financial_data_dimension_key));					
				//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $my_doc_financial_data[document_data][pdf_gen_doc_format] A: ' . wsl_vartotext($my_doc_financial_data['document_data']['pdf_gen_doc_format']));
				if ( $my_doc_financial_data['document_data']['pdf_gen_doc_format'] != 'detailed' ) {
					$result = '';
					$error = 'This field can be used only in Detailed mode'; 	
				}
				
			}
			

			wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $error: ' . wsl_vartotext($error));
			
			//if there is an error and hide_error attribute is not set, then give back the error message as result (hide_error = true)
			if ( ! empty($error) ) {
				
				$hide_error = in_array( get_option( 'filo_hide_error_messages_on_filodocs' ), array('yes', '1') );
				wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode filo_hide_error_messages_on_filodocs: ' . wsl_vartotext(get_option( 'filo_hide_error_messages_on_filodocs' )));
				wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $hide_error: ' . wsl_vartotext($hide_error));
				if ( $hide_error == true ) {
					$result = null;
				} else {
					$result = $error;
				}
			}
			
		}

		//Apply br after data, according to the shortcode attribute (e.g. br=true)
		if ( isset($br) and $br == true ) {
			$result .= '<br>';
		}

		//Apply special display formats, according to the shortcode attribute (e.g. format="currency")
		if ( isset($format) ) {
			
			switch ($format) {

				case 'currency':
					if ( is_numeric($result)) {
						$currency = array( 'currency' => $finadoc->get_currency() );
						$result = wc_price( $result, $currency);
					}
					break;
					
			}
			
		}
		
		//If isset attribute is set, then the result is not important, but it is important whetere the result exists or not (not null or null)
		//If there would be result, then give back the content of the shortcode (the enclosed content), otherwise we give back null string.  
		//(e.g. isset=true)
		//e.g. '[filogy_doc isset=true "document_data" "is_cancel_type"] --CONTENT-- [/filogy_doc]';
		/*if ( isset($isset) and $isset == true ) {
			if ( $result != '' ) {
				$result = do_shortcode( $content ); //give back the content, and apply the shortcode is our shortcode contains another embedded shortcodes
			} else {
				$result = null;
			}
		}*/
		
		if ( $short_code_tag == 'filogy_doc_show_if' ) {
			wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode filogy_doc_show_if $result: ' . wsl_vartotext($result));
			if ( $result != '' and empty($error) ) { // in case of error, we do not show the content
				$result = do_shortcode( $content ); //give back the content, and apply the shortcode is our shortcode contains another embedded shortcodes
				wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode filogy_doc_show_if $result A: ' . wsl_vartotext(''));
			} else {
				$result = null;
				wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode filogy_doc_show_if $result B: ' . wsl_vartotext(''));
			}
		}
		
		//wsl_log(null, 'class-filo-financial-document.php filogy_doc_shortcode $result: ' . wsl_vartotext($result));

		return $result;
		
	}


	/**
	 * get_filo_sample_order_id_option
	 */	
	public static function get_filo_sample_order_id_option() {

		$filo_sample_order_id_option = get_option( 'filo_sample_order_id' );
		
		// if filo_sample_order_id is not set, then use the newest one 
		if ( empty($filo_sample_order_id_option) ) {
				
			$finadoc_title_list = FILO_Financial_Document::get_finadoc_title_list( $doc_types = null, $orderby = 'desc', $item_limit = 1 );
			
			//get the first finadoc id, thus the first array key
			reset($finadoc_title_list);
			$filo_sample_order_id_option = key($finadoc_title_list);
			
			//wsl_log(null, 'class-filo-financial-document.php get_filo_sample_order_id_option $finadoc_title_list: ' . wsl_vartotext($finadoc_title_list));
			//wsl_log(null, 'class-filo-financial-document.php get_filo_sample_order_id_option $filo_sample_order_id_option: ' . wsl_vartotext($filo_sample_order_id_option));
			
		}
		
		return $filo_sample_order_id_option;
		
	}

	/**
	 * get_doc_id_invbld
	 */	
	public static function get_doc_id_invbld() {
		global $post, $filo_post_types_financial_documents;

		//wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $_GET: ' . wsl_vartotext($_GET));
		//wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $filo_post_types_financial_documents: ' . wsl_vartotext($filo_post_types_financial_documents));
		
		/*
		//get post 'sub argument' of return argument of the URL if we are on customizer page  
		if ( isset($_GET['return']) ) {
			
			$parsed_customizer_return_url = parse_url($_GET['return']);
			
			if ( isset($parsed_customizer_return_url['query']) ) {
				
				parse_str($parsed_customizer_return_url['query'], $parsed_customizer_return_url_query_args);
				
				if ( isset($parsed_customizer_return_url_query_args['post'])) {
					$customizer_template_post_id = $parsed_customizer_return_url_query_args['post'];
				}
				
			}
			
			//e.g. 
			//	$_GET: Array
			//		(
			//		    [autofocus] => Array
			//		        (
			//		            [control] => color1
			//		        )
			//		
			//		    [filo_usage] => doc
			//		    [return] => /wp-admin/post.php?post=2943&action=edit
			//		)
			//	
			//$parsed_return_url: Array
			//	(
			//	    [path] => /wp-admin/post.php
			//	    [query] => post=2943&action=edit
			//	)
			//	
			//$parsed_return_url_query_args: Array
			//	(
			//	    [post] => 2943
			//	    [action] => edit
			//	)
			
			//wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $parsed_return_url: ' . wsl_vartotext($parsed_return_url));
			//wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $parsed_return_url_query_args: ' . wsl_vartotext($parsed_return_url_query_args));
			
		}*/
		
		$doc_id_inv_builder_template = null;
		if ( isset($post) and $post->post_type == 'filoinv_template' ) {
			
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $post: ' . wsl_vartotext($post));
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $post->post_type: ' . wsl_vartotext($post->post_type));
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $post->ID: ' . wsl_vartotext($post->ID));
			
			//if the actual post is an invoice template of invoice builder, 
			//then get the sample order id that is set for this template in it's metadata  
			$doc_id_inv_builder_template = get_post_meta( $post->ID, 'filogy_sample_order_id', true );
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $doc_id_inv_builder_template: ' . wsl_vartotext($doc_id_inv_builder_template));
		}
		
		wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $doc_id_inv_builder_template: ' . wsl_vartotext($doc_id_inv_builder_template));
		
		//$filo_sample_order_id_option = get_option( 'filo_sample_order_id' );
		
		////wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $filo_sample_order_id_option: ' . wsl_vartotext($filo_sample_order_id_option));
		
		//get doc_id
		if ( isset($_GET['doc_id']) and ! empty($_GET['doc_id']) ) {
			//if doc_id http parameter is set, then use it 
			$doc_id = wc_clean( $_GET['doc_id'] ); //+wc_clean
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $doc_id 1: ' . wsl_vartotext($doc_id));  
		} elseif ( isset($filo_post_types_financial_documents) and is_array($filo_post_types_financial_documents)  // FILO_Post_Types::is_filo_financial_document( $post->post_type ) 
			and isset($post) and in_array($post->post_type, $filo_post_types_financial_documents) ) {
			//if the actual post is a filo financial document, then use the global $post->ID 
			$doc_id = $post->ID;
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $doc_id 2: ' . wsl_vartotext($doc_id));
		} elseif ( ! empty($doc_id_inv_builder_template) ) {
			//if the actual post is an invoice template of invoice builder, 
			//then get the sample order id that is set for this template in it's metadata  
			$doc_id = $doc_id_inv_builder_template;
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $doc_id 3: ' . wsl_vartotext($doc_id));
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $sample_order_id 3: ' . wsl_vartotext($sample_order_id));
		/*} elseif ( isset($customizer_template_post_id) ) {
			//if we found a post_id in post sub parameter of a 'return' URL parameter. This came from a link of a Filogy template actions metabox link of a siteorigin invoice editor page, that link goes to a customizer page to customize the "template order" (so this is a filoinv invoice template post id)
			//We get the sample order id that is set for this template in it's metadata
			//(in this case, we need doc_id from a constructor, befor initialization hook, so we can get doc_id in a strange way only from url $_GET parameter)
			//(ONLY FOR Siteorigin templates, where return parameter is applied)
			$doc_id = get_post_meta( $customizer_template_post_id, 'filogy_sample_order_id', true );
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $doc_id 4: ' . wsl_vartotext($doc_id));
		*/
		} elseif ( isset($_GET['filo_sample_order_id']) ) {
			//if filo_sample_order_id is set by a link to navigate customizer page, then use this
			$doc_id = wc_clean( $_GET['filo_sample_order_id'] ); //+wc_clean
			wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $doc_id 5: ' . wsl_vartotext($doc_id));  
		
		//} elseif( ! empty( $filo_sample_order_id_option ) ) {
			//if filo_sample_order_id is set, then use it
		//	$doc_id = $filo_sample_order_id_option;
		} else {
			return "Error: unknown document id";
		}

		wsl_log(null, 'class-filo-financial-document.php get_doc_id_invbld $doc_id: ' . wsl_vartotext($doc_id));
		
		return $doc_id;
	}

	/**
	 * generate_document_number
	 */	
	public function generate_document_number( $sequence_id = null, $to_save = false, $is_draft = false, $pseudo_doc_type = null ) {
		global $wpdb;
	
		//if $pseudo_doc_type is set, then use it us post type (and we have to do some thing differently)
		if ( ! empty($pseudo_doc_type) ){
			$order_type = $pseudo_doc_type;
			$is_pseudo = true;
		} else {
			$order_type = $this->get_doc_type();
			if ( is_null($order_type) ) {	
				$order_type = $this->order_type;
			}
			$is_pseudo = false;
		}
	
		wsl_log(null, 'class-filo-financial-document.php generate_document_number 0 $sequence_id: ' .  wsl_vartotext($sequence_id));
		wsl_log(null, 'class-filo-financial-document.php generate_document_number 0 $to_save: ' .  wsl_vartotext($to_save));
		wsl_log(null, 'class-filo-financial-document.php generate_document_number 0 $is_draft: ' .  wsl_vartotext($is_draft));
		
		wsl_log(null, 'class-filo-financial-document.php generate_document_number 0 $order_type: ' .  wsl_vartotext($order_type));
		//wsl_log(null, 'class-filo-financial-document.php generate_document_number 0 $this: ' .  wsl_vartotext($this)); //too big
		wsl_log(null, 'class-filo-financial-document.php generate_document_number 0 $pseudo_doc_type: ' .  wsl_vartotext($pseudo_doc_type));
	
		if ( ! $is_draft ) { //finalized - Generate finalized (not draft) document number	
			if ( ! $sequence_id )
				$sequence_id = $this->get_numbering_sequence_id($pseudo_doc_type);
	
			$sequences = get_option( $order_type . '_sequences');
			
			wsl_log(null, 'class-filo-financial-document.php generate_document_number $sequences: ' . wsl_vartotext($sequences));
			wsl_log(null, 'class-filo-financial-document.php generate_document_number $sequence_id: ' . wsl_vartotext($sequence_id));
			
			if ( empty( $sequences)) {

				$error_message = __( 'There is no sequences are set for this document type (or for the cases).', 'filo_text' );
				wsl_log(null, 'class-filo-financial-document.php generate_document_number ERROR: ' . wsl_vartotext($error_message));
				throw new FILO_Validation_Exception( $error_message, 400 );
				
			}
			
			//is $sequence_id is not set, then get the first (default) sequence_id from settings
			if ( empty( $sequence_id ) )
				$sequence_id = key($sequences);
				
			$sequence = $sequences[ $sequence_id ];
			
			wsl_log(null, 'class-filo-financial-document.php generate_document_number 2 $sequence_id: ' . wsl_vartotext($sequence_id));
			
			
			$sysdate_year = ($sequence['year_handling']=='yes' or $sequence['year_handling']=='yes_restart') ? date("Y", time()) : '';
			wsl_log(null, 'class-filo-financial-document.php generate_document_number $sysdate_year2: ' . wsl_vartotext($sysdate_year));
			
			if ( ! is_numeric($sequence['first_number']) )
				$sequence['first_number'] = 0;
			
			//select next sequential number inside the same sequence_id, prefix, postfix and year
			//$next_sequential_number = $wpdb->get_results
			//if nothing is found, then returns 1
			//meta value mast be cast as number (unsigned), without this 9 is greather than 10
			//in case of pseudo doc type, search for shop_order post type and meta_keys that has the needed pseudo doc type prefix (and doc_status is not important)
			$sql=	 "select ifnull(max(  cast(numbering_sequential_number.meta_value as UNSIGNED)  ) + 1, {$sequence['first_number']}) as next_sequential_number 
					from {$wpdb->prefix}posts posts
					left outer join {$wpdb->prefix}postmeta as doc_status on doc_status.post_id=posts.id and meta_key = '_doc_status'
					left outer join {$wpdb->prefix}postmeta as numbering_sequence_id on numbering_sequence_id.post_id=posts.id and numbering_sequence_id.meta_key = '" . ($is_pseudo ? "_" . $pseudo_doc_type : "") . "_numbering_sequence_id'
					left outer join {$wpdb->prefix}postmeta as numbering_prefix on numbering_prefix.post_id=posts.id and numbering_prefix.meta_key = '" . ($is_pseudo ? "_" . $pseudo_doc_type : "") . "_numbering_prefix'
					left outer join {$wpdb->prefix}postmeta as numbering_suffix on numbering_suffix.post_id=posts.id and numbering_suffix.meta_key = '" . ($is_pseudo ? "_" . $pseudo_doc_type : "") . "_numbering_suffix'
					left outer join {$wpdb->prefix}postmeta as numbering_year on numbering_year.post_id=posts.id and numbering_year.meta_key = '" . ($is_pseudo ? "_" . $pseudo_doc_type : "") . "_numbering_year'
					left outer join {$wpdb->prefix}postmeta as numbering_sequential_number on numbering_sequential_number.post_id=posts.id and numbering_sequential_number.meta_key = '" . ($is_pseudo ? "_" . $pseudo_doc_type : "") . "_numbering_sequential_number'
					where " . ( ! $is_pseudo ? "posts.post_type = '{$order_type}'" : "posts.post_type = 'shop_order'" ) . 					 
						( ( ! $is_pseudo and FILO_TYPE != 'filo_invoice_type' ) ? " and ( ifnull(doc_status.meta_value, '') not in ('', 'draft') or post_type = 'filo_case' ) " : "" ) . "
						and numbering_sequence_id.meta_value = '{$sequence['sequence_id']}'
						and ifnull(numbering_prefix.meta_value, '') = '{$sequence['prefix']}'
						and ifnull(numbering_suffix.meta_value, '') =  '{$sequence['suffix']}'" .
						(($sequence['year_handling']=='yes_restart') ? " and ifnull( numbering_year.meta_value, '') = {$sysdate_year}" : "");
	
				
				
				wsl_log(null, 'class-filo-financial-document.php $sql: ' .  wsl_vartotext(rtrim($sql, '\r\n\t' )));
				
				$next_sequential_number_result = $wpdb->get_results($sql);
				
				$next_sequential_number = $next_sequential_number_result[0]->next_sequential_number;
	
				wsl_log(null, 'class-filo-financial-document.php $next_sequential_number: ' . wsl_vartotext($next_sequential_number));
	
				$document_number = 	$sequence['prefix'] . 
									(!empty($sequence['prefix']) ? $sequence['separator'] : "") .
									str_pad($next_sequential_number, $sequence['padding_length'], $sequence['padding_string'], STR_PAD_LEFT) .
									(!empty($sequence['suffix']) ? $sequence['separator'] : "") . 
									$sequence['suffix'] . 
									(($sequence['year_handling']=='yes' or $sequence['year_handling']=='yes_restart') ? $sequence['separator'] . $sysdate_year : '');
	
				$this->numbering_sequence_id = $sequence['sequence_id'];
				$this->numbering_prefix = $sequence['prefix'];
				$this->numbering_suffix = $sequence['suffix'];
				$this->numbering_year = $sysdate_year;
				$this->numbering_sequential_number = $next_sequential_number;
				$this->document_number = $document_number;
	
				if( $to_save) { //} and empty($this->get_document_number) ) {
					if ( ! $is_pseudo ) { // normal, not pseudo
						update_post_meta( $this->id, '_numbering_sequence_id', $sequence['sequence_id'] );
						update_post_meta( $this->id, '_numbering_prefix', $sequence['prefix'] );
						update_post_meta( $this->id, '_numbering_suffix', $sequence['suffix'] );
						update_post_meta( $this->id, '_numbering_year', $sysdate_year );
						update_post_meta( $this->id, '_numbering_sequential_number', $next_sequential_number );
						update_post_meta( $this->id, '_document_number', $document_number);
					} else { // pseudo
						update_post_meta( $this->id, '_' . $pseudo_doc_type . '_numbering_sequence_id', $sequence['sequence_id'] );
						update_post_meta( $this->id, '_' . $pseudo_doc_type . '_numbering_prefix', $sequence['prefix'] );
						update_post_meta( $this->id, '_' . $pseudo_doc_type . '_numbering_suffix', $sequence['suffix'] );
						update_post_meta( $this->id, '_' . $pseudo_doc_type . '_numbering_year', $sysdate_year );
						update_post_meta( $this->id, '_' . $pseudo_doc_type . '_numbering_sequential_number', $next_sequential_number );
						update_post_meta( $this->id, '_' . $pseudo_doc_type . '_document_number', $document_number);
					}
					
				}
					
		} else { //draft - Generate draft document number

			$document_number = __('Draft', 'filo_text') . '/' . $this->id; 

			$this->document_number = $document_number;

			if( $to_save) { //} and empty($this->get_document_number) ) {
				update_post_meta( $this->id, '_document_number', $document_number);
			}
					
		}

		wsl_log(null, 'class-filo-financial-document.php $document_number: ' . wsl_vartotext($document_number));

		return $document_number;
		
	}	

    /**
     * Decide if all sequences are set
     */
    public static function is_all_sequences_are_set() {
		global $wpdb, $filo_post_types_financial_documents;

		$post_types = $filo_post_types_financial_documents;
		
		$sequence_option_name_list = "'";
		$count_sequence_option_names = 0;
		if ( is_array($post_types) )
		foreach ($post_types as $post_type) {
			$count_sequence_option_names += 1;
			$sequence_option_name_list .= "','" . $post_type . '_sequences';	
		}
		$sequence_option_name_list .= "'";
		
		//wsl_log(null, 'class-filo-financial-document.php is_all_sequences_are_set $count_sequence_option_names: ' . wsl_vartotext($count_sequence_option_names ));
		//wsl_log(null, 'class-filo-financial-document.php is_all_sequences_are_set $sequence_option_name_list: ' . wsl_vartotext($sequence_option_name_list ));
	
		$count_sequence_options = $wpdb->get_var(
			"
			select count(*) 
			from {$wpdb->prefix}options 
			where option_name in ({$sequence_option_name_list})
			and option_value != '';
			"
		);

		//wsl_log(null, 'class-filo-financial-document.php is_all_sequences_are_set $count_sequence_options: ' . wsl_vartotext($count_sequence_options ));
		
		if ( $count_sequence_options == $count_sequence_option_names) { //if number of existing sequence options is less than the required option names, so there is option name to which no option belongs 
			$all_sequences_are_set = true;			
		} else {
			$all_sequences_are_set = false;
		}

		wsl_log(null, 'class-filo-financial-document.php is_all_sequences_are_set $all_sequences_are_set: ' . wsl_vartotext($all_sequences_are_set ));
				
		return $all_sequences_are_set;
	
	}
	//#endif_2ESE
	 
	//public function calc_line_taxes() //THIS FUNCTION IS MOVED TO THE PARENT FILO_Order class
		
	/**
	 * Concatenate document type and subtype (- delimited)
	 *
	 * @param string $document_type
	 * @param string $document_subtype
	 * @return string
	 */
	public function concatenate_doc_type_and_subtype( $document_type, $document_subtype ) {
	
		return $document_type . ($document_subtype == '' ? '' : '-' . $document_subtype);
	
	}

	/**
	 *
	 * refresh actual item open qty
	 * close type can be used when a document should be copy in more roles (e.g: a Before Delivery invoice to payment (normal close_type) and bef_del delivery (bef_del_delivery close type) )
	 * default value is empty string, this means normal close type
	 * (Originaly in FILO admin doc action)
	 */
	static public function get_doc_type_registration_value( $doc_type, $doc_subtype, $registered_data_key ) {
			
		$doc_type_object = get_post_type_object( $doc_type );
		
		//$doc_subtype = $this->get_doc_subtype(); param
		
		//wsl_log(null, 'class-filo-financial-document.php get_doc_type_registration_value $doc_type_object: ' . wsl_vartotext($doc_type_object));
		//wsl_log(null, 'class-filo-financial-document.php get_doc_type_registration_value $doc_subtype: ' . wsl_vartotext($doc_subtype));
		
		//set spec openess types that is to be calculated
		if ( isset($doc_subtype) && $doc_subtype != '' ) { //if it is a subtype doc
		
			$doc_subtype_object = $doc_type_object->subtypes[$doc_subtype];
			
			//if exists the given subtype for our financial document type (subtypes are custom post registration data) 
			if ( isset( $doc_subtype_object ) && is_array( $doc_subtype_object ) ) { //this is the array of name-values
			
				if ( isset( $doc_subtype_object[$registered_data_key] ) )  { //this is a value of a specific data key
					
					return $doc_subtype_object[$registered_data_key];
					
				}
				
			}
			
		} else { //if it is a main doc
			
			if ( isset( $doc_type_object->$registered_data_key ) )
				
				return $doc_type_object->$registered_data_key;
			
		}
		
	}

	
	/**
	 * Evaluate if document is editable according to document state  
	 *
	 */
	public function is_editable( $is_editable = false ) {
		//global $is_filo_settings_ok;
		
		// Just for Filogy Invoice (we use this if condition to prevent that Filogy Invoice mini specific code is executed during normal development)				
		if ( FILO_TYPE == 'filo_invoice_type' ) {
			
			// use WooCommerce standard function (parent class)
			$this->editable = parent::is_editable();
		}		
				
		return apply_filters( 'filo_order_is_editable', $this->editable, $this );
			
	}
	

	
	/**
	 * Make this document valid 
	 */
	public function make_valid() {
		global $wpdb;
		global $is_filo_settings_ok;
		
		//if none all settings are ok, then do nothing 
		if ( ! $is_filo_settings_ok ) {
			return false;
		}
		
		wsl_log(null, 'class-filo-financial-document.php make_valid 0: ' . wsl_vartotext(''));
		//wsl_log(null, 'class-filo-financial-document.php make_valid 0 $this: ' . wsl_vartotext($this)); //big
		
		//$doc_status = $this->get_doc_status();
		
		wsl_log(null, 'class-filo-financial-document.php make_valid 0 $this->get_doc_validated(): ' . wsl_vartotext($this->get_doc_validated()));
		
		$is_made_valid_now = false;
		
		//if the document not valid yet,
		//set it valid and generate a document number
		if ( ! $this->get_doc_validated() ) {

			wsl_log(null, 'class-filo-financial-document.php make_valid 0 _doc_validated true: ' . wsl_vartotext(''));
			
			update_post_meta( $this->id, '_doc_validated', true );

			//Earlier PRO!		
			
			//$this->set_doc_status('open');
			$is_draft = false;
			$this->generate_document_number( null, true, $is_draft );
			
			//it is not used anymore 
			//update_post_meta( $this->id, '_creation_date', date("Y-m-d", time()) ); //default value is sysdate //pstdt
			
			//Earlier PRO!
			
		}

		//Earlier PRO!		
				
	}		

	/**
	 * create_order_custom_tasks after order is created by the customer on the frontend: set open quantities, make the order valid
	 * Called from frontend checkout page, after order is created
	 */
	function create_order_custom_tasks ($order_id, $posted) {
		global $is_filo_settings_ok;
		
		//if none all settings are ok, then do nothing 
		if ( ! $is_filo_settings_ok ) {
			return false;
		}
			
		//save seller address fields (the same way as the backend metaboxes)	
		FILO_Meta_Box_Financial_Document_Head_Data::init_address_fields( false );
		FILO_Meta_Box_Financial_Document_Head_Data::save_seller( $order_id );
			
		wsl_log(null, 'class-filo-financial-document.php create_order_custom_tasks $order_id: ' . wsl_vartotext($order_id));
			
		$the_order = new FILO_FinaDoc_Shop_Order($order_id);
		
		$the_order->make_valid();
	}

	/**
	 * set_filo_technical_user
	 * 
	 * If a document copy is initiated from front-end, current user is empty (id is null) (or has not got sufficient rights for copyinf docs).
	 * We have to apply a special filo_technical_user to create document through Orders API.
	 * If filo_technical_user does not exist then create it.
	 */
	static function set_filo_technical_user() {
			

		$original_current_user = wp_get_current_user();
		//$current_user_changed = false;	
						
		wsl_log(null, 'class-filo-financial-document.php set_filo_technical_user $original_current_user: ' . wsl_vartotext($original_current_user));
		
		//if ( $original_current_user == null or $original_current_user->ID == 0) {
		
			$user_name = 'filo_technical_user';
			$user_id = username_exists( $user_name );
			if ( !$user_id ) {
				$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
				$user_id = wp_create_user( $user_name, $random_password );
				
				wsl_log(null, 'class-filo-financial-document.php create_payment_doc_based_on_order $user_id: ' . wsl_vartotext($user_id));
				
				$user = new WP_User( $user_id );
				
				$user->add_cap( 'shop_manager' );
				
				wsl_log(null, 'class-filo-financial-document.php create_payment_doc_based_on_order add_cap $user: ' . wsl_vartotext($user));				
				
			}
		
			wp_set_current_user( $user_id );
			//$current_user_changed = true;
			
			wsl_log(null, 'class-filo-financial-document.php create_payment_doc_based_on_order 2 $user_id: ' . wsl_vartotext($user_id));			
			
		//}
		
		//we return original user, to the caller calles the function that sets it back after document creation
		return $original_current_user;
		
	}

	/**
	 * reset_original_user_from_technical_user
	 * 
	 * Set original user who was active before set_filo_technical_user
	 */
	static function reset_original_user_from_technical_user( $original_current_user ) {
		
		wp_set_current_user( $original_current_user );
		
		wsl_log(null, 'class-filo-financial-document.php reset_original_user_from_technical_user $original_current_user: ' . wsl_vartotext($original_current_user));
		
	}		

	/**
	 * get_finadoc_list
	 * 
	 * if not detailed then judt id-s will be given back, and filogy specific "order functions" like get_document_number is not needed, which is good when initializing period, where not all of the settings are done, but we have to know if we have any financial document
	 * 
	 */
	public static function get_finadoc_title_list( $doc_types = null, $orderby = 'asc', $item_limit = null, $is_detailed = true ) {
		global $wpdb, $filo_post_types_financial_documents;

		//set default walue as all filo document type
		if ( empty($doc_types) ) {
			$doc_types = $filo_post_types_financial_documents;
		}

		$doc_type_list_txt = wc_clean( "'" . implode( "', '", $doc_types ) . "'" ); //acccid
		$where_doc_types = "and finadoc.post_type in ({$doc_type_list_txt}) ";

		$item_limit_tag = '';
		if ( ! empty($item_limit) ) {
			$item_limit_tag = "limit {$item_limit}";
		}
		
					
		$sql = "
			select finadoc.id from {$wpdb->prefix}posts as finadoc
			where finadoc.post_status <> 'auto-draft'
			{$where_doc_types}
			order by finadoc.id desc
			{$item_limit_tag}
		";

		$finadoc_list = $wpdb->get_col( $sql );
		
		$finadoc_list2 = array();
		if ( is_array($finadoc_list) )
		foreach ($finadoc_list as $finadoc_id) {
			
			if ( $is_detailed ) {
				$finadoc = wc_get_order( $finadoc_id ); //filo_get_order
				
				$document_number = $finadoc->get_document_number();
				$username = $finadoc->get_doc_username( $with_link = false );
				$doc_type_label_short_name = $finadoc->get_doc_type_label_singular_short_name();
				//$finadoc_title = $finadoc->get_doc_title();
				//$finadoc_title = sprintf( _x( '%s', 'Order number by X', 'woocommerce' ), $username ); //MODIFY RaPe
				$finadoc_title = sprintf( _x( '%s by %s (%s)', 'Order number by X (doctype)', 'woocommerce' ), $document_number, $username, $doc_type_label_short_name );
				
				$finadoc_list2[$finadoc_id]	= $finadoc_title;
			} else { //not detailed, just id-s will be returned
				$finadoc_list2[$finadoc_id]	= $finadoc_id;
			}
			 		
		}
		
		//wsl_log(null, 'class-filo-financial-document.php get_finadoc_list $sql: ' . wsl_vartotext( $sql ));
		//wsl_log(null, 'class-filo-financial-document.php get_finadoc_list $finadoc_list: ' . wsl_vartotext( $finadoc_list ));
		//wsl_log(null, 'class-filo-financial-document.php get_finadoc_list $finadoc_list2: ' . wsl_vartotext( $finadoc_list2 ));
		
		return $finadoc_list2;
		
	}
	
}

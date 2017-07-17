<?php
if ( !defined('ABSPATH') ) exit;

/**
 * FILO_Admin_Menus -> extends class-wc-admin-menus.php
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */ 
class FILO_Admin_Meta_Boxes extends FILO_Admin_Meta_Boxes_FW {

	/**
	 * construct
	 */
	public function __construct() {
		global $is_filo_settings_ok;
		wsl_log(null, 'class-filo-admin-meta-boxes.php __construct $is_filo_settings_ok: ' . wsl_vartotext($is_filo_settings_ok));

		add_action( 'add_meta_boxes', array( $this, 'set_meta_boxes' ), 150 );
		
		// We only need user profile metaboxes before settings are not ok, it is it in set_meta_boxes function defined above. The following part is not needed.
		if ( $is_filo_settings_ok ) {
			
			add_action( 'add_custom_page_meta_boxes', array( $this, 'add_custom_page_meta_boxes' ), 140 );
			add_filter( 'hide_custom_page_menu', array( $this, 'hide_custom_page_menu' ), 0 );
			
			//it has to be confrom to remove_action or remove_all_actions line of save_meta_boxes function (otherwise save functions of metaboxes are executed twice)
			//add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );
			//add_action( 'save_post', 'FILO_Admin_Meta_Boxes::save_meta_boxes', 9999, 2 ); //Filogy save metaboxes sould be executed after all other metaboxes are saved (e.g. Filogy save metaboxes call the validation and that the account_and_inventory_postings, and accounting has to be based on the saved data (deleted items was not saved first, and it was posted to the journal))
			add_action( 'save_post', array( $this, 'save_meta_boxes' ), 9999, 2 ); //like static //Filogy save metaboxes sould be executed after all other metaboxes are saved (e.g. Filogy save metaboxes call the validation and that the account_and_inventory_postings, and accounting has to be based on the saved data (deleted items was not saved first, and it was posted to the journal))
	
			//
			// Save Sales Invoice Meta Boxes
			//
			// In order:
			// 		Save the Sales Invoice items
			// 		Save the Sales Invoice totals
			// 		Save the Sales Invoice downloads
			// 		Save Sales Invoice data - also updates status and sends out admin emails if needed. Last to show latest data.
			// 		Save actions - sends out other emails. Last to show latest data.
			//		
	
			//Call FILO action metabox save method for handling copy actions from normal order
			//add_action( 'woocommerce_process_shop_order_meta', 'FILO_Meta_Box_Financial_Document_Actions::save', 55, 2 );
			
			//add_actions save_filo_....._meta for every financial_document type
			//e.g: 	add_action( 'save_wfc_sales_invoice_meta', 'WC_Meta_Box_Order_Items::save', 10, 2 );
			//		add_action( 'save_wfc_sales_invoice_meta', 'FILO_Meta_Box_Financial_Document_Head_Data::save', 40, 2 ); 
			//		add_action( 'save_wfc_sales_invoice_meta', 'FILO_Meta_Box_Financial_Document_Actions::save', 50, 2 );
			
			global $filo_post_types_financial_documents;

			if (isset( $filo_post_types_financial_documents ) && is_array( $filo_post_types_financial_documents ) )	
			foreach ($filo_post_types_financial_documents as $doc_type) {
	
				//wsl_log(null, 'class-filo-admin-meta-boxes.php __construct $doc_type: ' . wsl_vartotext($doc_type));
				
				add_action( 'save_wfc_' . $doc_type . '_meta', 'FILO_Meta_Box_Financial_Document_Head_Data::save', 40, 2 );
	
				// Just for Filogy Invoice (mini) (we use this if condition to prevent that Filogy Invoice mini specific code is executed during normal development)				
				if ( FILO_TYPE == 'filo_invoice_type' and $doc_type == 'shop_order' ) {
				//if ( $doc_type == 'shop_order' ) { //TEST RaPe
					add_action( 'save_wfc_' . $doc_type . '_meta', 'FILO_Meta_Box_Financial_Document_Actions_Mini::save', 70, 2 );				
				}			
			
			}
			
			//This is outside of transaction handling, so it become part of FILO_Account::create method:
			//add_filter( 'wp_insert_post_data' , 'FILO_Meta_Box_Account::modify_data_before_save' , '99', 2 );		
	
			// Error handling (for showing errors from meta boxes on next page load)
			add_action( 'admin_notices', array( $this, 'output_errors' ) );
			//add_action( 'shutdown', array( $this, 'save_errors' ) ); //Save is called from the code what add the error, because double load of pages shutdown more times, the first load saves the error messages, the second load updates the error messages by empty values, so the messages would be deleted! 
		
		}
		
	}

	/*
	 * start_transaction
	 */
	/*public static function start_transaction() {
		global $wpdb;

		$wpdb->query('START TRANSACTION');
		wsl_log(null, 'class-filo-admin-meta-boxes.php start_transaction: ' . wsl_vartotext(''));

	}
	*/
	
	/*
	 * end_transaction
	 * 
	 * End transaction always commits, rollback should be done at the place where error was catched.
	 */
	 /*
	public static function end_transaction() {
		global $wpdb;

		$wpdb->query('COMMIT');
		wsl_log(null, 'class-filo-admin-meta-boxes.php end_transaction COMMIT: ' . wsl_vartotext(''));


		//if there were no errors, then commit, otherwise rollback
		//$errors = maybe_unserialize( get_option( 'filo_meta_box_errors' ) );
		//
		//if ( empty( $errors ) ) {
		//	$wpdb->query('COMMIT');
		//	wsl_log(null, 'class-filo-admin-meta-boxes.php end_transaction COMMIT: ' . wsl_vartotext(''));
		//} else {
		//	$wpdb->query('ROLLBACK');
		//	wsl_log(null, 'class-filo-admin-meta-boxes.php end_transaction ROLLBACK: ' . wsl_vartotext(''));
		//}
	}
	*/
	
	/**
	 * set_meta_boxes
	 */
	public function set_meta_boxes() {
		global $is_filo_settings_ok;
		global $filo_post_types, $filo_post_types_financial_documents, $trans_match_financial_document_type, $post, $filo_pseudo_types_financial_documents;

		wsl_log(null, 'class-filo-admin-meta-boxes.php set_meta_boxes 0: ' . wsl_vartotext( '' ));

		// we only need user profile metaboxes before settings are not ok
		if ( $is_filo_settings_ok ) {
			
			wsl_log(null, 'class-filo-admin-meta-boxes.php set_meta_boxes: ' . wsl_vartotext( '' ));
			wsl_log(null, 'class-filo-admin-meta-boxes.php set_meta_boxes $filo_post_types: ' . wsl_vartotext( $filo_post_types ));
			wsl_log(null, 'class-filo-admin-meta-boxes.php set_meta_boxes $post: ' . wsl_vartotext( $post ));
			
			//REMOVE META BOXES			
			
			//ADD META BOXES
			
			$screen = get_current_screen();
	
			//add_actions_box statements for every financial_document type
			//e.g: 
			//	add_meta_box( 'filo-document-head-data', __( 'Invoice Data', 'woocommerce' ), 'FILO_Meta_Box_Financial_Document_Head_Data::output', $doc_type, 'normal', 'high' );
			//	add_meta_box( 'woocommerce-order-items', __( 'Invoice Items', 'woocommerce' ), 'FILO_Meta_Box_Order_Items::output', $doc_type, 'normal', 'high' ); // this may be MODIFY-ed to decide that own or original order items page is used (e.g. calc_line_taxes button)
			//	add_meta_box( 'woocommerce-order-notes', __( 'Invoice Notes', 'woocommerce' ), 'WC_Meta_Box_Order_Notes::output', $doc_type, 'side', 'default' );
			//	add_meta_box( 'woocommerce-order-actions', __( 'Sales Invoice Actions', 'woocommerce' ), 'FILO_Meta_Box_Financial_Document_Actions::output', $doc_type, 'side', 'high' );
			//	add_meta_box( 'filo-seller', __( 'Seller Data', 'filo_text' ), 'FILO_Meta_Box_Seller::output', $doc_type, 'side', 'high' );
			
			if (isset( $filo_post_types_financial_documents ) && is_array( $filo_post_types_financial_documents ) )
			foreach ($filo_post_types_financial_documents as $doc_type) { //e.g: filo_sa_invoice
			
				wsl_log(null, 'class-filo-admin-meta-boxes.php add_meta_boxes $doc_type: ' . wsl_vartotext( $doc_type ));
	
				$post_type_obj = get_post_type_object( $doc_type );
			
				wsl_log(null, 'class-filo-admin-meta-boxes.php add_meta_boxes $post_type_obj: ' . wsl_vartotext( $post_type_obj ));
				
				//$doc_type_name =$post_type_obj->labels->singular_name;
				
				if ( isset($post_type_obj->add_filo_meta_boxes) ) {
					$add_filo_meta_boxes = $post_type_obj->add_filo_meta_boxes;
				}
				
				if ( ! isset($add_filo_meta_boxes) or $add_filo_meta_boxes === null) {
					$add_filo_meta_boxes = true; //default value is true (if it is not given)
				}
	
				//wsl_log(null, 'class-filo-admin-meta-boxes.php add_meta_boxes $doc_type_name: ' . wsl_vartotext( $doc_type_name ));
				wsl_log(null, 'class-filo-admin-meta-boxes.php add_meta_boxes $add_filo_meta_boxes: ' . wsl_vartotext( $add_filo_meta_boxes ));
	
				if ( $add_filo_meta_boxes ) {
	
					add_meta_box( 'filo-seller', __( 'Seller Data', 'filo_text' ), 'FILO_Meta_Box_Seller::output', $doc_type, 'side', 'high' );
	
	
					//wsl_log(null, 'class-filo-admin-meta-boxes.php add_meta_boxes $post: ' . wsl_vartotext( $post ));
					
					$has_valid_pseudo_document = false;
					
					if ( isset($post) and $post->post_type == 'shop_order' ) {
					//if ( true ) {	
					
						// wc_get_order is allowed to execute only for order or other financial documents, but not for pages, products and other post types else the following error is raised: (WC3INVORD)
						// 		PHP Fatal error:  Uncaught exception 'Exception' with message 'Invalid order.' in D:\wamp\www\demo1\wp-content\plugins\woocommerce\includes\data-stores\abstract-wc-order-data-store-cpt.php:95
						$finadoc = wc_get_order( $post->ID ); //filo_get_order
										
						//check if the order has any valid pseudo document
						if ( ! empty($finadoc)) {
							
							if ( isset($filo_pseudo_types_financial_documents) and is_array($filo_pseudo_types_financial_documents) )
							foreach ($filo_pseudo_types_financial_documents as $pseudo_doc_type) {
								//wsl_log(null, 'class-filo-admin-meta-boxes.php add_meta_boxes set_meta_boxes $finadoc: ' . wsl_vartotext( $finadoc ));
								$is_pseudo_doc_valid = $finadoc->is_pseudo_doc_valid( $pseudo_doc_type );
								
								if ($is_pseudo_doc_valid) {
									$has_valid_pseudo_document = true;
								}
							}
						}
					}
	
					// Just for Filogy Invoice (mini), or if an earlier document made using Filogy Invoice (mini) but Filogy Mini is upgraded to a higher Filogy component (we use this if condition to prevent that Filogy Invoice mini specific code is executed during normal development)				
					if ( ( FILO_TYPE == 'filo_invoice_type' or $has_valid_pseudo_document ) and $doc_type == 'shop_order' ) {
					//if ( $doc_type == 'shop_order' ) { //TEST RaPe
						add_meta_box( 'class-filo-meta-box-financial-document-actions-mini', __( 'Document Actions', 'filo_text' ), 'FILO_Meta_Box_Financial_Document_Actions_Mini::output', $doc_type, 'side', 'high' );
					}
								
				}
	
			}
			
		}

		wsl_log(null, 'class-filo-admin-meta-boxes.php set_meta_boxes 6: ' . wsl_vartotext( '' ));

		//User - Partner Metaboxes
		add_meta_box( 'filo-partner-save-button', __( 'Save Partner', 'filo_text' ), 'FILO_Meta_Box_Partner_Save_Button::output', 'user-edit', 'side', 'high' );
		add_meta_box( 'filo-wc-show_user_profile', __( 'Partner Addresses', 'filo_text' ), 'FILO_Admin_Partner::output_wc_add_customer_meta_fields_metabox', 'user-edit', 'normal', 'high' );

	}

	
	/**
	 * Hide Custom Page Menus
	 */
	public function hide_custom_page_menu($hide_menu_array) { 
		
		return $hide_menu_array;
	}
	
	/**
	 * Add Custom Page Meta Boxes
	 */
	public function add_custom_page_meta_boxes($current_screen_id) {
		
		wsl_log(null, 'class-filo-admin-meta-boxes.php add_custom_page_meta_boxes $current_screen_id: ' . wsl_vartotext( $current_screen_id ));
		
	}
	
	/**
	 * save_meta_boxes
	 */
	public function save_meta_boxes( $post_id, $post ) {
		global $wpdb, $filo_post_types;
		
		//remove_action( 'save_post', 'FILO_Admin_Meta_Boxes::save_meta_boxes', 9999);
		remove_all_actions( 'save_post', 9999);

		$saveable = filo_check_metabox_saving_conditions( $post_id, $post );
		
		if ( ! $saveable ) {
			return;
		}

		wsl_log(null, 'class-filo-admin-meta-boxes.php save_meta_boxes START TRANSACTION: ' . wsl_vartotext(''));
		$wpdb->query('START TRANSACTION');
		
		wsl_log(null, 'class-filo-admin-meta-boxes.php $filo_post_types: ' . wsl_vartotext( $filo_post_types ));
		
		//call an action named save_wfc_..._meta where ... is the post type name
		//this is handle post types registered in $filo_post_types array
		if ( in_array( $post->post_type, $filo_post_types ) ) {
			do_action( 'save_wfc_' . $post->post_type . '_meta', $post_id, $post );
		}

		wsl_log(null, 'class-filo-admin-meta-boxes.php after metabox saves WC_Admin_Meta_Boxes::$meta_box_errors: ' . wsl_vartotext( WC_Admin_Meta_Boxes::$meta_box_errors ));
		wsl_log(null, 'class-filo-admin-meta-boxes.php after metabox saves FILO_Admin_Meta_Boxes::$meta_box_errors: ' . wsl_vartotext( FILO_Admin_Meta_Boxes::$meta_box_errors ));
		
		/*		
		//GET POSTMETAS FOR TESTING:
		$sql = $wpdb->prepare( "
			select * from {$wpdb->prefix}postmeta where post_id = %d
			"
			,$post_id 
		);
		$results = $wpdb->get_results($sql);
		wsl_log(null, 'class-filo-admin-meta-boxes.php METAS BEFORE TRANSACTION CLOSE $results: ' . wsl_vartotext( $results ));
		*/
		
		//Metabox save error handling
		//In the save functions of metaboxes, errors should be catched, and use WC_Admin_Meta_Boxes::add_error and FILO_Admin_Meta_Boxes::add_error functions (or the error message can be added directly without try-catch)
		//If no error has been added, then we can COMMIT, else ROLLBACK.
		//(The added errors will be displayed later.)  
		
		if ( empty(WC_Admin_Meta_Boxes::$meta_box_errors) and empty(FILO_Admin_Meta_Boxes::$meta_box_errors) ) {
			
			$wpdb->query('COMMIT');
			wsl_log(null, 'class-filo-admin-meta-boxes.php save_meta_boxes COMMIT: ' . wsl_vartotext(''));
			
			global $create_result;
			if ( isset($create_result['warning_messages']) && is_array($create_result['warning_messages']) && !empty($create_result['warning_messages']) )
			foreach ( $create_result['warning_messages'] as $warning_message ) {

				WC_Admin_Meta_Boxes::add_error( __( 'Account saving WARNING', 'filo_text' ) . ': ' . $warning_message ); //RaPe +F	
					
			}
			
			
		} else {
			
			$wpdb->query('ROLLBACK');
			wsl_log(null, 'class-filo-admin-meta-boxes.php save_meta_boxes ROLLBACK WC_Admin_Meta_Boxes::$meta_box_errors: ' . wsl_vartotext(WC_Admin_Meta_Boxes::$meta_box_errors));
			
		} 
		
		/*
		//GET POSTMETAS FOR TESTING:
		$sql = $wpdb->prepare( "
			select * from {$wpdb->prefix}postmeta where post_id = %d
			"
			,$post_id 
		);
		$results = $wpdb->get_results($sql);
		wsl_log(null, 'class-filo-admin-meta-boxes.php METAS AFTER TRANSACTION CLOSE $results: ' . wsl_vartotext( $results ));
		*/
		
		FILO_Admin_Meta_Boxes::save_errors(); //moved to outside of transaction to be not rollbacked in case of rollback

		//add_action( 'save_post', 'FILO_Admin_Meta_Boxes::save_meta_boxes', 9999, 2 );

	}

}

new FILO_Admin_Meta_Boxes();

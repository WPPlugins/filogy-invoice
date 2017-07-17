<?php
if ( !defined('ABSPATH') ) exit;

/**
 * Financial_Document_Actions_Mini Meta Box
 * 
 * for pseudo document hedling(sales delivery note and sales invoice)
 *
 * @package     Filogy/Admin/Metabox
 * @subpackage 	Financials
 * @category    Admin/Metabox
 */
class FILO_Meta_Box_Financial_Document_Actions_Mini extends FILO_Metabox {

	protected static $form_fields;
	protected static $sequence_options;

	/**
	 * output
	 */
	public static function output( $post, $business_object = null, $is_wrappers = true ) {

		parent::output (
			$post, //$post
			null, //$business_object
			false //$is_wrappers
		);

		//this confirmation script is only needed if modification of a validated document is not allowed
		$filo_enable_modification_validated_pseudo_doc = get_option('filo_enable_modification_validated_pseudo_doc');
		if ( $filo_enable_modification_validated_pseudo_doc != 'yes' ) {
			?>
			<script type="text/javascript" id="filo-order-mini-pseudo-action-scripts">
				( function( $ ) {
					$(document).ready(function(){
			
					
						$( '.button.save_pseudo_doc_data' ).on( 'click', function(e){
							if ( ! confirm( 'Are you sure that you wish to validate this document? The document cannot be modified furthermore and this action cannot be undone.' ) ) {
								e.preventDefault();      
							}			
							return;
						})
					
					});
				} )( jQuery );		
			</script>
			<?php
		}
		
	}

	/**
	 * init_form_fields
	 */
	public static function init_form_fields( $business_object, $post = null ) {

		$order = wc_get_order( $post );
			
		wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php $order: ' . wsl_vartotext($order));
		
		global $filo_post_types_financial_documents, $filo_pseudo_types_financial_documents;
		
		$base_priority = 0;
		self::$form_fields = array();
		
		$is_enabled_validated_doc_modification = false;
		$filo_enable_modification_validated_pseudo_doc = get_option('filo_enable_modification_validated_pseudo_doc');
		if ( isset($filo_enable_modification_validated_pseudo_doc) and $filo_enable_modification_validated_pseudo_doc == 'yes' ) {
			$is_enabled_validated_doc_modification = true;
		}
		
		//loop for pseudo doc types that should be generated from order as pdf (e.g dlivery not, invoice)
		if(isset($filo_pseudo_types_financial_documents) and is_array($filo_pseudo_types_financial_documents))
		foreach ($filo_pseudo_types_financial_documents as $doc_type) {

			//shop order is not pseudo document			
			if ( $doc_type != 'shop_order') {
				
				$pseudo_doc_type = $doc_type;
				wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php init_form_fields $pseudo_doc_type: ' . wsl_vartotext($pseudo_doc_type));

				$is_pseudo_doc_valid = $order->is_pseudo_doc_valid( $pseudo_doc_type );
				
				wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php init_form_fields $is_pseudo_doc_valid: ' . wsl_vartotext($is_pseudo_doc_valid));
				
				//generate a custom_attributes array that contains read_only or empty 
				//according to $is_pseudo_doc_valid value and $is_enabled_validated_doc_modification
				//if a document validated and modificateion of validated doc is NOT enabled, then set readonly mode  
				$if_readonly_custom_attributes = array();
				if ( $is_pseudo_doc_valid and ! $is_enabled_validated_doc_modification ) {
					$if_readonly_custom_attributes['readonly'] = '';
				}

				wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php init_form_fields FILO_IS_FREE: ' . wsl_vartotext(FILO_IS_FREE));

				$if_readonly_comment_custom_attributes = array();
				$comment_title_postfix = null;				

				$pseudo_doc_type_reg_data = FILO_Financial_Document::get_doc_type_registration_data_static( $pseudo_doc_type, $doc_subtype = null );
				$pseudo_doc_type_name = $pseudo_doc_type_reg_data['labels']->singular_short_name;
				
				//wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php $pseudo_doc_type_reg_data: ' . wsl_vartotext($pseudo_doc_type_reg_data));
				//wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php $pseudo_doc_type_name: ' . wsl_vartotext($pseudo_doc_type_name));
				 
				//*** numbering_sequence_option ***
				//create $sequence_options array, keys and values contains the sequence names
				//wsl_log(null, 'class-filo-meta-box-sales-invoice-data.php init_form_fields $order->get_doc_type() . _sequences: ' . wsl_vartotext($order->post->post_type . '_sequences'));
				$sequences = get_option($pseudo_doc_type . '_sequences'); //e.g: filo_sa_invoice_sequences
				//wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php init_form_fields $sequences: ' . wsl_vartotext($sequences));
				
				self::$sequence_options = array();
				if (isset( $sequences ) && is_array( $sequences ) ) {
				
					foreach ($sequences as $sequence) {
						//self::$sequence_options[$sequence['sequence_id']] = $sequence['sequence_name'] . ' [ ' . __('next','filo_text') . ': ' . $order->generate_document_number($sequence['sequence_id'], false) . ' ]';
						self::$sequence_options[$sequence['sequence_id']] = $sequence['sequence_name'];
					}		
				
				}
				//wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php init_form_fields self::$sequence_options: ' . wsl_vartotext(self::$sequence_options));
		
				//*** completion_date ***
				$compdate = $order->get_completion_date($pseudo_doc_type);
				if ( $compdate == '' ) { 
					//$compdate = date("Y-m-d", time()); //default value is sysdate
					$compdate = $order->default_completion_date();
				}
		
				//*** due_date ***
				$duedate = $order->get_due_date($pseudo_doc_type);
				if ($duedate == '') { 
					//$duedate = date("Y-m-d", time() + 10 * 24*60*60); //default value is sysdate + x days
					$duedate = $order->default_due_date();
				}

				//*** creation_date ***
				$creation_date = $order->get_creation_date($pseudo_doc_type);
				if ($creation_date == '') { 
					$creation_date = date("Y-m-d", time()); //default value is sysdate;
				}
		
				$numbering_sequence_id = $order->get_numbering_sequence_id($pseudo_doc_type);
		
				
				/*
				//if order is editable now show options or already has saved sequence id, then also show options to display the selected option
				if ( $order->is_editable() or ! empty( $numbering_sequence_id) ) {
					$numbering_sequence_options	= self::$sequence_options;
				} else { //otherwise this may be an old order, that is not editable and does not have sequence number, then we give an empty list, for not to display the first existing option (we need an empty field) 
					$numbering_sequence_options = array();
				}
				*/	
		
				//woocommerce_wp_text_input array (not only text): wc-meta-box-functions.php
				//details in filo-meta-box-functions.php filo_output_field function
				//in case of new field,  it should also be added to the save method
				$form_fields_1 = array( 
					array(
						'type' 			=> 'select',
						'id'			=> $pseudo_doc_type . '_numbering_sequence_id',				
						'label'			=> __( 'Sequence Name', 'filo_text' ) . ':',
						'description'	=> (( empty(self::$sequence_options) ) ? __('THIS LIST IS EMPTY! Add items before use this function.', 'filo_text') . ' ' : __('Determines sequence from which invoice number is generated.', 'filo_text')) .  
											sprintf(__( 'Go to WooCommerce Settings / Documents / %s to create or edit sequences, displayed on this list.', 'filo_text' ), $pseudo_doc_type_name),
						'desc_tip'		=>  true,
						'desc_tip_in_label' => true,
						'value'		  	=> $order->get_numbering_sequence_id($pseudo_doc_type),
						//'class'    		=> 'chosen_select', //set advanced select
						'options'  		=> self::$sequence_options, //array of keys and values
						'form_field_class' => 'form-field form-field-wide filo-sequence-field',
						//'is_wrapper'    => false,
						'custom_attributes' => array_merge( $if_readonly_custom_attributes, 
							array(
							//	'style'			=> 'min-width:300px;',	
							//	'required'		=> '',			
							)
						),
						'field_order'	=> $base_priority + 5,
					), 

					array(
						'type'			=> 'text', //'date_picker',
						'id' 			=> $pseudo_doc_type . '_document_number', 				
						'label'			=> sprintf( __( '%s Number', 'filo_text' ), $pseudo_doc_type_name),				 
						//'placeholder' => _x( 'YYYY-MM-DD', 'placeholder', 'woocommerce' ),
						'description'	=> __( 'Number of the validated document.', 'filo_text' ), 
						'desc_tip' 		=>  true,
						'desc_tip_in_label' => true,
						//'value'			=> date_i18n( 'Y-m-d @ h:m', strtotime( $order->post->post_date ) ),
						'value'			=> $order->get_document_number($pseudo_doc_type),
						'class'			=> 'readonly_date filo_doc_date', 
						'form_field_class' => 'form-field form-field-wide',
						'custom_attributes' => array(
							'readonly' 		=> '',
						//	'style'			=> 'max-width:150px;',
						//	//'pattern' 		=> '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
						),
						'field_order'	=> $base_priority + 8, 
					),	
					array(
						'type'			=> 'text', //'date_picker',
						'id' 			=> $pseudo_doc_type . '_creation_date', 				
						'label'			=> __( 'Creation date', 'filo_text' ) . ':',				 
						//'placeholder' => _x( 'YYYY-MM-DD', 'placeholder', 'woocommerce' ),
						'description'	=> __( 'Creation date of the validated document.', 'filo_text' ), 
						'desc_tip' 		=>  true,
						'desc_tip_in_label' => true,
						//'value'			=> date_i18n( 'Y-m-d @ h:m', strtotime( $order->post->post_date ) ),
						'value'			=> $creation_date,
						'class'			=> 'readonly_date filo_doc_date', 
						'form_field_class' => 'form-field form-field-wide',
						'custom_attributes' => array(
							'readonly' 		=> '',
						//	'style'			=> 'max-width:150px;',
						//	//'pattern' 		=> '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
						),
						'field_order'	=> $base_priority + 10, 
					),	
					/*array(
						'type'     => 'to_save',
						'id'			=> '_numbering_sequence_id',
						'field_order'	=> 20,				
					),*/
								
					/*
					array(
						'type' 			=> 'hidden',
						'id'			=> $pseudo_doc_type . '_numbering_prefix',				
						'value'		  	=> $order->get_numbering_prefix($pseudo_doc_type),
						'field_order'	=> $base_priority + 30,
					),
					array(
						'type' 			=> 'hidden',
						'id'			=> $pseudo_doc_type . '_numbering_suffix',				
						'value'		  	=> $order->get_numbering_suffix($pseudo_doc_type),
						'field_order'	=> $base_priority + 40,
					),
					array(
						'type' 			=> 'hidden',
						'id'			=> $pseudo_doc_type . '_numbering_year',				
						'value'		  	=> $order->get_numbering_year($pseudo_doc_type),
						'field_order'	=> $base_priority + 50,
					),
					*/			
					
							
				);
				
				$form_fields_2 = array();
				if ( $pseudo_doc_type == 'filo_sa_invoice' ) {
					$form_fields_2 = array( 
						array( 
							'type'    		=> 'text', //'date_picker',
							'id'			=> $pseudo_doc_type . '_completion_date',				
							'label'			=> __( 'Completion date', 'filo_text' ) . ':',
							'description'	=> __( 'Completion date of this document.', 'filo_text' ),
							'desc_tip' 		=>  true,		
							'desc_tip_in_label' => true,		
							'value'			=> $compdate,
							'class'			=> ($is_pseudo_doc_valid ? 'readonly_date filo_doc_date ' : 'date-picker filo_doc_date '),
							'form_field_class' => 'form-field form-field-wide',
							'custom_attributes' => array_merge( $if_readonly_custom_attributes, 
								array(
									//	'style'			=> 'max-width:150px;',				
									//	'maxlength' => '10',
									//	'pattern'   => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
									//	'required'		=> '',			
								)
							),
						 	'field_order'	=> $base_priority + 12,
						),
						array(
							'type'    		=> 'text', //'date_picker',
							'id'			=> $pseudo_doc_type . '_due_date',				
							'label'			=> __( 'Due date', 'filo_text' ) . ':',
							'description'	=> __( 'Due Date of this Invoice.', 'filo_text' ),
							'desc_tip' 		=>  true,
							'desc_tip_in_label' => true,								
							'value'			=> $duedate,
							'class'			=> ($is_pseudo_doc_valid ? 'readonly_date filo_doc_date ' : 'date-picker filo_doc_date '),
						 	'form_field_class' => 'form-field form-field-wide',
							'custom_attributes' => array_merge( $if_readonly_custom_attributes, 
								array(
									//	'style'			=> 'max-width:150px;',				
									//	'maxlength' => '10',
									//	'pattern'   => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
									//	'required'		=> '',			
								)
							),
						 	'field_order'	=> $base_priority + 14,
						),		
					);			 	
				}
				
				$form_fields_3 = array();						 	
				
				$form_fields_1 = array_merge($form_fields_1, $form_fields_2, $form_fields_3);
				
				//the following view uses the previously set $form_fields_1 variable, and the resulted html code is necessary for the following $form_fields_2 field
				ob_start();
				include('views/html-order-mini-pseudo-actions.php');
				$mini_pseudo_actions_html = ob_get_clean();

				$form_fields_4 = array(
					array(
						'type'				=> 'html_code',
						'html_content'		=> $mini_pseudo_actions_html,				
						//'id'				=> '_account_save',				
						//'label'				=> __( 'Document Status', 'filo_text' ) . ':',
						'field_order'	=> $base_priority + 60,
					),
				);
			
				$base_priority += 100;
				
				$form_fields_1 = array_merge($form_fields_1, $form_fields_4);
			
				wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php $form_fields_1: ' . wsl_vartotext($form_fields_1));
			
				$form_fields_1 = apply_filters('filo_meta_box_' . $pseudo_doc_type . '_data_fields-mini', $form_fields_1 ); //e.g: filo_meta_box_sales_invoice_data_fields
			
				self::$form_fields = array_merge(self::$form_fields, $form_fields_1 );
				
			}
			
		}
		
		/*
		$base_priority += 100;
		$form_fields_3 = array(
			array(
				'type' 			=> 'hidden',
				'id'			=> '_has_pseudo_docs',				
				'value'		  	=> 'yes',
				'field_order'	=> $base_priority + 10,
			),
		);
		self::$form_fields = array_merge(self::$form_fields, $form_fields_3);
		*/
		
		self::$form_fields = wsl_array_column_sort(self::$form_fields, "field_order", SORT_ASC); //sort by "field_order" field 		
		
		wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php self::$form_fields: ' . wsl_vartotext(self::$form_fields));
		
		return self::$form_fields;
	}


	/**
	 * save
	 */
	public static function save( $post_id, $post, $business_object = null ) {
		global $filo_post_types_financial_documents;
			
		////wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php save $_POST: ' . wsl_vartotext($_POST));
		
		try {		
			$order = wc_get_order( $post_id );
			
			$form_field_ids = array('_numbering_sequence_id', '_creation_date', '_completion_date', '_due_date', '_numbering_prefix', '_numbering_suffix', '_numbering_year', '_pseudo_doc_comment');		
			
			$filo_enable_modification_validated_pseudo_doc = get_option('filo_enable_modification_validated_pseudo_doc');
			$is_enabled_validated_doc_modification = false;
			if ( isset($filo_enable_modification_validated_pseudo_doc) and $filo_enable_modification_validated_pseudo_doc == 'yes' ) {
				$is_enabled_validated_doc_modification = true;
			}
			
			
			//examine whether any of the pseudo doc save button is pressed		
			//loop for pseudo doc types for this reason
			if(isset($filo_post_types_financial_documents) and is_array($filo_post_types_financial_documents))
			foreach ($filo_post_types_financial_documents as $doc_type) {
	
				$pseudo_doc_type = $doc_type;
				
				//if the save submit button exists in post parameters (thus the appropriate pseudo doc save button was pressed)
				if ( isset($_POST['save_pseudo_doc_' . $pseudo_doc_type]) ) { //e.g. $_POST['save_pseudo_doc_filo_sa_deliv_note']
	
					//we can save it, if it has not saved yet, thus we examine if creation date was set
					
					/*$saved_creation_date = get_post_meta( $post_id, '_' . $pseudo_doc_type . '_creation_date', true );
					wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php save $saved_creation_date: ' . wsl_vartotext($saved_creation_date));*/
					//if ( empty($saved_creation_date) ) {
					//if ( true ) {
					wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php save $order->is_pseudo_doc_valid( $pseudo_doc_type ): ' . wsl_vartotext($order->is_pseudo_doc_valid( $pseudo_doc_type )));
					
					// we save the validation only if it is not valid yet (creation date is empty)
					// or modification after validation is enabled
					if ( ! $order->is_pseudo_doc_valid( $pseudo_doc_type ) or $is_enabled_validated_doc_modification ) {
						
						// we have to save this pseudo doc type data, if it has not saved yet
						// go through on every saveable fields and save the value
				
						foreach ( $form_field_ids as $form_field_id ) {
							
							//add pseudo doc prefix to the field key
							//saved fileld name id equal to the field id
							$form_field_save = $form_field_id = $pseudo_doc_type . $form_field_id;
							
							if ( isset( $_POST[ $form_field_id ] ) ) {
								update_post_meta( $post_id, '_' . $form_field_save, wc_clean( $_POST[ $form_field_id ] ) );
								
								wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php save $post_id: ' . wsl_vartotext($post_id));
								wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php save postmeta key: ' . wsl_vartotext('_' . $form_field_save));
								wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php save postmeta value: ' . wsl_vartotext(wc_clean( $_POST[ $form_field_id ] )));
							}
							
						}
						
						//generate and save document number for pseudo document
						$order->generate_document_number( 
											$sequence_id = isset($_POST[$pseudo_doc_type . '_numbering_sequence_id']) ? wc_clean( $_POST[$pseudo_doc_type . '_numbering_sequence_id'] ) : null, //+wc_clean 
											$to_save = true, 
											$is_draft = false, 
											$pseudo_doc_type
	 					);
						
						//it is independent on pseudo doc types, if one doc pseudo doc type is set, then save it
						update_post_meta( $post_id, '_has_pseudo_docs', 'yes' );
					
					}
				
				}
						
			}

		} catch (FILO_Validation_Exception $ve) {
			
			//catch these:  throw new FILO_Validation_Exception( $message, $code );
			//and add FILO error message
			//class-filo-admin-meta-boxes.php-> end_transaction will be rollback it if there were error
			
			//WC_Admin_Meta_Boxes::add_error( __( 'Financial document saving ERROR', 'filo_text' ) . ' - ' . $ve->getMessage() ); //RaPe +F
			FILO_Admin_Meta_Boxes::add_error( __( 'Financial document saving ERROR', 'filo_text' ) . ' - ' . $ve->getMessage() ); //RaPe +F
			

			//$wpdb->query('ROLLBACK'); 
			wsl_log(null, 'class-filo-meta-box-financial-document-actions-mini.php: ' . wsl_vartotext(''));
			
		}

		
	}

}

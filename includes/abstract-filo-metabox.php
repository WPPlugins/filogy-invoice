<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Abstract Metabox
 *
 * Abstract metabox output and save
 * In output and save methodes of concrete classes init_form_fields function should be implemented,
 * where metabox data field is specified according to the possibilities of filo-meta-box-functions.php filo_output_field function.
 * In the background a business class can be create to handle getter and setter and other methodes. This can be instantiate in 
 * the output and save functions of concrete class, and pass these business objects to the output and save abstract functions, 
 * who pass them to the concrete init_form_fields function to get values to be set into fields displayed. 
 *
 * @class       FILO_Metabox
 * @package     Filogy/Abstracts
 * @subpackage 	Financials
 * @category    Abstract Class
 * @abstract
 */
 //abstract class FILO_Metabox {
 abstract class FILO_Metabox {	

	/**
	 * Initialize form fields of metabox
	 * 
	 * @abstract
	 * @param object $business_object if a business class is needed for getting fild values, it can be bassed in this parameter
	 */
	public static function init_form_fields( $business_object, $post = null ) {
		//like abstract
	}
	//abstract public static function init_form_fields( $business_object ); //Strict standards

	/**
	 * output
	 * 
	 * @param object $post
	 * @param object $business_object
	 * @param boolean $is_wrappers 
	 * @return void
	 */
	public static function output( $post, $business_object = null, $is_wrappers = true ) {

		wsl_log(null, 'abstract-filo-metabox.php output $post: ' . wsl_vartotext($post));
		wsl_log(null, 'abstract-filo-metabox.php output $business_object: ' . wsl_vartotext($business_object));

		//$form_fields = static::init_form_fields( $business_object, $post = null );
		$form_fields = static::init_form_fields( $business_object, $post );
		
		wsl_log(null, 'abstract-filo-metabox.php output $form_fields: ' . wsl_vartotext($form_fields));

		wp_nonce_field( 'woocommerce_save_data', 'woocommerce_meta_nonce' );
		
		if ( isset($post) ) {
			$post_type_obj = get_post_type_object( $post->post_type );
			$doc_type_name = $post_type_obj->labels->singular_name;
		}
		$readonly = false;
		
		wsl_log(null, 'abstract-filo-metabox.php output $is_wrappers: ' . wsl_vartotext($is_wrappers));
		
		if ($is_wrappers) {
			?>
			<!--<style type="text/css">
				#post-body-content, #titlediv, #major-publishing-actions, #minor-publishing-actions, #visibility, #submitdiv { display:none }
			</style>-->
			
			<style type="text/css">
				#post-body-content {
					margin-bottom: 0px;
				}
			</style>
			
			<div class="panel-wrap woocommerce">
				<!--<input name="post_title" type="hidden" value="<?php echo empty( $post->post_title ) ? $doc_type_name : esc_attr( $post->post_title ); ?>" />-->
				<div id="order_data" class="panel">
					<h2 class="filo_metabox_inner_title"><?php echo apply_filters('filo_metabox_inner_title_' . $post->post_type, empty( $post->post_title ) ? $doc_type_name : esc_attr( $post->post_title ) ); ?></h2>
					
					<?php
					}
						
						//Display fields that defined in init_form_fields()
						filo_output_fields( $form_fields, $readonly );
						
					if ($is_wrappers) {						
					?>
	
				</div>
			</div>
			<?php
		}
	}

	/**
	 * populate
	 * 
	 * @param number $post_id
	 * @param object $post
	 * @param object $business_object
	 * @return object$business_object
	 */
	public static function populate( $post_id, $post = null, $business_object = null ) {
		global $wpdb;

		if ( isset( $business_object ) ) {

			//$form_fields = static::init_form_fields( $business_object, $post = null );
			$form_fields = static::init_form_fields( $business_object, $post );
	
			//wsl_log(null, 'abstract-filo-metabox.php save $post_id: ' . wsl_vartotext($post_id));
			//wsl_log(null, 'abstract-filo-metabox.php save $business_object: ' . wsl_vartotext($business_object));
			//wsl_log(null, 'abstract-filo-metabox.php save $form_fields: ' . wsl_vartotext($form_fields));
			wsl_log(null, 'abstract-filo-metabox.php save $_POST: ' . wsl_vartotext($_POST));
		
			//Save fields that defined in init_form_fields()
			if ( isset($form_fields) and is_array($form_fields) ) 
			foreach ($form_fields as $form_field) {
				
				if ($form_field['type'] != 'html_code') {
	
					//if $form_field['save'] is given, then it was saved on this name, otherwise name was the field id
					$form_field_id = $form_field['id']; 
					$form_field_save = (isset($form_field['save']) and $form_field['save']) ?  $form_field['save'] : $form_field['id']; //if save is not given then saved fileld name id equal to the id 
					////wsl_log(null, 'abstract-filo-metabox.php $form_field[save]: ' . wsl_vartotext($form_field['save']));			
					wsl_log(null, 'abstract-filo-metabox.php $form_field_id / $form_field_save: ' . wsl_vartotext($form_field_id) . '; ' . wsl_vartotext($form_field_save));			
					
					//the object attribute key is $form_field['id'], the value of this object attribute is the meta that we got 
					$business_object->$form_field['id'] = get_post_meta( $post_id, '_' . $form_field_save, true );
					
				}
				
			}
	
			wsl_log(null, 'abstract-filo-metabox.php populate $business_object: ' . wsl_vartotext($business_object));
		}

		return $business_object;
	
	}

	/**
	 * save
	 * 
	 * @param number $post_id 
	 * @param object $post
	 * @param object $business_object
	 * @return void
	 */
	public static function save( $post_id, $post, $business_object = null ) {
		global $wpdb;

		//$form_fields = static::init_form_fields( $business_object, $post = null );
		$form_fields = static::init_form_fields( $business_object, $post );

		wsl_log(null, 'abstract-filo-metabox.php save $post_id: ' . wsl_vartotext($post_id));
		wsl_log(null, 'abstract-filo-metabox.php save $business_object: ' . wsl_vartotext($business_object));
		wsl_log(null, 'abstract-filo-metabox.php save $form_fields: ' . wsl_vartotext($form_fields));
		wsl_log(null, 'abstract-filo-metabox.php save $_POST: ' . wsl_vartotext($_POST));
	
		//Save fields that defined in init_form_fields() 
		if ( isset($form_fields) and is_array($form_fields) )
		foreach ($form_fields as $form_field) {
			
			if ($form_field['type'] != 'html_code') {

				//if $form_field['save'] is given, then save with this name, otherwise save name is the field id
				$form_field_id = $form_field['id']; 
				$form_field_save = (isset($form_field['save']) and $form_field['save']) ?  $form_field['save'] : $form_field['id']; //if save is not given then saved fileld name id equal to the id 
				
				////wsl_log(null, 'abstract-filo-metabox.php $form_field[save]: ' . wsl_vartotext($form_field['save']));			
				wsl_log(null, 'abstract-filo-metabox.php $form_field_id / $form_field_save: ' . wsl_vartotext($form_field_id) . '; ' . wsl_vartotext($form_field_save));			
				wsl_log(null, 'abstract-filo-metabox.php $_POST: ' . wsl_vartotext($_POST));
				
				if ( isset( $_POST[ $form_field_id ] ) ) {
					update_post_meta( $post_id, '_' . $form_field_save, wc_clean( $_POST[ $form_field_id ] ) );
					
					wsl_log(null, 'abstract-filo-metabox.php update_post_meta update postmeta post_id: ' . wsl_vartotext($post_id));
					wsl_log(null, 'abstract-filo-metabox.php update_post_meta update postmeta key: ' . wsl_vartotext('_' . $form_field_save));
					wsl_log(null, 'abstract-filo-metabox.php update_post_meta update postmeta value: ' . wsl_vartotext(wc_clean( $_POST[ $form_field_id ] )));
				}
				
			}
			
		}
	
	}

	/**
	 * Save data by calling entity class
	 * 
	 * @param int $post_id - id of post to save
	 * @param object $post - post object
	 * @param object $business_object - an empty business object, the attributes will be set, except not savable fields
	 * @param array $not_savable_field_names - array of field names, that have not have to save  
	 */
	public static function save_object( $post_id, $post, $business_object, $not_savable_field_names = array() ) {
		global $wpdb;

		try {

			//$wpdb->query('START TRANSACTION'); //moved to class-filo-admin-meta-boxes.php-> end_transaction

			//$form_fields = static::init_form_fields( $business_object, $post = null );
			$form_fields = static::init_form_fields( $business_object, $post );
	
			wsl_log(null, 'abstract-filo-metabox.php save_object $post_id: ' . wsl_vartotext($post_id));
			wsl_log(null, 'abstract-filo-metabox.php save_object $business_object: ' . wsl_vartotext($business_object));
			wsl_log(null, 'abstract-filo-metabox.php save_object $form_fields: ' . wsl_vartotext($form_fields));
			wsl_log(null, 'abstract-filo-metabox.php save_object $_POST: ' . wsl_vartotext($_POST));
	
			$business_object->id = $post_id;
		
			//Save fields that defined in init_form_fields() 
			foreach ($form_fields as $form_field) {
				
				if ($form_field['type'] != 'html_code') {
	
					//if $form_field['save'] is given, then save with this name, otherwise save name is the field id
					$form_field_id = $form_field['id']; 
					$form_field_save = (isset($form_field['save']) and $form_field['save']) ?  $form_field['save'] : $form_field['id']; //if save is not given then saved fileld name id equal to the id 
					
					////wsl_log(null, 'abstract-filo-metabox.php save_object $form_field[save]: ' . wsl_vartotext($form_field['save']));			
					wsl_log(null, 'abstract-filo-metabox.php save_object $form_field_id / $form_field_save: ' . wsl_vartotext($form_field_id) . '; ' . wsl_vartotext($form_field_save));
					
					//if it not an excepted field, then set it
					if ( !in_array( $form_field_save, $not_savable_field_names ) ) {
						
						if ( isset( $_POST[ $form_field_id ] ) ) {
							//update_post_meta( $post_id, '_' . $form_field_save, wc_clean( $_POST[ $form_field_id ] ) );
							
							wsl_log(null, 'abstract-filo-metabox.php save_object $_POST[ $form_field_id ]: ' . wsl_vartotext($_POST[ $form_field_id ]));
							wsl_log(null, 'abstract-filo-metabox.php save_object $business_object: ' . wsl_vartotext($business_object));
							
							$set_function_name = 'set_' . $form_field_save; 
							$business_object->$set_function_name(wc_clean( $_POST[ $form_field_id ] ));
							
							wsl_log(null, 'abstract-filo-metabox.php save_object update_post_meta update postmeta post_id: ' . wsl_vartotext($post_id));
							wsl_log(null, 'abstract-filo-metabox.php save_object update_post_meta update postmeta key: ' . wsl_vartotext('_' . $form_field_save));
							wsl_log(null, 'abstract-filo-metabox.php save_object update_post_meta update postmeta value: ' . wsl_vartotext(wc_clean( $_POST[ $form_field_id ] )));
						}
						
					}
					
				}
				
			}
	
			global $create_result;
			$create_result = $business_object->create();
		
			//$wpdb->query( 'COMMIT' ); //moved to class-filo-admin-meta-boxes.php-> end_transaction
			
			//It should be done after commit, because commit is not performed later of error was adding, it is moved to class-filo-admin-meta-boxes.php->save_meta_boxes
			/*if ( isset($create_result['warning_messages']) && is_array($create_result['warning_messages']) && !empty($create_result['warning_messages']) )
			foreach ( $create_result['warning_messages'] as $warning_message ) {

				WC_Admin_Meta_Boxes::add_error( __( 'Account saving WARNING', 'filo_text' ) . ': ' . $warning_message ); //RaPe +F	
					
			}*/
		
		} catch (FILO_Validation_Exception $ve) {
				
			//WC_Admin_Meta_Boxes::add_error( __( 'Account saving ERROR', 'filo_text' ) . ': ' . $ve->getMessage() ); //RaPe +F
			FILO_Admin_Meta_Boxes::add_error( __( 'Account saving ERROR', 'filo_text' ) . ': ' . $ve->getMessage() ); //RaPe +F
			
			//$wpdb->query('ROLLBACK'); 
			wsl_log(null, 'abstract-filo-metabox.php save_object ROLLBACK: ' . wsl_vartotext(''));
			
		}
		
	}

}

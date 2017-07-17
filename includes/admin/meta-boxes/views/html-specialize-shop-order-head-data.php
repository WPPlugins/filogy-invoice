<?php

/**
 * Declare specialities of sales order (shop order) admin head data
 *
 * @package     Filogy/Admin/Metabox/Views
 * @subpackage 	Financials
 * @author      WebshopLogic
 * @category    Metabox/Views
 */

if ( !defined('ABSPATH') ) exit;

add_filter('filo_meta_box_shop_order_data_fields', 'filo_meta_box_shop_order_data_fields', 15, 2); //called from class-filo-meta-box-financial-document-head-data.php init_form_fields
//add_action('filo_admin_shop_order_head_data_save', 'filo_admin_shop_order_head_data_save'); //called from class-filo-meta-box-financial-document-head-data.php save
//add_action('filo_admin_shop_order_head_data_before_save', 'filo_admin_shop_order_head_data_save'); //called from class-filo-meta-box-financial-document-head-data.php save
add_action('filo_admin_shop_order_head_data_before', 'filo_admin_shop_order_head_data_before');


wsl_log(null, 'html-specialize-shop-order-head-data.php: ' . wsl_vartotext(''));


//add woocommerce order status to general head data of sales order (shop_order) admin page
function filo_meta_box_shop_order_data_fields ($form_fields, $order) {


	wsl_log(null, 'html-specialize-shop-order-head-data.php filo_meta_box_shop_order_data_fields $order: ' . wsl_vartotext($order));

	/*
	// Order Status has already been added to shop_orders, thus it is unnecessary to be added here
	$form_fields[] = array(
				'type'    		=> 'select',
				'id'			=> 'order_status',				
				'label'			=> __( 'Order status', 'filo_text' ) . ':',
				'description'	=> __( 'XXXClassical WooCommerce status of the order.', 'filo_text' ),
				'desc_tip' 		=>  true,
				'desc_tip_in_label' => true,		
				'value'			=> 'wc-' . $order->get_status(),
				'options'		=> wc_get_order_statuses(),
				//'class'			=> '',
				'custom_attributes' => array(
					'style'			=> 'max-width:150px;',				
					'maxlength' => '10',
					'pattern'   => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
					'required'		=> '',					
				),
	);
	*/
	
	//*** due_date ***
	$duedate = $order->get_due_date();
	if ( $duedate == '' and $order->is_editable() ) {
		$duedate = $order->default_due_date();
	}
	
	$form_fields[] = array(
				'type'    		=> 'text', //'date_picker',
				'id'			=> '_due_date',				
				'label'			=> __( 'Due date', 'filo_text' ) . ':',
				'description'	=> __( 'Due Date of this document.', 'filo_text' ),
				'desc_tip' 		=>  true,		
				'desc_tip_in_label' => true,				
				'value'			=> $duedate,
				'class'			=> 'date-picker filo_doc_date',
				'form_field_class' => 'form-field form-field-wide',
				'custom_attributes' => array(
				//	'style'			=> 'max-width:150px;',				
					'maxlength' => '10',
					'pattern'   => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])',
					'required'		=> '',					
				),
				'field_order'	=> 16,
	);
	
	
	return $form_fields;
	
}

/*function filo_admin_shop_order_head_data_save ($order) {
	
	wsl_log(null, 'html-specialize-shop-order-head-data.php filo_admin_shop_order_head_data_save $order: ' . wsl_vartotext($order));
	
	$old_order_status = 'wc-' . $order->get_status();
	$new_order_status = $_POST['order_status'];
	
	//if the new status is different than the existing, and already have order status, then update it (if order status is empty, then leave the normal WC mechanism to work, no update needed)
	if ( $old_order_status != $new_order_status and $old_order_status != null ) {
		
		wsl_log(null, 'html-specialize-shop-order-head-data.php filo_admin_shop_order_head_data_save update_order_status $new_order_status: ' . wsl_vartotext($new_order_status));
		
	    $order->update_status( $new_order_status ); //This caused the following error, because inside update_status, order save is called without a mandatory data: PHP Fatal error:  Call to a member function getOffsetTimestamp() on a non-object in D:\wamp\www\demo2\wp-content\plugins\woocommerce\includes\data-stores\abstract-wc-order-data-store-cpt.php on line 124
		//$order->set_status( $new_order_status ); //That is why set_status would be better before save, because it has not call the save again. But before save, the later called save can overwrite this value by the posted order_status field, but we do the same here, thus it is innecessary here!?  
		
	}
	
}*/


//add set due_date script to general head data of sales_invoice admin page
function filo_admin_shop_order_head_data_before($order) {
	
	//Do the same as sales invoice
	filo_admin_filo_sa_invoice_head_data_before($order);	

}

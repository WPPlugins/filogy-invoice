<?php

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Document_Shop_Order') ) :

/**
 * Sales Order document generation and settings (sales_order)
 * It is called "Shop Order" for technical purposes, not FILO_Document_Sales_Order
 *
 * @package     Filogy/Documents
 * @subpackage 	Financials
 * @author      WebshopLogic
 * @category    Documents
 */
class FILO_Document_Shop_Order extends FILO_Document {

	/**
	 * construct
	 */
	function __construct() {

		$this->id 				= 'document_shop_order';
		$this->title 			= __( 'Sales Order', 'filo_text' );
		$this->description		= __( 'Sales Order is an agreement between you and the customer, that the customer buys a specific quantity of given goods at a specified price from you.', 'filo_text' );

		$this->template_base	= FILO()->plugin_path() . '/templates/'; 
		$this->template_html 	= 'documents/document-standard-complex.php';

		// Triggers for this doscument
		add_filter( 'filo_generate_shop_order_document', array( $this, 'trigger' ), 10, 3);
		
		//Specialize setting fields
		add_filter( 'filo_settings_document_shop_order_data_fields', array( $this, 'filo_settings_shop_order_data_fields' ) ); 
		
		wsl_log(null, 'class-filo-document-shop-order.php __construct: ' . wsl_vartotext(''));

		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @param string
	 * @param number
	 * @return object
	 */
	function trigger( $content, $order_id, $pseudo_doc_type = null ) {
		
		if ( $order_id ) {
			$this->object 		= wc_get_order( $order_id ); //filo_get_order
			
			//ToDo RaPe ?
			$this->find['order-date']      = '{order_date}';
			$this->find['order-number']    = '{order_number}';
			
			//$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
			$this->replace['order-date']   = wc_format_datetime( $this->object->get_date_created() );
			$this->replace['order-number'] = $this->object->get_order_number();
			
			$this->find = apply_filters( 'filo_document_tag_find', $this->find, $this );
			$this->replace = apply_filters( 'filo_document_tag_replace', $this->replace, $this );
			
		}
		
		if ( $pseudo_doc_type ) {
			$this->pseudo_doc_type = $pseudo_doc_type;
		}
		
		wsl_log(null, 'class-filo-document-shop-order.php trigger $this: ' . wsl_vartotext($this));
		
		return $this;
	}

	/**
	 * Specialize Shop Order setting fields
	 *
	 * @access public
	 * @param array $form_fields
	 * @return array
	 */
	function filo_settings_shop_order_data_fields( $form_fields ) {
		
		$form_fields['due_days'] = array(
			'title' 		=> __( 'Due Days', 'filo_text' ),
			'type' 			=> 'number',
			//'description'   => __( 'The number of days till the due date after the document creation date.', 'filo_text' ),
			'desc_tip' 	    => __( 'The number of days till the due date after the document creation date.', 'filo_text' ),
			'placeholder' 	=> '',
			'css'			=> 'width:70px',
			'default' 		=> '15',
			'custom_attributes' => array(
				'min' 	=> '0',
				'max'   => '365',
			),
			'field_order'	=> 15,
		);
		
		wsl_log(null, 'class-filo-document-shop-order.php $form_fields: ' . wsl_vartotext($form_fields));

		return $form_fields;
		
	}

}

endif;

return new FILO_Document_Shop_Order();
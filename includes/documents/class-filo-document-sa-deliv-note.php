<?php
if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Document_Sa_Deliv_Note') ) :

/**
 * Sales Delivery Note document generation and settings
 *
 * @package     Filogy/Documents
 * @subpackage 	Financials
 * @category    Documents
 */
class FILO_Document_Sa_Deliv_Note extends FILO_Document {

	/**
	 * construct
	 */
	function __construct() {

		$this->id 				= 'document_filo_sa_deliv_note';
		$this->title 			= __( 'Sales Delivery Note', 'filo_text' );
		$this->description		= __( 'Sales Delivery Note is an evidence that the ordered goods have been delivered to the customer, indicating the products, quantities, and agreed prices for products or services.', 'filo_text' );

		$this->template_base	= FILO()->plugin_path() . '/templates/'; 
		$this->template_html 	= 'documents/document-standard-complex.php';

		// Triggers for this doscument
		add_filter( 'filo_generate_filo_sa_deliv_note_document', array( $this, 'trigger' ), 10, 2);
		
		//Specialize setting fields
		add_filter( 'filo_settings_document_filo_sa_deliv_note_data_fields', array( $this, 'filo_settings_sales_delivery_note_data_fields' ) ); 
		
		wsl_log(null, 'class-filo-document-sales-delivery-note.php __construct: ' . wsl_vartotext(''));

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
	function trigger( $content, $order_id ) {
		
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
		
		wsl_log(null, 'class-filo-document-sales-delivery-note.php trigger $this: ' . wsl_vartotext($this));
		
		return $this;
	}

	/**
	 * Specialize Sales Delivery Note setting fields
	 *
	 * @access public
	 * @param array $form_fields
	 * @return array
	 */
	function filo_settings_sales_delivery_note_data_fields( $form_fields ) {

		return $form_fields;
		
	}

}

endif;

return new FILO_Document_Sa_Deliv_Note();

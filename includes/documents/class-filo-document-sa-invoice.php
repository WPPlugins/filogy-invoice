<?php
if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Document_Sa_Invoice') ) :

/**
 * Sales Invoice document generation and settings
 *
 * @package     Filogy/Documents
 * @subpackage 	Financials
 * @category    Documents
 */
class FILO_Document_Sa_Invoice extends FILO_Document {

	/**
	 * construct
	 */
	function __construct() {

		$this->id 				= 'document_filo_sa_invoice';
		$this->title 			= __( 'Sales Invoice', 'filo_text' );
		$this->description		= __( 'Sales Invoice is a financial document issued by you as a seller to your customer, relating to a sale transaction, indicating the products, quantities, and agreed prices for products or services that you had provided to the customer.', 'filo_text' );

		$this->template_base	= FILO()->plugin_path() . '/templates/'; 
		$this->template_html 	= 'documents/document-standard-complex.php';

		// Triggers for this doscument
		add_filter( 'filo_generate_filo_sa_invoice_document', array( $this, 'trigger' ), 10, 2);
		
		//Specialize setting fields
		add_filter( 'filo_settings_document_filo_sa_invoice_data_fields', array( $this, 'filo_settings_sales_invoice_data_fields' ) ); 
		
		wsl_log(null, 'class-filo-document-sales-invoice.php __construct: ' . wsl_vartotext(''));

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
			
			wsl_log(null, 'class-filo-document-sales-invoice.php trigger $this->object: ' . wsl_vartotext($this->object));
			//$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
			$this->replace['order-date']   = wc_format_datetime( $this->object->get_date_created() );
			$this->replace['order-number'] = $this->object->get_order_number();
			
			$this->find = apply_filters( 'filo_document_tag_find', $this->find, $this );
			$this->replace = apply_filters( 'filo_document_tag_replace', $this->replace, $this );
		}
		
		//wsl_log(null, 'class-filo-document-sales-invoice.php trigger $this: ' . wsl_vartotext($this));
		
		return $this;
	}

	/**
	 * Specialize Sales Invoice setting fields
	 *
	 * @access public
	 * @param array $form_fields
	 * @return array
	 */
	function filo_settings_sales_invoice_data_fields( $form_fields ) {
		
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
		
		wsl_log(null, 'class-filo-document-sales-invoice.php $form_fields: ' . wsl_vartotext($form_fields));

		return $form_fields;
		
	}

}

endif;

return new FILO_Document_Sa_Invoice();

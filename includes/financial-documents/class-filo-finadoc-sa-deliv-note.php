<?php

/**
 * Handle Sales Delivery Note and Return financial document special attributes and behavior
 *
 * @package     Filogy/Finadocs
 * @subpackage 	Financials
 * @category    Finadocs
 */

class FILO_FinaDoc_Sa_Deliv_Note extends FILO_Financial_Document {  //excents FILO_Financial_Document and WC_Order so not WC_Abstract_Order, because refund is handled only in WC_Order

	/**
	 * construct
	 */
	public function __construct( $order ) {

		$this->order_type = 'filo_sa_deliv_note';

		parent::__construct( $order );
	}
		
	public static function default_completion_date() {
		$default_completion_date = date("Y-m-d", time()); //default value is sysdate
		//wsl_log(null, 'class-filo-finadoc-sa-delivery-note.php default_completion_date $default_completion_date: ' . wsl_vartotext($default_completion_date));
		return $default_completion_date;
	}

	//if it is called from a batch process, finadoc_id is needed, because in this case we dont have global $post variable
	/*public function default_due_date( $finadoc_id = null ) {
		global $post;
		
		if ( $finadoc_id == null ) {
			$doc_type = $post->post_type;
		} else {
			$doc_type = get_post_type($finadoc_id);
		}

		$document_options = get_option('woocommerce_document_' . $doc_type . '_settings'); //e.g. 'filo_document_sales_invoice_settings
		
		if ( !empty($document_options['due_days']) ) {
			$default_due_date = date("Y-m-d", time() + $document_options['due_days'] * 24*60*60); //default value is sysdate + x days //ToDo RaPe
		} else {
			$default_due_date = null;			
		}			
				
		return $default_due_date;
		
	}*/

}

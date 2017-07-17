<?php
/**
 * Handle Sales Order financial document special attributes and behavior (sales_order)
 * It is called "Shop Order" for technical purposes, not FILO_FinaDoc_Sales_Order
 * 
 * @package     Filogy/Finadocs
 * @subpackage 	Financials
 * @category    Finadocs
 */
class FILO_FinaDoc_Shop_Order extends FILO_Financial_Document {  //excents FILO_Financial_Document and WC_Order so not WC_Abstract_Order, because refund is handled only in WC_Order

	/**
	 * construct
	 */
	public function __construct( $order ) {

		$this->order_type = 'shop_order';

		parent::__construct( $order );
	}

	public static function set_document_data_to_copy ($from_doc) {
		//Called from class-filo-admin-doc-actions.php copy_document, to set special values of copied fields(e.g default values of some fields)
		
		$from_doc['order']['_completion_date'] = self::default_completion_date() ;
		$from_doc['order']['_due_date'] = self::default_due_date() ;
		
		return $from_doc;
	}

	/*public function get_completion_date() {
		return get_post_meta( $this->id, '_completion_date', true );
	}

	public function get_due_date() {
		return get_post_meta( $this->id, '_due_date', true );
	}*/

	public static function default_completion_date() {
		$default_completion_date = date("Y-m-d", time()); //default value is sysdate
		return $default_completion_date;
	}

	//if it is called from a batch process, finadoc_id is needed, because in this case we dont have global $post variable
	public static function default_due_date( $finadoc_id = null ) {
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
		
	}

	/**
	 * Post journal entry, and inventory transactions
	 * 
	 * No journal entry posting needed
	 * 
	 * Change committed quantity (in normal case increase) in inventory
	 * 
	 */
	public function account_and_inventory_postings() {
	}
}
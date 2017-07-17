<?php

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Admin_Finadoc_List_Table') ) :

/**
 * Financial Document List Table
 *
 * FILO_Admin_Finadoc_List_Table Class
 *
 * Handles the edit posts views and some functionality on the edit post screen for WC post types.
 * The name of this class may (FILO_Admin_Finadoc_List_Table (class-filo-admin-finadoc-list-table.php)), but it is financial_document specific
 * 
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */
 
class FILO_Admin_Finadoc_List_Table extends WC_Admin_Post_Types {

	/**
	 * construct
	 */
	public function __construct() {
		
		wsl_log(null, 'class-filo-admin-finadoc-list-table.php __construct: ' . wsl_vartotext( '' ));
		
		global $filo_post_types_financial_documents;
		
		if (isset( $filo_post_types_financial_documents ) && is_array( $filo_post_types_financial_documents ) )
		foreach ($filo_post_types_financial_documents as $filo_post_types_financial_document) {
			
			add_action( 'woocommerce_admin_order_actions_end', array( $this, 'render_financial_document_actions' ), 2 ); //ADD RaPe //Add custom actions
			
		}
		
		include_once( 'class-filo-admin-meta-boxes.php' );
		
		
	}

	/**
	 * @param  string $column
	 */
	public function render_financial_document_actions( $the_order ) {
		global $post; //, $woocommerce, $the_order;

						do_action( 'filo_admin_order_actions_start', $the_order );

						//wsl_log(null, 'class-filo-admin-finadoc-list-table.php render_financial_document_actions $the_order: ' . wsl_vartotext($the_order));
						
						$order_type_object = get_post_type_object( $the_order->get_doc_type() ); //$order_type_object in WC v2.4.10
						$doc_type_name = $order_type_object->labels->singular_name;

						$actions = array();
						
						$actions['print'] = array(
							'url' 		=> wp_nonce_url( home_url() . '?filo_individual_page=filo_generate_pdf&doc_id=' . $post->ID , 'filo_generate_pdf_' . $post->ID, 'filo_nonce' )  . '&filo_usage=doc_view',
							'name' 		=> sprintf ( __( 'Print %s', 'filo_text' ), $doc_type_name ),
							'action' 	=> "print",
							'target'	=> "_blank",
						);

						if ( FILO_TYPE == 'filo_invoice_type' ) {
							// display print buttons of delivery note and invoice pseudo doc types in the shop_order list 
							// it is available only in FILOFY INVOICE (free and pro)
							
							$actions['print_pseudo_sa_deliv_note'] = array(
								'url' 		=> wp_nonce_url( home_url() . '?filo_individual_page=filo_generate_pdf&doc_id=' . $post->ID , 'filo_generate_pdf_' . $post->ID, 'filo_nonce' )  . '&filo_usage=doc_view&filo_pseudo_doc_type=filo_sa_deliv_note',
								'name' 		=> __( 'Print Delivery Note', 'filo_text' ),
								'action' 	=> "print",
								'target'	=> "_blank",
							);

							$actions['print_pseudo_sa_invoice'] = array(
								'url' 		=> wp_nonce_url( home_url() . '?filo_individual_page=filo_generate_pdf&doc_id=' . $post->ID , 'filo_generate_pdf_' . $post->ID, 'filo_nonce' )  . '&filo_usage=doc_view&filo_pseudo_doc_type=filo_sa_invoice',
								'name' 		=> __( 'Print Invoice', 'filo_text' ),
								'action' 	=> "print",
								'target'	=> "_blank",
							);
						}
						
						$actions = apply_filters( 'filo_admin_order_actions', $actions, $the_order );

						wsl_log(null, 'class-filo-admin-finadoc-list-table.php render_financial_document_actions $actions: ' . wsl_vartotext($actions));

						foreach ( $actions as $action ) {
							printf( '<a class="button tips %s" href="%s" data-tip="%s" target="%s" >%s</a>', esc_attr( isset($action['action']) ? $action['action'] : '' ), esc_url( isset($action['url']) ? $action['url'] : '' ), esc_attr( isset($action['name']) ? $action['name'] : '' ), esc_attr( isset($action['target']) ? $action['target'] : ''), esc_attr( isset($action['name']) ? $action['name'] : ''  ) ); //MODIFY RaPe
						}

						do_action( 'filo_admin_order_actions_end', $the_order );

	}
	
}

endif;

new FILO_Admin_Finadoc_List_Table();
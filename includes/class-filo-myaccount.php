<?php
/**
 * Hande Financial document in My Account
 * 
 * @package     Filogy/Classes
 * @subpackage 	Financials
 * @category    Class
 * 
 */
class FILO_Myaccount  { 

	/**
	 * construct
	 */
	public function __construct() {

		add_filter('woocommerce_my_account_my_orders_actions', array( $this, 'my_account_my_order_financial_document_actions' ), 10, 2 );

	}

	/**
	 * my_account_my_order_financial_document_actions
	 * add buttons to my order list on my account page (print buttons into every order line for print all documents that base document is the actual order, and also pseudo docs)
	 *
	 * @since 2.2
	 * @return array
	 */
	public function my_account_my_order_financial_document_actions ( $actions, $order ) {
		global $wpdb, $filo_post_types_financial_documents; 

		//comma separated list with apostrophes		
		$filo_post_types_financial_documents_commasep = "'" . implode("','", $filo_post_types_financial_documents) . "'";

		$current_user_id = get_current_user_id();
		
		$order_id = $order->get_id();
		
		//select base documents of the order, and order itself 
		$sql = 
		"		
			select 
				posts.id doc_id,
				posts.post_type
			from {$wpdb->prefix}posts as posts
			left outer join {$wpdb->prefix}postmeta as base_order on base_order.post_id = posts.id and base_order.meta_key = '_base_order_id'
			left outer join {$wpdb->prefix}postmeta as customer_user on customer_user.post_id = posts.id and customer_user.meta_key = '_customer_user'			
			left outer join {$wpdb->prefix}postmeta as doc_status on doc_status.post_id = posts.id and doc_status.meta_key = '_doc_status'
			where posts.id = {$order_id} or /* the order itself can be printed without any other codition */ 
				(base_order.meta_value = {$order_id}  /* other documents those based on the order can be printed if the conditions below are fullfilled */
				and customer_user.meta_value = {$current_user_id}		
				and doc_status.meta_value not in ('draft')
				and posts.post_type in ({$filo_post_types_financial_documents_commasep}))
		";
		
		wsl_log(null, 'class-filo-myaccount.php my_account_my_order_financial_document_actions $sql: ' . wsl_vartotext($sql));
		
		$results = $wpdb->get_results( $sql ); 
		
		wsl_log(null, 'class-filo-myaccount.php $filo_post_types_financial_documents_commasep: ' . wsl_vartotext($filo_post_types_financial_documents_commasep));
		
		wsl_log(null, 'class-filo-myaccount.php $results: ' . wsl_vartotext($results));		

		$i = 0;
		foreach ( $results as $result ) {

			$i++;
			$financial_document_id = $result->doc_id;
			wsl_log(null, 'class-filo-myaccount.php my_account_my_order_financial_document_actions $financial_document_id: ' . wsl_vartotext($financial_document_id));

			$finadoc = wc_get_order( $result->doc_id ); 
			$doc_name = $finadoc->get_doc_type_label_singular_short_name(); 
			//$post_type_obj = get_post_type_object( $result->post_type );
			
			$actions['filo_generate_pdf' . $i] = array(
				'url'  => wp_nonce_url( home_url() . '?filo_individual_page=filo_generate_pdf&doc_id=' . $financial_document_id , 'filo_generate_pdf_' . $financial_document_id, 'filo_nonce' ) . '&filo_usage=doc_view',
				//'name' => __( 'Print', 'filo_text' ) . ' ' . $post_type_obj->labels->singular_name,
				'name' => __( 'Print', 'filo_text' ) . ' ' . $doc_name,  
			);
			
			$doc_type = $finadoc->get_doc_type();
			
			// shop orders can have pseudo documents, that have to be able to printed
			// if it is a shop order, than examine weather it has a valid pseudo doc, if so then we display the button
			if ( $doc_type == 'shop_order' ) {
		
				//loop for pseudo doc types that should be generated from this order as pdf (e.g dlivery not, invoice)
				if(isset($filo_post_types_financial_documents) and is_array($filo_post_types_financial_documents))
				foreach ($filo_post_types_financial_documents as $potential_pseudo_doc_type) {
		
					//shop order is not pseudo document			
					if ( $potential_pseudo_doc_type != 'shop_order' ) {
						
						$pseudo_doc_type = $potential_pseudo_doc_type;
						wsl_log(null, 'class-filo-myaccount.php my_account_my_order_financial_document_actions $pseudo_doc_type: ' . wsl_vartotext($pseudo_doc_type));
		
						$is_pseudo_doc_valid = $order->is_pseudo_doc_valid( $pseudo_doc_type );
						
						wsl_log(null, 'class-filo-myaccount.php my_account_my_order_financial_document_actions $is_pseudo_doc_valid: ' . wsl_vartotext($is_pseudo_doc_valid));
						
						if ( $is_pseudo_doc_valid ) {
							
							$pseudo_order_type_object = get_post_type_object( $pseudo_doc_type ); //$order_type_object in WC v2.4.10
							$pseudo_doc_type_name = $pseudo_order_type_object->labels->singular_name;
							
							
							$i++;

							$actions['filo_generate_pdf' . $i] = array(
								'url'  => wp_nonce_url( home_url() . '?filo_individual_page=filo_generate_pdf&doc_id=' . $financial_document_id , 'filo_generate_pdf_' . $financial_document_id, 'filo_nonce' ) . '&filo_usage=doc_view&filo_pseudo_doc_type=' . $pseudo_doc_type,
								'name' => __( 'Print', 'filo_text' ) . ' ' . $pseudo_doc_type_name,  
							);
							
						}
						
					}
				
				}

			}
						
		}

		wsl_log(null, 'class-filo-myaccount.php my_account_my_order_financial_document_actions $actions: ' . wsl_vartotext($actions));

		return $actions;
		
	}

}

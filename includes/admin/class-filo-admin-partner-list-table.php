<?php

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Admin_Partner_List_Table') ) :

/**
 * Partner List Table
 * 
 * At this moment we do not use this.
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */
class FILO_Admin_Partner_List_Table {

	/**
	 * construct
	 */
	public function __construct() {
		
		//filo_parner mode is removed from the list, it is remained only in the edit page
		//so partner financial data is available from the customised "Users" menu, and not from a new "Partners" menu
		//if( isset($_GET['mode']) and $_GET['mode'] == 'filo_partner' ) {
		
		// http://jasonjalbuena.com/wordpress-add-and-sort-custom-column-in-users-admin-page/
		add_filter( 'manage_users_columns', array( $this, 'modify_user_table' ) );
		add_filter( 'manage_users_custom_column', array( $this, 'modify_user_table_row' ), 10, 3 );
		
		// Sort
		add_filter( 'manage_users_sortable_columns', array( $this, 'sortable_columns' ) );
		
		// Filters
		add_filter( 'pre_user_query', array( $this, 'alter_the_query' ) ); //++ //do query
		
		// Add custom row action (under username a new link beside edit and delete)
		add_filter('user_row_actions', array( $this, 'add_user_row_actions' ), 10, 2);
		
		//}
			
	}

	function modify_user_table( $column ) {
	    	
		//add new column: Balance	
	    $column['partner_balance'] = __('Balance', 'filo_text');
		$column['actions'] = __('Actions', 'filo_text');
		
		//remove existin column: Posts
		//unset( $column['posts'] );
		
		wsl_log(null, 'class-filo-admin-partner-list-table.php modify_user_table $column: ' . wsl_vartotext($column));
		
	
	    return $column;
	}
	
	function modify_user_table_row( $val, $column_name, $user_id ) {
		
		$return = null;
		
	    switch ($column_name) {
	        case 'actions' :

				$partner_id = $user_id;

				$return =  '<a class="preview button filo_partner_financial_data_button" href="' . admin_url( 'user-edit.php?mode=filo_partner&user_id=' . $partner_id ) . '" >' . 
							__( 'Financial Data', 'filo_text' ) . '</a>';
				
				//http://yoursite.com/wp-admin/user-edit.php?mode=filo_partner&user_id=409&wp_http_referer=/wp-admin/users.php?mode=filo_partner
				
	            break;

	
	        default:
	    }
	
	    return $return;
	}

	
	/**
	 * Add new row actions (under username a new link beside edit and delete)
	 */
		
	function add_user_row_actions($actions, $user_object) {
			
		$actions['financial_data'] = '<a class="filo_partner_financial_action" href="' . admin_url( 'user-edit.php?mode=filo_partner&user_id=' . $user_object->ID ) . '" >' . 
		__( 'Financial Data', 'filo_text' ) . '</a>';
		
		return $actions;
	}
	
	
	/**
	 * sortable_columns
	 * e.g. http://scribu.net/wordpress/custom-sortable-columns.html
	 */
	public function sortable_columns( $columns ) { // keep it for later use

		/*
		
		// EXAMPLE //
		
		$custom = array(
			'partner_balance' => 'partner_balance',
		);

		$columns = wp_parse_args( $custom, $columns );

		wsl_log(null, 'class-filo-admin-partner-list-table.php $columns: ' . wsl_vartotext($columns));
		*/

		return $columns;
	}

	/**
	 * At this moment no sort needed
	 * Modify query, add order by clauses
	 */
	public function alter_the_query( $request ) { //++	 
		global $typenow;

		/*
		 
		// EXAMPLE //
		
		//see wp-includes\user.php 785, 798
		$vars = $request->query_vars;
		
		// Sorting
		if ( isset( $vars['orderby'] ) ) {

			// financial_document_status
			if ( 'partner_balance' == $vars['orderby'] ) {
				$vars = array_merge( $vars, array(
					//'meta_key' 	=> '_doc_statusXXXXX',
					//'orderby' 	=> 'meta_value'
					
					'orderby' 	=> 'ID'
				) );
			}
			
		}
		

		wsl_log(null, 'class-filo-admin-partner-list-table.php alter_the_query $vars: ' . wsl_vartotext($vars));
		
		*/
	
	}

}

endif;

new FILO_Admin_Partner_List_Table();

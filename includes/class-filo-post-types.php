<?php

if ( !defined('ABSPATH') ) exit;

/**
 *
 * Register post types
 *
 * 
 * @package     Filogy/FinancialDocuments/Classes
 * @subpackage 	Financials
 * @category    Class
 * 
 */
class FILO_Post_Types {

	/**
	 * Contsructor
	 */
	public function __construct() {
		
		wsl_log(null, 'class-filo-post-types.php __construct 0: ' . wsl_vartotext(''));		
	
		add_action( 'init', array( 'FILO_Post_Types', 'register_taxonomies' ), 5 );
		
		add_action( 'init', array( 'FILO_Post_Types', 'register_post_types_1' ), -10 );
		add_action( 'init', array( 'FILO_Post_Types', 'register_post_types_2' ), 7 ); //should be greater than 5 and less than 10
		
		add_action( 'init', 'FILO_Post_Types::register_post_status', 10 );
		
		add_filter( 'woocommerce_register_post_type_shop_order', array( $this, 'regmod_post_type_sa_order' ) );
		
		add_filter( 'woocommerce_screen_ids', array( $this, 'add_to_woocommerce_screen_ids' ) );
		
		//Remove filo post types during wc_check_cart_items. this must be before class-wc-cart.php -> add_action( 'woocommerce_check_cart_items', array( $this, 'check_cart_items' ), 1 ); (our priority: -10)
		//without this remove, class-wc-cart.php -> check_cart_item_stock counts all order items as $held_stock (e.g. a purchase invoice hold the stock because WC count it as a sales order!). That is why, we have to remove filo types.
		add_action( 'woocommerce_check_cart_items', array( $this, 'remove_filo_post_types' ), -10 );  

		//These values should be set in the constructor, because it is used in this before init: register_activation_hook( FILO_PLUGIN_FILE, array( $this, 'filo_activation2' ) ); 		
		global $filo_post_types, $filo_pseudo_types_financial_documents;
		$filo_post_types = array();
		$filo_pseudo_types_financial_documents = array();
		 
		$filo_post_types[] = 'shop_order'; 		
	}


	/**
	 * Register Filogy post types 1.
	 * menus "before" sales order
	 */
	public static function register_post_types_1() {
		
		wsl_log(null, 'class-filo-post-types.php register_post_types_1 0: ' . wsl_vartotext(''));
		
		self::register_post_type_init();
		
		self::register_post_type_sa_order();
		self::register_post_type_sa_deliv_note();
		self::register_post_type_sa_invoice();
	}

	/**
	 * Register Filogy post types 2.
	 * menus "after" sales order
	 */
	public static function register_post_types_2() {
				
	}


	/**
	 * Register Filogy taxonomies.
	 */
	public static function register_taxonomies() {

	}

	/**
	 * Register post type initialization
	 * Delete arrays before filled in by the following action calles
	 */
	public static function register_post_type_init() {

		
		global $filo_post_types, $filo_post_types_financial_documents, $close_openness_types;
		//$filo_post_types = array(); //contains all post type name handled by Filogy - Moved to constructor
		$filo_post_types_financial_documents = array(); //contains all financial document kind of post type names handled by Filogy
		
		//other post types that has FILO metabox to save:
		//$filo_post_types[] = 'product'; //moved to constructor 
	}

	//SALES
	//--------------------------------------
	//Sales submenus are reorder here: class-filo-admin-menus.php sales_submenu_order()

	/**
	 * After Register post types
	 */

	/**
	 * 
	 * wc_order_status_control setting roules
	 * 
	 * Roules in the following priority:
	 * 
	 * 10. Pay -> wc-completed (if Downloadable product and order is not opened) 
	 * 20. Pay -> wc-processing (if no any delivery or normal invoice) 
	 * 30. Delivery -> wc-completed
	 * 40. Before Delivery Delivery -> wc-completed
	 * 50. Invoice (normal) -> wc-completed 
	 * 
	 * Comments:
	 * 10. If pay for a downloadable, and order is not opened, then new order status is completed
	 * 20. If pay, but not after delivery, then new order status is processing
	 * 30. After delivery the new order status is completed
	 * 40. After reverse delivery, the new order status is completed
	 * 50. After invoicing (only normal invoice, not reveresed) the new order status is completed
	 * 
	 */


	/*
	public static function is_filo_financial_document( $post_type ) {
		global $filo_post_types_financial_documents;
		
		//if $filo_post_types_financial_documents global has already set, then we examine accorfing to it
		if ( is_array($filo_post_types_financial_documents) and ! empty($filo_post_types_financial_documents) ) {
			$return = in_array($post_type, $filo_post_types_financial_documents);
		} else { //if it has not set yet, we evaluate the prefixes of the post_type name (it is a less precise solution for the early callers - e.g. for customizer)
			if ( strpos($post_type, 'filo_sa_') === 0 or strpos($post_type, 'filo_pu_') or $post_type == 'shop_order' ) {
				$return == true;
			} else {
				$return == false;
			}
		}
		
		return $return;
	}
	*/

	/**
	 * Register post type sales_order
	 */
	public static function register_post_type_sa_order() {
		global $filo_post_types, $filo_post_types_financial_documents, $filo_sales_types_financial_documents, $close_openness_types;
		
		//sales_order is a special kind of document tecnically, because it is registered by WooCommerce as "shop_order"
		
		//shop_order
		//$filo_post_types[] = 'shop_order'; //moved to constructor
		$filo_post_types_financial_documents[] = 'shop_order';
		$filo_sales_types_financial_documents[] = 'shop_order';
				
	}

	/**
	 * Modify registration of post type sales order
	 * This is stanndard shop order (sales order) post type, registered by Woocommerce.
	 * The standard WC registration is modified here by the filter.
	 */
	public static function regmod_post_type_sa_order( $reg_data ) {
		global $is_filo_settings_ok;
		
		//this is needed for initial sequence settings (class-filo-do-setup.php sequence_settings), before filo settings are ok
		$reg_data['labels']['code']  = __( 'SO', 'filo_text' );
		
		if ( $is_filo_settings_ok ) {
			//wsl_log(null, 'class-filo-post-types.php regmod_post_type_sa_order $reg_data: ' . wsl_vartotext($reg_data)); //big
			
			$reg_data['labels']['short_name'] 	      = __( 'Order', 'filo_text' );
			$reg_data['labels']['singular_short_name'] = __( 'Order', 'filo_text' );
			//$reg_data['labels']['code']               = __( 'SO', 'filo_text' ); //move up
			
			$reg_data['publicly_queryable'] = true; //it is necessary for reaching this doc using http://yoursite.com/?post_type=your_type&p=123 links (protected by nonce)
			$reg_data['class_name']     = 'FILO_FinaDoc_Shop_Order';
				
			//wsl_log(null, 'class-filo-post-types.php regmod_post_type_sa_order 2 $reg_data: ' . wsl_vartotext($reg_data)); //big

		}
			
		return $reg_data;	
		
	}

	public static function secondary_shop_order_submenu_link() {
		
				
		//add a secondary link for handling Shop Orders
		//if the primary order link is in the Original WooCommerce place, then add secondary link to Filogy menu.
		//if the primary order link is moved into Filogy menu, then add secondary link to WooCommerce menu.
		
		$filo_move_order_submenu_into_filo_sales_menu = get_option( 'filo_move_order_submenu_into_filo_sales_menu' );
		
		if ( $filo_move_order_submenu_into_filo_sales_menu == 'yes' ) {
			
			//submenu_link:
			$shop_orders_link_to_add = add_query_arg( array( 'post_type' => 'shop_order', 'filogy_secondary_menu' => 'true' ), 'edit.php' ); //not: admin_url( 'admin.php' );  edit.php?post_type=shop_order
			add_submenu_page( 'woocommerce', __( 'Orders', 'filo_text' ),  __( 'Orders', 'filo_text' ), 'manage_woocommerce', $menu_slug = $shop_orders_link_to_add, $function = ''); 
			
		} else {

			//submenu_link:
			//we apply filogy_menu=true in the submenu page link, because without it, the link would be the same as original WooCommerce order menu link, and this secondary menu is activated, and not the promary original WooCommerce Order link. 
			$shop_orders_link_to_add = add_query_arg( array( 'post_type' => 'shop_order', 'filogy_secondary_menu' => 'true' ), 'edit.php' ); //not: admin_url( 'admin.php' );  edit.php?post_type=shop_order&filogy_menu=true
			add_submenu_page( 'filo_sales', __( 'Sales Orders', 'filo_text' ),  __( 'Sales Orders', 'filo_text' ), 'manage_woocommerce', $menu_slug = $shop_orders_link_to_add, $function = ''); 
			
		}		
		
	}
	
	
	/**
	 * Register post type sales_delivery_note
	 */
	public static function register_post_type_sa_deliv_note() {
		global $filo_post_types, $filo_post_types_financial_documents, $filo_sales_types_financial_documents, $filo_pseudo_types_financial_documents, $close_openness_types, $is_filo_settings_ok;
				
		//sales_delivery_note
		//$filo_post_types[] = 'filo_sa_deliv_note'; //moved to constructor
		$filo_post_types_financial_documents[] = 'filo_sa_deliv_note';
		$filo_sales_types_financial_documents[] = 'filo_sa_deliv_note';
		
		//in case of filo invoice plugin type, it is a pseudo document
		//if (FILO_TYPE == 'filo_invoice_type') {
		$filo_pseudo_types_financial_documents[] = 'filo_sa_deliv_note';
		//}
		 
		wc_register_order_type(
			'filo_sa_deliv_note',
			apply_filters( 'filogy_register_post_type_sa_deliv_note',
				array(
					'labels'              => array(
							'name'               => __( 'Sales Delivery Notes', 'filo_text' ),
							'singular_name'      => __( 'Sales Delivery Note', 'filo_text' ),
							'short_name'          => __( 'Delivery Notes', 'filo_text' ),
							'singular_short_name' => __( 'Delivery Note', 'filo_text' ),
							'code'               => __( 'SD', 'filo_text' ),
							'add_new'            => __( 'Add Sales Delivery Note', 'filo_text' ),
							'add_new_item'       => __( 'Add New Sales Delivery Note', 'filo_text' ),
							'edit'               => __( 'Edit', 'filo_text' ),
							'edit_item'          => __( 'Edit Sales Delivery Note', 'filo_text' ),
							'new_item'           => __( 'New Sales Delivery Note', 'filo_text' ),
							'view'               => __( 'View Sales Delivery Note', 'filo_text' ),
							'view_item'          => __( 'View Sales Delivery Note', 'filo_text' ),
							'search_items'       => __( 'Search Sales Delivery Notes', 'filo_text' ),
							'not_found'          => __( 'No Sales Delivery Notes found', 'filo_text' ),
							'not_found_in_trash' => __( 'No Sales Delivery Notes found in trash', 'filo_text' ),
							'parent'             => __( 'Parent Sales Delivery Notes', 'filo_text' ),
							'menu_name'          => _x( 'Sales Delivery Notes', 'Admin menu name', 'filo_text' )
						),
					'description'         => __( 'This is where Sales Delivery Notes are stored.', 'filo_text' ),
					'public'              => false,
					'show_ui'             => $is_filo_settings_ok, //if there is no settings defect, then menu should be displayed (true) //true,
					'capability_type'     => 'filo_sa_deliv_note',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true, //it is necessary for reaching this doc using http://yoursite.com/?post_type=your_type&p=123 links (protected by nonce)
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_woocommerce' ) ? 'filo_sales' : false,
					'menu_position'       => 1,
					'hierarchical'        => false,
					'show_in_nav_menus'   => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title', 'comments', 'custom-fields' ),
					'has_archive'         => false,
			
					'exclude_from_orders_screen' => false,
					'add_order_meta_boxes'       => false, //to enque meta-boxes-order js script by woocommerce, it should be true, but then all order metabox would be displayed
					'exclude_from_order_count'   => true,
					'exclude_from_order_views'   => true,
					'class_name'                 => 'FILO_FinaDoc_Sa_Deliv_Note',
					'direction'					 => 'sales',
				)
			)
		);
			
	}
	

	/**
	 * Register post type sales_invoice
	 */
	public static function register_post_type_sa_invoice() {
		global $filo_post_types, $filo_post_types_financial_documents, $filo_sales_types_financial_documents, $filo_pseudo_types_financial_documents, $close_openness_types, $trans_match_financial_document_type, $is_filo_settings_ok;
				
		//sales_invoice
		//$filo_post_types[] = 'filo_sa_invoice'; //moved to constructor
		$filo_post_types_financial_documents[] = 'filo_sa_invoice';
		$filo_sales_types_financial_documents[] = 'filo_sa_invoice';
		
		//in case of filo invoice plugin type, it is a pseudo document
		//if (FILO_TYPE == 'filo_invoice_type') {
		$filo_pseudo_types_financial_documents[] = 'filo_sa_invoice';
		//}

		wc_register_order_type(
			'filo_sa_invoice',
			apply_filters( 'filogy_register_post_type_sa_invoice',
				array(
					'labels'              => array(
							'name'               => __( 'Sales Invoices', 'filo_text' ),
							'singular_name'      => __( 'Sales Invoice', 'filo_text' ),
							'short_name'          => __( 'Invoices', 'filo_text' ),
							'singular_short_name' => __( 'Invoice', 'filo_text' ),
							'code'               => __( 'SI', 'filo_text' ),
							'add_new'            => __( 'Add Sales Invoice', 'filo_text' ),
							'add_new_item'       => __( 'Add New Sales Invoice', 'filo_text' ),
							'edit'               => __( 'Edit', 'filo_text' ),
							'edit_item'          => __( 'Edit Sales Invoice', 'filo_text' ),
							'new_item'           => __( 'New Sales Invoice', 'filo_text' ),
							'view'               => __( 'View Sales Invoice', 'filo_text' ),
							'view_item'          => __( 'View Sales Invoice', 'filo_text' ),
							'search_items'       => __( 'Search Sales Invoices', 'filo_text' ),
							'not_found'          => __( 'No Sales Invoices found', 'filo_text' ),
							'not_found_in_trash' => __( 'No Sales Invoices found in trash', 'filo_text' ),
							'parent'             => __( 'Parent Sales Invoices', 'filo_text' ),
							'menu_name'          => _x( 'Sales Invoices', 'Admin menu name', 'filo_text' )
						),
					'description'         => __( 'This is where Sales Invoices are stored.', 'filo_text' ),
					'public'              => false,
					'show_ui'             => $is_filo_settings_ok, //if there is no settings defect, then menu should be displayed (true) //true,
					'capability_type'     => 'filo_sa_invoice',
					'map_meta_cap'        => true,
					'publicly_queryable'  => true, //it is necessary for reaching this doc using http://yoursite.com/?post_type=your_type&p=123 links (protected by nonce)
					'exclude_from_search' => true,
					'show_in_menu'        => current_user_can( 'manage_woocommerce' ) ? 'filo_sales' : false,
					'hierarchical'        => false,
					'show_in_nav_menus'   => false,
					'rewrite'             => false,
					'query_var'           => false,
					'supports'            => array( 'title', 'comments', 'custom-fields' ),
					'has_archive'         => false,
			
					'exclude_from_orders_screen' => false,
					'add_order_meta_boxes'       => false, //to enque meta-boxes-order js script by woocommerce, it should be true, but then all order metabox would be displayed
					'exclude_from_order_count'   => true,
					'exclude_from_order_views'   => true,
					'class_name'                 => 'FILO_FinaDoc_Sa_Invoice',
					'direction'					 => 'sales',
				)
			)
		);
		
	}

	/**
	 * Register our custom post statuses, used for order status
	 */
	public static function register_post_status() { 
	
		wsl_log(null, 'class-filo-post-types.php register_post_status 0: ' . wsl_vartotext(''));
	
		//this part is important, because the documents would be hidden on post list pages
		//so remove_post_status_parse_query($array_query) is not used in filogy-framework\includes\admin\class-filo-admin-finadoc-list-table.php

		// 1. call the WC default function that register wc post statuses 		
		WC_Post_Types::register_post_status();

		
		// 2. change public and publicly_queryable values to true
		global $wp_post_statuses;
		
		if( isset($wp_post_statuses) && is_array($wp_post_statuses))
		foreach ($wp_post_statuses as $wp_post_status_key => $wp_post_status_value) {
			if ( strpos($wp_post_status_key, 'wc-') === 0 ) { //if $wp_post_status_key begins wc-
				
				//set public and publicly_queryable to true
				//this is needed to find them in class-wp-posts-list-table query
				$wp_post_statuses[$wp_post_status_key]->public = 1;
				$wp_post_statuses[$wp_post_status_key]->publicly_queryable = 1;
				
			}
		}
				
		//wsl_log(null, 'class-filo-post-types.php register_post_status $wp_post_statuses: ' . wsl_vartotext($wp_post_statuses));
	}

	function add_to_woocommerce_screen_ids ( $screen_ids ) {
					
		return $screen_ids;
		
	}
	//#endif_2ESE
	
	static function remove_filo_post_types() {
		global $wc_order_types, $filo_post_types_financial_documents, $wp_post_types;
		
		wsl_log(null, 'class-filo-post-types.php remove_filo_post_types 0: ' . wsl_vartotext(''));
		
		foreach ($filo_post_types_financial_documents as $filo_post_type) {
			if ( $filo_post_type != 'shop_order' ) {
				unset( $wc_order_types[$filo_post_type] );
				unset( $wp_post_types[$filo_post_type] );
			}
		}

		//remove_action( 'init', array( FILO_Post_Types, 'register_post_types_1' ), -10 );
		//remove_action( 'init', array( FILO_Post_Types, 'register_post_types_2' ), 7 );
		
	}
	
	/**
	 * get_finadoc_screen_ids
	 * 
	 * calculate financial document admin screen names, getting financial document type names and type names with "edit-" prefix
	 */
	public static function get_finadoc_screen_ids() {
		global $filo_post_types_financial_documents;
	
		$finadoc_screens = $filo_post_types_financial_documents;
		$finadoc_screens[] = 'filo_case'; //filo_case is not a real financial document, but need these assets
		
		wsl_log(null, 'class-filo-post-types.php get_finadoc_creen_ids $finadoc_screens: ' . wsl_vartotext($finadoc_screens));
		
		if (isset( $filo_post_types_financial_documents ) && is_array( $filo_post_types_financial_documents ) )
		foreach ($filo_post_types_financial_documents as $fin_doc_edit_screen)
    		$finadoc_screens[] = 'edit-' . $fin_doc_edit_screen;

		wsl_log(null, 'class-filo-post-types.php get_finadoc_creen_ids $finadoc_screens: ' . wsl_vartotext($finadoc_screens));

		return $finadoc_screens;
		
	}

}

return new FILO_Post_Types();

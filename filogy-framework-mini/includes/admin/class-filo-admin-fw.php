<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * FILO_Admin -> expand class-wc-admin.php
 *
 * @package     Filogy/Admin
 * @subpackage 	Framework
 * @author      WebshopLogic - Peter Rath
 * @author 		WooThemes (original file)
 * @category    Admin
 * 
 * @based_on	class-wc-admin.php file in WooCommerce plugin by WooThemes
 * 
 */
class FILO_Admin_FW {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_action( 'admin_footer', 'wc_print_js', 25 );

		//----------
		//add_filter( 'woocommerce_get_settings_pages', 'FILO_Admin_Settings::get_wc_settings_pages', 5, 1 );
		//add_action( 'woocommerce_admin_field_html_code', 'FILO_Admin_Settings::output_html_code', 10, 1 ); // html_code
		//add_action( 'woocommerce_admin_field_date_picker', 'FILO_Admin_Settings::output_date_picker', 10, 1 ); //date_picker

		//after wc-settings submenu loaded, what calls: settings_page() -> WC_Admin_Settings::output() -> do_action( 'woocommerce_settings_start' ); so it includes WC_Admin_Settings 
		//we have to include FILO_Admin_Settings			
		//add_action( 'woocommerce_settings_start', 'FILO_Admin_Settings::output_before' );
		
		//This is overwrite report url title, to display a message that report modul is not installed
		//If report modul is installed, then it is write back to the right url, with a higher filter priority
		add_filter('filo_report_url', array($this, 'filo_report_url'), 10, 2);
		
	}

	/**
	 * Include any classes we need within admin.
	 * 
	 * @based_on FILO_Admin->includes WC_v2.4.10
	 */
	public function includes() { // MODIFY RaPe
		// Functions
		include_once( 'filo-meta-box-functions.php' );

		wsl_log(null, 'class-filo-admin.php includes $_GET: ' . wsl_vartotext($_GET));

		// Classes
		//include_once( 'class-filo-admin-finadoc-list-table.php' );
		//include_once( 'class-filo-admin-partner-list-table.php' );
		
		//if ($_GET['post_type'] == 'filo_case')
		//	include_once( 'class-filo-admin-case-list-table.php' );

		//if ($_GET['post_type'] == 'filo_trans_match')
		//	include_once( 'class-filo-admin-trans-match-list-table.php' );
		
		// Classes we only need during non-ajax requests
		
		if ( ! is_ajax() ) {
			//include( 'class-filo-admin-menus.php' );
			//include( 'class-filo-admin-setup-page.php' );
			//include( 'class-filo-admin-setup-page_frame.php' );
			//include( 'class-filo-admin-notices.php' );
			//include_once( 'class-filo-admin-assets.php' );
			include( 'class-filo-admin-message-page.php' );
		}
		
	}

	/**
	 * Include admin files conditionally
	 * 
	 * @based_on FILO_Admin->conditional_includes WC_v2.4.10
	 */
	public function conditional_includes() { // MODIFY RaPe
		$screen = get_current_screen();

		wsl_log(null, 'class-filo-admin.php conditonal_includes $screen: ' .  wsl_vartotext($screen));

	}
	
	
	/**
	 * admin_enqueue_scripts
	 * 
	 * Called from class-filo-admin-assets.php, this should be done for using own item saving tax_calcing and total_calcing functions of finadocs
	 */
	public static function admin_enqueue_scripts( $post ) {
			
		//ToDo: it would be great that this code was handled by WooCommerce, but a condition there prevent to perform it, and no filter or action for override
		$params = array(
			'remove_item_notice'            => __( 'Are you sure you want to remove the selected items? If you have previously reduced this item\'s stock, or this order was submitted by a customer, you will need to manually restore the item\'s stock.', 'woocommerce' ),
			'i18n_select_items'             => __( 'Please select some items.', 'woocommerce' ),
			'i18n_do_refund'                => __( 'Are you sure you wish to process this refund? This action cannot be undone.', 'woocommerce' ),
			'i18n_delete_refund'            => __( 'Are you sure you wish to delete this refund? This action cannot be undone.', 'woocommerce' ),
			'i18n_delete_tax'               => __( 'Are you sure you wish to delete this tax column? This action cannot be undone.', 'woocommerce' ),
			'remove_item_meta'              => __( 'Remove this item meta?', 'woocommerce' ),
			'remove_attribute'              => __( 'Remove this attribute?', 'woocommerce' ),
			'name_label'                    => __( 'Name', 'woocommerce' ),
			'remove_label'                  => __( 'Remove', 'woocommerce' ),
			'click_to_toggle'               => __( 'Click to toggle', 'woocommerce' ),
			'values_label'                  => __( 'Value(s)', 'woocommerce' ),
			'text_attribute_tip'            => __( 'Enter some text, or some attributes by pipe (|) separating values.', 'woocommerce' ),
			'visible_label'                 => __( 'Visible on the product page', 'woocommerce' ),
			'used_for_variations_label'     => __( 'Used for variations', 'woocommerce' ),
			'new_attribute_prompt'          => __( 'Enter a name for the new attribute term:', 'woocommerce' ),
			'calc_totals'                   => __( 'Calculate totals based on order items, discounts, and shipping?', 'woocommerce' ),
			'calc_line_taxes'               => __( 'Calculate line taxes? This will calculate taxes based on the customers country. If no billing/shipping is set it will use the store base country.', 'woocommerce' ),
			'copy_billing'                  => __( 'Copy billing information to shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
			'load_billing'                  => __( 'Load the customer\'s billing information? This will remove any currently entered billing information.', 'woocommerce' ),
			'load_shipping'                 => __( 'Load the customer\'s shipping information? This will remove any currently entered shipping information.', 'woocommerce' ),
			'featured_label'                => __( 'Featured', 'woocommerce' ),
			'prices_include_tax'            => esc_attr( get_option('woocommerce_prices_include_tax') ),
			'round_at_subtotal'             => esc_attr( get_option( 'woocommerce_tax_round_at_subtotal' ) ),
			'no_customer_selected'          => __( 'No customer selected', 'woocommerce' ),
			'plugin_url'                    => WC()->plugin_url(),
			'ajax_url'                      => admin_url('admin-ajax.php'),
			'order_item_nonce'              => wp_create_nonce("order-item"),
			'add_attribute_nonce'           => wp_create_nonce("add-attribute"),
			'save_attributes_nonce'         => wp_create_nonce("save-attributes"),
			'calc_totals_nonce'             => wp_create_nonce("calc-totals"),
			'get_customer_details_nonce'    => wp_create_nonce("get-customer-details"),
			'search_products_nonce'         => wp_create_nonce("search-products"),
			'grant_access_nonce'            => wp_create_nonce("grant-access"),
			'revoke_access_nonce'           => wp_create_nonce("revoke-access"),
			'add_order_note_nonce'          => wp_create_nonce("add-order-note"),
			'delete_order_note_nonce'       => wp_create_nonce("delete-order-note"),
			'calendar_image'                => WC()->plugin_url().'/assets/images/calendar.png',
			'post_id'                       => isset( $post->ID ) ? $post->ID : '',
			'base_country'                  => WC()->countries->get_base_country(),
			'currency_format_num_decimals'  => absint( get_option( 'woocommerce_price_num_decimals' ) ),
			'currency_format_symbol'        => get_woocommerce_currency_symbol(),
			'currency_format_decimal_sep'   => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
			'currency_format_thousand_sep'  => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
			'currency_format'               => esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format() ) ), // For accounting JS
			'rounding_precision'            => WC_ROUNDING_PRECISION,
			'tax_rounding_mode'             => WC_TAX_ROUNDING_MODE,
			'product_types'                 => array_map( 'sanitize_title', get_terms( 'product_type', array( 'hide_empty' => false, 'fields' => 'names' ) ) ),
			'default_attribute_visibility'  => apply_filters( 'default_attribute_visibility', false ),
			'default_attribute_variation'   => apply_filters( 'default_attribute_variation', false ),
			'i18n_download_permission_fail' => __( 'Could not grant access - the user may already have permission for this file or billing email is not set. Ensure the billing email is set, and the order has been saved.', 'woocommerce' ),
			'i18n_permission_revoke'        => __( 'Are you sure you want to revoke access to this download?', 'woocommerce' ),
			'i18n_tax_rate_already_exists'  => __( 'You cannot add the same tax rate twice!', 'woocommerce' ),
			'i18n_product_type_alert'       => __( 'Your product has variations! Before changing the product type, it is a good idea to delete the variations to avoid errors in the stock reports.', 'woocommerce' )
		);

		wp_localize_script( 'wc-admin-meta-boxes', 'woocommerce_admin_meta_boxes', $params );
	}		

}

return new FILO_Admin_FW();

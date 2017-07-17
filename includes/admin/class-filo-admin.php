<?php

if ( !defined('ABSPATH') ) exit;

/**
 * FILO_Admin -> expand class-wc-admin.php
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */ 
class FILO_Admin {

	/**
	 * construct
	 */
	public function __construct() {
		global $is_filo_settings_ok;

		wsl_log(null, 'class-filo-admin.php __construct 0: ' . wsl_vartotext(''));
		wsl_log(null, 'class-filo-admin.php __construct $is_filo_settings_ok: ' . wsl_vartotext($is_filo_settings_ok));
	
		add_action( 'init', array( $this, 'init' ) );
		
		
		//----------
		add_filter( 'woocommerce_get_settings_pages', 'FILO_Admin_Settings::get_wc_settings_pages', 5, 1 );
		add_action( 'woocommerce_admin_field_html_code', 'FILO_Admin_Settings::output_html_code', 10, 1 ); // html_code
		add_action( 'woocommerce_admin_field_date_picker', 'FILO_Admin_Settings::output_date_picker', 10, 1 ); //date_picker

		//after wc-settings submenu loaded, what calls: settings_page() -> WC_Admin_Settings::output() -> do_action( 'woocommerce_settings_start' ); so it includes WC_Admin_Settings 
		//we have to include FILO_Admin_Settings			
		add_action( 'woocommerce_settings_start', 'FILO_Admin_Settings::output_before' );
		if ( FILO_TYPE == 'filo_invoice_type' ) {
			add_action( 'woocommerce_order_actions_end', 'FILO_Admin::order_metabox_output_print_button', 10, 1 ); //just for mini version
		}
		
		//add_filter( 'body_class', array( $this, 'body_class' ) );
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		 
		
	}

	/**
	 * init
	 */	 
	public function init() { // MODIFY RaPe
		global $is_filo_settings_ok;

		// Functions
		wsl_log(null, 'class-filo-admin.php includes $_GET: ' . wsl_vartotext($_GET));

		// Classes
		if ( $is_filo_settings_ok ) {
			include_once( 'class-filo-admin-finadoc-list-table.php' );
		}
				
		include_once( 'class-filo-admin-partner-list-table.php' );
			
		// Classes we only need during non-ajax requests
		if ( ! is_ajax() ) {
			include( 'class-filo-admin-menus.php' );
			//include( 'class-filo-admin-setup-page.php' );
			
			//include( 'class-filo-admin-setup-page_frame.php' );
		}

		
		if ( ! is_ajax() ) {	
			//Filo admin notice must not be displayed on filo partner editor page, because it does not work on that page. (You do not have sufficient permissions to access this page.)
				
			if ( ! isset($_GET['mode']) or ( isset($_GET['mode']) and $_GET['mode'] != 'filo_partner') ) {
				include( 'class-filo-admin-notices.php' );
			}

			//if ( $is_filo_settings_ok ) {							
				include_once( 'class-filo-admin-assets.php' );
			//}
			
		}
		
	}
	//just for mini version
	/**
	 * order_metabox_output_print_button
	 */
	public static function order_metabox_output_print_button($post_id) { // ADD RaPe
		
		?>
		<li class="wide">	
			<a class="preview button filo_generate_pdf" href="<?php print wp_nonce_url( home_url() . '?filo_individual_page=filo_generate_pdf&doc_id=' . $post_id , 'filo_generate_pdf_' . $post_id, 'filo_nonce') . '&filo_usage=doc_view'; ?>" target="_blank">
					<?php echo __( 'Print', 'filo_text' ); ?>
			</a>
		</li>
		<?php
	}
	
	/**
	 * admin_body_class
	 * There WooCommerce are css settings for .post-type-shop_order, post-type-shop_order is a css class in html body tag.
	 * We have to insert this class to the body for all finadoc.
	 * https://wordpress.stackexchange.com/questions/44444/body-class-hook-for-admin-pages
	 */
	public function admin_body_class( $classes ) {
		
		//wsl_log(null, 'class-filo-admin.php body_class 0: ' . wsl_vartotext(''));
		
		$screen = get_current_screen();
		$finadoc_screens = FILO_Post_Types::get_finadoc_screen_ids();
		
		//wsl_log(null, 'class-filo-admin.php $screen->id: ' . wsl_vartotext($screen->id));
		//wsl_log(null, 'class-filo-admin.php $finadoc_screens: ' . wsl_vartotext($finadoc_screens));

		//we need this for case and trans_match screens
		$finadoc_screens[] = 'edit-filo_case';
		$finadoc_screens[] = 'edit-filo_trans_match';
					
		// filo_case is a finadoc screen, but it has another primary column			
		if ( in_array( $screen->id, $finadoc_screens ) ) {
			
			$classes = $classes . ' post-type-shop_order';
			
		}
				
		return $classes;
		
	}		
		
}

return new FILO_Admin();

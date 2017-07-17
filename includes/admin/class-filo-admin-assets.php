<?php
if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Admin_Assets') ) :
	
/**
 * FILO_Admin_Assets -> expand class-wc-admin-assets.php
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */
class FILO_Admin_Assets {

	/**
	 * construct
	 */
	public function __construct() {
		
		wsl_log(null, 'class-filo-admin-assets.php __construct 0: ' . wsl_vartotext(''));
		
		//include_once( ABSPATH . 'wp-content/plugins/' . FILO()->filogy_framework_path_tag() . '/includes/wsl-core-functions.php' );
		include_once( WP_PLUGIN_DIR . '/' . FILO()->filogy_framework_path_tag() . '/includes/wsl-core-functions.php' );
		
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ),90 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ),90 ); //this should be executed after original woocommerce class-wc-admin-assets.php, wp_register_script have to be done  ; priority > 10
		//add_action( 'admin_head', array( $this, 'product_taxonomy_styles' ) );

		include_once( 'class-filo-admin-meta-boxes.php' );
		
	}

	/**
	 * admin_enqueue_styles
	 */
	public function admin_enqueue_styles() {
		
		$screen = get_current_screen();
		wsl_log(null, 'class-filo-admin-assets.php admin_styles $screen->id: ' . wsl_vartotext($screen->id));
		wsl_log(null, 'class-filo-admin-assets.php admin_styles wc_get_screen_ids(): ' . wsl_vartotext(wc_get_screen_ids()));

		if ( in_array( $screen->id, wc_get_screen_ids() ) ) {

			wp_enqueue_style( 'filo_admin_styles', FILO()->plugin_url() . '/assets/css/filo-admin.css', array() );
		}
		
		wp_enqueue_style( 'filo_admin_always_styles', FILO()->plugin_url() . '/assets/css/filo-admin_always.css', array() );
		
	}

	/**
	 * admin_enqueue_scripts
	 * This should be executed after original woocommerce class-wc-admin-assets.php, wp_register_script have to be done earlyer
	 */
	public function admin_enqueue_scripts() {

		global $wp_query, $post;
		
		$screen = get_current_screen();
		
		wsl_log(null, 'class-filo-admin-assets.php wp_enqueue_script0: ' );
		//TODO RaPe

		$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'filo_settings', FILO()->plugin_url() . '/assets/js/admin/settings.js', array( 'jquery' ), FILO()->version );
		wp_register_script( 'filo_design_customizer_link', FILO()->plugin_url() . '/assets/js/admin/design_customizer_link.js', array( 'jquery' ), FILO()->version );

		// FILO admin pages
		
		wsl_log(null, 'class-filo-admin-assets.php wp_enqueue_script $screen->id: ' . wsl_vartotext($screen->id));
		if ( in_array( $screen->id, wc_get_screen_ids() ) or $screen->id == 'filoinv_template') {

			wp_enqueue_script( 'filo_settings' );

			$params = array(
				'ajax_url'                  => WC()->ajax_url(),
				'get_formatted_seller_data_nonce'	=> wp_create_nonce("get_formatted_seller_data"),
				
			);

			wp_localize_script( 'filo_settings', 'filo_settings', $params );

		}
		
		//moved to FILO_Post_Types::get_finadoc_screen_ids()
		//calculate financial document admin screen names, getting financial documebt type names and type names with "edit-" prefix
		//global $filo_post_types_financial_documents;
	
		//$finadoc_screens = $filo_post_types_financial_documents;
		//$finadoc_screens[] = 'filo_case'; //filo_case is not a real financial document, but need these assets
		
		//wsl_log(null, 'class-filo-admin-assets.php admin_scripts $finadoc_screens: ' . wsl_vartotext($finadoc_screens));
		
		//if (isset( $filo_post_types_financial_documents ) && is_array( $filo_post_types_financial_documents ) )
		//foreach ($filo_post_types_financial_documents as $fin_doc_edit_screen)
    	//	$finadoc_screens[] = 'edit-' . $fin_doc_edit_screen;

		//wsl_log(null, 'class-filo-admin-assets.php admin_scripts $screen->id: ' . wsl_vartotext($screen->id));
		//wsl_log(null, 'class-filo-admin-assets.php admin_scripts $finadoc_screens: ' . wsl_vartotext($finadoc_screens));

		global $is_filo_settings_ok;
		if ( $is_filo_settings_ok ) {

			$finadoc_screens = FILO_Post_Types::get_finadoc_screen_ids();
	
			if (isset( $finadoc_screens ) && is_array( $finadoc_screens ) )
			if ( in_array( $screen->id, $finadoc_screens ) ) { //e.g: array( 'filo_sa_invoice', 'edit-sales_invoice' )
				
				//ToDo: it would be great that the called code was handled by WooCommerce, but a condition there prevent to perform it, and no filter or action for override
				FILO_Admin_FW::admin_enqueue_scripts( $post );
	  
			}

		}
			
		if ( $screen->id == 'customize' ) {
			//enqueue: sc-color - A color parsing and manipulation library
			wp_enqueue_script( 'filo-sc-color', FILOFW()->plugin_url() . '/modules/sc-color/lib/surfacecurve-color.js', array(), '0.4.0' ); // MODIFY RaPe
		}

		//add_logo begin
        //if ( 'settings_page_add-logo-to-admin/add-logo-to-admin' == $hook ) { //RaPe ToDo
            wp_enqueue_media();
            //wp_enqueue_script( 'filo-admin-add-logo-select-image', FILOFW()->plugin_url() . '/assets/js/admin/add-logo-select-image.js', array( 'jquery', 'media-upload', 'media-views' ), WC_VERSION, true );
			wsl_log(null, 'class-filo-admin-assets.php wp_enqueue_script: ' );
        //}
		//add_logo end
 
	}

}

endif;

return new FILO_Admin_Assets();

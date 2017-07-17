<?php
/**
 * FILO_Admin_Partner -> extends WordPress user functions
 *
 * @package     Filogy/Admin
 * @subpackage 	Framework
 * @category    Admin
 * 
 * @based_on	edit-form-advanced.php, dashboard.php files in WordPress
 */
class FILO_Admin_Partner {

	/**
	 * Initialize 
	 *
	 */
	public function __construct() {
		//it is moved to class-filo-initial-functions.php 
		//add_filter('woocommerce_customer_meta_fields', 'FILO_Admin_Partner::change_customer_label_to_partner', 10, 1);		
	}

	/**
	 * This function is called from user-edit admin page, when display that page. (if mode=filo_partner URL parameter is set) 
	 * Sets the styles and javascripts for metabox handlig
	 * It displays metaboxes registered to user-edit page.
	 * 
	 * @based_on edit-form-advanced.php, dashboard.php
	 */
	public static function output_partner_metaboxes( $profileuser ) {

		//Normally it is called		
		//if the user-edit admin page is opened in filo_partner mode (&mode=filo_partner)
		//then mode parameter in URL contains filo_partner
		//if( isset($_GET['mode']) and $_GET['mode'] == 'filo_partner' ) {


		//Now it is called from 'user_edit_form_tag' action, that is inside of a little <form> tag. ( <form id="your-profile" ... )
		//We have to close this tag befor output the content and open a new empty tag after the content.
		//if it was called from show_user_profile or edit_user_profile actions that would not be sufficient					
		
		echo '>'; //dummy closing tag for form tag ( <form id="your-profile" ... )
		?>	
			<style>
				/*hide this 3 fields on user-edit admin page*/
				.form-table tr.user-rich-editing-wrap,
				.form-table tr.user-comment-shortcuts-wrap, 
				.form-table tr.user-admin-bar-front-wrap {
				  display:none !important;
				}
				
				/*hide Personal Options header on user-edit admin page*/
				#your-profile > h3 {
					display:none !important;
				}				
			</style>			 
		<?php
		
		//Unfortunately these lines has to be copied here, because no action or filter that can integrate this part to custom functions
		
		wp_enqueue_script( 'common' ); 
		wp_enqueue_script('post');
		wp_enqueue_script('editor-expand');
		if ( wp_is_mobile() )
			wp_enqueue_script( 'jquery-touch-punch' );

		/**
		 * Fires after all built-in meta boxes have been added.
		 *
		 * @since 3.0.0
		 *
		 * @param string  $post_type Post type.
		 * @param WP_Post $post      Post object.
		 */
		do_action( 'add_meta_boxes', 'user', $profileuser );
		
		/**
		 * Fires after all built-in meta boxes have been added, contextually for the given post type.
		 *
		 * The dynamic portion of the hook, `$post_type`, refers to the post type of the post.
		 *
		 * @since 3.0.0
		 *
		 * @param WP_Post $post Post object.
		 */
		do_action( 'add_meta_boxes_' . 'user', $profileuser );
		
		$columns_css = 'columns-2';
		
		$screen = get_current_screen();
		
		?>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder <?php echo $columns_css; ?>">
				<div id="postbox-container-1" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'side', '' ); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
				</div>
				<div id="postbox-container-3" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'column3', '' ); ?>
				</div>
				<div id="postbox-container-4" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'column4', '' ); ?>
				</div>
			</div>
		</div>
		<?php
		echo '<table class="form-table"></table>'; //dummy table, without this "Personal Options" h2 title would be next to the "Partner Addresses" metabox, under the "Transaction Matching" metabox.
		echo '<div></div'; //dummy opening tag (without close), this is the pair of the dummy closing tag before this content
	}



	/**
	 * This function is called from user-edit admin page, when save its content. (if mode=filo_partner URL parameter is set) 
	 */
	public static function save_partner_metaboxes() {
		
		wsl_log(null, 'class-filo-admin-partner.php save_partner_metaboxes 0: ' .  wsl_vartotext(''));
		
	}


	/**
	 * Remove original user profile fields: color picker, api_key
	 * including WooCommerce customer meta fields (it is output in a metabox, instead of normal form fields)
	 * Some fields are hidden in output_partner_metaboxes function, applying css.
	 */
	public static function remove_original_user_profile_options() {
	    # Disable visual editor checkbox
	    global $wp_rich_edit_exists;
	    $wp_rich_edit_exists = false;
	    # Disable choice of admin color scheme from profile.php
	    remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');
		
		$removed_fuct_names_1 = wsl_remove_filter_like( 'show_user_profile', 'add_customer_meta_fields' );
		$removed_fuct_names_2 = wsl_remove_filter_like( 'edit_user_profile', 'add_customer_meta_fields' );
		$removed_fuct_names_3 = wsl_remove_filter_like( 'show_user_profile', 'add_api_key_field' );
		$removed_fuct_names_4 = wsl_remove_filter_like( 'edit_user_profile', 'add_api_key_field' );
	}


	//Displaying user-edit admin page specially, replace original WooCommerce customer fields to a metabox
	//This function is called from add_meta_box, and call the original wp customer field displaying function
	//to output the fields in the metabox.
	//Original WC save function is applied in class-wc-admin-profile.php:
	//		add_action( 'personal_options_update', array( $this, 'save_customer_meta_fields' ) );
	//		add_action( 'edit_user_profile_update', array( $this, 'save_customer_meta_fields' ) ); 
	public static function output_wc_add_customer_meta_fields_metabox() {

		if( isset($_GET['user_id']) ) {

			$profileuser = get_user_to_edit( wc_clean( $_GET['user_id'] ) ); //+wc_clean
			wsl_log(null, 'class-filo-admin-partner.php output_wc_add_customer_meta_fields_metabox $_GET[user_id]: ' .  wsl_vartotext($_GET['user_id']));
			wsl_log(null, 'class-filo-admin-partner.php output_wc_add_customer_meta_fields_metabox $profileuser: ' .  wsl_vartotext($profileuser));

			$wc_admin_profile =	new WC_Admin_Profile();
			
			//After calling constructor of WC_Admin_Profile, WC user prrofil custom fields shoud be removed, because it is moved to metaboxes
			FILO_Admin_Partner::remove_original_user_profile_options();
			
			$wc_admin_profile->add_customer_meta_fields( $profileuser );
			
		}

	}
	
	/*
	 * Insert mode parameter into html form action url, to remain this parameter after submit 
	 */
	public static function set_mode_param_in_user_edit_url( $url, $path ) {
		wsl_log(null, 'class-filo-admin-partner.php set_mode_param_in_user_edit_url $url: ' .  wsl_vartotext($url));
		wsl_log(null, 'class-filo-admin-partner.php set_mode_param_in_user_edit_url $path: ' .  wsl_vartotext($path));
		
		$url .= '?mode=filo_partner';
		
		return $url;
	}

	/*
	 * Change the labels of Customer Billing/Shipping Address to Partner Billing/Shipping Address on Edit User page
	 */
	public static function change_customer_label_to_partner($partner_meta_fields) {

		wsl_log(null, 'class-filo-admin-partner.php change_customer_label_to_partner $partner_meta_fields: ' .  wsl_vartotext($partner_meta_fields));
			 	
		$partner_meta_fields['billing']['title'] = __( 'Partner Billing Address', 'filofw_text' );
		$partner_meta_fields['shipping']['title'] = __( 'Partner Shipping Address', 'filofw_text' );
		
		return $partner_meta_fields;
	 }

}
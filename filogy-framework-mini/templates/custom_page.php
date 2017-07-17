<?php
/**
 * Custom page (Normal page or dashboard with metaboxes)
 *
 * @package     Filogy/Templates
 * @subpackage 	Framework
 * @category    Templates
 */

/*
 * Example of how to add a custom dashboard page to the menu:
 * 
 
	add_action('admin_menu', array( $this,'add_my_custom_dashboard_menu') );
	function add_my_custom_dashboard_menu() {
		add_dashboard_page( 'My Custom Dashboard', 'My Custom Dashboard', 'read', 'my-custom-dashboard', array( $this,'create_my_custom_dashboard') );
	}

	function create_my_custom_dashboard() {
		include_once( 'custom_custom_page.php'  ); //include this file
	}
 

 * Use add_custom_page_meta_boxes and add_custom_page_meta_boxes_{current_screentype} actions to add meta boxes to your custom page
 * All other metaboxes will be removed when the custom page is displayed
 * Use custom_page_layout filter to set page layout type: "page" or "dashboard" - metabox layout like a normal page or a metabox
 * 
 * https://wordpress.org/plugins/sweet-custom-dashboard
 */



/** Load WordPress Bootstrap */
//require_once( ABSPATH . 'wp-admin/admin.php' );

/** Load WordPress dashboard API */
//require_once(ABSPATH . 'wp-admin/includes/dashboard.php');

$current_screen = get_current_screen();

wsl_log(null, 'custom_page.php wp_dashboard $_POST: ' . wsl_vartotext($_POST));
wsl_log(null, 'custom_page.php wp_dashboard $_GET: ' . wsl_vartotext($_GET));
global $filo_custom_page_action;

wsl_log(null, 'custom_page.php wp_dashboard $filo_custom_page_action: ' . wsl_vartotext($filo_custom_page_action));

if ( isset($_POST['save']) ) {

	wsl_log(null, 'custom_page.php before submit_custom_page $current_screen: ' . wsl_vartotext($current_screen));
	submit_custom_page($current_screen);
	
	//$_post do not forget the value on page refresh so the save submit button value get stuck, so the page is saved on every refresh
	//the following line delete $_post value on every page refresh, so save submit button should be pressed again to save (refresh will not save)
	//http://stackoverflow.com/questions/8335828/how-to-delete-post-variable-upon-pressing-refresh-button-on-browser-with-php
	//header('location:index.php?page=' . $_GET['page']); //e.g. 'location:index.php?page=do_trans_match'
	//removed, because of this: Warning: Cannot modify header information - headers already sent by (output started at wp-admin\includes\template.php:1877) in wp-content\plugins\filogy-framework\templates\custom_page.php on line 48
	
}

//After submit, the new page have to get the same GET parameters than the submitted page has (it is available in the posted _wp_http_referer variable)
//In order to do this, we need the same action parameters in <form> than the URL of the original page (_wp_http_referer variable except /wp-admin/ prefix), so we get it from $_POST parameters, and use this value without prefix as the <form> tag action parameter
$original_url_action = '';
if ( isset($_POST['_wp_http_referer']) ) {
	$original_url_action = str_replace( '/wp-admin/', '', wc_clean( $_POST['_wp_http_referer'] ) );  //cut /wp-admin/ prefix from URL, e.g. /wp-admin/admin.php?page=do_trans_match&customer_user_id=4 -> admin.php?page=do_trans_match&customer_user_id=4 //+wc_clean
}

// Layout: 
//	"page": normal two column metabox layout
//	"full_page": one column metabox layout
//	"dashboard": dashboard layout
// custom_page_layout filter makes possible to set the layout type (page/dashboard) depending on the current screen
$custom_page_layout = 'page';
$custom_page_layout = apply_filters( 'custom_page_layout_' . $current_screen->id, $custom_page_layout );
	
if( in_array($custom_page_layout, array( 'page', 'full_page' ) ) ) {
		
	wp_enqueue_script('post');
	$_wp_editor_expand = $_content_editor_dfw = false;
	
} elseif ( $custom_page_layout == 'dashboard' ) {
	
	wp_enqueue_script( 'dashboard' );
	
}

add_thickbox();

if ( wp_is_mobile() )
	wp_enqueue_script( 'jquery-touch-punch' );

$parent_file = 'index.php';

global $submenu;

//Get page code parameter from url
$page_name = wc_clean( $_GET['page'] ); //+wc_clean

/*
 * Get page title (3) by page code (2) from $submenu array
 *  
 * Example of submenu array:
 * $submenu[index.php] => Array (
 * 	[12] => Array
 *		([0] => Custom Dashboard, [1] => read, [2] => custom-dashboard, [3] => Custom Dashboard )
 * 	)
 */

$title = '';
foreach ($submenu[$parent_file] as $submenu_item) {
	if ($submenu_item[2] == $page_name) {
		$title = $submenu_item[3];
	}
}

$screen = get_current_screen();


//include( ABSPATH . 'wp-admin/admin-header.php' );

//use filo_custom_page_form_parameters_.... (e.g. filo_custom_page_form_parameters_do_trans_match -> &customer_user_id=xxx)

	//original <form> tag (it has to be modified because after submit, some URL parameter was lost)
	//<form name="custom_page" action="<!php echo $filo_custom_page_action; !>?page=<!php 
	//			echo $_GET['page'] . 
	//			apply_filters('filo_custom_page_form_parameters_' . $_GET['page'], null); 
	//	!>" method="post" id="custom_page"<!php do_action( 'custom_page_form_tag', $current_screen, $custom_page_layout ); !> >

?>

<div class="wrap">
	<h2><?php echo esc_html( $title ); ?></h2>

	<form name="custom_page" action="<?php echo $original_url_action; ?>" method="post" id="custom_page"<?php do_action( 'custom_page_form_tag', $current_screen, $custom_page_layout );?> >
	
		<?php 
		//wp_nonce_field($nonce_action);
		wp_nonce_field( 'woocommerce_save_data' );
		
		//Remove all existing metaboxes
		 
		do_action( 'custom_page_form_top', $current_screen, $custom_page_layout );  
		 
		global $wp_meta_boxes;
		unset($wp_meta_boxes['dashboard_page_custom-dashboard']);
		
		//Metaboxes on custom pages should be applied using add_custom_page_meta_boxes and add_custom_page_meta_boxes_{current_screentype} actions
		//e.g: add_meta_box( 'my_meta_box', __( 'My Meta Box', 'mylang' ), 'My_Meta_Box_Class::output', dashboard_page_my_custom_page, 'normal', 'high' );
		do_action( 'add_custom_page_meta_boxes', $current_screen->id );
		do_action( 'add_custom_page_meta_boxes_' . $current_screen->id );
		
		//---
		if( in_array($custom_page_layout, array( 'page', 'full_page' ) ) ) {
	
			if ($custom_page_layout == 'page') {
				$columns_css = " columns-2";
			} else {
				$columns_css = ""; //in case of full page 2 columns layout is not necessarry
			}
			
			?>
			<div id="poststuff">	
			<div id="post-body" class="metabox-holder<?php echo $columns_css; ?>">
	
				<div id="postbox-container-1" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'side', '' ); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>	
				</div>
	
			</div>
			<?php
		
		} elseif ( $custom_page_layout == 'dashboard' ) {
	
			//wp_dashboard();
			?>
			<div id="dashboard-widgets-wrap">
			<?php
			
			$screen = get_current_screen();
			wsl_log(null, 'custom_page.php wp_dashboard $screen: ' . wsl_vartotext($screen));
			$columns = absint( $screen->get_columns() );
			$columns_css = '';
			if ( $columns ) {
				//$columns_css = " columns-$columns";
				$columns_css = " columns-2";
			}
			
			?>
			<div id="dashboard-widgets" class="metabox-holder<?php echo $columns_css; ?>">
	
				<div id="postbox-container-1" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'normal', '' ); ?>
				</div>
				<div id="postbox-container-2" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'side', '' ); ?>				
				</div>
				<div id="postbox-container-3" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'column3', '' ); ?>
				</div>
				<div id="postbox-container-4" class="postbox-container">
				<?php do_meta_boxes( $screen->id, 'column4', '' ); ?>
				</div>
				
			</div>
			<?php
						
		}
	
		wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
		wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
		//---
		
		do_action( 'custom_page_form_end', $current_screen, $custom_page_layout );
				 
		?>
		</div><!-- poststuff or dashboard-widgets-wrap -->
	
	</form>

</div><!-- wrap -->

<?php

//require( ABSPATH . 'wp-admin/admin-footer.php' ); //-include wp-admin

function submit_custom_page($current_screen) {
		
	wsl_log(null, 'custom_page.php submit_custom_page 0: ' . wsl_vartotext(''));
			
	//TODO RaPe (special rights)
	// Check user has permission to edit
	if ( ! current_user_can( 'edit_posts' ) ) {
		//return new WP_Error( 'edit_posts', __( 'You are not allowed to submit this page.', financiallogic ) );
		FILO_Admin_Meta_Boxes::add_error( __( 'You are not allowed to submit this page in Filogy.', 'filofw_text' ) );
		FILO_Admin_Meta_Boxes::save_errors();
		
		wsl_log(null, 'custom_page.php submit_custom_page 1: ' . wsl_vartotext('You are not allowed to submit this page.'));
		return;
	}	

	wsl_log(null, 'custom_page.php submit_custom_page do_action: ' . wsl_vartotext('filo_save_custom_page_' . $current_screen->id));	
	do_action( 'filo_save_custom_page_' . $current_screen->id );	

	
}

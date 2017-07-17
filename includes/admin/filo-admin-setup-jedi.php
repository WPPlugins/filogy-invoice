<?php
/**
 * Filogy about page
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */

// load admin bootstrap 
//require_once( dirname( __FILE__ ) . '/../../../../../wp-admin/admin.php' ); //MODIFY RaPe //-include wp-admin

if ( current_user_can( 'install_plugins' ) ) {
	add_thickbox();
	wp_enqueue_script( 'plugin-install' );
}

$major_version = FILOFW()->version;

$show_setup_notice = ! FILO_Do_Setup::is_filo_settings_ok();

$is_all_sequences_are_set = FILO_Financial_Document::is_all_sequences_are_set();

$default_seller_user_id = get_option('filo_document_seller_user');

do_action ('filo_output_setup_screen');


//Document Sequence Settings

//decide if filo_sequence_settings button is visible
$is_all_sequences_are_set = FILO_Financial_Document::is_all_sequences_are_set();
$show_filo_sequence_settings_button = false;
if ( !$is_all_sequences_are_set ) { //if number of existing sequence options is less than the required option names, so there is option name to which no option belongs 
	$show_filo_sequence_settings_button = true;			
}

if ($show_filo_sequence_settings_button) {
	$filo_sequence_settings_desc = 
		'<div id="filo_notice" class="update-nag install_notice">' . 
		__( 'Document sequences have not been set up yet.', 'filo_text' );
	 
	$filo_sequence_settings_desc .= 
		'<p><a href="' . 
		add_query_arg( array( 'page' => 'filo_admin_setup_jedi', 'do_filo_sequence_settings' => 'true'), admin_url( 'admin.php' ) ) . 
		'" class="button-primary">' . __( 'Set Defaults', 'filo_text' ) . '</a></p>';

	$filo_sequence_settings_desc .=		
		'</div>';
		
} else {
	
	$filo_sequence_settings_desc = 
		'<div id="filo_message" class="update-nag install_notice">' . 
		__( 'Document sequences are set.', 'filo_text' ) .
		'</div>';
}							



//Install predefines Customizer Skins 

//decide if filo_install_predefined_customizer_skins button is visible

$predefined_skin_count_installed = FILO_Do_Setup::count_installed_predefined_customizer_skins_data();
$possible_skin_count_of_this_version = FILO_Do_Setup::get_count_possible_skins_of_version();
$filo_install_predefined_customizer_skins_button = false;
//if ($predefined_skin_count_installed <= 1) { // 1 skin (default skin) is installed by activation hook, thus if we have only 1 or less skins, than we have to display install skins button
if ( $predefined_skin_count_installed < $possible_skin_count_of_this_version ) { // if installed skins less then the possible skins, then the install skin busson is active 
	$filo_install_predefined_customizer_skins_button = true;
}		

if ($filo_install_predefined_customizer_skins_button) {
	$filo_install_predefined_customizer_skins_desc = 
		'<div id="filo_notice" class="update-nag install_notice">' . 
		__( 'Predefined document skins have not been installed yet.', 'filo_text' );
	 
	$filo_install_predefined_customizer_skins_desc .= 
		'<p><a href="' . add_query_arg( array( 'page' => 'filo_admin_setup_jedi', 'do_filo_predefined_customizer_skin_install' => 'true'), admin_url( 'admin.php' ) ) . 
		'" class="button-primary">' . __( 'Install', 'filo_text' ) . '</a></p>';

	$filo_install_predefined_customizer_skins_desc .= 			
		'</div>';			
} else {
	$filo_install_predefined_customizer_skins_desc = 
		'<div id="filo_message" class="update-nag install_notice">' .
		__( 'Predefined document skins are installed.', 'filo_text' ) .
		'</div>';
}

//Set blocks

//image sizes: 1010 x 568
$major_features = array(

	array(
		'src'         => FILO()->plugin_url() . '/assets/images/about_sequence_settings.png',		
		'heading'     => __( 'Document Sequence Settings', 'filo_text'  ),
		'description' =>
			__( 'Document numbers can have a prefix and/or suffix part, and independent sequence numbers belong to the different prefixes or postfixes.', 'filo_text' ) . 
			__( 'Document number sequences can be used for this purpose, the default settings can be set here.', 'filo_text' ) .
			__( ' Select "Document" tab of "WooCommerce / Settings" menu, and choose the appropriate document link to modify default sequence settings ', 'filo_text' ) .
			'<a href="' . add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'document' ), admin_url( 'admin.php' ) ) . '" target="_blank">' . __( 'here', 'filo_text' ) . '</a>' . '.' .		
			$filo_sequence_settings_desc,
	),
	
	array(
		'src'         => FILO()->plugin_url() . '/assets/images/about_install_skins_1010x568_v4.png',		
		'heading'     => __( 'Predefined Document Customizer Skins', 'filo_text'  ),
		'description' =>
			__( 'You can freely customize your Financial Document design.', 'filo_text' ) . 
			__( 'You can install predefined, beautifully designed document skins, that can be used for creating your own design.', 'filo_text' ) .
			__( 'If you install these skins, some demo logo inages will be inserted into your WordPress Media Library.', 'filo_text' ) .
			__( 'Predefined document skins can be installed here, by clicking on the button below.', 'filo_text' ) .
			$filo_install_predefined_customizer_skins_desc,
	),
	
		
);


//show if default seller id is not an integer
$show_default_seller_user_missing = false;
if ( empty( $default_seller_user_id ) ) { 
	$show_default_seller_user_missing = true;			
}
wsl_log(null, 'class-filo-do-setup.php filo-admin-setup-jedi.php $default_seller_user_id: ' . wsl_vartotext($default_seller_user_id));
wsl_log(null, 'class-filo-do-setup.php filo-admin-setup-jedi.php isset( $default_seller_user_id ): ' . wsl_vartotext(isset( $default_seller_user_id )));
wsl_log(null, 'class-filo-do-setup.php filo-admin-setup-jedi.php empty( $default_seller_user_id ): ' . wsl_vartotext(empty( $default_seller_user_id )));
wsl_log(null, 'class-filo-do-setup.php filo-admin-setup-jedi.php is_int( $default_seller_user_id ): ' . wsl_vartotext(is_int( $default_seller_user_id )));

$filo_seller_settings_desc = '';
if ( $show_default_seller_user_missing ) {
	$filo_seller_settings_desc = 
		'<div id="filo_notice" class="update-nag install_notice">' . 
		__( 'Seller has not been set up yet.', 'filo_text' );

	$filo_seller_settings_desc .=		
		'</div>';
}			

//Random order
//shuffle( $major_features );

$minor_features = array(
	array(
		'src'         => FILO()->plugin_url() . '/assets/images/about_icon_009_financials.svg',	
		'heading'     => __( 'Financial Settings', 'filo_text' ),
		'description' => 
			__( 'Set your Financial Settings here.', 'filo_text' ) .
			'<p class="filo-actions">' .
			'<a href="' . add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'financials' ), admin_url( 'admin.php' ) ) .
			'" class="button-primary">' . __( 'Financial Settings', 'filo_text' ) . '</a>' .
			'</p>',
		
	),
	array(
		'src'         => FILO()->plugin_url() . '/assets/images/about_icon_540_documents.svg',	
		'heading'     => __( 'Document Settings', 'filo_text' ),
		'description' => 
			__( 'Set your Document Settings here.', 'filo_text' ) .
			(isset($filo_seller_settings_desc) ? $filo_seller_settings_desc : '') . 
			'<p class="filo-actions">' .
			'<a href="' . add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'document' ), admin_url( 'admin.php' ) ) .
			'" class="button-primary">' . __( 'Document Settings', 'filo_text' ) . '</a>' .
			'</p>',
	),
);

$tech_features = array(
	//array(
	//	'heading'     => __( '...' ),
	//	'description' => __( '...' ),
	//),
);

?>

	<div class="wrap about-wrap filo-about">
		<h1><?php printf( __( 'Welcome to %s %s', 'filo_text' ), FILO_NAME, $major_version ); ?></h1>

		<div class="about-text filo-about-text">
			<?php
				
				if ( $show_setup_notice ) {
					$text = sprintf( __( '%s has not been set up yet, you can setup it here.', 'filo_text' ), FILO_NAME );
				} else {
					$text = sprintf( __( '%s is successfully set.', 'filo_text' ), FILO_NAME );
				}

				echo( __( 'Thank you for installing!', 'filo_text' ) . ' ' . $text );
				
			?>
		</div>

		<h2 class="nav-tab-wrapper">
			<a href="admin.php?page=filo_admin_setup_jedi" class="nav-tab nav-tab-active"><?php _e( 'Setup Jedi' ); ?></a>
			<!-- more tabs goes here -->
		</h2>

		<div class="headline-feature feature-section one-col">
			<div style="display:inline-block; vertical-align:middle; width: 80%;">
				<h2><?php _e( 'Before you start', 'filo_text' ); ?></h2>
					
				<p><?php printf (__( '
					%s is a complex application. Before start, some settings have to be done. 
					The most important settings are the followings. Setup Jedi helps you and adjusts most of the settings. 
					Please click on the red buttons below to tell Setup Jedi to do the settings.
					', 'filo_text' ), FILO_NAME); ?></p>
			</div>
			<div style="display:inline-block; vertical-align:middle">
			    <img src="<?php echo FILO()->plugin_url(); ?>/assets/images/yoda.png" alt="Setup Jedi" height="170" width="135">
			</div>
			
		</div>

		<?php include( FILOFW()->plugin_path() . '/templates/about.php' ); ?>
		 
	</div>
<?php

//include( ABSPATH . 'wp-admin/admin-footer.php' ); //-include wp-admin

return;

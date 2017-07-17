<?php

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Admin_Notices') ) :

/**
 * 
 * FILO_Admin_Notices -> expand class-wc-admin-notices.php
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */
class FILO_Admin_Notices {

	/**
	 * construct
	 */
	public function __construct() {
		
		add_action( 'admin_print_styles', array( $this, 'create_notice' ) );
		
		wsl_log(null, 'class-filo-admin-notices.php __construct: ' . wsl_vartotext(''));
		
	}

	/**
	 * create_notice
	 */
	public function create_notice() {
		
		//decide if show_setup_notice buttons are visible (if there is no any account set or not all sequences are set)
		//show message if settings are not ok
		
		
		$filo_scrict_settings_validaton = get_option('filo_scrict_settings_validaton');
		
		wsl_log(null, 'class-filo-admin-notices.php create_notice $filo_scrict_settings_validaton: ' . wsl_vartotext($filo_scrict_settings_validaton));
		
		if ( isset($filo_scrict_settings_validaton) and $filo_scrict_settings_validaton == 'yes' ) {
			$strickt = true;
		}
		
		$show_setup_notice = ! FILO_Do_Setup::is_filo_settings_ok( $strickt );

		wsl_log(null, 'class-filo-admin-notices.php create_notice $show_setup_notice: ' . wsl_vartotext($show_setup_notice));
		
		//wsl_log(null, 'class-filo-admin-notices.php create_notice $show_setup_notice: ' . wsl_vartotext($show_setup_notice));
		
		
		if ( ( isset($_GET['page']) and $_GET['page'] != 'filo_admin_setup_jedi' or ! isset($_GET['page']) ) and $show_setup_notice and current_user_can('filo_do_setup') ) {
			
			wp_enqueue_style( 'woocommerce-activation', plugins_url(  '/assets/css/activation.css', WC_PLUGIN_FILE ) );
			add_action( 'admin_notices', array( $this, 'show_setup_notice' ) );
			
		}

		/*if (...) {
			add_action( 'admin_notices', array( $this, '..._notice' ) );
		}
		*/
		
		return $show_setup_notice;

	}

	/**
	 * Show the setup notices
	 */
	public function show_setup_notice() {
		
		wsl_log(null, 'class-filo-admin-notices.php show_setup_notice 0: ' . wsl_vartotext(''));

		?>
			<div id="filo_notice" class="update-nag install_notice">
				<p><?php _e( '<strong>Filogy has not been set up yet.</strong>', 'filo_text' ); ?></p>
				
				<p class="submit">
					<a href="<?php echo admin_url( 'admin.php?page=filo_admin_setup_jedi' ); ?>" class="filo-admin-setup button-primary"><?php _e( 'Filogy Setup Jedi', 'filo_text' ); ?></a>
					<a href="<?php echo add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'financials' ), admin_url( 'admin.php' ) ); ?>" class="button-primary"><?php _e( 'Financial Settings', 'filo_text' ); ?></a>
					<a href="<?php echo add_query_arg( array( 'page' => 'wc-settings', 'tab' => 'document' ), admin_url( 'admin.php' ) ); ?>" class="button-primary"><?php _e( 'Document Settings', 'filo_text' ); ?></a>
				</p>
			</div>
		<?php

	}

}

endif;

return new FILO_Admin_Notices();

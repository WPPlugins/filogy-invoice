<?php

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('FILO_Admin_Menus') ) :

/**
 * FILO_Admin_Menus -> expand class-wc-admin-menus.php
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */
class FILO_Admin_Menus {

	/**
	 * construct
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		
		add_action( 'admin_head', array( $this, 'remove_menus' ) );
	}

	/**
	 * admin_menu
	 */
	public function admin_menu() {
		global $menu, $is_filo_settings_ok;

		add_menu_page( __( 'Filogy', 'filo_text' ), __( 'Filogy', 'filo_text' ), 'manage_woocommerce', 'filo_financials', null, null, '55.6' );
		if ( $is_filo_settings_ok ) {
		}
		//add_menu_page( __( 'Financials', 'filo_text' ), __( 'Financials', 'filo_text' ), 'manage_woocommerce', 'filo_financials', null, null, '55.8' );
		
		wsl_log(null, 'class-filo-admin-menus.php admin_menu 0: ' . wsl_vartotext(''));
		
	}

	/**
	 * Remove unneeded menu items (first submenus inside each main menu)
	 */	 
	 
	public function remove_menus() {

		//global $submenu;
		//wsl_log(null, 'class-filo-admin-menus.php remove_menus $submenu: ' . wsl_vartotext($submenu));
		remove_submenu_page( 'filo_financials', 'filo_financials' );
		
	}
	
}

endif;

return new FILO_Admin_Menus();


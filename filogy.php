<?php
/*
Plugin Name: Filogy Invoice
Plugin URI: http://filogy.com/filogy-main/filogy-invoice/
Description: This application do the financial tasks in your WooCommerce webshop.
Version: 1.1.0
Author: WebshopLogic - Peter Rath
Author URI: http://www.webshoplogic.com
Requires at least: 3.8
Tested up to: 4.8

Text Domain: filo_text
Domain Path: /languages/

@package Filogy
@category Financials
 */

$this_version = '1.1.0';
$this_plugin_name = 'Filogy Invoice';
$this_upgrade_url = 'http://webshoplogic.com/';
$this_renew_license_url = 'http://webshoplogic.com/my-account';

//FILO_TYPE:
$this_plugin_type = in_array($this_plugin_name, array('Filogy Invoice (Professional)','Filogy Invoice')) ? 'filo_invoice_type' : 'filo_financial_type';

//FILO_IS_FREE
$this_plugin_is_free = in_array($this_plugin_name, array('Filogy Invoice')) ? true : false;
/*
	Filogy Invoice
	Copyright (C) 2017 Peter Rath - WebshopLogic
	
	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/  

/* 
 * This plugin contains or partly based on the following packages, find below the corresponding copyright and license information for them: 
 * The following packages may contain additional sub packages. These sub packages are attributed inside the packages.
 * 
 * Thanks for the authors!
 * 
 * Credits:
 *  
 */
 /** 
 * WooCommerce
 *
 * Description: An e-commerce toolkit that helps you sell anything. 
 *  
 * @author     WooThemes
 * @copyright  Copyright 2015 by the contributors 
 * @license    GNU General Public License GNU Version 3, 29 June 2007	
 * @link       https://woocommerce.com/
 * 
 * In this plugin there are parts that based on WooCommerce plugin.
 */
  
/**
 * dompdf is an HTML to PDF converter
 * 
 * @author Benj Carson <benjcarson@digitaljunkies.ca>
 * @author Helmut Tischer <htischer@weihenstephan.org>
 * @author Fabien Ménager <fabien.menager@gmail.com>
 * @author Brian Sweeney <eclecticgeek@gmail.com>
 * @license http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @link    http://dompdf.github.com/
 * 
 * subdir: modules/dompdf   
 */

/**
 * Add Logo to Admin plugin v1.6: Upload logo function is made by partly redistribution of Add Logo to Admin plugin v1.6, after modification.
 * 
 * @author     c.bavota <cbavota@gmail.com>
 * @copyright  Copyright 2014 (http://bavotasan.com/2011/add-your-logo-to-the-wordpress-admin-and-login-page/)
 * @license    GPL2, 
 * @link       http://bavotasan.com/2011/add-your-logo-to-the-wordpress-admin-and-login-page/
 * @author_URI http://bavotasan.com
 * 
 */
 
/**
 * jQuery.fontselect - A font selector for the Google Web Fonts api
 * Easy Google Font Selector With jQuery - Fontselect
 * 
 * @author     Tom Moor
 * @copyright  Copyright (c) 2011 Tom Moor
 * @license    MIT Licensed, 
 * @link       https://github.com/tommoor/fontselect-jquery-plugin
 * @link       http://www.jqueryscript.net/text/Easy-Google-Web-Font-Selector-With-jQuery-Fontselect.html
 * @author_URI http://tommoor.com
 * 
 * @version 0.1
 *
 * subdir: modules/fontselect-jquery-plugin
 * 
 */ 
 
/** 
 *
 * sc-color - A color parsing and manipulation library
 *
 * Description: A JavaScript color parsing and manipulation library designed for convenience and flexibility (rather than runtime performance).
 *  
 * @author     Benjamin Cronin
 * @copyright  Copyright (c) 2013-2015, Benjamin Cronin 
 * @license    MIT License (MIT).	
 * @link       http://surfacecurve.org
 * @link       https://bitbucket.org/bcronin/surfacecurve-color.git
 * 
 * subdir: sc-color
 * 
 */

//CONSTANTS
if ( ! defined('FILO_PLUGIN_FILE') ) { //If the user try to activate Filo and Filo Invoice (free) at the same time, double constant declaration would be happaned. This line is eliminate this problem. 
	define( 'FILO_PLUGIN_FILE', __FILE__ );
	define( 'FILO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
	define( 'FILO_VERSION', $this_version );
	define( 'FILO_NAME', $this_plugin_name );
	define( 'FILO_TYPE', $this_plugin_type );
	define( 'FILO_IS_FREE', $this_plugin_is_free );
	define( 'FILO_UPGRADE_URL', $this_upgrade_url );
	define( 'FILO_RENEW_LICENSE_URL', $this_renew_license_url );
}

if ( !defined('ABSPATH') ) exit;

if ( !class_exists('Filogy') ) :

/**
 * Filogy class
 */
final class Filogy {

	public $plugin_name = FILO_NAME;
	public $version = FILO_VERSION;

	public $plugin_text_domain = 'filo_text';

	protected static $_instance = null;
	
	protected $plugin_path;
	protected $plugin_url;
	protected $filogy_framework_path_tag = 'filogy-framework'; // or filogy/filogy-framework-mini

	//array of 'name'=>array( 'min_version'=>'xxx', 'max_version'=>'yyy'); max_version is optional
	public $prerequires_plugins = 
		array(
			'woocommerce/woocommerce.php' => array('min_version'=> '2.6.0', 'max_version'=>null),
		);
		
	/**
	 * instance
	 */
	public static function instance() {
		
		//wsl_log(null, 'filogy.php instance 0: ' .  wsl_vartotext( '' ));
		
		if ( is_null( self::$_instance ) ) {
			
			//wsl_log(null, 'filogy.php instance 1: ' .  wsl_vartotext( '' ));
			
			self::$_instance = new self();
			
			//wsl_log(null, 'filogy-framework.php instance $_COOKIE: ' .  wsl_vartotext($_COOKIE));
		}
		return self::$_instance;
	}

	/**
	 * construct
	 */
	public function __construct() {

		//wsl_log(null, 'filogy.php __construct 0: ' .  wsl_vartotext( '' ));
		
		include_once( 'includes/filo-core-functions.php' );
		
		//these are needed to be able to use the same, unified variable names ($this->version, $this->plugin_name) in the later codes in different plugins  
		$this->version = FILO_VERSION;
		$this->plugin_name = FILO_NAME;

		//If Framework is not installed, then we can use a local copy of the core functions
		//All functions are in "!function_exists" block
		include_once( 'includes/wsl-core-functions.php' );

		//At the and of this file we examine if Filogy and Filogy Free are not activated at the same time.		
				
		//$prerequires_plugin_names = array('woocommerce/woocommerce.php', 'filogy-framework/filogy-framework.php'); //MODIFY RaPe
		//$missing_active_plugins = wsl_chcek_missing_plugins( $prerequires_plugin_names );
		$missing_active_plugins = wsl_chcek_missing_plugins( $this->prerequires_plugins );

		//wsl_log(null, 'filogy.php __construct $missing_active_plugins: ' .  wsl_vartotext($missing_active_plugins));


		//Stop FILO activation if prerequired plugins are not activated		 
		//If one of the prerequired plugin is missing, then we should stop FILO activation, because activation uses these plugins (e.g. FILO_Do_Setup extends WC_Install)
		if ( ! empty($missing_active_plugins) ) {
			//Stop FILO activation
			register_activation_hook( FILO_PLUGIN_FILE, array( $this, 'activation_error_prereq_plugin_needed' ) );
			//add_action( 'activated_plugin', array($this, 'activation_error_prereq_plugin_needed2'), 10, 2); //is not used anymore
		}
		
        //check if prerequired plugins are installed and active
    	if ( ! empty($missing_active_plugins) ) {
    		
    		//add_action( 'init', array( $this, 'init_when_prerequires_plugin_not_active' ), 10 );
    		add_action( 'admin_notices', array( $this, 'init_when_prerequires_plugin_not_active' ), 10 );
			return;
		}

		//check prerequired versions 
		$wrong_plugin_versions = wsl_chcek_prerequired_plugin_versions( $this->prerequires_plugins );
		if ( ! empty($wrong_plugin_versions) ) {
    		add_action( 'admin_notices', array( $this, 'init_when_prerequires_plugin_has_wrong_version' ), 20 );
			return;
		}
		
		//MINIMOD BEGIN - just for mini
		/*	
		// check if filogy plugin is active, if not, then use filogy_mini			
		$plugins_to_check = array(
			'filogy/filogy.php' => array('min_version'=> '1.0.0', 'max_version'=>null), //however filogy is not a prerequired plugin of this framework, but activated framework causes some problem, if using without filogy
		);
		$missing_filogy_plugin = wsl_chcek_missing_plugins( $plugins_to_check );
		
		// Include Filogy Mini if needed
		// if filogy plugin is not active (so missing, thus missing is not empty), then load filogy mini
		if ( ! empty($missing_filogy_plugin) ) {
			include_once( 'filogy-mini/filogy-mini.php' );
		}
		*/
		// check if filogy-framework plugin is active, if not, then use filogy_mini			
		$plugins_to_check = array(
			'filogy-framework/filogy-framework.php' => array('min_version'=> '1.0.0', 'max_version'=>null), //however filogy is not a prerequired plugin of this framework, but activated framework causes some problem, if using without filogy
		);
		$missing_filogy_framework_plugin = wsl_chcek_missing_plugins( $plugins_to_check );
		
		wsl_log(null, 'filogy-invoice-builder.php __construct $missing_filogy_framework_plugin: ' .  wsl_vartotext($missing_filogy_framework_plugin));
		wsl_log(null, 'filogy-invoice-builder.php __construct empty($missing_filogy_framework_plugin): ' .  wsl_vartotext(empty($missing_filogy_framework_plugin)));
	
		// Include Filogy Framework Mini if needed, and if exists
		// if filogy framework plugin is not active (so missing, thus missing is not empty), then load filogy framework mini
		if ( ! empty($missing_filogy_framework_plugin) and in_array('filogy-framework/filogy-framework.php', $missing_filogy_framework_plugin) ) {
			
			if ( file_exists ( WP_PLUGIN_DIR . '/filogy-invoice/filogy-framework-mini/filogy-framework.php' )) { 
				$this->filogy_framework_path_tag = 'filogy-invoice/filogy-framework-mini'; // we have to include the framework parts here instead of the "filogy-framework" directory (in filogy-framework.php)
				include_once( 'filogy-framework-mini/filogy-framework.php' );
				
			} else {
				$message = $this->plugin_name . ': ' . __( 'Filogy Framework Mini is missing. Filogy plugin cannot work without this.', 'filo_text' );
				wsl_show_admin_message($message, true);
				return;
			}
			
		}
		
		//MINIMOD END
			
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}
		
		spl_autoload_register( array( $this, 'autoload' ) );

		register_activation_hook( FILO_PLUGIN_FILE, array( $this, 'filo_activation_needed' ) ); 		
		
		add_action( 'plugins_loaded', array($this, 'plugins_loaded') );
		
		wsl_log(null, 'filogy.php __construct 9: ' .  wsl_vartotext(''));
		
		add_filter( 'customize_loaded_components', 'Filogy::disable_customizer_core_components', 10, 2 );
		
	}

	/**
	 * disable_customizer_core_components
	 * 
	 * If we are customizing a filogy document, then disable the Customizer core components (nav_menus, widgets)
	 * This filter should be call here at the very beginning (before plugins loaded), that is why it is here. 
	 */
	static function disable_customizer_core_components($components, $wp_customize_manager) {
		
		if ( (isset($_GET['filo_usage']) and $_GET['filo_usage'] == 'doc') ) {
			
			if ( is_array($components))
			foreach ($components as $key => $value) {
				unset($components[$key]);
			}
			
		}
		
		return $components;
	}

	//https://wordpress.org/support/topic/pos-not-showing-in-admin
	function plugins_loaded() {
			
		wsl_log(null, 'filogy.php plugins_loaded 0: ' .  wsl_vartotext(''));
		
		//if ( in_array( 'filogy-framework/filogy-framework.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		//	$this->on_filofw_loaded();
		//}

		$prereq_plugins_loaded = true;		
		foreach ( $this->prerequires_plugins as $prerequires_plugin => $version ) {

			//if any of prerequired plugins is not active, then the flag variable will be false
			if ( ! in_array( $prerequires_plugin, apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
				$prereq_plugins_loaded = false;	
			}

		}
					
		//if all the necessary plugins was active, then call the construct_on_prereq_plugins_loaded function
		if ( $prereq_plugins_loaded ) {
			$this->construct_on_prereq_plugins_loaded();
		}
		
	}


	public function construct_on_prereq_plugins_loaded() {
		
		//1
		
		wsl_log(null, 'filogy.php construct_on_prereq_plugins_loaded 0: ' .  wsl_vartotext(''));

		// Chek if another plugin is needed as prerequired plugin
		// If so, then we include it first
		// Other plugin can set filo_prerequired_files option
		
		$filo_prerequired_files = get_option('filo_prerequired_files');
		
		//wsl_log(null, 'filogy-framework.php $filo_prerequired_files: ' .  wsl_vartotext($filo_prerequired_files));
		
		if ( isset($filo_prerequired_files) and is_array($filo_prerequired_files ) )
		foreach ( $filo_prerequired_files as $filo_prerequired_file ) {
			include_once( $filo_prerequired_file );
			wsl_log(null, 'filogy.php include $filo_prerequired_files: ' .  wsl_vartotext($filo_prerequired_file));
		}

		// Check if plugin is disabled (by another plugin)
		// If filo is iself is not enough for issuing legal invoie, becouse another plugin have to be applied to done necessary customisations, then filo can be disabled if that plugin is deactivated
		// Filo can be disabled by that program by setting filo_disable_plugin option. That another plugin can eliminate this blocking of filo using filo_disable_plugin filter, if it is activated. 
		// Deactivating that plugin, filo will be disabled (because of filo_disable_plugin is set and the filter is not eliminate blocking.)
		
		$disable_plugin = get_option('filo_disable_plugin');
		$disable_plugin = apply_filters( 'filo_disable_plugin', $disable_plugin);
		
    	if ( $disable_plugin == 'true' ) {
			return;
		}
		
		//2
				
		//include_once( 'includes/wsl-core-functions.php' );
		include_once( 'includes/class-filo-do-setup.php' );
		//include_once( 'includes/class-filo-customizer-config.php' );
		
		$filo_activation_needed = get_option('filo_activation_needed');
		if ( $filo_activation_needed === 'yes') {
			do_action('filo_activation');
		}

		global $is_filo_settings_ok;
		$is_filo_settings_ok = FILO_Do_Setup::is_filo_settings_ok( $strict = false ); //hide menus (none strict mode, so tehere can be none initialized product to show menus)

		wsl_log(null, 'filogy.php construct_on_prereq_plugins_loaded $is_filo_settings_ok: ' .  wsl_vartotext($is_filo_settings_ok));


		if ( is_admin() ) {
			include_once( 'includes/admin/class-filo-admin.php' );
			//include_once( 'includes/admin/class-filo-admin-fw.php' );
			include_once( 'includes/abstract-filo-metabox.php' );             // Abstract MetaBox
		}

		//move it earlier
		//global $is_filo_settings_ok;
		//$is_filo_settings_ok = FILO_Do_Setup::is_filo_settings_ok( $strict = false ); //hide menus (none strict mode, so tehere can be none initialized product to show menus)

		// Post types
		include_once( 'includes/class-filo-post-types.php' );                     // Registers post types
		include_once( 'templates/documents/filo_register_document_template.php' );                     // Registers post types
		

		// Include abstract classes
		//include_once( 'includes/abstracts/abstract-filo-order.php' );             // Orders	
			
		//if ( isset($_GET['filo_usage']) and $_GET['filo_usage'] == 'doc' ) {
		include_once( 'includes/customize/class-filo-customize-manager.php' );             // Orders
		//}
					
		
		wsl_log(null, 'filogy.php construct_on_prereq_plugins_loaded: ' .  wsl_vartotext(''));			

		//ACTIONS, FILTERS
		add_action( 'init', array( $this, 'init' ), 10 );

		//Modify php-reports config
		//add_filter( 'filo_php_reports_config', array( $this, 'filo_php_reports_config') );

		// Loaded action
		do_action( 'filogy_loaded' );
		
	}

	/**
	 * init
	 */
	public function init() {
		global $is_filo_settings_ok;
		
		load_plugin_textdomain( 'filo_text', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
		if ( $is_filo_settings_ok ) {
			$this->order_factory     = new FILO_Order_Factory();                     // Order Factory to create new order instances
		}
		
		$this->initial_functions = new FILO_Initial_Functions();                 // Initial Functions
		
		do_action('filogy_after_initial_functions');
		$this->my_account        = new FILO_Myaccount();                         // My Account		
		//$this->initial_functions = new FILO_Initial_Functions();                 // Initial Functions
		
		$wsl_helper_options = get_option('webshoplogic_helper', array());
		$enable_logging = isset($wsl_helper_options['enable_logging']) ? $wsl_helper_options['enable_logging'] : null;
		wsl_log(null, 'filogy.php $wsl_helper_options: ' .  wsl_vartotext($wsl_helper_options));
		wsl_log(null, 'filogy.php $enable_logging: ' .  wsl_vartotext($enable_logging));
		if ( $enable_logging == '1' ) {
			wsl_log(null, 'filogy.php $enable_logging XXX: ' .  wsl_vartotext(''));
		}
		
	}

	/**
	 * plugin_path
	 */
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;
		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * plugin_url
	 */
	public function plugin_url() {
		if ( $this->plugin_url ) return $this->plugin_url;
		return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * template_path
	 */
	public function template_path() {
		
		$template_path = 'filogy/';
		
		//in case of Filogy Invoice Free, we have the specific 'filogy-invoice/' path 
		if ( FILO_TYPE  == 'filo_invoice_type' and FILO_IS_FREE ) {
			$template_path = 'filogy-invoice/';
		}

		return apply_filters( 'filo_template_path', $template_path );
	}

	/**
	 * filogy_framework_path_tag
	 */
	public function filogy_framework_path_tag() {
		return $this->filogy_framework_path_tag;
	}
	
	/**
	 * Autoload FILO plugin classes, by calling autoload function of framework
	 */
	public function autoload( $class ) {
	
		//call the framework autoload function, pass this plugin path (....wp-content\plugins\filogy) to include from here the classes
		FILOFW()->autoload( $class, self::plugin_path() );
		
	}

	/**
	 * Document Class. //ADD RaPe
	 *
	 * @return FILO_Documents
	 */	 
	public function documenter() { 
		return FILO_Documents::instance(); 
	}
	
	/**
	 * FILO activation necessary
	 * 
	 * First we just mark in an option that activation needed, then later we call the activation according to this option
	 * This is necessary because WP activation is called too early, and before it some mandarory function had not finished.
	 * 
	 * The option is set here, and this will be cleared after the activation happaned.
	 */
	public function filo_activation_needed() {
		update_option( 'filo_activation_needed', 'yes' );
	}
	
	/**
	 * This function throws an exeption during FILO activation if one of the prerequired plugin is not activated
	 */
	public function activation_error_prereq_plugin_needed() {
		
		//The format style hides h1, that contains "Exception thrown" text written very larg size
		$format = '<style> h1{display: none; font-size: 1em; font-weight: normal;} </style>';
		
		$message = FILO_NAME . ' - ' . __( 'Please activate WooCommerce fist, then "Filogy Framework", then "Filogy", because Filogy needs this prerequired plugin. ', 'filo_text' ) . $format;
		wsl_log(null, 'filogy.php activation_error: ' .  wsl_vartotext($message));
		//throw new Exception( $message, 400 );
		wsl_trigger_activation_error( $message, E_USER_ERROR );
	}

	/**
	 * This function throws an exeption during FILO activation if one of the prerequired plugin is not activated
	 * 
	 * This was another way, for handling plugin activation errors. Right after activation, we deactivate the plugin. 
	 * We do not use, becouse on the admin pager the "Plugin activated." message is displayed if the plugin was deactivated during activation, and this message cannot be changed.
	 * This was called by this: add_action( 'activated_plugin', array($this, 'activation_error_prereq_plugin_needed2'), 10, 2);
	 */
	/*public function activation_error_prereq_plugin_needed2($plugin, $network_wide) {
		
		
		$message = FILO_NAME . ' - ' . __( 'Filogy needs prerequired plugins. One or more of them is not activated. Please activate WooCommerce fist, then "Financial Logic Framework", then "Financial Logic".', 'filo_text' ) . $format;
		wsl_log(null, 'filogy.php activation_error2: ' .  wsl_vartotext($message));
		deactivate_plugins( $plugin, $silent = false, $network_wide );
		
	}*/
	
	/**
	 * init_when_prerequires_plugin_not_active
	 */
	public function init_when_prerequires_plugin_not_active() {
		
		//Just show an error message, e.g. 	 
    	//showAdminMessage(__('Filogy Framework plugin is based on WooCommerce plugin. Please install and activate <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce - excelling eCommerce</a> first.','filofw_text'), true);

		$missing_active_plugins = wsl_chcek_missing_plugins( $this->prerequires_plugins );

		//change technical plugin names to display names
		foreach ( $missing_active_plugins as $key => $missing_active_plugin ) {
			$missing_active_plugins[$key] = $this->plugin_display_name( $missing_active_plugin );
		}
    	    
    	$message = sprintf( __( '%s plugin depends on the following plugins, which is not installed or activated. Please install and activate:', $this->plugin_text_domain ), $this->plugin_name); 
	
		//display all of missing plugins in a list
		foreach ($missing_active_plugins as $missing_active_plugin) {
			$message .= '<li>' . $missing_active_plugin . '</li>'; 
		}
		
    	wsl_show_admin_message($message, true);
		//showAdminMessage(__('Financial Logic Framework plugin is based on WooCommerce plugin. Please install and activate <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce - excelling eCommerce</a> first.','filofw_text'), true);
		
	}	

	/**
	 * init_when_prerequires_plugin_has_wrong_version
	 */
	public function init_when_prerequires_plugin_has_wrong_version() {

		$wrong_plugin_versions = wsl_chcek_prerequired_plugin_versions( $this->prerequires_plugins );

		//change technical plugin names to display names
		foreach ( $wrong_plugin_versions as $key => $wrong_plugin_version ) {
			$wrong_plugin_versions[$key]['name'] = $this->plugin_display_name( $wrong_plugin_version['name'] );
			$wrong_plugin_versions[$key]['min_version'] = empty( $wrong_plugin_version['min_version'] ) ? __('not limited', $this->plugin_text_domain) :  $wrong_plugin_version['min_version'];
			$wrong_plugin_versions[$key]['max_version'] = empty( $wrong_plugin_version['max_version'] ) ? __('not limited', $this->plugin_text_domain) :  $wrong_plugin_version['max_version'];
		}
		
		$message = sprintf( __( '%s plugin depends on the following plugins, that version is insufficient:', $this->plugin_text_domain ), $this->plugin_name); 
	
		//display all of missing plugins in a list
		foreach ( $wrong_plugin_versions as $key => $wrong_plugin_version ) {
			$message .= 
			      '<li>'
				. $wrong_plugin_version['name']
				.  __( ' installed version: ',$this->plugin_text_domain) . $wrong_plugin_version['actual_version']
				.  __( ', minimum version: ',$this->plugin_text_domain) . $wrong_plugin_version['min_version']
				.  __( ', maximum version: ',$this->plugin_text_domain) . $wrong_plugin_version['max_version']
				. '</li>'; 
		}
		
    	wsl_show_admin_message($message, true);
		
	}

	/**
	 * plugin_display_name
	 */
	public function plugin_display_name( $plugin_name ) {


		switch ( $plugin_name ) {
			case 'woocommerce/woocommerce.php':
				$plugin_display_name = __('<a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce - excelling eCommerce</a>', $this->plugin_text_domain);
				break;
			
			case 'filogy-framework/filogy-framework.php':
				//@todo RaPe: modify url
				$plugin_display_name = __('<a href="http://webshoplogic.com/" target="_blank">Filogy Framework</a>', $this->plugin_text_domain);					
				break;
				
			case 'filogy/filogy.php':
			case 'filogy-invoice/filogy.php':
				//@todo RaPe: modify url
				$plugin_display_name = __('<a href="http://webshoplogic.com/" target="_blank">Filogy</a>', $this->plugin_text_domain);					
				break;
				
			default:
				$plugin_display_name = $plugin_name;
				
		}			

		return apply_filters( 'filo_plugin_display_name', $plugin_display_name, $plugin_name );
			
	}

	/**
	 * Modify php-reports config
	 */
/*	 
	public function filo_php_reports_config($config) { 

		$plugin_path = PhpReportsForWPCore()->plugin_path(); //RaPe Modify
		$wp_admin_url = PhpReportsForWPCore()->wp_admin_url(); //RaPe Modify wp_admin_url();

		$config['reportDir'] = FILO()->plugin_path() . '/report-spec';		
		
		return $config;

	}
*/
}

endif;


//BEGIN: PREVENT ACTIVATION OF FILOGY INVOICE FREE AND OTHER FILOGY PLUGIN AT THE SAME TIME

//Every filogy plugin (filogy_finance, filogy_invoice) base file is filogy/filogy.php, 
//except filogy invoice free, because it is in the WordPress plugin directory, and it's nema is Filogy Invoice, this it's base file must be filogy-invoice/filogy.php
//filogy/filogy and filogy-invoice/filogy must not activated at the same time, thus if the other has already been activated, then the other must not be activated.
//Stop filogy or filogy_invoice (free) activation if the other is active

include_once( 'includes/wsl-core-functions.php' );

if ( ! function_exists ( 'filo_activation_error_other_filogy_is_active' ) ) {
	/**
	 * This function throws an exeption during FILO activation if filogy/filogy.php and filogy_invoice/filogy.php are both active 
	 */
	function filo_activation_error_other_filogy_is_active() {

		//The format style hides h1, that contains "Exception thrown" text written very larg size
		$format = '<style> h1{display: none; font-size: 1em; font-weight: normal;} </style>';

		$message = __( 'Filogy Invoice (Free) and other Filogy plugins cannot be activated at the same time. Deactivate the currently active Filogy plugin before activation of this plugin. ', 'filo_text' ) . $format;
		
		wsl_log(null, 'filogy.php activation_error: ' .  wsl_vartotext($message));
		//throw new Exception( $message, 400 );
		wsl_trigger_activation_error( $message, E_USER_ERROR );
	}
}


$plugin_basename = plugin_basename( __FILE__ );

//wsl_log(null, 'filogy.php $plugin_basename 1: ' .  wsl_vartotext($plugin_basename));
//wsl_log(null, 'filogy.php wsl_chcek_if_plugin_active( filogy/filogy.php ) 1: ' .  wsl_vartotext(wsl_chcek_if_plugin_active( 'filogy/filogy.php' )));
//wsl_log(null, 'filogy.php wsl_chcek_if_plugin_active( filogy-invoice/filogy.php ) 1: ' .  wsl_vartotext(wsl_chcek_if_plugin_active( 'filogy-invoice/filogy.php' )));

if ( 
	( $plugin_basename == 'filogy-invoice/filogy.php' and wsl_chcek_if_plugin_active( 'filogy/filogy.php' ) ) or
	( $plugin_basename == 'filogy/filogy.php' and wsl_chcek_if_plugin_active( 'filogy-invoice/filogy.php' ) )
	) {
	//Stop FILO activation
	register_activation_hook( __FILE__, 'filo_activation_error_other_filogy_is_active' );
	wsl_log(null, 'filogy.php register_activation_hook filo_activation_error_other_filogy_is_active 1: ' .  wsl_vartotext($plugin_basename));
}

//END: PREVENT ACTIVATION OF FILOGY INVOICE FREE AND OTHER FILOGY PLUGIN AT THE SAME TIME


if ( ! function_exists ( 'FILO' ) ) { //If the user try to activate Filo and Filo Invoice (free) at the same time, double FILO instantiation would be happaned. This line is eliminate this problem.
	
	function FILO() {
		return Filogy::instance();
	}
	
	$GLOBALS['filogy'] = FILO();
	
}



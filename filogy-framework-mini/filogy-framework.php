<?php
/*
Plugin Name: Filogy Framework (Mini)
Plugin URI: http://filogy.com/filogy-framework/
Description: This framework ensures the base functionality of a financial application.
Version: 1.1.0
Author: WebshopLogic - Peter Rath
Author URI: http://www.webshoplogic.com
Requires at least: 3.8
Tested up to: 4.7.2

Text Domain: filofw_text
Domain Path: /languages/

@package Filogy
@category Core
@author WebshopLogic
*/

$this_version = '1.1.0';
$this_plugin_name = 'Filogy Framework (Mini)';
$this_upgrade_url = 'http://webshoplogic.com/';
$this_renew_license_url = 'http://webshoplogic.com/my-account';

 
  
//CONSTANTS
define( 'FILOFW_PLUGIN_FILE', __FILE__ );
define( 'FILOFW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'FILOFW_VERSION', $this_version );
define( 'FILOFW_NAME', $this_plugin_name );
define( 'FILOFW_UPGRADE_URL', $this_upgrade_url );
define( 'FILOFW_RENEW_LICENSE_URL', $this_renew_license_url );
  
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Filogy_Framework' ) ) :

/**
 * Filogy_Framework class
 */
final class Filogy_Framework {

	public $version = FILOFW_VERSION;
	public $plugin_name = FILOFW_NAME;

	public $plugin_text_domain = 'filofw_text';	

	protected static $_instance = null;
	
	//array of 'name'=>array( 'min_version'=>'xxx', 'max_version'=>'yyy'); max_version is optional
	public $prerequires_plugins = 
		array(
			'woocommerce/woocommerce.php' => array('min_version'=> '2.6.0', 'max_version'=>null),
			//'filogy/filogy.php' => array('min_version'=> '1.0.0', 'max_version'=>null), //however filogy is not a prerequired plugin of this framework, but activated framework causes some problem, if using without filogy
		);

	/**
	 * instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.1
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.1
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'woocommerce' ), '2.1' );
	}

	/**
	 * Constructor.
	 * @access public
	 * @return WooCommerceFinancials
	 */
	public function __construct() {

		// Define constants
		//$this->define_constants();

		//moved to wsl-core-functions.php -> WSL_LOG_DIR
		//if ( ! defined( 'FILO_LOG_DIR' ) ) {
		//	define( 'FILO_LOG_DIR', ABSPATH . 'wp-content/filo-data/logs/' );
		//}

		//from 'filogy-framework' or 'filogy/filogy-framework-mini'
		if ( strpos( dirname( __FILE__ ), 'filogy-framework-mini' ) === false ) {
			$path_tag = 'filogy-framework';
		} else {
			$path_tag = 'filogy-invoice/filogy-framework-mini';
		} 
		//include_once( ABSPATH . 'wp-content/plugins/' . $path_tag . '/includes/wsl-core-functions.php' );
		include_once( WP_PLUGIN_DIR . '/' . $path_tag . '/includes/filo-core-functions-fw.php' );
		include_once( WP_PLUGIN_DIR . '/' . $path_tag . '/includes/wsl-core-functions.php' );

		//If Framework is not installed, then we can use a local copy of the core functions
		//All functions are in "!function_exists" block
		//include_once( 'includes/wsl-core-functions.php' );
		
		//$prerequires_plugin_names = array('woocommerce/woocommerce.php'); //MODIFY RaPe
		//$missing_active_plugins = wsl_chcek_missing_plugins( $prerequires_plugin_names );
		$missing_active_plugins = wsl_chcek_missing_plugins( $this->prerequires_plugins );
		
        //check if prerequired plugins are installed and active
    	if ( ! empty($missing_active_plugins) ) {
    		add_action( 'admin_notices', array( $this, 'init_when_prerequires_plugin_not_active' ), 10 );
			return;
		}

		//check if prerequired versions
		$wrong_plugin_versions = wsl_chcek_prerequired_plugin_versions( $this->prerequires_plugins );
		if ( ! empty($wrong_plugin_versions) ) {
    		add_action( 'admin_notices', array( $this, 'init_when_prerequires_plugin_has_wrong_version' ), 20 );
			return;
		}
				
		// Auto-load classes on demand
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}
		
		//wsl_log(null, 'filogy-framework.php CSS add action: ' . wsl_vartotext(FILOFW()->plugin_url() . '/templates/documents/css/document_styles.css'));		

		spl_autoload_register( array( $this, 'autoload' ) );

		add_action( 'plugins_loaded', array($this, 'plugins_loaded') );
	
	}


	//https://wordpress.org/support/topic/pos-not-showing-in-admin
	function plugins_loaded() {

		// Include abstract classes
		include_once( 'includes/abstracts/abstract-filo-order.php' );             // Orders

		//if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		//	$this->on_prereq_plugins_loaded();
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
		
		// Chek if another plugin is needed as prerequired plugin
		// If so, then we include it first
		// Other plugin can set filo_prerequired_files option
		
		$filo_prerequired_files = get_option('filo_prerequired_files');
		
		//wsl_log(null, 'filogy-framework.php $filo_prerequired_files: ' .  wsl_vartotext($filo_prerequired_files));
		
		if ( isset($filo_prerequired_files) and is_array($filo_prerequired_files ) )
		foreach ( $filo_prerequired_files as $filo_prerequired_file ) {
			include_once( $filo_prerequired_file );
			wsl_log(null, 'filogy-framework.php include $filo_prerequired_files: ' .  wsl_vartotext($filo_prerequired_file));
		}
	
		
		// Check if plugin is disabled
		// If filo is iself is not enough for issuing legal invoie, becouse another plugin have to be applied to done necessary customisations, then filo can be disabled if that plugin is deactivated
		// Filo can be disabled by that program by setting filo_disable_plugin option. That another plugin can eliminate this blocking of filo using filo_disable_plugin filter, if it is activated. 
		// Deactivating that plugin, filo will be disabled (because of filo_disable_plugin is set and the filter is not eliminate blocking.)
		
		$disable_plugin = get_option('filo_disable_plugin');
		$disable_plugin = apply_filters( 'filo_disable_plugin', $disable_plugin);
		
    	if ( $disable_plugin == 'true' ) {
			return;
		}

		//2
		
		// Include required files
		$this->includes(); 
		
		//add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ), 10 );
		
		add_action( 'init', array( $this, 'init' ), 10 );

		// Loaded action
		do_action( 'filogy_framework_loaded' );
		
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	 
	/**
	 * Autoload FILOFW and other FILO plugin classes
	 * 
	 * According to class and directory name conventions this procedure defines the path and file name 
	 * from the class name, and includes it.
	 * There is two types, one is the subdirectories of includes directory, the other is includes directory itself.
	 */
	public function autoload( $class, $plugin_path = null ) {
		//external caller (e.g filogy plugin, so not the framework) can call this framework autoload function, pass they own plugin path (e.g. ....wp-content\plugins\filogy) to include from there the classes
		
		if ( $plugin_path == null ) {
			$plugin_path = self::plugin_path();
		}
		
		//from 'filogy-framework' or 'filogy/filogy-framework-mini'
		if ( strpos( dirname( __FILE__ ), 'filogy-framework-mini' ) === false ) {
			$path_tag = 'filogy-framework';
		} else {
			$path_tag = 'filogy-invoice/filogy-framework-mini';
		} 
		//include_once( ABSPATH . 'wp-content/plugins/' . $path_tag . '/includes/wsl-core-functions.php' );
		include_once( WP_PLUGIN_DIR . '/' . $path_tag . '/includes/wsl-core-functions.php' );
		//wsl_log(null, 'filogy-framework.php AUTOLOAD $class: ' . wsl_vartotext($class));
		//wsl_log(null, 'filogy-framework.php AUTOLOAD $plugin_path: ' . wsl_vartotext($plugin_path));	

		$path  = null;
		$class = strtolower( $class );
		$file = 'class-' . str_replace( '_', '-', $class ) . '.php';
		
		//wsl_log(null, 'Autoload $file: ' . wsl_vartotext($file));			

		// 1. subdirectories if include dir
		if ( strpos( $class, 'filo_meta_box' ) === 0 ) {
			$path = $plugin_path . '/includes/admin/meta-boxes/';
		} elseif ( $class == 'wc_admin_report' ) { //include class-wc-admin-report.php if it is needed for FILO
			$path = WC()->plugin_path() . '/includes/admin/reports/';  
		} elseif ( strpos( $class, 'filo_admin_report' ) === 0 ) {
			$path = $plugin_path . '/includes/admin/reports/';
		} elseif ( strpos( $class, 'filo_admin' ) === 0 ) {
			$path = $plugin_path . '/includes/admin/';
		} elseif ( strpos( $class, 'filo_customize' ) === 0 ) {
			$path = $plugin_path . '/includes/customize/';
		} elseif ( strpos( $class, 'filo_finadoc' ) === 0 ) {
			$path = $plugin_path . '/includes/financial-documents/';
		} elseif ( strpos( $class, 'filo_widget' ) === 0 ) { //siteorigin style widgets
			$path = $plugin_path . '/includes/widgets/' . str_replace( '_', '-', $class ) . '/';
			$file = str_replace( '_', '-', $class ) . '.php';  //overwrite $file variable without 'class-' prefix
		}
			
		//wsl_log(null, 'filogy-framework.php AUTOLOAD $path . $file: ' . wsl_vartotext($path . '  ' . $file));
		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}

		// 2. include dir
		// Fallback
		if ( strpos( $class, 'filo_' ) === 0 ) {
			$path = $plugin_path . '/includes/';
		}


		//wsl_log(null, 'filogy-framework.php AUTOLOAD 2 $path . $file: ' . wsl_vartotext($path . '  ' . $file));
		if ( $path && is_readable( $path . $file ) ) {
			include_once( $path . $file );
			return;
		}			

	}
		

	/**
	 * Define WC Constants
	 */
	/*private function define_constants() {
		define( 'FILOFW_PLUGIN_FILE', __FILE__ );
		define( 'FILOFW_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'FILOFW_VERSION', $this->version );
		
	}*/

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {

		//WooCommerce has to be included first, it is a prerequisite plugin
		if ( strpos( dirname( __FILE__ ), 'filogy-framework-mini' ) === false ) {
			include_once(dirname( __FILE__ ) . '/../woocommerce/woocommerce.php'); //ToDo RaPe
		} else {
			include_once(dirname( __FILE__ ) . '/../../woocommerce/woocommerce.php'); //ToDo RaPe
		}
		
		//include php-reports-wp main file
		//include_once( 'php-reports-wp.php' );
		
		include_once( 'includes/wsl-core-functions.php' );

		if ( is_admin() ) {
			//include_once( 'includes/admin/class-filo-admin.php' );
			include_once( 'includes/admin/class-filo-admin-fw.php' );
			//include_once( 'includes/abstracts/abstract-filo-metabox.php' );             // Abstract MetaBox
		}

		if ( defined( 'DOING_AJAX' ) ) {
			$this->ajax_includes();
		}

		// Post types
		//include_once( 'includes/class-filo-post-types.php' );                     // Registers post types

		// Include abstract classes
		//include_once( 'includes/abstracts/abstract-filo-order.php' );             // Orders
		
		//if ( isset($_GET['filo_usage']) and $_GET['filo_usage'] == 'doc' ) {
		//	include_once( 'includes/class-filo-customize-manager.php' );             // Orders
		//}
			
	}

	/**
	 * Include required ajax files.
	 */
	public function ajax_includes() {
		//include filo own or MODIFIED ajax class
		include_once( 'includes/class-filo-ajax.php' );                           // Ajax functions for admin and the front-end
	}


	/**
	 * Include core widgets
	 */
	public function include_widgets() {

	}

	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		do_action( 'before_filogy_framework_init' );

		// Set up localisation
		$this->load_plugin_textdomain();

		// Load class instances
		//$this->order_factory     = new FILO_Order_Factory();                     // Order Factory to create new order instances
		$this->countries         = new FILO_Countries();                         // Countries class
		//$this->general_functions = new FILO_General_Functions();                 // General Functions
		//$this->initial_functions = new FILO_Initial_Functions();                // Initial Functions
		//$this->my_account        = new FILO_Myaccount();                         // My Account


		//$doc_actions  = new FILO_Admin_Doc_Actions();

		$filo_programnames = array(
			'filo_program_sortname' => 'FILO',
			'filo_program_name' 	=> 'Filogy',
			'filo_program_fullname' => 'Filogy for WooCommerce',
		);
		
		$filo_programnames = apply_filters( 'filo_program_names', $filo_programnames );
				
		define( 'FILO_PROGRAM_SORTNAME', $filo_programnames['filo_program_sortname'] );
		define( 'FILO_PROGRAM_NAME', $filo_programnames['filo_program_name'] );
		define( 'FILO_PROGRAM_FULLNAME', $filo_programnames['filo_program_fullname'] );
		
		// Init action
		do_action( 'filogy_framework_init' );

	}

	/**
	 * Load Localisation files.
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( 'filofw_text', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}


	/** Helper functions */

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		
		wsl_log(null, 'filogy-framework.php URL: ' . wsl_vartotext( untrailingslashit( plugins_url( '/', __FILE__ ) ) ));
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
		
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'filo_template_path', 'filogy-framework/', $this );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * init_when_prerequires_plugin_not_active
	 */
	public function init_when_prerequires_plugin_not_active() {
		
		//Just show an error message. 	 
    	//showAdminMessage(__('Filogy Framework plugin is based on WooCommerce plugin. Please install and activate <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce - excelling eCommerce</a> first.','filofw_text'), true);

		$missing_active_plugins = wsl_chcek_missing_plugins( $this->prerequires_plugins );

		//change technical plugin names to display names
		foreach ( $missing_active_plugins as $key => $missing_active_plugin ) {
			$missing_active_plugins[$key] = $this->plugin_display_name( $missing_active_plugin );
		}
    	    
    	$message = sprintf( __( '%s plugin is depends on the following plugins, which are not installed or activated. Please install and activate:', $this->plugin_text_domain ), $this->plugin_name); 
	
		//display all of missing plugins in a list
		foreach ($missing_active_plugins as $missing_active_plugin) {
			$message .= '<li>' . $missing_active_plugin . '</li>'; 
		}
		
    	wsl_show_admin_message($message, true);
		//showAdminMessage(__('Filogy Framework plugin is based on WooCommerce plugin. Please install and activate <a href="http://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce - excelling eCommerce</a> first.','filofw_text'), true);
		
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
		
		$message = sprintf( __( '%s plugin is depends on the following plugins, that version is insufficient:', $this->plugin_text_domain ), $this->plugin_name); 
	
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

}

endif;

/**
 * Returns the main instance of FILO to prevent the need to use globals.
 *
 * @return WooCommerceFinancials
 */
function FILOFW() {
	return Filogy_Framework::instance();
}

$GLOBALS['filogy_framework'] = FILOFW();
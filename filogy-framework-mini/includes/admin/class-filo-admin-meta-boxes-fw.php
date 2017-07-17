<?php
if ( !defined('ABSPATH') ) exit;

/**
 * FILO_Admin_Menus -> extends class-wc-admin-menus.php
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */ 
class FILO_Admin_Meta_Boxes_FW extends WC_Admin_Meta_Boxes {

	static $meta_box_errors = array(); //inherit
	
	/**
	 * Add an error message
	 * FILO_Admin_Meta_Boxes::add_error(...) is not saved automatically, FILO_Admin_Meta_Boxes::save_errors() should be called 
	 * (WC_Admin_Meta_Boxes::add_error(...) is saved automatically)
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option
	 */
	public function save_errors() {
		update_option( 'filo_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * output_errors
	 */
	public function output_errors() {
		
		wsl_log(null, 'class-filo-admin-meta-boxes.php output_errors 0: ' . wsl_vartotext( '' ));
		
		$errors = maybe_unserialize( get_option( 'filo_meta_box_errors' ) );
		
		wsl_log(null, 'class-filo-admin-meta-boxes.php output_errors $errors: ' . wsl_vartotext($errors));

		if ( ! empty( $errors ) ) {

			echo '<div id="woocommerce_errors" class="error fade">';
			foreach ( $errors as $error ) {
				echo '<p>' . esc_html( $error ) . '</p>';
			}
			echo '</div>';

			// Clear
			delete_option( 'filo_meta_box_errors' );
		}
	}

}

new FILO_Admin_Meta_Boxes_FW();
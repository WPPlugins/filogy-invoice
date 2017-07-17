<?php
/**
 * FILO Customize Setting class.
 *
 * Extend WordPress Customize Setting classes
 * to handle filo_css_property, filo_css_selector, filo_css_wrapper and filo_css_measurement_unit
 * We also handle that always the actual skin options be used when the customizer setting controls are displayed (get_root_value).

 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Setting extends WP_Customize_Setting {
	
	public $filo_css_property = '';
	public $filo_css_selector = '';
	public $filo_css_wrapper = '';
	public $filo_css_measurement_unit = ''; // (e.g. px or %)
	
	/**
	 * The original method of WP_Customize_Setting parent class is overridden here,
	 * for get the options of active skin dinamically, 
	 * to be able to set these values in customizer fields 
	 * when the customizer is opened/refreshed.
	 */
	protected function get_root_value( $default = null ) {
		
		return FILO_Customize_Manager::get_root_value( $default );

	}
		
}
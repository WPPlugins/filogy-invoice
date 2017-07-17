<?php

/**
 * WooCommerce countries -> MODIFY of class-wc-countries.php
 *
 * @package     Filogy/Classes
 * @subpackage 	Framework
 * @author      WebshopLogic - Peter Rath
 * @author 		WooThemes (original file)
 * @category    Class
 * 
 * @based_on	class-wc-countries.php file in WooCommerce plugin by WooThemes 
 */
class FILO_Countries {

	/**
	 * filo_localisation_address_formats_namediv
	 * 
	 * put name part of addresses in <div class=\"filo_address_name filo_h4\"> </div> tags
	 */
	function filo_localisation_address_formats_namediv($formats) {

		//wsl_log(null, 'class-filo-countries.php filo_localisation_address_formats_namediv $formats 0: ' . wsl_vartotext( $formats ));

		// Common formats
		//$postcode_before_city = "<div class=\"filo_address_name filo_h4\">' . '{company}\n{name}</div>\n{address_1}\n{address_2}\n{postcode} {city}\n{country}";
		$postcode_before_city = "<div class=\"filo_address_name filo_h4\">{company}\n{name}</div>\n{address_1}\n{address_2}\n{postcode} {city}\n{country}";

		$formats = apply_filters('filo_localisation_address_formats', array(
				'default' => "<div class=\"filo_address_name filo_h4\">{name}\n{company}</div>\n{address_1}\n{address_2}\n{city}\n{state}\n{postcode}\n{country}",
				'AU' => "<div class=\"filo_address_name filo_h4\">{name}\n{company}</div>\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
				'AT' => $postcode_before_city,
				'BE' => $postcode_before_city,
				'CA' => "<div class=\"filo_address_name filo_h4\">{company}\n{name}</div>\n{address_1}\n{address_2}\n{city} {state} {postcode}\n{country}",
				'CH' => $postcode_before_city,
				'CN' => "{country} {postcode}\n{state}, {city}, {address_2}, {address_1}\n<div class=\"filo_address_name filo_h4\">{company}\n{name}</div>",
				'CZ' => $postcode_before_city,
				'DE' => $postcode_before_city,
				'EE' => $postcode_before_city,
				'FI' => $postcode_before_city,
				'DK' => $postcode_before_city,
				'FR' => "<div class=\"filo_address_name filo_h4\">{company}\n{name}</div>\n{address_1}\n{address_2}\n{postcode} {city_upper}\n{country}",
				'HK' => "<div class=\"filo_address_name filo_h4\">{company}\n{first_name} {last_name_upper}</div>\n{address_1}\n{address_2}\n{city_upper}\n{state_upper}\n{country}",
				'HU' => "<div class=\"filo_address_name filo_h4\">{name}\n{company}</div>\n{city}\n{address_1}\n{address_2}\n{postcode}\n{country}",
				'IS' => $postcode_before_city,
				'IT' => "<div class=\"filo_address_name filo_h4\">{company}\n{name}</div>\n{address_1}\n{address_2}\n{postcode}\n{city}\n{state_upper}\n{country}",
				'JP' => "{postcode}\n{state}{city}{address_1}\n{address_2}\n<div class=\"filo_address_name filo_h4\">{company}\n{last_name} {first_name}</div>\n {country}",
				'TW' => "{postcode}\n{city}{address_2}\n{address_1}\n<div class=\"filo_address_name filo_h4\">{company}\n{last_name} {first_name}</div>\n {country}",
				'LI' => $postcode_before_city,
				'NL' => $postcode_before_city,
				'NZ' => "<div class=\"filo_address_name filo_h4\">{name}\n{company}</div>\n{address_1}\n{address_2}\n{city} {postcode}\n{country}",
				'NO' => $postcode_before_city,
				'PL' => $postcode_before_city,
				'SK' => $postcode_before_city,
				'SI' => $postcode_before_city,
				'ES' => "<div class=\"filo_address_name filo_h4\">{name}\n{company}</div>\n{address_1}\n{address_2}\n{postcode} {city}\n{state}\n{country}",
				'SE' => $postcode_before_city,
				'TR' => "<div class=\"filo_address_name filo_h4\">{name}\n{company}</div>\n{address_1}\n{address_2}\n{postcode} {city} {state}\n{country}",
				'US' => "<div class=\"filo_address_name filo_h4\">{name}\n{company}</div>\n{address_1}\n{address_2}\n{city}, {state} {postcode}\n{country}",
				'VN' => "<div class=\"filo_address_name filo_h4\">{name}\n{company}</div>\n{address_1}\n{city}\n{country}",
			));
			
		wsl_log(null, 'class-filo-countries.php filo_localisation_address_formats_namediv $formats: ' . wsl_vartotext( $formats ));
					
		return $formats;
	}

}

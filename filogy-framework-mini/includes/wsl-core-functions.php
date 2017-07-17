<?php
/**
 * General core functions available on both the front-end and admin.
 * 
 * All functions are in "!function_exists" block
 *
 * @package     Filogy/Functions
 * @subpackage 	Framework
 * @category    Functions
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'WSL_DATA_DIR' ) ) {
	//define( 'WSL_DATA_DIR', ABSPATH . 'wp-content/wsl-data/' );
	define( 'WSL_DATA_DIR', WP_CONTENT_DIR . '/wsl-data/' );
}


if ( ! defined( 'WSL_LOG_DIR' ) ) {
	//define( 'WSL_LOG_DIR', ABSPATH . 'wp-content/wsl-data/logs/' );
	define( 'WSL_LOG_DIR', WP_CONTENT_DIR . '/wsl-data/logs/' );
	
}

if ( ! defined( 'WSL_PLUGINS_DIR' ) ) {
	//define( 'WSL_PLUGINS_DIR', ABSPATH . 'wp-content/plugins/' );
	define( 'WSL_PLUGINS_DIR', WP_PLUGIN_DIR  . '/' );
}

if ( ! function_exists ( 'wsl_word_cap' ) ) {
	/**
	 * Format $source_text to make first letters of each section uppercase. Sections are separated by $separator.
	 *
	 * @param float $source_text
	 * @param array $separator (default: array())
	 * @return string
	 */
	function wsl_word_cap( $source_text, $separator = ' ' ) {
	    $source_parts = explode( $separator, $source_text );
	    $return_parts = array();
	
	    foreach ( $source_parts as $source_part ) {
	        $return_part = ucfirst( strtolower($source_part) );
	        $return_parts[] = $return_part;
	    }
	    $return = implode( $separator, $return_parts );
	    return $return;
	}
}

if ( ! function_exists ( 'wsl_str_replace_after_string' ) ) {
	/**
	 * str replace after that part of the subject, that begins with the start string
	 * 
	 */
	function wsl_str_replace_after_string( $search, $replace , $subject, $count = null, $start_string ) {
		
		//string strstr(string $haystack, string $needle, [bool $before])
		
		// if no start string found, then return the original subject
		if ( strpos( $subject, $start_string ) ===  false ) {
			return $subject;
		}	
		
		//subject before the first character of start string
		$before = strstr( $subject, $start_string, true );
		
		//subject after the last character of start string, we have to replace in this
		$after = strstr( $subject, $start_string, false );
		
		if ( $count == null )
			$after = str_replace( $search, $replace, $after );
		else
			$after = wsl_str_replace_first( $search, $replace, $after );
		
		return $before . $after;
		
	}
}


if ( ! function_exists ( 'wsl_str_replace_first' ) ) {
	/**
 	* string replace of the first occurrence
 	*/
	function wsl_str_replace_first( $search, $replace, $subject ) {
	    $position = strpos( $subject, $search );
	    if ( $position !== false ) {
	        $subject = substr_replace( $subject, $replace, $position, strlen($search) );
	    }
	    return $subject;
	}
}

if ( ! function_exists ( 'wsl_array_column_sort' ) ) {
	/**
	 * Sort an array by values of one or more associative field
	 * See at: http://php.net/manual/en/function.array-multisort.php
	 * e.g: $ordered_array = wsl_array_column_sort($data_array, "age", SORT_DESC, "name", SORT_ASC);
	 * 
	 * @param array $array_to_order
	 * @param string $key_name1
	 * @param string $key_name sort_order1 (SORT_ASC / SORT_DESC)
	 * @param string $key_name...
	 * @param string $key_name sort_order... (SORT_ASC / SORT_DESC)
	 * @return array
	 */

	function wsl_array_column_sort() {

		$args = func_get_args();
		
		if ( ! is_array($args[0])) {
			return;
		}
		
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
				}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
		 
	}
	
}

if ( ! function_exists ( 'wsl_sign' ) ) {
	/**
	 * Sign of a number
	 * 
	 * @param number 
	 * @return number
	 */
	
	function wsl_sign( $number ) { 
	    return ( $number > 0 ) ? 1 : ( ( $number < 0 ) ? -1 : 0 ); 
	} 	
}

if ( ! function_exists ( 'wsl_nvl' ) ) {
	/**
	 * NVL: if first parameter has NULL value replace it with the second parameter
	 * 
	 * @param number 
	 * @return number
	 */
	function wsl_nvl($val, $replace) {
	
	    if( is_null($val) || $val === '' || $val == null )  
	    	$ret = $replace;
	    else 
	    	$ret = $val;
		
		return $ret;
		
	}
}

if ( ! function_exists ( 'wsl_read_csv' ) ) {
	/**
	 * Read a csv file containing header line, and give back an associative array
	 * @link http://steindom.com/articles/shortest-php-code-convert-csv-associative-array 
	 * @param string $file e.g. FILOFW()->plugin_path() . '/includes/aaa.txt')
	 * @return array $csv 
	 */

 	function wsl_read_csv( $file ) {

		//$rows = array_map( 'str_getcsv', file($file) ); //get all rows in a csv file
		$rows = array_map( 
			function($array_value){ return str_getcsv($array_value, ';'); }, 
			file($file) 
		); //get all rows in a csv file
		
		$header = array_shift($rows);
		$header = array_map( 'trim', $header ); //trim all column in the header row

		$csv = array();
		
		foreach ($rows as $row) {
			
			$row = array_map( 'trim', $row ); //trim all column in the current row
			try {
				$csv[] = array_combine($header, $row); //combine keys and values into an associative array
			} catch ( Exception $e ) {
				throw new Exception( 'File format error, at: ' . $file . ': ' . $row, 400 );
			}

		}
		
		return $csv;

	}

}


if ( ! function_exists ( 'wsl_remove_filter_like' ) ) {
	/**
	 * Removes a function from a specified filter hook, if function_to_remove is not known exactly (for example it's name contains an object id)
	 * (e.g. for WP 4.7)
	 *
	 * @param string   $hook_name_tag                Filter hook
	 * @param callback $function_to_remove Part of the name of the function
	 * @param int      $priority           Priority
	 * @return boolean Whether the function existed before it was removed.
	 */
	function wsl_remove_filter_like( $hook_name_tag, $function_to_remove, $priority = 10 ) {
		wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $hook_name_tag: ' . wsl_vartotext($hook_name_tag));	
		wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $function_to_remove 1: ' . wsl_vartotext($function_to_remove));
		wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $GLOBALS[wp_filter]: ' . wsl_vartotext($GLOBALS['wp_filter']));

		// $GLOBALS[wp_filter] e.g.
		//    [customize_save_after] => WP_Hook Object														// hook name tag (type 1 example)
		//        (
		//            [callbacks] => Array
		//                (
		//                    [10] => Array																	// priority
		//                        (
		//                            [_delete_option_fresh_site] => Array
		//                                (
		//                                    [function] => _delete_option_fresh_site						// function name
		//                                    [accepted_args] => 1
		//                                )
		//
		//                            [FILO_Customize_Manager::save_after] => Array
		//                                (
		//                                    [function] => FILO_Customize_Manager::save_after				// function name
		//                                    [accepted_args] => 1
		//                                )
		//                        )
		//                )
		//
		//            [iterations:WP_Hook:private] => Array
		//                (
		//                )
		//            [current_priority:WP_Hook:private] => Array
		//                (
		//                )
		//            [nesting_level:WP_Hook:private] => 0
		//            [doing_action:WP_Hook:private] => 
		//        )
		//
		//    [show_user_profile] => WP_Hook Object															// hook name tag (type 2 example)
		//        (
		//            [callbacks] => Array
		//                (
		//                    [10] => Array																	// priority
		//                        (
		//                            [0000000051c1dc2700000000d3ca0173add_customer_meta_fields] => Array
		//                                (
		//                                    [function] => Array											// function array
		//                                        (
		//                                            [0] => WC_Admin_Profile Object						// function object
		//                                                (
		//                                                )
		//
		//                                            [1] => add_customer_meta_fields						// function name
		//                                        )
		//
		//                                    [accepted_args] => 1
		//                                )
		//                            [0000000051c1dc2700000000d3ca0173get_customer_meta_fields] => Array
		//                                (
		//                                    [function] => Array
		//                                        (
		//                                            [0] => WC_Admin_Profile Object
		//                                                (
		//                                                )
		//
		//                                            [1] => get_customer_meta_fields
		//                                        )
		//                                    [accepted_args] => 1
		//                                )
		//                        )
		//                )
		//            [iterations:WP_Hook:private] => Array
		//                (
		//                )
		//            [current_priority:WP_Hook:private] => Array
		//                (
		//                )
		//            [nesting_level:WP_Hook:private] => 0
		//            [doing_action:WP_Hook:private] => 
		//        )
		//
		//    [edit_user_profile] => WP_Hook Object
		//		....			
		
		$removed_filter_function_full_names = false;
		
		if ( isset( $GLOBALS['wp_filter'][ $hook_name_tag ] ) ) {
			
			$wp_hook_object = $GLOBALS['wp_filter'][ $hook_name_tag ];
			
			wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $wp_hook_object: ' . wsl_vartotext($wp_hook_object));
			
			if ( property_exists ( $wp_hook_object , 'callbacks' ) ) {
				$callbacks = $wp_hook_object->callbacks;	
			}
			
		}
		
		wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $callbacks: ' . wsl_vartotext($callbacks));
		
		if ( isset($callbacks) and is_array($callbacks) ) {
				
			if ( isset($callbacks[$priority]) and is_array($callbacks[$priority] ) ) 
			foreach ( $callbacks[$priority] as $callback_function_id => $callback_function_data ) {
				
				wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $callback_function_id: ' . wsl_vartotext($callback_function_id));
				wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $callback_function_data: ' . wsl_vartotext($callback_function_data));
				
				if ( is_array($callback_function_data) and isset($callback_function_data['function'])) {
					
					//let's decide whether the function definition is an array (object+function name) or a string (just function name)
					if ( is_array($callback_function_data['function'])) {
						
						//function def is array
						$object = $callback_function_data['function'][0];
						$function_name = $callback_function_data['function'][1];
						
					} else {
						
						//function def is string
						$object = null;
						$function_name = $callback_function_data['function'];
						
					}
					
				}
				
				wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $object: ' . wsl_vartotext($object));
				wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $function_name: ' . wsl_vartotext($function_name));
				
				//if a function name partially contains the needed function name
				if ( strrpos($function_name, $function_to_remove) !== false ) {
					
					wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like REMOVE $function_name: ' . wsl_vartotext($function_name));
					wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like REMOVE $callback_function_id: ' . wsl_vartotext($callback_function_id));
	
					unset( $GLOBALS['wp_filter'][ $hook_name_tag ]->callbacks[ $priority ][ $callback_function_id ] );
					
					$removed_filter_function_full_names[$function_to_remove] = $function_name;

					//unset higher levels if empty					
					if ( empty( $GLOBALS['wp_filter'][ $hook_name_tag ]->callbacks[ $priority ] ) ) {
						unset( $GLOBALS['wp_filter'][ $hook_name_tag ]->callbacks[ $priority ] );
					}
					if ( empty( $GLOBALS['wp_filter'][ $hook_name_tag ] ) ) {
						$GLOBALS['wp_filter'][ $hook_name_tag ] = array();
					}
					
				}
				
			}
		
		}
		
		return $removed_filter_function_full_names;
		
	}	 
	/*
	//for earlier WP versions
	function wsl_remove_filter_like( $hook_name_tag, $function_to_remove, $priority = 10 ) {
		wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $hook_name_tag: ' . wsl_vartotext($hook_name_tag));	
		wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $function_to_remove 1: ' . wsl_vartotext($function_to_remove));
		wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $GLOBALS[wp_filter]: ' . wsl_vartotext($GLOBALS['wp_filter']));
		
		$removed_filter_function_full_names = false;
		
		if ( isset( $GLOBALS['wp_filter'][ $hook_name_tag ][ $priority ] ) and is_array( $GLOBALS['wp_filter'][ $hook_name_tag ][ $priority ] ) )
		foreach ($GLOBALS['wp_filter'][ $hook_name_tag ][ $priority ] as $funct_full_name => $funct_value) {
			
			//if a function name partially contains the needed function name
			if ( strrpos($funct_full_name, $function_to_remove) !== false ) {
				
				wsl_log(null, 'wsl-core-functions.php wsl_remove_filter_like $funct_full_name: ' . wsl_vartotext($funct_full_name));

				unset( $GLOBALS['wp_filter'][ $hook_name_tag ][ $priority ][ $funct_full_name ] );
				
				$removed_filter_function_full_names[$function_to_remove] = $funct_full_name;
				
				if ( empty( $GLOBALS['wp_filter'][ $hook_name_tag ][ $priority ] ) ) {
					unset( $GLOBALS['wp_filter'][ $hook_name_tag ][ $priority ] );
				}
				if ( empty( $GLOBALS['wp_filter'][ $hook_name_tag ] ) ) {
					$GLOBALS['wp_filter'][ $hook_name_tag ] = array();
				}
				unset( $GLOBALS['merged_filters'][ $hook_name_tag ] );
				
			}

			
		}
		
		return $removed_filter_function_full_names;
		
	}*/

}

if ( ! function_exists ( 'wsl_vartotext' ) ) {
	/*
	 * Convert any type of variables to readable text
	 */
	function wsl_vartotext($var, $convert_to_html_format = false, $removable_techn_keywords = array()) {
			ob_start();
			if ($convert_to_html_format) echo '<pre>';
			print_r($var);
			if ($convert_to_html_format) echo '</pre>';
			$ret = ob_get_clean();
				
			if ( ! empty($removable_techn_keywords) and is_array($removable_techn_keywords) ) 
			foreach ( $removable_techn_keywords as $removable_techn_keyword ) {
				$ret = str_replace($removable_techn_keyword, '', $ret);
			}
			
			return $ret;
	}				
}

if ( ! function_exists ( 'wsl_is_logging_enabled' ) ) {
	/*
	 * Check if logging is enabled
	 */
	function wsl_is_logging_enabled() {

		$wsl_helper_options = get_option('webshoplogic_helper', array());
		
		if (isset($wsl_helper_options['enable_logging'])) {
			$is_logging_enabled = $wsl_helper_options['enable_logging'];
		} else {
			$is_logging_enabled = null;
		}
		
		return $is_logging_enabled;
		 
	}
}

if ( ! function_exists ( 'wsl_log' ) ) {
	/*
	 * Log message to file
	 */
	function wsl_log($filename, $msg) { 
	
		$is_logging_enabled = wsl_is_logging_enabled();

		if ( $is_logging_enabled == '1' ) {
			
			$seller_user_id = get_option('filo_document_seller_user');
			//create filo-logs directory
			if ( wp_mkdir_p( WSL_LOG_DIR ) ) {
				 
				if ( !isset($filename) or $filename=='nvl' or $filename==null) {
					//$filename = untrailingslashit( plugin_dir_path( __FILE__ ) ).'/log.txt';
					$filename = untrailingslashit( WSL_LOG_DIR .'log.txt' );
				}
			
				// open file
				$fd = fopen($filename, "a");
				// append date/time to message
				$str = "[" . date("Y/m/d h:i:s", time()) . "] " . $msg; 	
				// write string
				fwrite($fd, $str . "\n");
				// close file
				fclose($fd);
			}
			
		}
	}
}


if ( ! function_exists ( 'wsl_download_and_store_file' ) ) {
	function wsl_download_and_store_file( $remote_url, $local_path ) {

		wsl_log(null, 'wsl-core-functions.php  wsl_download_and_store_file $remote_url: ' . wsl_vartotext($remote_url));
		wsl_log(null, 'wsl-core-functions.php  wsl_download_and_store_file $local_path: ' . wsl_vartotext($local_path));

		// Get remote content
		$response = wp_remote_get( $remote_url, array( 'timeout' => 120, 'httpversion' => '1.1' ) );

        // Check for error
		if ( is_wp_error( $response ) ) {
			$error_message = __( 'Error during file download: ', 'filo_text' );
			$error_message .= $response->get_error_message(); //wp_strip_all_tags( $response->get_error_message() );
			//throw new Exception( $error_message, 400 );
			//WC_Admin_Meta_Boxes::add_error( $error_message );
			return $error_message;
		}

        // Parse remote content file
		$data = wp_remote_retrieve_body( $response );

        // Check for error
		if ( is_wp_error( $data ) ) {
			$error_message = __( 'Error during parsing Google Fonts data: ', 'filo_text' );
			$error_message .= $data->get_error_message(); //wp_strip_all_tags( $data->get_error_message() );
			//throw new Exception( $error_message, 400 );
			//WC_Admin_Meta_Boxes::add_error( $error_message );
			return $error_message;
		}


		$local_path_dir = pathinfo( $local_path, PATHINFO_DIRNAME ); // PATHINFO_DIRNAME | PATHINFO_BASENAME | PATHINFO_EXTENSION | PATHINFO_FILENAME
		wsl_log(null, 'wsl-core-functions.php  wsl_download_and_store_file $local_path_dir: ' . wsl_vartotext($local_path_dir));

		if ( ! file_exists ( $local_path )) {
		
			//create filo-logs directory
			if ( wp_mkdir_p( $local_path_dir ) ) {
	
				// Store the responsed content locally
				file_put_contents( $local_path, $data );
				
				wsl_log(null, 'wsl-core-functions.php  wsl_download_and_store_file file_put_contents: ' . wsl_vartotext(''));
				
			}
		}
	}
}


if ( ! function_exists ( 'filo_check_metabox_saving_conditions' ) ) {
	/**
	 * filo_check_metabox_saving_conditions
	 * 
	 */	 
	 	 
	function filo_check_metabox_saving_conditions( $post_id, $post ) {

		// $post_id and $post are required
		//if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
		if ( empty( $post_id ) || empty( $post ) ) {
			return false;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return false;
		}

		// Check the nonce
		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) {
			return false;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return false;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return false;
		}

		return true;
		
	}
	
}		

if ( ! function_exists ( 'wsl_show_admin_message' ) ) {
	function wsl_show_admin_message($message, $errormsg = false)
	{
		if ($errormsg) {
			echo '<div id="message" class="error">';
		}
		else {
			echo '<div id="message" class="updated fade">';
		}
	
		echo "<p><strong>$message</strong></p></div>";
	}
}

if ( ! function_exists ( 'wsl_chcek_if_plugin_active' ) ) {
	/**
	 * wsl_chcek_if_plugin_active
	 * 
	 * @param text $plugin_name
	 */
	function wsl_chcek_if_plugin_active( $plugin_name ) {
		//e.g. name: 'woocommerce/woocommerce.php'
		
		$active_plugins = (array) get_option( 'active_plugins', array() );
		$active_sitewide_plugins = (array) get_site_option( 'active_sitewide_plugins');

		wsl_log(null, 'wsl-core-functions.php  wsl_chcek_if_plugin_active $active_plugins: ' . wsl_vartotext($active_plugins));
		wsl_log(null, 'wsl-core-functions.php  wsl_chcek_if_plugin_active $active_sitewide_plugins: ' . wsl_vartotext($active_sitewide_plugins));

		$is_plugin_active = 
			in_array( $plugin_name, $active_plugins ) || 
			( is_multisite() and isset($active_sitewide_plugins[$plugin_name]) );
			
		return $is_plugin_active;
	}
}

if ( ! function_exists ( 'wsl_chcek_missing_plugins' ) ) {
	/**
	 * chcek_prerequires_plugins
	 * 
	 * @param array $prerequires_plugin_names array of 'name'=>array( 'min_version'=>'xxx', 'max_version'=>'yyy'); max_version is optional
	 */
	function wsl_chcek_missing_plugins( $prerequires_plugins ) {
		//e.g. name: 'woocommerce/woocommerce.php'
		
		$missing_active_plugins = array();

		$active_plugins = (array) get_option( 'active_plugins', array() );
		$active_sitewide_plugins = (array) get_site_option( 'active_sitewide_plugins');

		wsl_log(null, 'wsl-core-functions.php  wsl_chcek_missing_plugins $prerequires_plugins: ' . wsl_vartotext($prerequires_plugins));
		wsl_log(null, 'wsl-core-functions.php  wsl_chcek_missing_plugins $active_plugins: ' . wsl_vartotext($active_plugins));
		wsl_log(null, 'wsl-core-functions.php  wsl_chcek_missing_plugins $active_sitewide_plugins: ' . wsl_vartotext($active_sitewide_plugins));

				
		if ( isset($prerequires_plugins) and is_array($prerequires_plugins) )
		foreach ( $prerequires_plugins as $prerequires_plugin_name => $prerequires_plugin_data) {

			wsl_log(null, 'wsl-core-functions.php  wsl_chcek_missing_plugins WP_PLUGIN_DIR . / . $prerequires_plugin_name: ' . wsl_vartotext(WP_PLUGIN_DIR . '/' . $prerequires_plugin_name));
			
			if ( ! file_exists ( WP_PLUGIN_DIR . '/' . $prerequires_plugin_name )) {  // if the prerequired plugin file does NOT exist e.g. '/..../wp-content/plugins/' . 'woocommerce/woocommerce.php'
			
				// if a plugin was installed but changed the path (e.h. filogy-framework/filogy-framework.php -> filogy/filogy-framework-mini/filogy-framework.php),
				// then WP does not recognize that is inactive in time (e.g when the framework-mini is included by filogy), thus it remains in active_pactive_sitewide_pluginslugins or  
				// that is why we have to check if the directory/file does exist or not first
				$missing_active_plugins[] = $prerequires_plugin_name;
	
			} else { // if the prerequired plugin file exists		

	
				$prerequires_plugin_active = 
					in_array( $prerequires_plugin_name, $active_plugins ) || 
					( is_multisite() and isset($active_sitewide_plugins[$prerequires_plugin_name]) );
						
				if ( ! $prerequires_plugin_active )
					$missing_active_plugins[] = $prerequires_plugin_name;
				
			}
		}

		wsl_log(null, 'wsl-core-functions.php  wsl_chcek_missing_plugins $missing_active_plugins: ' . wsl_vartotext($missing_active_plugins));
		
		return $missing_active_plugins;
	}
}


if ( ! function_exists ( 'wsl_chcek_prerequired_plugin_versions' ) ) {
	/**
	 * chcek_prerequired_plugin_versions
	 * 
	 * @param array $prerequires_plugin_names array of 'name'=>array( 'min_version'=>'xxx', 'max_version'=>'yyy'); max_version is optional
	 */
	function wsl_chcek_prerequired_plugin_versions( $prerequires_plugins ) {
		//e.g. name: 'woocommerce/woocommerce.php'
		
		
		$default_headers = array(
			'Version' => 'Version',
		);
				
		$wrong_plugin_versions = array();		
		if ( isset($prerequires_plugins) and is_array($prerequires_plugins) )
		foreach ( $prerequires_plugins as $prerequires_plugin_name => $prerequires_plugin_data) {
			
			//$plugin_actual_version_data = get_file_data( ABSPATH . 'wp-content/plugins/' . $prerequires_plugin_name, $default_headers, 'plugin' );
			$plugin_actual_version_data = get_file_data( WP_PLUGIN_DIR . '/' . $prerequires_plugin_name, $default_headers, 'plugin' );
			$plugin_actual_version = $plugin_actual_version_data['Version'];
			
			$plugin_min_version = $prerequires_plugin_data['min_version'];
			$plugin_max_version = $prerequires_plugin_data['max_version'];
			
			//version_compare ( string $version1 , string $version2 [, string $operator ] )
			if ( 	( empty($plugin_min_version) or version_compare($plugin_actual_version , $plugin_min_version, '>=') ) and 
 					( empty($plugin_max_version) or version_compare($plugin_actual_version , $plugin_max_version, ' <=') )
			) { //version is OK
				//nothing to do
			} else { //version is wrong
				$wrong_plugin_versions[] = array (
					'name' 				=> $prerequires_plugin_name,
					'actual_version' 	=> $plugin_actual_version,
					'min_version' 		=> $plugin_min_version,
					'max_version' 		=> $plugin_max_version,
				);
			}
				
		}

		/*
			e.g.
			array 0 => 
			    array 
			      'name' => string 'woocommerce/woocommerce.php'
			      'actual_version' => string '1.1.0'
			      'min_version' => string '2.4.10'
			      'max_version' => null		
		*/
		
		return $wrong_plugin_versions;
				
	}
}

if ( ! function_exists ( 'wsl_file_uploader_html' ) ) {
	/**
	 * wsl_file_uploader_html
	 */
	function wsl_file_uploader_html( 
			$field_id,
			$actual_value_of_uploaded_url = '', 
			$field_label = 'Upload file',
			$field_tip = 'Upload or delete file.', 
			$field_description = 'Upload or delete file.', 
			$is_image = false 
		) {
		
		ob_start();
		    		
					
		$file_url = $actual_value_of_uploaded_url;
		
		wsl_log(null, 'wsl-core-functions.php  wsl_file_uploader_html $field_id: ' . wsl_vartotext($field_id));
		wsl_log(null, 'wsl-core-functions.php  wsl_file_uploader_html $file_url: ' . wsl_vartotext($file_url));
					
		if ( $is_image ) {	
		    $image =  
		    $display = ( $file_url ) ? '' : 'style="display: none;"';
	    }
		?>
	
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo $field_id; ?>"><?php echo $field_label; ?></label>
				<img class="help_tip" data-tip="<?php echo $field_tip; ?>" src="<?php echo(plugins_url()); ?>/woocommerce/assets/images/help.png" height="16" width="16" />	
			</th>
			<td class="forminp">
				<div class="order_data_column">
	
	                <input type="text" id="wsl_upload_file_url" name="<?php echo $field_id; ?>" value="<?php echo esc_url( $file_url ); ?>" readonly />
	                <input type="hidden" id="wsl_upload_file_attachment_id" name="<?php echo $field_id; ?>_attachement_id" value="" readonly />
	                <?php if ( $is_image ) { ?>	
		                <div id="wsl_upload_image">
		                	<?php echo ( $file_url ) ? '<img src="' . esc_url( $file_url ) . '" alt="" />' : ''; ?>
		                </div>
		            <?php } ?>
	                <div id="wsl_upload_buttons">
	                	<a href="#" class="wsl_upload_file button"><?php _e( 'Upload', 'filofw_text' ); ?></a>&nbsp;&nbsp;&nbsp;<a href="#" class="wsl_delete_file button" <?php echo $display; ?>><?php _e( 'Delete', 'filofw_text' ); ?></a>
	                    <span class="description"><?php echo $field_description; ?></span>
	                </div>
		
				</div>
			</td>
		</tr>
	
		<script>	
			( function($) {
				var file_frame;
				$( '.order_data_column' )
					.on( 'click', '.wsl_upload_file', function(e) {
					    e.preventDefault();
			
					    if ( file_frame )
					        file_frame.remove();
			
					    file_frame = wp.media.frames.file_frame = wp.media( {
					        title: $(this).data( 'uploader_title' ),
					        button: {
					            text: $(this).data( 'uploader_button_text' )
					        },
					        multiple: false
					    } );
			
					    file_frame.on( 'select', function() {
					        var attachment = file_frame.state().get( 'selection' ).first().toJSON();
					        //console.log( attachment );
							$( '#wsl_upload_file_url' ).val( attachment.url );
							$( '#wsl_upload_image' ).html( '<img src="' + attachment.url + '" alt="' + attachment.url + '" />' );
							$( '#wsl_upload_file_attachment_id' ).val( attachment.id );
					    } );
			
					    file_frame.open();
					    $( '.wsl_delete_file' ).show();
					} )
					.on( 'click', '.wsl_delete_file', function(e) {
					    e.preventDefault();
					    $(this).hide();
						$( '#wsl_upload_file_url' ).val( '' );
						$( '#wsl_upload_image' ).html( '' );
					} );
			
			} )(jQuery);
		</script>	
	
		<?php
	
		return ob_get_clean();
	
	}
}

if ( ! function_exists ( 'wsl_rrmdir' ) ) {
	/**
	 * remove directory recusively
	 */
	function wsl_rrmdir($dir) { //Add RaPe - remove not empty directory recursively
	   if (is_dir($dir)) { 
	     $objects = scandir($dir); 
	     foreach ($objects as $object) { 
	       if ($object != "." && $object != "..") { 
	         if (filetype($dir."/".$object) == "dir") wsl_rrmdir($dir."/".$object); else unlink($dir."/".$object); 
	       } 
	     } 
	     reset($objects); 
	     rmdir($dir); 
	   } 
	} 
}

if ( ! function_exists ( 'wsl_rcopy' ) ) {
	/**
	 * copy directory recursively
	 */		
	function wsl_rcopy($src,$dst) { 
	    $dir = opendir($src); 
	    @mkdir($dst); 
	    while(false !== ( $file = readdir($dir)) ) { 
	        if (( $file != '.' ) && ( $file != '..' )) { 
	            if ( is_dir($src . '/' . $file) ) { 
	                wsl_rcopy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	            else { 
	                copy($src . '/' . $file,$dst . '/' . $file); 
	            } 
	        } 
	    } 
	    closedir($dir); 
	} 	
}

if ( ! function_exists ( 'wsl_trigger_activation_error' ) ) {
	/**
	 * wsl_trigger_activation_error
	 * 
	 * We use it to handle plugin activation errors.
	 * 
	 */	
	 	
	function wsl_trigger_activation_error($message, $errno) {
		if(isset($_GET['action']) and $_GET['action'] == 'error_scrape') {
			echo '<strong>' . $message . '</strong>';
			exit;
		} else {
			trigger_error($message, $errno);
		}
	}
}

if ( ! function_exists ( 'wsl_get_act_url' ) ) {
	/**
	 * wsl_get_act_url
	 * 
	 */
	function wsl_get_act_url() {
		$act_url  = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) ? 'https' : 'http';
		$act_url .= '://' . wc_clean( $_SERVER['SERVER_NAME'] ); //+wc_clean
		$act_url .= in_array( $_SERVER['SERVER_PORT'], array( '80', '443' ) ) ? '' : ":" . wc_clean( $_SERVER['SERVER_PORT'] ); //+wc_clean
		$act_url .= wc_clean( $_SERVER['REQUEST_URI'] ); //+wc_clean
		return $act_url;
	}
}


if ( ! function_exists ( 'wsl_insert_file_to_media_lib' ) ) {
	/**
	 * wsl_insert_file_to_media_lib
	 * 
	 * Add file to media library programmatically:
	 * 
	 */
	function wsl_insert_file_to_media_lib( $include_file_path_and_name, $image_title ) {
	
		$filename = basename( $include_file_path_and_name );
		
		$existing_media_lib_row = wsl_get_media_lib_url_by_filename($filename);
		
		wsl_log(null, 'class-filo-setup.php wsl_insert_file_to_media_lib $existing_media_lib_row: ' . wsl_vartotext($existing_media_lib_row));
		
		//if the file with the same file name has already been inserted, then return it's url (the file will not be inserted again)
		if ( ! empty($existing_media_lib_row) ) {
			return $existing_media_lib_row->ID; 
		}
		
		$upload_attributes = wp_upload_bits( $filename, null, file_get_contents($include_file_path_and_name) );
		
		wsl_log(null, 'class-filo-setup.php wsl_insert_file_to_media_lib $upload_attributes: ' . wsl_vartotext($upload_attributes));
		
		if( empty($image_title) ) {
			$image_title = preg_replace('/\.[^.]+$/', '', $filename);
		}
		
		if (!$upload_attributes['error']) {
			
			$filetype = wp_check_filetype( $filename, null );
			
			$attachment = array(
				'guid' => $upload_attributes['url'],
				'post_mime_type' => $filetype['type'], // of attachment post
				'post_title' => $image_title,
				'post_content' => '', 
				'post_status' => 'inherit'
			);
			
			$attachment_id = wp_insert_attachment( $attachment, $upload_attributes['file'] );
			
			if ( ! is_wp_error($attachment_id)) {
					
				// Include wp-admin - image.php file of WP Core according to recommendation of WordPress.org Codex
				// https://codex.wordpress.org/Function_Reference/wp_insert_attachment:				
				require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
				
				$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_attributes['file'] );
				wp_update_attachment_metadata( $attachment_id,  $attachment_data );
				
			}
			
		}		

		return $attachment_id;
	}	
}


if ( ! function_exists ( 'wsl_get_media_lib_url_by_filename' ) ) {
	/**
	 * wsl_get_media_lib_url_by_filename
	 * 
	 * Search for a file in a media library by filename
	 * 
	 */
	function wsl_get_media_lib_url_by_filename( $filename ) {
		global $wpdb;
	
		$filename_without_extension = pathinfo( $filename, PATHINFO_FILENAME ); // PATHINFO_DIRNAME | PATHINFO_BASENAME | PATHINFO_EXTENSION | PATHINFO_FILENAME
		$filename_without_extension = str_replace('_', '\_', $filename_without_extension); // my_image -> my\_image; replace _ to \_ because in mysql _ is a ine character wilchard, and \_ means a concrete _ character in like query
		$fil_extension = pathinfo( $filename, PATHINFO_EXTENSION ); 

		//search for exact filename	
		$sql = $wpdb->prepare( "
			select * from {$wpdb->prefix}posts where post_type = 'attachment'
			and guid = %s
			",
			$filename //e.g. http://yoursite.com/wp-content/uploads/2017/01/myimage.png
		);
		$media_lib_row = $wpdb->get_row($sql);

		//search for exact filename
		if ( empty($media_lib_row) ) {	
			$sql = $wpdb->prepare( "
				select * from {$wpdb->prefix}posts where post_type = 'attachment'
				and guid like %s
				",
				"%/" . $filename_without_extension . '.' . $fil_extension //e.g. %/myimage.png (all that ends width / + the given filename)
			);
			$media_lib_row = $wpdb->get_row($sql);
		}
		
		//search for filename '-XX' style, like myimage-03.png if our needle filename is myimage.png
		if ( empty($media_lib_row) ) {
			$sql = $wpdb->prepare( "
				select * from {$wpdb->prefix}posts where post_type = 'attachment'
				and guid like %s
				",
				"%/" . $filename_without_extension . '-%.' . $fil_extension  //e.g. %/myimage-03.png (all that ends width / + the given filename, included a -... part)
			);
			$media_lib_row = $wpdb->get_row($sql);
		}
		
		return $media_lib_row;
			
	}
}


if ( ! function_exists ( 'wsl_handle_hierarchical_array_values_recursively' ) ) {
	function wsl_handle_hierarchical_array_values_recursively( $array, $callback_functions ) {
		global $wpdb;
		
		if ( isset($array) and is_array($array) ) 
		foreach ($array as $array_key => $array_value) {
				
			$array_value2 = $array_value; //default
			
			// evaluate, that the actual array element is another array, or a simple value
			if ( is_array($array_value) ) { // embedded array
				
				//call the function recursively, if we found an embedded array
				$array_value2 = wsl_handle_hierarchical_array_values_recursively( $array_value, $callback_functions );
				
			} else { // simple value
			
				//we use $array_value2 not to change the foreach variable
				$array_value2 = $array_value; 
			
				// do all the callback functions
				if ( is_array($callback_functions) )
				foreach( $callback_functions as $callback_function ) {
				
					if (is_array($callback_function)) {
						$callback_result = $callback_function[0]->$callback_function[1]( $array_key, $array_value2 ); //e.g. call $my_class->$my_function( $my_array_key, $my_array_value )
					} else {
						
						wsl_log(null, 'wsl-core-functions.php wsl_handle_hierarchical_array_values_recursively $callback_function: ' . wsl_vartotext($callback_function));
	
						// e.g. class-wp-hook.php - apply_filters() - $value = call_user_func_array( $function, $args )					
						$callback_result = call_user_func_array( $callback_function, array($array_key, $array_value2) );  //e.g. call $my_function( $my_array_key, $my_array_value )
						
						//$callback_result = $callback_function( $array_key, $array_value );  //e.g. call $my_function( $my_array_key, $my_array_value )
											
					}
					$array_value2 = $callback_result['result_value']; //change the actual array key value to the desired result value given back by the callback function
					
				} 
				
			}
			
			$array[$array_key] = $array_value2; 
			
		}
		
		return $array;

	}
}



if ( ! function_exists ( 'wsl_enqueue_core_scripts' ) ) {
	/**
	 * wsl_enqueue_core_scripts
	 * 
	 * Enqueue core scripts if it is not enqueued yet.
	 */
	/*
	function wsl_enqueue_core_scripts() {

		$this_file_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		
		wsl_log(null, 'wsl-core-functions.php wsl_enqueue_core_scripts $this_file_url: ' . wsl_vartotext($this_file_url));

		//cut the end of this file absolute path, to get the plugin main directory_path
		//e.g. http://webshoplogic.com/wp-content/plugins/filogy-framework/includes -> http://webshoplogic.com/wp-content/plugins/filogy-framework/
		$plugin_dir_path = str_replace( 'includes', '', $this_file_url );
		
		
		wsl_log(null, 'wsl-core-functions.php wsl_enqueue_core_scripts 0: ' . wsl_vartotext(''));
		wsl_log(null, 'wsl-core-functions.php wsl_enqueue_core_scripts __FILE__...: ' . wsl_vartotext($plugin_dir_path . 'assets/js/filo-core-script.js'));

		if( ! wp_script_is( 'wsl-core-scripts', 'enqueued' ) ) {
			wsl_log(null, 'wsl-core-functions.php wsl_enqueue_core_scripts ENQ: ' . wsl_vartotext(''));
			wp_enqueue_script( 'wsl-core-scripts', $plugin_dir_path . 'assets/js/filo-core-script.js', array("jquery") );
		}
	}
	add_action( 'admin_enqueue_scripts', 'wsl_enqueue_core_scripts' );
	add_action( 'wp_enqueue_scripts', 'wsl_enqueue_core_scripts' );
	*/
}


if ( ! function_exists ( 'wsl_log_deprecated_function_run' ) ) {
	/**
	 * wsl_log_deprecated_function_run
	 * 
	 * Log runs of deprecated functions
	 */

	function wsl_log_deprecated_function_run($function, $replacement, $version) {

		wsl_log(null, 'wsl-core-functions.php wsl_log_deprecated_function_run $function: ' . wsl_vartotext($function));
		wsl_log(null, 'wsl-core-functions.php wsl_log_deprecated_function_run $replacement: ' . wsl_vartotext($replacement));
		wsl_log(null, 'wsl-core-functions.php wsl_log_deprecated_function_run $version: ' . wsl_vartotext($version));

	}
	do_action( 'deprecated_function_run', 'wsl_log_deprecated_function_run', 10, 3 );
}
<?php
/**
 * General core functions available on both the front-end and admin.
 * 
 * @package     Filogy/Functions
 * @category    Functions
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Set nocache constants and headers.
 * 
 * @based on WC_Cache_Helper::nocache() function
 */
function filo_nocache() {
	if ( ! defined( 'DONOTCACHEPAGE' ) ) {
		define( "DONOTCACHEPAGE", true );
	}
	if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
		define( "DONOTCACHEOBJECT", true );
	}
	if ( ! defined( 'DONOTCACHEDB' ) ) {
		define( "DONOTCACHEDB", true );
	}
	nocache_headers();
}
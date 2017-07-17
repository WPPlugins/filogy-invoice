<?php

do_action( 'filo_document_footer' ); //use our own action instead of wp_footer(), because we do not need to display other wp elements like menu.
do_action( 'wp_footer_filo' ); //QQQ21
//wp_footer(); //QQQ21

//do_action( 'filo_document_footer' );
remove_filter('woocommerce_localisation_address_formats', array(FILOFW()->countries, 'filo_localisation_address_formats_namediv'));

?>

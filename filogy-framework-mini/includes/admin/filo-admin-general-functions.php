<?php
/**
 * Filogy Admin General and Helper Functions
 *
 * @package     Filogy/Admin
 * @subpackage 	Financials
 * @category    Admin
 */

/**
 * Retrieve or display list of financial documents as a dropdown (select list).
 *
 * see: wp_dropdown_pages();
 *
 */
function filo_dropdown_finadocs( $args = '', $enable_select_tag = true ) {

	$defaults = array(
		'doc_types' => null,
		'orderby' => 'asc',
		'name' => 'page_id', 
		'id' => '',
		'class' => '',
		'show_option_none' => '', 
		'show_option_no_change' => '',
		'option_none_value' => '',
		'value_field' => 'ID',
	);
	
	wsl_log(null, 'filo-admin-general-functions.php filo_dropdown_finadocs 0 $args: ' . wsl_vartotext($args));

	$args = wp_parse_args( $args, $defaults );

	$finadoc_title_list = FILO_Financial_Document::get_finadoc_title_list( $args['doc_types'], $args['orderby'], $args['item_limit'] );
	
	//wsl_log(null, 'filo-admin-general-functions.php filo_dropdown_finadocs $finadoc_title_list: ' . wsl_vartotext($finadoc_title_list));
	
	$output = '';
	if ( ! empty( $finadoc_title_list ) ) {
		$class = '';
		if ( ! empty( $args['class'] ) ) {
			$class = " class='" . esc_attr( $args['class'] ) . "'";
		}

		if ( $enable_select_tag ) {
			$output .= "<select name='" . esc_attr( $args['name'] ) . "'" . $class . " id='" . esc_attr( $args['id'] ) . "'>\n";
		}
		
		if ( $args['show_option_no_change'] ) {
			$output .= "\t<option value=\"-1\">" . $args['show_option_no_change'] . "</option>\n";
		}
		if ( $args['show_option_none'] ) {
			$output .= "\t<option value=\"" . esc_attr( $args['option_none_value'] ) . '">' . $args['show_option_none'] . "</option>\n";
		}

		if ( isset($finadoc_title_list) and is_array($finadoc_title_list) )		
		foreach ($finadoc_title_list as $finadoc_id => $finadoc_title) {
			$output .= "\t<option value=\"" . esc_attr( $finadoc_id ) . '">' . $finadoc_title . "</option>\n";
		}
		
		if ( $enable_select_tag ) {
			$output .= "</select>\n";
		}
		
		$html = apply_filters( 'filo_dropdown_finadocs', $output, $args, $finadoc_title_list );
		
		if ( isset($args['echo']) and $args['echo'] ) {
			echo $html;
		}
		
	return $html;
	
	}
	
}

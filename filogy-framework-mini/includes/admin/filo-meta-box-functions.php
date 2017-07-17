<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
* WooCommerce Meta Box Functions, input table in metaboxes
*
* @package     	Filogy/Admin/Functions
* @subpackage 	Framework
* @author      	WebshopLogic - Peter Rath
* @author 		WooThemes (original file)
* @category    	Admin
* 
* @based_on 	class-wc-admin-settings.php file in WooCommerce plugin by WooThemes
*/


/**
 * Generate input table with fields
 *
 * @access public
 * @param string $input_table_name
 * @param array $fields fields meta datas
 * @param array $table_values rows default values
 * @param boolean $readonly
 * @param boolean $add_row if add row button is enabled 
 * @param boolean $remove_row if remove row button is enabled
 * @param string include_type where to be included this input table: metabox, list_table
 * @return void
 * 
 * Go throuhgh on table_values->item_values->field line 
 * 
 */
function filo_output_input_table( $input_table_name, $fields, $table_values, $readonly = false, $add_row = true, $remove_row = true, $drag_and_drop_sort = true, $include_type = null ) {
	//renames:
	//$item_values -> $table_values
	
	$hide_column_css = '';
	
	wsl_log(null, 'filo-meta-box-functions.php filo_output_input_table $add_row: ' . wsl_vartotext($add_row));
	wsl_log(null, 'filo-meta-box-functions.php filo_output_input_table $table_values: ' . wsl_vartotext($table_values));
	wsl_log(null, 'filo-meta-box-functions.php filo_output_input_table $include_type: ' . wsl_vartotext($include_type));
	
	if ( $include_type == 'metabox' ) { //in case of metaboxes tr and /tr nedded on the begining and end 
		?>
	    <tr valign="top">
	    <?php
	} //end of IF $include_type == 'metabox'
   	
   	if ( $include_type == 'metabox' or $include_type == null) { //in case of metaboxes table, thead and /table, /thead nedded on the begining and end
   		?>
   		
		    <table id="<?php echo $input_table_name; ?>" class="widefat wc_input_table sortable" cellspacing="0">
	    		<thead>
	    			<tr>
	    				<?php if ($drag_and_drop_sort) { ?> 
	    					<th class="sort">&nbsp;</th>
	    				<?php } ?>
						<?php 
						if (isset( $fields ) && is_array( $fields ) )
						foreach ( $fields as $field ) {
							//wsl_log(null, 'filo-meta-box-functions.php filo_output_input_table $field: ' . wsl_vartotext($field)); 
							?>
								<th <?php echo isset($field['table_column_width']) ? ('width="' . $field['table_column_width'] . '"' ) : ''; ?>><?php echo isset($field['label']) ? $field['label'] : ''; ?>&nbsp;<span class="tips" data-tip="<?php echo isset($field['description']) ? $field['description']: ''; ?>">[?]</span></th>
							<?php } ?>
	    			</tr>
	    		</thead>
	    		<tbody class="data_rows">
	    <?php
	} //end of IF $include_type == 'metabox'
	
	wsl_log(null, 'filo-meta-box-functions.php filo_output_input_table 11 $table_values: ' . wsl_vartotext($table_values));

	//tbody content is needed in case of all include types 
	$i = -1;
	
	if (isset( $table_values ) && is_array( $table_values ) )
	foreach ( $table_values as $item_key => $item_values ) { //one row
		$i++;

		echo '<tr class="data_row">';
		if ($drag_and_drop_sort)
			echo '<td class="sort"></td>';
	
		if (isset( $fields ) && is_array( $fields ) )  
		foreach ( $fields as $field ) { //one field from initiated field array using on this actual row; this means one field, this field could have more attribute (e.g. value and select options)
			
			if ( isset($item_values[$field['id']]) && is_array($item_values[$field['id']]) )
			foreach ($item_values[$field['id']] as $item_values_attribute_key => $item_values_attribute_value) { // one field attribute; keys e.g.: value or options  
				
				$field[$item_values_attribute_key] = $item_values[$field['id']][$item_values_attribute_key]; //originaly field does not contain 'value' element, we put into it according to tha actual row's actual field id's value, before display it the filo_output_field function; e.g.: $field['value'] = $item_values[$field['id']]['value'];	
				
			}

			wsl_log(null, 'filo-meta-box-functions.php $field X: ' . wsl_vartotext($field));
			wsl_log(null, 'filo-meta-box-functions.php $item_values: ' . wsl_vartotext($item_values));
 
			echo '<td id="' . $field['id'] . '">';
				echo filo_output_field( $field, $readonly, $input_table_name, $item_key);
			echo '</td>';

			//hide column of hidden fields (without this an empty column would be displayed)
			//in the first row, for the hidden fields write the appropriate css that hides the column 
			if ( $i == 0 and $field['type'] == 'hidden' ) {
				$hide_column_css .= '<style>' . '#' . $field['id'] . '{ display: none; }</style>';
			}

		}			
					
        echo '</tr>';
		
	}
	
   	if ( $include_type == 'metabox' or $include_type == null) { //in case of metaboxes table, thead and /table, /thead nedded on the begining and end
   		?>
	        	</tbody>
	        	<?php if ( $add_row or $remove_row ) { ?>
	    		<tfoot>
	    			<tr>
	    				<th colspan="30">
	    					<?php if ( $add_row  ) { ?>
	    						<a href="#" class="add button"><?php _e( '+ Add Row', 'filofw_text' ); ?></a>
	    					<?php } ?>
	    					<?php if ( $remove_row ) { ?> 
	    						<a href="#" class="remove_rows button"><?php _e( 'Remove selected row(s)', 'filo_text' ); ?></a>
	    					<?php } ?>
	    				</th>
	    			</tr>
	    		</tfoot>
	    		<?php } ?>
	        </table>
	        <?php
	} //end of IF $include_type == 'metabox'
	
	echo $hide_column_css;
	
   	if ( $include_type == 'metabox' or $include_type == null) { //in case of metaboxes table add and remove row handling scripts are needed		        

        //inside of script tags could not be additional enters
        
        ?>
       	<script type="text/javascript">
			jQuery(function() {
				jQuery('#<?php echo $input_table_name; ?>').on( 'click', 'a.add', function(){

					var size = jQuery('#<?php echo $input_table_name; ?> tbody .data_row').size();

					jQuery('<tr class="data_row">\
                			<td class="sort"></td>\
                				<?php
                					if (isset( $fields ) && is_array( $fields ) )
									foreach ( $fields as $field ) {
	 
					 					//$field['value'] = $item_values[$field['id']]; //originaly field does not contain 'value' element, we put into it according to tha actual row's actual field id's value, before display it the filo_output_field function  
										?><td><?php echo filo_output_field( $field, $readonly, $input_table_name, null, true ); ?></td>\
										<?php //this php open tag should be in new line, right after the previous row, without empty lines between them.

									}			
                				
                				?>
	                    </tr>').appendTo('#<?php echo $input_table_name; ?> tbody');

					return false;
				});
			});
		</script>

	<?php
	} //end of IF $include_type == 'metabox'	

	if ( $include_type == 'metabox' ) { //in case of metaboxes tr and /tr nedded on the begining and end
		?>
    	</tr>
	    <?php
	}
	
}

/**
 * Output group of HTML field
 *
 * @access public
 * @param array $field
 * @param boolean $readonly 
 * @return void
 */
function filo_output_fields( $fields, $readonly = false ) {
	global $thepostid, $post;

	wsl_log(null, 'filo-meta-box-functions.php filo_output_fields $fields: ' . wsl_vartotext($fields));

	if (isset( $fields ) && is_array( $fields ) )
	foreach ( $fields as $field ) {
			
		filo_output_field( $field, $readonly );
		

	}
}


/**
 * Output a text input box.
 *
 * @access public
 * @param array $field field metadata array
 * @param array $readonly if the calue should be desplayed ad read only, then it has to be true
 * @param string $input_table_name in case of input_table display it is the name of it
 * @param boolean $is_input_table_js_fields in case of input table, a js script is used to add new rows. If this JS calles the function, it has to be true, to use slitly different field display
 * @return void
 * 
 * @based_on class-wc-admin-settings.php WC v2.2.6
 */
function filo_output_field( $field, $readonly = false, $input_table_name = '', $input_table_row_key = null, $is_input_table_js_fields = false ) {
	//ToDo RaPe: To be tested all parts!

	global $thepostid, $post;
	
	$field = apply_filters( 'filo_output_field', $field, $readonly );
	wsl_log(null, 'filo-meta-box-functions.php $field: ' . wsl_vartotext($field));

	$pid=null;
	if(isset($post)) 
		$pid = $post->ID;

	//set default values
	$thepostid              = empty( $thepostid ) ? $pid : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : ''; //texts, textarea
	
	//$field['class'] //texts, textarea, checkbox, select + multiselect, radio
	if 		( isset( $field['class'] ) )		$field['class'] = $field['class'];
	elseif 	( $field['type'] == 'checkbox' )  	$field['class'] = 'checkbox';
	elseif 	( $field['type'] == 'price' )  		$field['class'] = 'short wc_input_price';
	elseif 	( $field['type'] == 'decimal' ) 	$field['class'] = 'short wc_input_decimal';
	else 										$field['class'] = 'short';

	$field['id'] = isset( $field['id'] ) ? $field['id'] : null;
		
	$field['class'] .= ' filo_metafield';
	
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : ''; //texts, textarea, checkbox, select + multiselect, radio
	$field['form_field_class'] = isset( $field['form_field_class'] ) ? $field['form_field_class'] : 'form-field'; //all //form-field class can be overwritten 	
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true ); //texts, textarea, checkbox, select + multiselect, radio
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id']; //texts, (textarea), checkbox, radio
	$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text'; //texts
	$data_type              = empty( $field['data_type'] ) ? '' : $field['data_type']; //texts
	$field['textarea_rows'] = isset( $field['textarea_rows'] ) ? $field['textarea_rows'] : '2'; //textarea
	$field['textarea_cols'] = isset( $field['textarea_cols'] ) ? $field['typetextarea_cols'] : '20'; //textarea
	$field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes'; //checkbox
	$field['is_wrapper']    = isset( $field['is_wrapper'] ) ? $field['is_wrapper'] : true; //all
	$field['html_content']  = isset( $field['html_content'] ) ? $field['html_content'] : ''; //html_code
	
	//if type = '+', then nothing to do here, save methods know about this that the field has to be saved according to a field group definition array.

	// Custom attribute handling
	$custom_attributes = array();

	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) )
		foreach ( $field['custom_attributes'] as $attribute => $value )
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';

	$general_wrapper = 
		$field['is_wrapper'] ? '<p class="' . esc_attr( $field['form_field_class'] ) . ' ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">' : '';
	$general_wrapper_close =
		$field['is_wrapper'] ? '</p>' : '';		
	

	$general_description = '';
	if ( ! empty( $field['description'] ) and $input_table_name == '' ) { //in case of input table there is no descriptions here

		if ( (isset( $field['desc_tip'] ) && false !== $field['desc_tip']) or (isset( $field['desc_tip_in_label'] ) && false !== $field['desc_tip_in_label']) ) {
			$general_description = '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WC()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			$general_description = '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}
	}

	$label_description = '';
	if ( isset( $field['desc_tip_in_label'] ) && false !== $field['desc_tip_in_label'] ) {
		$label_description = ' ' . $general_description;
		$general_description = '';
	}

	$general_label = '';
	if ( ! empty( $field['label'] ) and $input_table_name == '' ) { //in case of input table there is no label here
		$general_label = 
			'<label for="' . esc_attr( $field['id'] ) . '">'
			. wp_kses_post( $field['label'] ) 
			. $label_description
			. '</label>';
	}


	if( $input_table_name == '' ) { // not input_table field_name
		$name = esc_attr( $field['name'] ) . 
			 	($field['type'] == 'multiselect' ? '[]' : '');
	} else { // input_table table_name[row_key][field_name] ( or table_name[new_' + size + '][field_name] ) 
		$name = esc_attr( $input_table_name ) . 
			 	($is_input_table_js_fields ? '[new_'.chr(39).' + size + '.chr(39).']' : '[' . $input_table_row_key . ']') . //if input table,  [new_' + size + '] inserted
			 	'[' . esc_attr( $field['name'] ) . ']' .  
			 	($field['type'] == 'multiselect' ? '[]' : '');
	}
	
	//class, name, id, custom_attributes declared once
	$general_attributes = 'class="' . esc_attr( $field['class'] ) . '" ' .
			 'name="' . $name . '" ' .
			 'id="' . esc_attr( $field['id'] ) . '" ' .
			 implode( ' ', $custom_attributes );
		

	wsl_log(null, 'filo-meta-box-functions.php 2 $field: ' . wsl_vartotext($field));
	
	$eol='';
	switch ( $field['type'] ) {
		
		// Standard text inputs and subtypes like 'number'
		case 'text':
		case 'price':
		case 'decimal':
		case 'number':
		case 'date':
		
			//texts
			switch ( $field['type'] ) {
				case 'price' :
					//$field['class'] .= ' wc_input_price'; //it is added earlyer
					$field['value']  = wc_format_localized_price( $field['value'] );
				break;
				case 'decimal' :
					//$field['class'] .= ' wc_input_decimal'; //it is added earlyer
					$field['value']  = wc_format_localized_decimal( $field['value'] );
				break;
			}
		
			if( $field['type'] == 'number' ) {
				$field_type = 'number';
			} else {
				$field_type = 'text';
			}
		
			echo rtrim( $general_wrapper . $eol, '\r\n\t' );
			echo rtrim( $general_label . $eol, '\r\n\t' );
			echo rtrim(	
					'<input ' . $eol .
					//'type="' . esc_attr( $field['type'] ) . '" ' . $eol .
					//'type="text" ' . $eol .
					 'type="' . esc_attr( $field_type ) . '" ' . $eol .
					 $general_attributes . $eol .
					 'value="' . esc_attr( $field['value'] ) . '"' . $eol .
					 'placeholder="' . esc_attr( $field['placeholder'] ) . '"' . $eol . 
					 '/>'
				, '\r\n\t');
				
			echo rtrim( $general_description, '\r\n\t' );
			echo $general_wrapper_close;
			
			break;	

		// Hidden field
		case 'hidden':
		
			echo rtrim(	
					'<input ' .
					 'type="' . esc_attr( $field['type'] ) . '" ' .
					 $general_attributes .  
					 'value="' . esc_attr( $field['value'] ) . '" ' .
					 '/>'
				, '\r\n\t');
			
			break;	


		// Textarea
		case 'textarea':
			echo rtrim( $general_wrapper, '\r\n\t' );
			echo rtrim( $general_label, '\r\n\t' );
			echo rtrim(	
					'<textarea type="' . esc_attr( $field['type'] ) . '" ' .
					$general_attributes . $eol . 
					'placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . 
					'rows="' . esc_attr( $field['textarea_rows'] ) . '" ' .   
					'cols="' . esc_attr( $field['textarea_cols'] ) . '" ' .
					'>' . esc_textarea( $field['value'] ) . '</textarea> '					
				, '\r\n\t');

			echo rtrim( $general_description, '\r\n\t' );
			echo $general_wrapper_close;									

			break;
			
		// Checkbox
		case 'checkbox':
			echo rtrim( $general_wrapper, '\r\n\t' );
			echo rtrim( $general_label, '\r\n\t' );
			echo rtrim(	
					'<input ' .
					'type="' . esc_attr( $field['type'] ) . '" ' .
					$general_attributes .  
					'value="' . esc_attr( $field['cbvalue'] ) . '" ' . 
					checked( $field['value'], $field['cbvalue'], false ) .  
					 '/>'
				, '\r\n\t');

			echo rtrim( $general_description, '\r\n\t' );
			echo $general_wrapper_close;			

			break;
			
		// Select fields			
		case 'select':
		case 'multiselect':
			echo rtrim( $general_wrapper, '\r\n\t' );
			echo rtrim( $general_label, '\r\n\t' );
			echo rtrim(	
					'<select ' . 
					$general_attributes . 
					($field['type'] == 'multiselect' ? 'multiple="multiple"' : '') .
					//' style="width:100%;" ' . 
					'>'
				, '\r\n\t');

			if (isset( $field['options'] ) && is_array( $field['options'] ) )
			foreach ( $field['options'] as $key => $value ) {
				echo rtrim(					
						'<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '>' . 
						esc_html( $value ) . 
						'</option>'
					, '\r\n\t');						
			}

			echo '</select> ';

			echo rtrim( $general_description, '\r\n\t' );
			echo $general_wrapper_close;		

			break;

		// Radio button
		case 'radio':
			echo rtrim(
					'<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '">
					<legend>' . wp_kses_post( $field['label'] ) . '</legend>
					<ul class="wc-radios">'
				, '\r\n\t');					

			if (isset( $field['options'] ) && is_array( $field['options'] ) )
			foreach ( $field['options'] as $key => $value ) {
				echo rtrim(					
						'<li><label><input ' . 
		        		'name="' . esc_attr( $field['name'] ) . '" ' .
		        		'value="' . esc_attr( $key ) . '" ' .
		        		'type="radio" ' .
		        		'class="' . esc_attr( $field['class'] ) . '" ' .
		        		checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . 
		        		'/> ' . esc_html( $value ) . 
		        		'</label></li>'
					, '\r\n\t');						
			}

    		echo '</ul>';

			echo rtrim( $general_description, '\r\n\t' );
			echo '</fieldset>';

			break;

		// Hidden field
		case 'html_code':
		
			echo rtrim( $general_wrapper . $eol, '\r\n\t' );
			echo rtrim( $general_label . $eol, '\r\n\t' );
		
			echo $field['html_content'];
			
			break;				
			
			
		// Default: run an action
		default:
			do_action( 'filo_metabox_field_type_' . $field['type'], $field, $readonly );
			break;			

	}

}


function filo_add_readonly_attribute ( $field, $readonly ) {

	//add "readonly" html custom attribut to each html field
	if (isset( $field ) && is_array( $field ) && $readonly )	{
		if ( $field['type'] == 'select' )
			$field['custom_attributes']['disabled'] = ''; //select fields has "disabled" attribute, other fields has readonly 
		else 
			$field['custom_attributes']['readonly'] = '';

				
		$field['type'] = str_replace(array ('email', 'number', 'color', 'multiselect', 'single_select_page', 
													'single_select_country', 'multi_select_countries'), 'text', $field['type']);
													
		$field['class'] = isset( $field['class'] ) ? $field['class'] : null; //eliminate Notice: Undefined index error if $field['class'] does not exist 													
		$field['class'] = str_replace('date-picker', '', $field['class']);
	}

	return $field;
	
}
add_filter( 'filo_output_field', 'filo_add_readonly_attribute', 10, 2 );


/**
 * Save input table into post meta
 *
 * @access public
 * @param string $input_table_name
 * @param array $fields fields meta datas
 * @param array $post_values $_POST
 * @return void
 */
function filo_save_input_table( $input_table_name, $fields, $post_values ) {
		

	wsl_log(null, 'filo-meta-box-functions.php 1 $post_values: ' . wsl_vartotext($post_values));
	
	if ( isset( $post_values[$input_table_name] ) && is_array( $post_values[$input_table_name] ) ) //table name: e.g: journal_entries
	foreach ( $post_values[$input_table_name] as $input_table_row_key => $input_table_row_values ) {
			
		wsl_log(null, 'filo-meta-box-functions.php 2 $input_table_row_key: ' . wsl_vartotext($input_table_row_key));
		wsl_log(null, 'filo-meta-box-functions.php 2 $input_table_row_values: ' . wsl_vartotext($input_table_row_values));    		
    	if ( strpos ( $input_table_row_key, 'new' ) === 0 ) { //if row key contains new (it has to be inserted, otherwise updated)
    	
    		wsl_log(null, 'filo-meta-box-functions.php 3 $post_values[post_ID]: ' . wsl_vartotext($post_values['post_ID']));
    		$new_row_post_data['post_type']     = $input_table_name;
			$new_row_post_data['post_parent']   = $post_values['post_ID'];
    		
    		//$row_post_id = wp_insert_post( $new_row_post_data );
			$post_id = wp_insert_post( $new_row_post_data, true );
			
			if ( is_wp_error( $post_id ) ) {
				$error_message = $post_id;
				wsl_log(null, 'filo-meta-box-functions.php filo_save_input_table ERROR: ' . wsl_vartotext($error_message));
				throw new FILO_Validation_Exception( wsl_vartotext($error_message), 400 );
			}
			
			$row_post_id = $post_id;
			
			wsl_log(null, 'filo-meta-box-functions.php 3 $row_post_id: ' . wsl_vartotext($row_post_id));    	    		
		} else {
			
			$row_post_id = $input_table_row_key;
			wsl_log(null, 'filo-meta-box-functions.php 4 $row_post_id: ' . wsl_vartotext($row_post_id));					
			
		} 
		wsl_log(null, 'filo-meta-box-functions.php 5 $fields: ' . wsl_vartotext($fields));
		
		// save all field
		foreach ( $fields as $field ) {
			wsl_log(null, 'filo-meta-box-functions.php $field 6: ' . wsl_vartotext($field));
						
			update_post_meta( $row_post_id, '_' . $field['id'], wc_clean( $input_table_row_values[$field['id']] ) ); // e.g.: field_id = jei_posting_date 
		}
		
	}
	
}

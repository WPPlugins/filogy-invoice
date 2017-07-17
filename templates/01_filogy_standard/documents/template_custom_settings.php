<?php
/**
 * Add custom setting field to customizer that is specific to this template
 */
function filo_doc_customizer_add_template_custom_settings( $wp_customize, $setting_id_prefix, $section_key, $start_priority ) {

		wsl_log(null, 'template_custom_settings.php 01 $setting_id_prefix: ' . wsl_vartotext( $setting_id_prefix ));

		$priority = $start_priority;
		
		
		// pdf_gen_doc_format
		
		$setting_id = $setting_id_prefix . '[pdf_gen_doc_format]';		
		$wp_customize->add_setting( $setting_id, array(
			'default'           => 'classic', 
			'transport'         => 'refresh', //'postMessage',
			'type' 				=> 'option',
		) );
	
		//type: select
		$wp_customize->add_control( $setting_id, array(
			'label' => __('PDF Document Format' , 'filo_text'),
			'description' => 
					__( 'Choose format of invoice items: ', 'filo_text') . '</br>' .
					'- ' . __( 'Classic: Simple item line columns, shipping fees and other fees are in invoice summary lines.', 'filo_text') . '</br>' .
					'- ' . __( 'Extra Lines: Simple item line columns, shipping fee and other fees placed as invoice line item.', 'filo_text') . '</br>' .
					'- ' . __( 'Detailed: More item line columns, contain VAT Amounts and Gross Totals for every line.', 'filo_text') . '</br>',
			'section'  => $section_key,
			'type' => 'select',
			'choices' => array(
					'classic'           => __( 'Classic (default)', 'filo_text' ),
					'extra_lines'       => __( 'Extra Lines', 'filo_text' ),
					'detailed'          => __( 'Detailed', 'filo_text' ),
				),
			'priority' => $priority,
		) );

		
		$priority ++;	

		
		// item_table_footer_label_column
		
		$setting_id = $setting_id_prefix . '[item_table_footer_label_column]';		
		$wp_customize->add_setting( $setting_id, array(
			'default'           => 'FILO_Widget_Invbld_Line_Item_Name', 
			'transport'         => 'refresh', //'postMessage',
			'type' 				=> 'option',
		) );
	
		//type: select
		$wp_customize->add_control( $setting_id, array(
			'label' => __('Items Footer Label Column' , 'filo_text'),
			'description' => __( 'PDF Document Items Table Footer Label Column', 'filo_text' ),
			'section'  => $section_key,
			'type' => 'select',
			'choices' => array(
					'FILO_Widget_Invbld_Line_Item_Name'      => __( 'Description (default)', 'filo_text' ),
					'FILO_Widget_Invbld_Line_Qty'            => __( 'Qty', 'filo_text' ),
					'FILO_Widget_Invbld_Line_Unit_Total_Net' => __( 'Unit Price', 'filo_text' ),
					'FILO_Widget_Invbld_Line_Tax_Labels'     => __( 'Vat Rates', 'filo_text' ),
					
				),
			'priority' => $priority,
		) );
		
		$priority ++;	


		/*
		// enable_tax_detail_lines
		
		$setting_id = $setting_id_prefix . '[enable_tax_detail_lines]';		
		$wp_customize->add_setting( $setting_id, array(
			'default'           => '', 
			'transport'         => 'refresh', //'postMessage',
			'type' 				=> 'option',
		) );
	
		//type: select
		$wp_customize->add_control( $setting_id, array(
			'label' => __( 'Display Tax detail lines', 'filo_text' ),
			'description' => __( 'In case of "Detailed" format display Tax summary lines in a separate table.', 'filo_text' ),
			'section'  => $section_key,
			'type' => 'select',
			'choices' => array(
					'1'      => __( 'Yes (default)', 'filo_text' ),
					''       => __( 'No', 'filo_text' ),
				),
			'priority' => $priority,
		) );
		*/
		
}

add_action( 'filo_doc_customizer_add_template_custom_settings', 'filo_doc_customizer_add_template_custom_settings', 10, 4 );
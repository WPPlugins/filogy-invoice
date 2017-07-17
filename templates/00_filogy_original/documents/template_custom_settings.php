<?php
/**
 * Add custom setting field to customizer that is specific to this template
 */
function filo_doc_customizer_add_template_custom_settings( $wp_customize, $setting_id_prefix, $section_key, $start_priority ) {

		$priority = $start_priority;

		$setting_id = $setting_id_prefix . '[not_customizable_text]';
		
		$wp_customize->add_setting( $setting_id, array() );
		
		$wp_customize->add_control( new FILO_Customize_Header_Control( $wp_customize, $setting_id, array(
				'label'	=> __( 'This template is not customizable here!', 'filo_text'),
				'description'	=> __( 'This is an old deprecated template, that is not customizable in this customizer window. If you have already used this template, you can use it hereinafter, but it is not recommended to chose this template if you need a new one. This template can be customized in "Documents" tab of Woocommerce Settings page, if "Enable deprecated document templates" checkbox is switched on there.', 'filo_text'),
				'section' => $section_key,
				'priority' => $priority,
			) 
		));			
		
}

add_action( 'filo_doc_customizer_add_template_custom_settings', 'filo_doc_customizer_add_template_custom_settings', 10, 4 );
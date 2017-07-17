<?php
/**
 * Customizer Control for delete skin option
 *
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Delete_Opt_Control extends WP_Customize_Control { 

	protected function render() {

		$act_option_stored_name = FILO_Customize_Manager::get_act_option_stored_name();
		
		$act_option_stored_name = FILO_Customize_Manager::get_act_option_stored_name();
		
		//wsl_log(null, 'class-filo-customize-delete-opt-control.php render $act_option_stored_name: ' . wsl_vartotext( $act_option_stored_name ));

		//$button_url = $this->get_act_url();
		$button_url = wsl_get_act_url();
		
		$button_url .= '&filo_delete_skin=' . $act_option_stored_name;
		
		// We have to create nonce specially, only the nonce parameter without the full url, because wp_nonce_url encode our URL thus if our space has already encoded to %20, it enocodes the % again, and we get %2520 and if we does not apply encoding before wp_nonce_url, then it converts space to +, thus we cannot get %20 for spaces using wp_nonce_url
		// That is why we generate only nonce field, and append it to our previously generated url. (// http://stackoverflow.com/questions/16084935/a-html-space-is-showing-as-2520-instead-of-20) It cannot be applied for the above reason: $button_url_with_nonce = wp_nonce_url( $button_url, 'filo_delete_skin_' . $act_option_stored_name, 'filo_delete_skin_nonce');
		$nonce = wp_nonce_url( '', 'filo_delete_skin_' . $act_option_stored_name, 'filo_delete_skin_nonce');
		
		//wsl_log(null, 'class-filo-customize-delete-opt-control.php render $nonce: ' . wsl_vartotext( $nonce ));
		//wsl_log(null, 'class-filo-customize-delete-opt-control.php render $button_url 0: ' . wsl_vartotext( $button_url ));
		
		$button_url_with_nonce = $button_url . '&' . substr($nonce, 1) . '&filo_new_template_name=null&filo_new_opt_name=null'; // change ? to &: ?filo_delete_skin_nonce=e5951c9a28 -> &filo_delete_skin_nonce=e5951c9a28
		
		// We have two delete buttons. The first can be pressed, that activate confirmations, the second do the real delete.
		printf(
			' <a class="button-link filo-customize-delete-opt-button filo-customize-button" href="#">%1$s</a>',
			esc_html( $this->label )
		);

		// span is needed to become click triggeredby jquery (http://stackoverflow.com/questions/5811122/how-to-trigger-a-click-on-a-link-using-jquery) 		
		printf(
			' <a class="button-link filo-customize-delete-opt-button2 filo-customize-button" href="%1$s"><span>%2$s</span></a>',
			$button_url_with_nonce,
			esc_html( $this->label )
		);		
				
	}

}
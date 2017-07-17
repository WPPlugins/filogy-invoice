<?php
/**
 * Customizer Control for color generator button
 *
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Generate_Color_Palette_Control extends WP_Customize_Control { 

	protected function render() {

		?>
			<div class="class="filo_customize_generate_color_palette_button_wrapper">
				<a class="button button-primary filo_customize_generate_color_palette_button page-title-action" style="margin: 10px 0;"><?php echo __( 'Generate and Update Colors Below', 'filo_text' );?></a>
			</div>
		<?php
				
	}
	
}

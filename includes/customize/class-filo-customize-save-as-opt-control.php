<?php
/**
 * Customizer Control for Save as Option field
 * 
 * 1. Add a copy of save button ander the field
 * 2. Add a hidden button, and the original event of customizer save button is bind there in class-filo-customize-manager.php > filo_before_save() JS function (hidden by css)
 * 
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 

class FILO_Customize_Save_As_Opt_Control extends WP_Customize_Control { 

	protected function render() {

		parent::render();

		?>
		<input type="submit" name="save" id="save" class="button button-primary save filo-customize-button" value="Save As ... &amp; Publish" style="float: right;">
		<input type="submit" name="save2" id="filo-customize-save2" class="button-primary" value="Save2" style="float: right;">
		<?php
				
	}
	
}

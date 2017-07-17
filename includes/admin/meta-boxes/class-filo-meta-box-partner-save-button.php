<?php
if ( !defined('ABSPATH') ) exit;

/**
 * Partner Trans Save Metabox
 *
 * @package     Filogy/Admin/Metabox
 * @subpackage 	Financials
 * @category    Admin/Metabox
 */
class FILO_Meta_Box_Partner_Save_Button {

	/**
	 * output
	 */
	public static function output( $post ) {

		?>
			<p class="submit filo-partner-save-wrapper">
				<input type="submit" name="submit" id="submit" class="button button-primary" value="Update Partner">
			</p>
			
			<style>
				.filo-partner-save-wrapper{
					text-align: right !important;
					margin-top: 0 !important;
					margin: 0;
					padding: 0;
				}
			</style>

		<?php	

	}

}

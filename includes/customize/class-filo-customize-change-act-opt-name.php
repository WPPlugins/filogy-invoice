<?php
/**
 * Customizer Control for Changing Actual Option Name (Change Skin)
 *
 * Control for changing actual template and skin option name
 * This contains two not saveable fields and a button containing the actual url plus the new template name and option name on what should be switched
 * - add filo_doc_filo_new_template_name field
 * - add filo_doc_filo_new_opt_name field
 * - add a button
 * 
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Change_Act_Opt_Name extends WP_Customize_Control {
	
	public $label_templ;
	public $choices_templ;	
	 

	protected function render() {

		$choices_of_new_template_name = array_merge( array('' => ''), $this->choices_templ);
		$choices_of_new_opt_name = array_merge( array('' => ''), $this->choices);
		
		
		?>
		<label class="customize-control">
			<span class="customize-control-title"><?php echo $this->label_templ; ?></span>
					
			<select id="filo_doc_filo_new_template_name">
				<?php
				foreach ( $choices_of_new_template_name as $ch_value => $ch_label )
					echo '<option value="' . esc_attr( $ch_value ) . '"' . '>' . $ch_label . '</option>';
				?>
			</select>
		</label>	
				
		<label class="customize-control">
			<span class="customize-control-title"><?php echo $this->label; ?></span>
					
			<select id="filo_doc_filo_new_opt_name">
				<?php
				foreach ( $choices_of_new_opt_name as $ch_value => $ch_label )
					echo '<option value="' . esc_attr( $ch_value ) . '"' . '>' . $ch_label . '</option>';
				?>
			</select>
		</label>		
		
		<?php
		
		$button_url = wsl_get_act_url();
			
		// if the actual url has not contain filo_new_template_name or filo_new_opt_name parameter yet, then add this parameter to it for using as Change button url 	
		if ( ! array_key_exists ( 'filo_new_template_name' , $_GET ) ) {
			$button_url .= '&filo_new_template_name=null'; //it is replaced by JavaScript
		}

		if ( ! array_key_exists ( 'filo_new_opt_name' , $_GET ) ) {
			$button_url .= '&filo_new_opt_name=null'; //it is replaced by JavaScript
		}

		printf(
			' <a class="button button-primary filo-doc-change-act-opt-name filo-customize-button" href="%1$s">%2$s</a>',
			$button_url,
			__( 'Open', 'filoinv_text' )
		);
	}

	/*
	// moved to wsl-core-functions.php
	//https://css-tricks.com/snippets/php/get-current-page-url/#comment-1604248
	private function get_act_url() {
		$act_url  = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ) ? 'https' : 'http';
		$act_url .= '://' . $_SERVER['SERVER_NAME'];
		$act_url .= in_array( $_SERVER['SERVER_PORT'], array( '80', '443' ) ) ? '' : ":" . $_SERVER['SERVER_PORT'];
		$act_url .= $_SERVER['REQUEST_URI'];
		return $act_url;
	}
	*/
	
	/**
	 * Enqueue scripts/styles
	 */
	/*public function enqueue() {
		
		parent::enqueue();

		//If filo_doc_act_opt_name field is changed, then update the URL of "Change" link using JavaScript
	 	//But the following script has no effect on the control itself, only on the preview window.
		//So this is implemented in print_scripts_of_controls() function of FILO_Customize_Manager 
		wp_enqueue_script( 'filo-customizer-controls', FILO()->plugin_url() . '/assets/js/filo-customizer-control.js', array(), FILOFW_VERSION );
	
	}*/
	
}

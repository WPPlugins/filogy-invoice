<?php
/**
 * Customizer control for displaying header text
 *
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Header_Control extends WP_Customize_Control {

	public function render_content() {
		?>
		<label>
			<h1 class="customize-control-title"><?php echo esc_html( $this->label ); ?></h1>
		</label>
		
		<div class="filogy-notice" style="display: block;">
			<em><?php echo esc_html( $this->description ); ?></em>
		</div>		
		<?php
	}
	
	/**
	 * Enqueue scripts/styles
	 */
	public function enqueue() {
		
		parent::enqueue();
		wp_enqueue_style( 'filo-customizer-controls', FILO()->plugin_url() . '/assets/css/filo-customizer-controls.css', array(), FILOFW_VERSION );
	
	}
	
}

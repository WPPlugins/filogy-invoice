<?php
/**
 * Customizer Control for mycolor references
 *
 * Add an additional class to the normal control: customize-control-color-palette-item_select
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Mycolor_Reference_Control extends WP_Customize_Control { 
	//We have to register it in customize_register:   $wp_customize->register_control_type( 'FILO_Customize_Adv_Color_Control' );
	
	protected function render() {
		$id    = 'customize-control-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
		$class = 'customize-control customize-control-' . $this->type;

		?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?> customize-control-color-palette-item_select">
			<div class="customize-control-color-palette-item_select_frame frame1">
				&nbsp;
			</div>
			
			<div class="customize-control-color-palette-item_select_frame frame2">
				<?php parent::render_content(); ?>
			</div>
			
		</li><?php
	}
	
	/**
	 * Enqueue scripts/styles
	 */
	public function enqueue() {
		
		parent::enqueue();
		wp_enqueue_style( 'filo-customizer-controls', FILO()->plugin_url() . '/assets/css/filo-customizer-controls.css', array(), FILOFW_VERSION );
	
	}
	
}

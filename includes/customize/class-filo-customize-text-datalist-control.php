<?php
 /**
 * Customizer Control for text datalist field 
 *
 * HTML5 datalist field (combo)
 * 
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Text_Datalist_Control extends WP_Customize_Control {

	public $type = 'text';
	public $statuses;

	/**
	 * render_content
	 */
	public function render_content() {
		
		?>

		<label>
			
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
			<input type="<?php echo esc_attr( $this->type ); ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> list="<?php echo esc_attr( $this->settings[ 'default' ]->id ) ?>_list"/>
			
			<datalist id="<?php echo esc_attr( $this->settings[ 'default' ]->id ) ?>_list">
				<?php
				foreach ( $this->choices as $value => $label )
					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . '>' . $label . '</option>';
				?>
			</datalist>
			
		</label>
		
		<?php
	
	}

}
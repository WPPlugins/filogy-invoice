<?php
/**
 * Customizer Control for select font
 *
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Fontselect_Control extends WP_Customize_Control {

	public $type = 'fontselect';
	public $statuses;

	/**
	 * __construct.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		//$this->statuses = array( '' => __('Default') );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * enqueue
	 */
	public function enqueue() {

		wp_enqueue_script(
			'filo_fontselect',
			//FILOFW()->plugin_url() .  '/modules/fontselect-jquery-plugin/jquery.fontselect.min.js',
			FILOFW()->plugin_url() .  '/modules/fontselect-jquery-plugin/jquery.fontselect.js',
			array( 'jquery' )
		);		
	
		wp_enqueue_style( 
			'filo_fontselect',
			FILOFW()->plugin_url() .  '/modules/fontselect-jquery-plugin/fontselect.css' 
		);
		
	}

	/**
	 * to_json
	 */
	public function to_json() {
		parent::to_json();
		//$this->json['defaultValue'] = $this->setting->default;
		//$this->json['value'] = $this->value();
		//wsl_log(null, 'class-filo-customize-fontselect-control.php to_json $this->json: ' .  wsl_vartotext($this->json)); //RaPe
		
	}

	/**
	 * render_content
	 */
	public function render_content() {
		
		$filo_fonts = new FILO_Fonts();
		$filo_google_font_names = $filo_fonts->get_google_font_names();
		?>
		
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
		
			<script>
				var filo_google_font_names = <?php echo json_encode( $filo_google_font_names ); ?>;
				jQuery('#font-<?php echo $this->instance_number; ?>').fontselect( filo_google_font_names );
			</script>
			<input type="text" id="font-<?php echo $this->instance_number; ?>" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
	
	
		</label>
	
	<?php
	}
	/**
	 * content_template
	 */
	public function content_template() {
	}
}
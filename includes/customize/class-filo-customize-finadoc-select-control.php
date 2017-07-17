<?php
/**
 * Customizer Control for select financial document (template)
 *
 * @package     Filogy/Admin
 * @subpackage 	Customizer
 * @category    Admin
 */ 
class FILO_Customize_Finadoc_Select_Control extends WP_Customize_Control {

	public $type = 'finadoc_select';
	public $doc_types;
	public $orderby;
	public $item_limit;
	public $show_option_none;
	public $option_none_value;
	public $show_option_no_change;
	

	/**
	 * __construct.
	 */
	public function __construct( $manager, $id, $args = array() ) {
		//$this->statuses = array( '' => __('Default') );
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * render_content
	 */
	protected function render_content() {
		
		include_once(FILOFW()->plugin_path() .  '/includes/admin/filo-admin-general-functions.php');
		
		$args['doc_types'] = $this->doc_types;
		$args['orderby'] = $this->orderby;
		$args['item_limit'] = $this->item_limit;
		$args['show_option_none'] = $this->show_option_none;
		$args['option_none_value'] = $this->option_none_value;
		$args['show_option_no_change'] = $this->show_option_no_change;
		
		$output = filo_dropdown_finadocs( $args, $enable_select_tag = false ); // we do not need select tag, just the inner options
		
		//wsl_log(null, 'class-filo-customize-finadoc-select-control.php render $output: ' . wsl_vartotext($output));
		
		?>
		
		
		<label class="customize-control">
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>


			<select <?php $this->link(); ?>>
				
				<?php echo $output; ?>
			</select>
					
		</label>	
				
		
		<?php
		
	}

}
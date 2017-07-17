<?php

if ( !defined('ABSPATH') ) exit;

/**
 * Generated Document Payment Method Template
 * 
 * @package     Filogy/DocumentTemplates
 * @subpackage 	Financials
 * @category    DocumentTemplates
 */

//add_filter('filo_payment_method_data_html', 'filo_payment_method_data_html');

?>

<div id="filo_payment_method_data_<?php echo $order->get_payment_method(); ?>" class="filo_payment_method_data"> <!--filo_doc_section-->		

	
	<div class="filo_headline"><?php _ex( 'Payment method details', 'filo_doc', 'filo_text' ); ?></div>
	
	<div class="filo_payment_method_title">
		<?php echo $order->get_payment_method_title(); ?>
	</div>
	
	<div class="filo_payment_method_data">
		<?php
			$payment_method_data_html = $order->get_payment_method_data_html();
			//$payment_method_data_html = apply_filters('filo_payment_method_data_html', $payment_method_data_html);
			
			//Move it from the function below, because this funciton was "redeclared" in case of bulk document generation
			$payment_method_data_html = str_replace('<h2>', '<div class="filo_headline_2">', $payment_method_data_html);
			$payment_method_data_html = str_replace('</h2>', '</div>', $payment_method_data_html);
			$payment_method_data_html = str_replace('<h3>', '<div class="filo_headline_3">', $payment_method_data_html);
			$payment_method_data_html = str_replace('</h3>', '</div>', $payment_method_data_html);
			$payment_method_data_html = str_replace('<strong>', '<div class="filo_value">', $payment_method_data_html);
			$payment_method_data_html = str_replace('</strong>', '</div>', $payment_method_data_html);
						
			echo $payment_method_data_html; 
		?>
	</div>

</div>
	
<?php

/*	function filo_payment_method_data_html ($payment_method_data_html) {
		
		
		$payment_method_data_html = str_replace('<h2>', '<div class="filo_headline_2">', $payment_method_data_html);
		$payment_method_data_html = str_replace('</h2>', '</div>', $payment_method_data_html);
		$payment_method_data_html = str_replace('<h3>', '<div class="filo_headline_3">', $payment_method_data_html);
		$payment_method_data_html = str_replace('</h3>', '</div>', $payment_method_data_html);
		$payment_method_data_html = str_replace('<strong>', '<div class="filo_value">', $payment_method_data_html);
		$payment_method_data_html = str_replace('</strong>', '</div>', $payment_method_data_html);

		return $payment_method_data_html;

	}
*/	
?>
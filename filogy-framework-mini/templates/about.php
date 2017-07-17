<?php
/**
 * 
 * General element template of about page
 * 
 * @package     Filogy/Templates
 * @subpackage 	Framework
 * @author      WebshopLogic - Peter Rath
 * @author      WordPress (origina file)
 * @category    Templates
 * 
 * @based_on	about.php file in WordPress 
 */
?>

		<div class="feature-section two-col">
			<hr class="line_between_rows">
			<?php $i = 0; ?>
			<?php foreach ( $major_features as $feature ) : ?>
			<?php $i++; ?>				
			<?php wsl_log(null, 'class-filo-about.php $feature: ' .  wsl_vartotext($feature)); ?>	
			<div class="col">
				<h3><?php echo $feature['heading']; ?></h3>
				<div class="media-container">
					<?php
					// Video.
					if ( is_array( $feature['src'] ) ) :
						echo wp_video_shortcode( array(
							'mp4'      => $feature['src']['mp4'],
							'ogv'      => $feature['src']['ogv'],
							'webm'     => $feature['src']['webm'],
							'loop'     => true,
							'autoplay' => true,
							'width'    => 500,
							'height'   => 284
						) );

					// Image.
					else:
					?>
					<img src="<?php echo esc_url( $feature['src'] ); ?>" />
					<?php endif; ?>
				</div>
				
				<p><?php echo $feature['description']; ?></p>
			</div>
			<?php if ( $i % 2 == 0 ) : //It's even: line after every second block, thus uner every row ?>
				<hr class="line_between_rows">
			<?php endif; ?>	
			<?php endforeach; ?>
			<?php if ( $i % 2 != 0 ) : //It's odd: line after the last row, if was not an even row right before ?>
				<hr class="line_between_rows">
			<?php endif; ?>	

		</div>

		<div class="feature-section three-col">
			<?php foreach ( $minor_features as $feature ) : ?>
			<div class="col">
				<div class="svg-container">
					<img src="<?php echo esc_attr( $feature['src'] ); ?>" />
				</div>
				<h3><?php echo $feature['heading']; ?></h3>
				<p><?php echo $feature['description']; ?></p>
			</div>
			<?php endforeach; ?>
		</div>

		<?php if ( isset($tech_features) and !empty($tech_features) ) { //MODIFY ?>
		<div class="changelog">
			<h3><?php _e( 'Change log' ); ?></h3>

			<div class="feature-section under-the-hood three-col">
				<?php foreach ( $tech_features as $feature ) : ?>
				<div class="col">
					<h4><?php echo $feature['heading']; ?></h4>
					<p><?php echo $feature['description']; ?></p>
				</div>
				<?php endforeach; ?>
			</div>

		</div>
		<?php } ?>		
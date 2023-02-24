<?php

function rates_page_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'column_1_title' => '',
		'column_1_desc' => '',
		'column_2_title' => '',
		'column_2_desc' => '',
		'column_3_title' => '',
		'column_3_desc' => '',
		'call_to_action_text' => '',
		'call_to_action_button_text' => '',
		'call_to_action_button_url' => '',
		'order' => '',
		'orderby' => ''
	), $atts ) );
	
	ob_start(); ?>
	
	<div class="mobile-rate-table-msg msg default"><p><?php esc_html_e('Mobile users, please swipe left/right to view prices','chauffeur'); ?></p></div>

		<!-- BEGIN .clearfix -->
		<div class="service-rate-table-wrapper clearfix">

			<!-- BEGIN .service-rate-table-inner-wrapper -->
			<div class="service-rate-table-inner-wrapper clearfix">
	
				<?php 
				
				if ( $order == '' ) {
					$order = 'DESC';
				} else {
					$order = $order;
				}
				
				if ( $orderby == '' ) {
					$orderby = 'title';
				} else {
					$orderby = $orderby;
				}
				
				// Query #1
				global $post;
				global $wp_query;
				$prefix = 'chauffeur_';
				$args = array(
					'post_type' => 'rates',
					'order' => $order,
					'orderby' => $orderby,
					'posts_per_page' => '9999'
				); ?>

				<?php $wp_query = new WP_Query( $args ); ?>
				<?php if ($wp_query->have_posts()) : ?>

					<!-- BEGIN .car-list-wrapper -->
					<div class="car-list-wrapper">

						<div class="blank-header"></div>

						<!-- BEGIN .car-list-inner -->
						<div class="car-list-inner">
							
							<?php while($wp_query->have_posts()) : ?>
								
								<?php $wp_query->the_post(); ?>
									
									<div class="car-list-section clearfix">
										
										<?php if( has_post_thumbnail() ) { ?>
											
											<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'chauffeur-image-style7' ); ?>
											<?php echo '<img src="' . $src[0] . '" alt="" />'; ?>

										<?php } ?>

										<p><strong><?php the_title(); ?></strong></p>
	
									</div>
	
							<?php endwhile; ?>

						<!-- END .car-list-inner -->
						</div>

					<!-- END .car-list-wrapper -->
					</div>

				<?php endif; ?>
				<?php wp_reset_query(); ?>
		
				<?php 
				//Query #2
				global $post;
				global $wp_query;
				$prefix = 'chauffeur_';
				$args = array(
					'post_type' => 'rates',
					'order' => $order,
					'orderby' => $orderby,
					'posts_per_page' => '9999'
				); ?>

				<?php $wp_query = new WP_Query( $args ); ?>
				<?php if ($wp_query->have_posts()) : ?>
					<!-- BEGIN .service-rate-wrapper -->
					<div class="service-rate-wrapper">

						<div class="service-rate-header"><p><strong><?php echo $column_1_title; ?></strong> <?php echo $column_1_desc; ?></p></div>

						<!-- BEGIN .service-rate-inner -->
						<div class="service-rate-inner">

							<?php while($wp_query->have_posts()) : ?>
								
								<?php $wp_query->the_post(); ?>

								<?php
								// Get rates data
								$rates_currency_unit = get_post_meta($post->ID, $prefix.'rates_currency_unit', true);	
								$rates_col_1_price = get_post_meta($post->ID, $prefix.'rates_col_1_price', true);	
								$rates_col_1_unit = get_post_meta($post->ID, $prefix.'rates_col_1_unit', true);	
								$rates_col_1_description = get_post_meta($post->ID, $prefix.'rates_col_1_description', true);	
								?>

								<div class="service-rate-section">
									<p><strong><span><?php echo $rates_currency_unit.$rates_col_1_price; ?></span><?php echo $rates_col_1_unit; ?></strong><?php echo $rates_col_1_description; ?></p>
								</div>

							<?php endwhile; ?>

						<!-- END .service-rate-inner -->
						</div>

					<!-- END .service-rate-wrapper -->
					</div>

				<?php endif; ?>
				<?php wp_reset_query(); ?>
	
				<?php 
				// Query #3
				global $post;
				global $wp_query;
				$prefix = 'chauffeur_';
				$args = array(
					'post_type' => 'rates',
					'order' => $order,
					'orderby' => $orderby,
					'posts_per_page' => '9999'
				); ?>

				<?php $wp_query = new WP_Query( $args ); ?>
				<?php if ($wp_query->have_posts()) : ?>

					<!-- BEGIN .service-rate-wrapper -->
					<div class="service-rate-wrapper">

						<div class="service-rate-header"><p><strong><?php echo $column_2_title; ?></strong> <?php echo $column_2_desc; ?></p></div>

						<!-- BEGIN .service-rate-inner -->
						<div class="service-rate-inner">

							<?php while($wp_query->have_posts()) : ?>
								<?php $wp_query->the_post(); ?>

								<?php
								// Get rates data
								$rates_currency_unit = get_post_meta($post->ID, $prefix.'rates_currency_unit', true);	
								$rates_col_2_price = get_post_meta($post->ID, $prefix.'rates_col_2_price', true);	
								$rates_col_2_unit = get_post_meta($post->ID, $prefix.'rates_col_2_unit', true);	
								$rates_col_2_description = get_post_meta($post->ID, $prefix.'rates_col_2_description', true);	
								?>

								<div class="service-rate-section">
									<p><strong><span><?php echo $rates_currency_unit.$rates_col_2_price; ?></span><?php echo $rates_col_2_unit; ?></strong><?php echo $rates_col_2_description; ?></p>
								</div>

							<?php endwhile; ?>

						<!-- END .service-rate-inner -->
						</div>

					<!-- END .service-rate-wrapper -->
					</div>

				<?php endif; ?>
				<?php wp_reset_query(); ?>
		
				<?php 
				// Query #4
				global $post;
				global $wp_query;
				$prefix = 'chauffeur_';
				$args = array(
					'post_type' => 'rates',
					'order' => $order,
					'orderby' => $orderby,
					'posts_per_page' => '9999'
				); ?>

				<?php $wp_query = new WP_Query( $args ); ?>
				<?php if ($wp_query->have_posts()) : ?>

					<!-- BEGIN .service-rate-wrapper -->
					<div class="service-rate-wrapper">

						<div class="service-rate-header"><p><strong><?php echo $column_3_title; ?></strong> <?php echo $column_3_desc; ?></p></div>

							<!-- BEGIN .service-rate-inner -->
							<div class="service-rate-inner">

								<?php while($wp_query->have_posts()) : ?>
								<?php $wp_query->the_post(); ?>

								<?php
								// Get rates data
								$rates_currency_unit = get_post_meta($post->ID, $prefix.'rates_currency_unit', true);	
								$rates_col_3_price = get_post_meta($post->ID, $prefix.'rates_col_3_price', true);	
								$rates_col_3_unit = get_post_meta($post->ID, $prefix.'rates_col_3_unit', true);	
								$rates_col_3_description = get_post_meta($post->ID, $prefix.'rates_col_3_description', true);	
								?>

								<div class="service-rate-section">
									<p><strong><span><?php echo $rates_currency_unit.$rates_col_3_price; ?></span><?php echo $rates_col_3_unit; ?></strong><?php echo $rates_col_3_description; ?></p>
								</div>

								<?php endwhile; ?>
						
							<!-- END .service-rate-inner -->
							</div>

						<!-- END .service-rate-wrapper -->
						</div>

					<?php endif; ?>
					<?php wp_reset_query(); ?>
		
				<!-- END .service-rate-table-inner-wrapper -->
				</div>

			<!-- END .clearfix -->
			</div>

			<div class="call-to-action-small clearfix">
				<h4><?php echo $call_to_action_text; ?></h4>
				<a href="<?php echo $call_to_action_button_url; ?>" class="call-to-action-button"><?php echo $call_to_action_button_text; ?></a>
			</div>
		
			<?php return ob_get_clean();

}

add_shortcode( 'service_rates_page', 'rates_page_shortcode' );

?>
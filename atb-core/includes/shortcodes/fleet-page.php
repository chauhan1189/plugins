<?php

function fleet_page_shortcode( $atts, $content = null ) {
		
		extract( shortcode_atts( array(
			'posts_per_page' => '10',
			'order' => '',
			'columns' => '',
			'fleet_type' => ''
		), $atts ) );

		global $post;
		global $wp_query;
		global $chauffeur_data;
		
		ob_start();

		// Set Posts Displayed Per Page
		if ( $posts_per_page != '' ) {
			$posts_per_page = $posts_per_page;
		} else {
			$posts_per_page = '1';
		}

		// Set Columns
		if ( $columns == '' ) {
			$columns = '2';
		} else {
			$columns = $columns;
		}

		// Set Posts Display Order
		if ( $order == 'newest' ) {
			$order = 'DESC';
		} elseif ( $order == 'oldest' ) {
			$order = 'ASC';
		} else {
			$order = 'DESC';
		}

		$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

		$args = array(
			'post_type' => 'fleet',
			'posts_per_page' => $posts_per_page,
			'paged' => $paged,
			'order' => $order
		);
		
		// Display From Category
		if ( $fleet_type != '' ) {
		
			$args = array(
				'post_type' => 'fleet',
				'tax_query' => array(
						'relation' => 'AND',
						array(
							'taxonomy' => 'fleet-type',
							'field'    => 'slug',
							'terms'    => $fleet_type,
						),
					),
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				'order' => $order
			);
		
		} else {

			$args = array(
				'post_type' => 'fleet',
				'posts_per_page' => $posts_per_page,
				'paged' => $paged,
				'order' => $order
			);

		}
		
		$wp_query = new WP_Query( $args );
		if ($wp_query->have_posts()) : ?>
		
		<!-- BEGIN .fleet-block-wrapper -->
		<div class="fleet-block-wrapper fleet-<?php echo $columns; ?>-cols clearfix">

			<?php while($wp_query->have_posts()) :
				$wp_query->the_post(); 
				
				$chauffeur_fleet_price_from = get_post_meta($post->ID, 'chauffeur_fleet_price_from', true);
				$chauffeur_fleet_short_description = get_post_meta($post->ID, 'chauffeur_fleet_short_description', true);
				
				?>
				
				<!-- BEGIN .fleet-block -->
				<div class="fleet-block">
					
					<?php if( has_post_thumbnail() ) { ?>
						
						<div class="fleet-block-image">
							
							<a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
								<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'chauffeur-image-style9' ); ?>
								<?php echo '<img src="' . $src[0] . '" alt="" />'; ?>
							</a>
						</div>

					<?php } ?>
					
					<div class="fleet-block-content">
						
						<?php if( $chauffeur_data['hide-pricing'] != '1' ) { ?>
							<div class="fleet-price"><?php echo esc_html_e('From','chauffeur') . ' ' . chauffeur_get_price($chauffeur_fleet_price_from); ?></div>
						<?php } ?>
						
						<h4><a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
						<div class="title-block3"></div>
						<?php echo $chauffeur_fleet_short_description; ?>
					</div>
					
				<!-- END .fleet-block -->
				</div>
				
			<?php endwhile; ?>

		<!-- END .yacht-listing-wrapper -->
		</div>
		
		<?php else : ?>
			<p><?php esc_html_e('No vehicles have been added yet','chauffeur'); ?></p>
		<?php endif;

			if ( $wp_query->max_num_pages > 1 ) : ?>

				<div class="clearboth"></div>

				<?php if(is_plugin_active('wp-pagenavi/wp-pagenavi.php')) {
					echo '<div class="pagination-wrapper clearfix">';
					wp_pagenavi();
					echo '</div>';
					echo '<div class="clearboth"></div>';
				} else { ?>

				<div class="pagination-wrapper clearfix">
					<p class="clearfix">
						<span class="fl prev-pagination"><?php next_posts_link( esc_html__( '&larr; Older posts', 'chauffeur' ) ); ?></span>
						<span class="fr next-pagination"><?php previous_posts_link( esc_html__( 'Newer posts &rarr;', 'chauffeur' ) ); ?></span>	
					</p>
				</div>

				<?php } ?>

			<?php endif; wp_reset_query();
			
			return ob_get_clean();
			
	}

add_shortcode( 'fleet_page', 'fleet_page_shortcode' );

?>
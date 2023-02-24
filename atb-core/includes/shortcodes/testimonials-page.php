<?php

function testimonials_page_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'posts_per_page' => '',
		'order' => ''
	), $atts ) );
	
	global $post;
	global $wp_query;
	$prefix = 'chauffeur_';
	
	// Set Testimonials Displayed Per Page
	if ( $posts_per_page != '' ) {
		$posts_per_page = $posts_per_page;
	} else {
		$posts_per_page = '1';
	}
	
	// Set Testimonials Display Order
	if ( $order == 'newest' ) {
		$testimonials_order = 'DESC';
	} elseif ( $order == 'oldest' ) {
		$testimonials_order = 'ASC';
	} else {
		$testimonials_order = 'DESC';
	}
	
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	
	$args = array(
	'post_type' => 'testimonial',
	'posts_per_page' => $posts_per_page,
	'paged' => $paged,
	);
	
	$wp_query = new WP_Query( $args );
	if ($wp_query->have_posts()) : ?>
	
		<!-- BEGIN .testimonial-list-wrapper -->
		<div class="testimonial-list-wrapper testimonial-list-wrapper-full">
			
		<?php while($wp_query->have_posts()) :
			
			$wp_query->the_post(); ?>
	
			<?php
				// Get testimonial data
				$testimonial_name = get_post_meta($post->ID, $prefix.'testimonial_name', true);
				$testimonial_other_details = get_post_meta($post->ID, $prefix.'testimonial_other_details', true);			
			?>
			
			<!-- BEGIN .testimonial-wrapper -->
			<div class="testimonial-wrapper">
				
				<div><span class="qns-open-quote">“</span><?php global $more;$more = 0;?><?php the_content(''); ?><span class="qns-close-quote">”</span></div>
				
				<?php if( has_post_thumbnail() ) { ?>

					<div class="testimonial-image">
						<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'chauffeur-image-style7' ); ?>
						<?php echo '<img src="' . $src[0] . '" alt="" />'; ?>
					</div>

				<?php } ?>
				
				<div class="testimonial-author"><p><?php echo esc_textarea($testimonial_name); ?> - <?php echo esc_textarea($testimonial_other_details); ?></p></div>
				
			<!-- END .testimonial-wrapper -->
			</div>
			
		<?php endwhile; ?>
		
		<!-- END .testimonial-list-wrapper -->
		</div>
		
		<?php else : ?>
			<p><?php esc_html_e('No testimonials have been added yet','chauffeur'); ?></p>
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

}

add_shortcode( 'testimonials_page', 'testimonials_page_shortcode' );

?>
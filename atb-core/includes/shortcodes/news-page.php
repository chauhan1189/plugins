<?php

function news_page_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'posts_per_page' => '',
		'order' => '',
		'columns' => '',
		'category' => '',
	), $atts ) );
	
	ob_start();
	
	global $post;
	global $wp_query;
	$prefix = 'chauffeur_';
	
	// Set news Displayed Per Page
	if ( $posts_per_page != '' ) {
		$posts_per_page = $posts_per_page;
	} else {
		$posts_per_page = '1';
	}
	
	// Set news Display Order
	if ( $order == 'newest' ) {
		$news_order = 'DESC';
	} elseif ( $order == 'oldest' ) {
		$news_order = 'ASC';
	} else {
		$news_order = 'DESC';
	}
	
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	
	$args = array(
	'post_type' => 'post',
	'posts_per_page' => $posts_per_page,
	'paged' => $paged,
	'cat' => $category
	);
	
	$wp_query = new WP_Query( $args );
	if ($wp_query->have_posts()) : ?>
	
	<?php if ( $columns == '1' ) { ?>
	
		<!-- BEGIN .news-block-wrapper -->
		<div class="news-block-wrapper news-block-wrapper-1-col-listing clearfix">
	
	<?php } else if ( $columns == '2' ) { ?>
	
		<!-- BEGIN .news-block-wrapper -->
		<div class="news-block-wrapper news-block-wrapper-2-col-listing clearfix">
			
	<?php } else if ( $columns == '3' ) { ?>
	
		<!-- BEGIN .news-block-wrapper -->
		<div class="news-block-wrapper news-block-wrapper-3-col-listing clearfix">
	
	<?php } else if ( $columns == '4' ) { ?>
	
		<!-- BEGIN .news-block-wrapper -->
		<div class="news-block-wrapper news-block-wrapper-4-col-listing clearfix">
	
	<?php } else if ( $columns == '5' ) { ?>
		
		<!-- BEGIN .news-block-wrapper -->
		<div class="news-block-wrapper news-block-wrapper-5-col-listing clearfix">
		
	<?php } else { ?>
		
		<!-- BEGIN .news-block-wrapper -->
		<div class="news-block-wrapper news-block-wrapper-1-col-listing clearfix">
		
	<?php } ?>
	
		<?php while($wp_query->have_posts()) :
			
			$wp_query->the_post(); ?>
			
			<?php if ( $columns == '2' || $columns == '3' || $columns == '4' || $columns == '5' ) { ?>
				
				<!-- BEGIN .news-block -->
				<div id="post-<?php the_ID(); ?>" <?php post_class("news-block"); ?>>

					<?php if( has_post_thumbnail() ) { ?>

						<div class="news-block-image">
							<a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
								<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'chauffeur-image-style1' ); ?>
								<?php echo '<img src="' . esc_url( $src[0] ) . '" alt="" />'; ?>
							</a>
						</div>

					<?php } ?>

					<!-- BEGIN .news-content-wrapper -->
					<div class="news-content-wrapper">

						<h3><a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>

						<!-- BEGIN .news-meta -->
						<div class="news-meta clearfix">
							<span class="nm-news-date"><a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
							<span class="nm-news-comments"><?php comments_popup_link(esc_html__( 'No Comments', 'chauffeur' ),esc_html__( '1 Comment', 'chauffeur' ),esc_html__( '% Comments', 'chauffeur' ),'',esc_html__( 'Comments Off','chauffeur')); ?></span>
						<!-- END .news-meta -->
						</div>

						<!-- BEGIN .news-description -->
						<div class="news-description">

							<?php global $more;$more = 0;?>
							<?php the_excerpt(); ?>

						<!-- END .news-description -->
						</div>

					<!-- END .news-content-wrapper -->
					</div>

				<!-- END .news-block -->
				</div>
			
			<?php } else { ?>
			
				<!-- BEGIN .news-block -->
				<div id="post-<?php the_ID(); ?>" <?php post_class("news-block"); ?>>

					<h3><a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>

					<!-- BEGIN .news-meta -->
					<div class="news-meta clearfix">
						<span class="nm-news-date"><a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_time( get_option( 'date_format' ) ); ?></a></span>
						<span class="nm-news-author"><?php esc_html_e( 'By', 'chauffeur' ); ?> <?php the_author_posts_link(); ?></span>
						<span class="nm-news-category"><?php the_category(', '); ?></span>
						<span class="nm-news-comments"><?php comments_popup_link(esc_html__( 'No Comments', 'chauffeur' ),esc_html__( '1 Comment', 'chauffeur' ),esc_html__( '% Comments', 'chauffeur' ),'',esc_html__( 'Comments Off','chauffeur')); ?></span>
					<!-- END .news-meta -->
					</div>

					<?php if( has_post_thumbnail() ) { ?>

						<div class="news-block-image">
							<a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
								<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'chauffeur-image-style2' ); ?>
								<?php echo '<img src="' . esc_url( $src[0] ) . '" alt="" />'; ?>
							</a>
						</div>

					<?php } ?>

					<!-- BEGIN .news-description -->
					<div class="news-description">

						<?php global $more;$more = 0;?>
						<?php the_content(esc_html__('Read More','chauffeur') . ' <i class="fa fa-angle-right"></i>'); ?>

					<!-- END .news-description -->
					</div>

				<!-- END .news-block -->
				</div>
			
			<?php } ?>
			
		<?php endwhile; ?>
		
		<!-- END .news-block-wrapper -->
		</div>
		
		<?php else : ?>
			<p><?php esc_html_e('No news has been added yet','chauffeur'); ?></p>
		<?php endif;

		if ( $wp_query->max_num_pages > 1 ) : ?>

			<div class="clearboth"></div>

			<?php if(is_plugin_active('wp-pagenavi/wp-pagenavi.php')) {
				echo '<div class="pagination-wrapper pagination-margin clearfix">';
				wp_pagenavi();
				echo '</div>';
				echo '<div class="clearboth"></div>';
			} else { ?>

			<div class="pagination-wrapper pagination-margin clearfix">
				<p class="clearfix">
					<span class="fl prev-pagination"><?php next_posts_link( esc_html__( '&larr; Older posts', 'chauffeur' ) ); ?></span>
					<span class="fr next-pagination"><?php previous_posts_link( esc_html__( 'Newer posts &rarr;', 'chauffeur' ) ); ?></span>	
				</p>
			</div>

			<?php } ?>

		<?php endif; wp_reset_query();
		
		return ob_get_clean();

}

add_shortcode( 'news_page', 'news_page_shortcode' );

?>
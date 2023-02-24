<?php

function news_carousel_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'section_title' => '',
		'posts_per_page' => '',
		'order' => '',
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
	'cat' => $category,
	'order' => $news_order
	); ?>
	
	<!-- BEGIN .content-wrapper-outer -->
	<section class="content-wrapper-outer content-wrapper clearfix latest-news-section">
	
	<h3 class="center-title"><?php if ($section_title == '') { echo esc_html_e( "Latest News", 'chauffeur' ); } else { echo $section_title; } ?></h3>
	<div class="title-block2"></div>
	
	<?php $wp_query = new WP_Query( $args );
	if ($wp_query->have_posts()) : ?>
		
		<!-- BEGIN .latest-news-block-wrapper -->
		<div class="owl-carousel2 latest-news-block-wrapper">
	
		<?php while($wp_query->have_posts()) :
			
			$wp_query->the_post(); ?>
			
			<!-- BEGIN .latest-news-block -->
			<div id="post-<?php the_ID(); ?>" <?php post_class("latest-news-block"); ?>>
				
				<?php if( has_post_thumbnail() ) { ?>

					<div class="latest-news-block-image">
						<a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
							<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'chauffeur-image-style8' ); ?>
							<?php echo '<img src="' . $src[0] . '" alt="" />'; ?>
						</a>
					</div>

				<?php } ?>

				<div class="latest-news-block-content">

					<h4><a href="<?php esc_url(the_permalink()); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h4>
					
					<!-- BEGIN .news-meta -->
					<div class="news-meta clearfix">
						<span class="nm-news-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
						<span class="nm-news-comments"><?php comments_popup_link(esc_html__( 'No Comments', 'chauffeur' ),esc_html__( '1 Comment', 'chauffeur' ),esc_html__( '% Comments', 'chauffeur' ),'',esc_html__( 'Comments Off','chauffeur')); ?></span>
					<!-- END .news-meta -->
					</div>

					<div class="latest-news-excerpt">
						
						<?php global $more;$more = 0;?>
						<?php the_excerpt(); ?>
						
					</div>

				</div>

			<!-- END .latest-news-block -->
			</div>
			
		<?php endwhile; ?>
		
		<!-- END .latest-news-block-wrapper -->
		</div>
		
		<?php else : ?>
			<p><?php esc_html_e('No news has been added yet','chauffeur'); ?></p>
		<?php endif;

		wp_reset_query(); ?>
		
		</section>
		
		<?php return ob_get_clean(); ?>

<?php }

add_shortcode( 'news_carousel', 'news_carousel_shortcode' );

?>
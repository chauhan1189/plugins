<?php

// header include

get_header();

?>
<div class="my-acts-page-main">

		<?php

			$current_user = wp_get_current_user();

			$complete_acts = get_user_meta($current_user->ID, '_user_complete_acts', true);
			
		?>

			<div class="my_acts_wrapper">
			
			<?php

			if(!is_user_logged_in()) {
			 	echo $button = '<a href="'.site_url().'/wp-login.php?redirect_to='.$_SERVER["REQUEST_URI"].'" class="login-to-complete act-btn-style btn">' . __('Please Login To Complete Act','act-lite') . '</a>';
			 	}
			else {
			
			if(!empty($complete_acts)) {
				$args = array(
				'post_type' => array( 'post' ),
				'orderby' => 'ASC',
				'post__in' => $complete_acts
			);

			$query = new WP_Query( $args );
			
				while( $query->have_posts() ) {
	$query->the_post();
	$featured_img_url = get_the_post_thumbnail_url(get_the_ID(),'full'); 
					?>
				
				<article class="blog-entry clr item-entry large-entry post-<?= get_the_ID(); ?> post type-post status-publish format-standard has-post-thumbnail hentry category-daily-acts entry has-media">

				<div class="thumbnail">
					<a href="" class="thumbnail-link no-lightbox">
						<img width="400" height="250" src="<?= $featured_img_url; ?>" class="attachment-full size-full wp-post-image" alt="<?= get_the_title(); ?>" itemprop="image"><span class="overlay"></span>		
					</a>
				</div>

				<header class="blog-entry-header clr">
				<h3 class="blog-entry-title entry-title">
					<a href="<?= get_the_permalink(); ?>" title="<?= get_the_title(); ?>" rel="bookmark"><?= get_the_title(); ?></a>
				</h3><!-- .blog-entry-title -->
			</header>

				<ul class="meta clr">
					<li class="meta-author" itemprop="name"><i class="icon-user"></i><a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>" title="Posts by <?= get_the_author_meta('display_name'); ?>" rel="author" itemprop="author" itemscope="itemscope" itemtype="http://schema.org/Person"><?= get_the_author_meta('display_name'); ?></a></li>
		
					<li class="meta-date" itemprop="datePublished"><i class="icon-clock"></i><?= get_the_date( 'F j, Y' ); ?></li>
		<?php $categories = get_the_category();
if ( ! empty( $categories ) ) { ?>
					<li class="meta-cat"><i class="icon-folder"></i><a href="<?= esc_url( get_category_link( $categories[0]->term_id ) ); ?>" rel="category tag"><?= esc_html( $categories[0]->name ); ?></a></li>
<?php } ?>
				</ul>

				<div class="blog-entry-summary clr" itemprop="text">
        			<p>
        				<?php $content = get_the_content();
    					echo $trimmed_content = wp_trim_words( $content, 20, '....' ); ?>
    				</p>
				</div>

				<?php
				echo $Acts->act_mark_button_listing();
				?>
				</article>	


			<?php
				}
				wp_reset_postdata();

			}
			else {
		
				echo  "<div class='alert error no-result'>" . __('Oops ! No acts completed.','act-lite') . "</div>";

			}

		} //end main else

?>

           </div>
		  

</div>
<?php
// include footer
get_footer();

?>
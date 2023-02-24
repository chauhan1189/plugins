<?php get_header(); ?>

<?php if ($chauffeur_title_style != 'no_title') { ?>
<div id="page-header" <?php echo wp_kses($chauffeur_page_header_image, $chauffeur_allowed_html_array); ?>>	
	
	<div class="page-header-inner">
		<h1><?php the_title(); ?></h1>
		<div class="title-block3"></div>
		<?php echo dimox_breadcrumbs();?>
	</div>
	
</div>
<?php } ?>

<!-- BEGIN .content-wrapper-outer -->
<div class="content-wrapper-outer <?php if( $chauffeur_page_layout == 'unboxed-full-width' ) {echo 'content-wrapper-full';} ?> clearfix">
			
	<!-- BEGIN .main-content -->
	<div class="<?php echo chauffeur_sidebar1($chauffeur_page_layout); ?>">

		<?php if ( post_password_required() ) {
			echo chauffeur_password_form();
		} else { ?>
			
			<!-- BEGIN .testimonial-list-wrapper -->
			<div class="testimonial-list-wrapper testimonial-list-wrapper-full">
			
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			
				<?php
					// Get testimonial data
					$testimonial_name = get_post_meta($post->ID, $prefix.'testimonial_name', true);
					$testimonial_other_details = get_post_meta($post->ID, $prefix.'testimonial_other_details', true);			
				?>
			
				<!-- BEGIN .testimonial-wrapper -->
				<div id="post-<?php the_ID(); ?>" class="testimonial-wrapper">
					
					<div><span class="qns-open-quote">“</span><?php the_content(); ?><span class="qns-close-quote">”</span></div>
										
					<?php if( has_post_thumbnail() ) { ?>

						<div class="testimonial-image">
							<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'chauffeur-image-style7' ); ?>
							<?php echo '<img src="' . $src[0] . '" alt="" />'; ?>
						</div>

					<?php } ?>

					<div class="testimonial-author"><p><?php echo esc_textarea($testimonial_name); ?> - <?php echo esc_textarea($testimonial_other_details); ?></p></div>
										
				<!-- END .testimonial-wrapper -->
				</div>

			<?php endwhile;endif; ?>
			
			<!-- END .testimonial-list-wrapper -->
			</div>
			
		<?php } ?>
		
	<!-- END .columns-two-thirds -->
	</div>
	
	<?php if ( $chauffeur_page_layout != 'full-width' ) { ?>
	
	<!-- BEGIN .columns-one-third -->
	<div class="<?php echo chauffeur_sidebar2($chauffeur_page_layout); ?>">
		
		<?php get_sidebar(); ?>
		
	<!-- END .columns-one-third -->
	</div>
	
	<?php } ?>

<!-- END .content-wrapper -->
</div>

<?php get_footer(); ?>
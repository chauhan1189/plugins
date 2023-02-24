<?php get_header(); ?>

<div id="page-header" <?php echo wp_kses($chauffeur_page_header_image, $chauffeur_allowed_html_array); ?>>	
	
	<div class="page-header-inner">
		<h1><?php the_title(); ?></h1>
		<div class="title-block3"></div>
		<?php echo dimox_breadcrumbs();?>
	</div>
	
</div>

<!-- BEGIN .content-wrapper -->
<div class="content-wrapper <?php if( $chauffeur_page_layout == 'unboxed-full-width' ) {echo 'content-wrapper-full';} ?> clearfix">
	
	<!-- BEGIN .main-content -->
	<div class="<?php echo chauffeur_sidebar1($chauffeur_page_layout); ?>">
		
		<p><?php esc_html_e('To create a Fleet category page, please go to "Pages > Add New", select the "Our Fleet Page" element in Visual Composer, and enter your category slug in the "Category" field.','chauffeur'); ?></p>
		
	<!-- END .main-content -->
	</div>
		
	<?php if ( $chauffeur_page_layout != 'full-width' ) { ?>
	<?php if ( $chauffeur_page_layout != 'unboxed-full-width' ) { ?>
	
	<!-- BEGIN .sidebar-content -->
	<div class="<?php echo chauffeur_sidebar2($chauffeur_page_layout); ?>">
		
		<?php get_sidebar(); ?>
		
	<!-- END .sidebar-content -->
	</div>
	
	<?php } ?>
	<?php } ?>

<!-- END .content-wrapper -->
</div>

<?php get_footer(); ?>
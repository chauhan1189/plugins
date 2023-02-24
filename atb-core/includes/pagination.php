<?php global $the_query; ?>

<?php if ( $the_query ) { ?>
	
	<?php if ( $the_query->max_num_pages > 1 ) : ?>

		<div class="clearboth"></div>

		<?php if(is_plugin_active('wp-pagenavi/wp-pagenavi.php')) {
			echo '<div class="pagination-wrapper clearfix">';
			wp_pagenavi( array( 'query' => $the_query ) );
			echo '</div>';
			echo '<div class="clearboth"></div>';
		} else { ?>

		<div class="pagination-wrapper clearfix">
			<p class="clearfix">
				<span class="fl prev-pagination"><?php next_posts_link( esc_html__( '&larr; Older posts', 'chauffeur' ), $the_query->max_num_pages ); ?></span>
				<span class="fr next-pagination"><?php previous_posts_link( esc_html__( 'Newer posts &rarr;', 'chauffeur' ), $the_query->max_num_pages ); ?></span>	
			</p>
		</div>

		<?php } ?>

	<?php endif; ?>
	
<?php } else {
	
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

	<?php endif; ?>
	
<?php } ?>
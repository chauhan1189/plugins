<?php

function videothumbnail_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'video_thumbnail_url' => '',
		'video_url' => ''
	), $atts ) );
	
	$output = '<div class="video-wrapper video-wrapper-page" style="background: url(' . wp_get_attachment_image_url( $video_thumbnail_url, 'full-image') . ');">
	<div class="video-play">
	<a href="' . $video_url . '" data-gal="prettyPhoto"><i class="fa fa-play"></i></a>
	</div>
	</div>';
	
	return $output;
	
}

add_shortcode( 'videothumbnail', 'videothumbnail_shortcode' );

?>
<?php

function video_text_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
			'section_title' => '',
			'button_text' => '',
			'button_url' => '',
			'video_thumbnail_url' => '',
			'video_url' => '',
			'background' => '',
		), $atts ) );
		
		if ( $background == 'dark' ) {
			
			$output = '<!-- BEGIN .clearfix -->
				<section class="clearfix">

					<!-- BEGIN .about-us-block -->
					<div class="about-us-block about-us-block-1">

						<h3>' . $section_title . '</h3>
						<div class="title-block4"></div>
						<p>' . $content . '</p>
						<a href="' . $button_url . '" class="button0">' . $button_text . '</a>

					<!-- END .about-us-block -->
					</div>

					<div class="video-wrapper video-wrapper-home" style="background:url(' . wp_get_attachment_image_url( $video_thumbnail_url, 'full-image') . ') no-repeat center top;">

						<div class="video-play">
							<a href="' . $video_url . '" data-gal="prettyPhoto"><i class="fa fa-play"></i></a>
						</div>

					</div>

				<!-- END .clearfix -->
				</section>';
			
		} else {
			
			$output = '<!-- BEGIN .clearfix -->
				<section class="clearfix about-us-block-2">

					<!-- BEGIN .about-us-block -->
					<div class="about-us-block">

						<h3>' . $section_title . '</h3>
						<div class="title-block4"></div>
						' . $content . '
						
						<div><a href="' . $button_url . '" class="button0">' . $button_text . '</a></div>

					<!-- END .about-us-block -->
					</div>

					<div class="video-wrapper video-wrapper-home" style="background:url(' . wp_get_attachment_image_url( $video_thumbnail_url, 'full-image') . ') no-repeat center top;">

						<div class="video-play">
							<a href="' . $video_url . '" data-gal="prettyPhoto"><i class="fa fa-play"></i></a>
						</div>

					</div>

				<!-- END .clearfix -->
				</section>';
			
		}
	
	return $output;

}

add_shortcode( 'video_text', 'video_text_shortcode' );

?>
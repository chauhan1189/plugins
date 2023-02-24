<?php

function socialmedia_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
			'facebook' => '',
			'flickr' => '',
			'googleplus' => '',
			'instagram' => '',
			'linkedin' => '',
			'pinterest' => '',
			'skype' => '',
			'soundcloud' => '',
			'tripadvisor' => '',
			'tumblr' => '',
			'twitter' => '',
			'vimeo' => '',
			'vine' => '',
			'yelp' => '',
			'youtube' => ''
		), $atts ) );

	$link_target = "_blank";
	
	$output = '<ul class="social-links clearfix">';
	
	if( isset($atts['facebook']) ) {
		$output .= '<li><a href="' . $atts['facebook'] . '" target="' . $link_target . '"><i class="fa fa-facebook"></i></a></li>';
	}
	
	if( isset($atts['flickr']) ) {
		$output .= '<li><a href="' . $atts['flickr'] . '" target="' . $link_target . '"><i class="fa fa-flickr"></i></a></li>';
	}
	
	if( isset($atts['googleplus']) ) {
		$output .= '<li><a href="' . $atts['googleplus'] . '" target="' . $link_target . '"><i class="fa fa-google-plus"></i></a></li>';
	}
	
	if( isset($atts['instagram']) ) {
		$output .= '<li><a href="' . $atts['instagram'] . '" target="' . $link_target . '"><i class="fa fa-instagram"></i></a></li>';
	}
	
	if( isset($atts['linkedin']) ) {
		$output .= '<li><a href="' . $atts['linkedin'] . '" target="' . $link_target . '"><i class="fa fa-linkedin"></i></a></li>';
	}
	
	if( isset($atts['pinterest']) ) {
		$output .= '<li><a href="' . $atts['pinterest'] . '" target="' . $link_target . '"><i class="fa fa-pinterest"></i></a></li>';
	}
	
	if( isset($atts['skype']) ) {
		$output .= '<li><a href="' . $atts['skype'] . '" target="' . $link_target . '"><i class="fa fa-skype"></i></a></li>';
	}
	
	if( isset($atts['tripadvisor']) ) {
		$output .= '<li><a href="' . $atts['tripadvisor'] . '" target="' . $link_target . '"><i class="fa fa-tripadvisor"></i></a></li>';
	}
	
	if( isset($atts['soundcloud']) ) {
		$output .= '<li><a href="' . $atts['soundcloud'] . '" target="' . $link_target . '"><i class="fa fa-soundcloud"></i></a></li>';
	}
	
	if( isset($atts['tumblr']) ) {
		$output .= '<li><a href="' . $atts['tumblr'] . '" target="' . $link_target . '"><i class="fa fa-tumblr"></i></a></li>';
	}
	
	if( isset($atts['twitter']) ) {
		$output .= '<li><a href="' . $atts['twitter'] . '" target="' . $link_target . '"><i class="fa fa-twitter"></i></a></li>';
	}
	
	if( isset($atts['vimeo']) ) {
		$output .= '<li><a href="' . $atts['vimeo'] . '" target="' . $link_target . '"><i class="fa fa-vimeo-square"></i></a></li>';
	}
	
	if( isset($atts['vine']) ) {
		$output .= '<li><a href="' . $atts['vine'] . '" target="' . $link_target . '"><i class="fa fa-vine"></i></a></li>';
	}
	
	if( isset($atts['yelp']) ) {
		$output .= '<li><a href="' . $atts['yelp'] . '" target="' . $link_target . '"><i class="fa fa-yelp"></i></a></li>';
	}
	
	if( isset($atts['youtube']) ) {
		$output .= '<li><a href="' . $atts['youtube'] . '" target="' . $link_target . '"><i class="fa fa-youtube-play"></i></a></li>';
	}
	
	$output .= '</ul>';
	
	return $output;

}

add_shortcode( 'socialmedia', 'socialmedia_shortcode' );

?>
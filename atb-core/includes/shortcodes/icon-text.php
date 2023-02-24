<?php

function icon_text_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'icon_icon_1' => '',
		'icon_title_1' => '',
		'icon_content_1' => '',
		'icon_icon_2' => '',
		'icon_title_2' => '',
		'icon_content_2' => '',
		'icon_icon_3' => '',
		'icon_title_3' => '',
		'icon_content_3' => '',
		'icon_icon_4' => '',
		'icon_title_4' => '',
		'icon_content_4' => ''
	), $atts ) );
	
	$output = '<div class="clearfix">';
		
	if ( $icon_icon_1 ) {
		$output .= '<div class="qns-one-half home-icon-wrapper-2">
		<div class="qns-home-icon"><i class="fa ' . $icon_icon_1 . '"></i></div>
		<div class="home-icon-inner">
			<h4>' . $icon_title_1 . '</h4>
			<div class="title-block3"></div>
			<p>' . $icon_content_1 . '</p>
		</div>
		</div>';
	}
		
	if ( $icon_icon_2 ) {
		$output .= '<div class="qns-one-half home-icon-wrapper-2 qns-last">
		<div class="qns-home-icon"><i class="fa ' . $icon_icon_2 . '"></i></div>
		<div class="home-icon-inner">
			<h4>' . $icon_title_2 . '</h4>
			<div class="title-block3"></div>
			<p>' . $icon_content_2 . '</p>
		</div>
		</div>';
	}
		
	if ( $icon_icon_3 ) {
		$output .= '<div class="qns-one-half home-icon-wrapper-2">
		<div class="qns-home-icon"><i class="fa ' . $icon_icon_3 . '"></i></div>
		<div class="home-icon-inner">
			<h4>' . $icon_title_3 . '</h4>
			<div class="title-block3"></div>
			<p>' . $icon_content_3 . '</p>
		</div>
		</div>';
	}
		
	if ( $icon_icon_4 ) {
		$output .= '<div class="qns-one-half home-icon-wrapper-2 qns-last">
		<div class="qns-home-icon"><i class="fa ' . $icon_icon_4 . '"></i></div>
		<div class="home-icon-inner">
			<h4>' . $icon_title_4 . '</h4>
			<div class="title-block3"></div>
			<p>' . $icon_content_4 . '</p>
		</div>
		</div>';
	}

	$output .= '</div>';
	
	return $output;
	
}

add_shortcode( 'icon_text', 'icon_text_shortcode' );

?>
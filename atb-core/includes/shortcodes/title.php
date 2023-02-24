<?php

function title_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
			'type' => '',
		), $atts ) );
	
	if ($type == 'title1') {
		$output = '<h3 class="center-title">' . $content . '</h3><div class="title-block2"></div>';
	} elseif ( $type == 'title2' ) {
		$output = '<h4>' . $content . '</h4><div class="title-block7"></div>';
	} elseif ( $type == 'h1' ) {
		$output = '<h1>' . $content . '</h1>';
	} elseif ( $type == 'h2' ) {
		$output = '<h2>' . $content . '</h2>';
	} elseif ( $type == 'h3' ) {
		$output = '<h3>' . $content . '</h3>';
	} elseif ( $type == 'h4' ) {
		$output = '<h4>' . $content . '</h4>';
	} elseif ( $type == 'h5' ) {
		$output = '<h5>' . $content . '</h5>';
	} elseif ( $type == 'h6' ) {
		$output = '<h6>' . $content . '</h6>';
	} else {
		$output = '<h3 class="center-title">' . $content . '</h3><div class="title-block2"></div>';
	}
	
	return $output;
	
}

add_shortcode( 'title', 'title_shortcode' );

?>
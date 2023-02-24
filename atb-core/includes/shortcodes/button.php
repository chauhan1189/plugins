<?php

function button_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'link_url' => '',
		'type' => 'button1',
		'rounded' => '',
		'center' => '',
		'target' => '_parent',
		'background_color' => '',
		'text_color' => ''
	), $atts ) );
	
	// Set button type
	if ($type == 'button1') {
		$type = 'button1 ';
	} elseif ($type == 'button2') {
		$type = 'button2 ';
	} elseif ($type == 'button3') {
		$type = 'button3 ';
	} elseif ($type == 'button4') {
		$type = 'button4 ';
	} elseif ($type == 'button5') {
		$type = 'button5 ';
	} elseif ($type == 'button6') {
		$type = 'button6 ';
	} elseif ($type == 'button7') {
		$type = 'button0 ';
	} else {
		$type = 'button1 ';
	}
	
	// Set rounded
	if ($rounded == 'true') {
		$rounded = 'rounded-button ';
	} elseif ($rounded == 'false') {
		$rounded = ' ';
	} else {
		$rounded = 'rounded-button ';
	}
	
	// Set center
	if ($center == 'true') {
		$center = 'button-center ';
	} elseif ($center == 'false') {
		$center = '';
	} else {
		$center = 'button-center ';	
	}
	
	// Set target
	if ($target == 'true') {
		$target = '_blank';
	} elseif ($target == 'false') {
		$target = '_parent';
	} else {
		$target = '_blank';
	}
	
	// Set background color
	if ($background_color != '') {
		$background_color = 'background: ' . $background_color . ';';
	}
	
	// Set text color
	if ($text_color != '') {
		$text_color = 'color: ' . $text_color . ';';
	}
	
	$output = '<a class="' . $type . $rounded . $center  . '" style="' . $background_color . $text_color . '" href="' . $link_url . '" target="' . $target .'">' . $content . '</a>';
	
	return $output;
	
}

add_shortcode( 'button', 'button_shortcode' );

?>
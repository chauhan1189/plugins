<?php

function link_blocks_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'columns' => '',
		'link_block_text_1' => '',
		'link_block_url_1' => '',
		'link_block_text_2' => '',
		'link_block_url_2' => '',
		'link_block_text_3' => '',
		'link_block_url_3' => '',
	), $atts ) );
	
	if ($columns == '3') {
		$link_block_col = 'link-block-3';
	} else {
		$link_block_col = 'link-block-2';
	}
	
	$output = '<ul class="link-blocks clearfix">';
	
		if ( $link_block_text_1 ) {
			$output .= '<li><h3><a href="' . $link_block_url_1 .'" class="' . $link_block_col . '"><span class="link-text">' . $link_block_text_1 .'</span><span class="link-arrow fa fa-angle-right"></span></a></h3></li>';
		}
		
		if ( $link_block_text_2 ) {
			$output .= '<li><h3><a href="' . $link_block_url_2 .'" class="' . $link_block_col . '"><span class="link-text">' . $link_block_text_2 .'</span><span class="link-arrow fa fa-angle-right"></span></a></h3></li>';
		}
		
		if ( $link_block_text_3 ) {
			$output .= '<li><h3><a href="' . $link_block_url_3 .'" class="' . $link_block_col . '"><span class="link-text">' . $link_block_text_3 .'</span><span class="link-arrow fa fa-angle-right"></span></a></h3></li>';
		}
	
	$output .= '</ul>';
	
	return $output;
	
}

add_shortcode( 'link_blocks', 'link_blocks_shortcode' );

?>
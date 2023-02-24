<?php

function call_to_action_small_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'section_background_image_url' => '',
		'section_text' => '',
		'button_text' => '',
		'button_url' => ''
	), $atts ) );
	
	$output = '<!-- BEGIN .content-wrapper-outer -->
	<section class=".content-wrapper-outer clearfix call-to-action-1-section" style="background:url(' . wp_get_attachment_image_url( $section_background_image_url, 'full-image') . ') no-repeat center top;">

		<div class="call-to-action-1-section-inner">

			<h3>' . $section_text . '</h3>
			<a href="' . $button_url . '" class="button0">' . $button_text . '</a>

		</div>

	<!-- END .content-wrapper-outer -->
	</section>';

	return $output;
	
}

add_shortcode( 'call_to_action_small', 'call_to_action_small_shortcode' );

?>
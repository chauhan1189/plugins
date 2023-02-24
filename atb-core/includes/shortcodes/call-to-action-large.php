<?php

function call_to_action_large_shortcode( $atts, $content = null ) {
	
	extract( shortcode_atts( array(
		'section_background_image_url' => '',
		'section_title' => '',
		'section_text' => '',
		'button_text' => '',
		'button_url' => ''
	), $atts ) );
	
	$output = '<!-- BEGIN .call-to-action-2-section -->
			<section class="clearfix call-to-action-2-section" style="background:url(' . wp_get_attachment_image_url( $section_background_image_url, 'full-image') . ') no-repeat center top;">

				<div class="call-to-action-2-section-inner">

					<h3>' . $section_title . '</h3>
					<div class="title-block5"></div>
					<p>' . $section_text . '</p>
					<a href="' . $button_url . '" class="button0">' . $button_text . '</a>

				</div>

			<!-- END .call-to-action-2-section -->
			</section>';

	return $output;
	
}

add_shortcode( 'call_to_action_large', 'call_to_action_large_shortcode' );

?>
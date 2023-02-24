<?php

function _isCurl(){
    return function_exists('curl_version');
}

function booking_debug_shortcode( $atts, $content = null ) {
	
	global $chauffeur_data;

	$form_type = 'one_way';
	$pickup_address = 'London, United Kingdom';
	$dropoff_address = 'Bristol, United Kingdom';
	
	if ( $form_type != 'flat' ) {

		$coordinates1 = get_coordinates($pickup_address);
		$coordinates2 = get_coordinates($dropoff_address);

		if ( !$coordinates1 || !$coordinates2 ) {	
		    $invalid_address = true;
		} else {

		    $dist = GetDrivingDistance($coordinates1['lat'], $coordinates2['lat'], $coordinates1['long'], $coordinates2['long']);
			$invalid_address = false;

		}

	} else {
		$dist = false;
	}

	if ( $dist['distance'] == false && $_POST['form_type'] != 'flat' ) {

		echo "<strong>Distance Calculation:</strong> NOT OK";
		echo '<br /><br /><hr class="space6" />';
		
	} else { 
		
		echo "<strong>Distance Calculation:</strong> OK";
		echo '<br /><br /><hr class="space6" />';
		
	}
	
	echo "<strong>PHP Version:</strong> " . phpversion();
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>PHP CURL:</strong> " . _isCurl();
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Google API Key:</strong> " . $chauffeur_data['google-map-api-key'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Booking Page URL:</strong> " . $chauffeur_data['booking-page-url'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Country Limit:</strong> " . $chauffeur_data['google-limit-country'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Units:</strong> " . $chauffeur_data['google-distance-matrix-unit'];
	echo '<br /><br /><hr class="space6" />';
		
	echo "<strong>get_coordinates:</strong> ";
	print_r( get_coordinates('London, United Kingdom') );
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>get_coordinates_debug:</strong> ";
	print_r( get_coordinates_debug('London, United Kingdom') );
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>GetDrivingDistance:</strong> ";
	print_r( GetDrivingDistance('51.507351', '51.454513', '-0.127758', '-2.587910') );
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>GetDrivingDistance_debug:</strong> ";
	print_r( GetDrivingDistance_debug('51.507351', '51.454513', '-0.127758', '-2.587910') );
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>PayPal Currency:</strong> " . $chauffeur_data['paypal-currency'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>PayPal Sandbox:</strong> " . $chauffeur_data['paypal-sandbox'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>PayPal Address:</strong> " . $chauffeur_data['paypal-address'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Stripe Currency:</strong> " . $chauffeur_data['stripe-currency'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Stripe Testmode:</strong> " . $chauffeur_data['stripe-testmode'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Stripe Live Secret Key:</strong> " . $chauffeur_data['stripe-live-secret-key'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Stripe Live Publishable Key:</strong> " . $chauffeur_data['stripe-live-publishable-key'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Stripe Test Secret Key:</strong> " . $chauffeur_data['stripe-test-secret-key'];
	echo '<br /><br /><hr class="space6" />';
	
	echo "<strong>Stripe Test Publishable Key:</strong> " . $chauffeur_data['stripe-test-publishable-key'];
	echo '<br /><br /><hr class="space6" />';
	
}

add_shortcode( 'booking_debug', 'booking_debug_shortcode' );

?>
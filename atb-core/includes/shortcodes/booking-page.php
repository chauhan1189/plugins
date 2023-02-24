<?php
function booking_page_shortcode( $atts, $content = null ) {
	
	if(!empty($atts['sidebar']) && isset($atts['sidebar']) && $atts['sidebar'] == TRUE ){
		$sidebar = TRUE;
		$sidebar_class = 'atb-with-sidebar';
	}else{
		$sidebar = FALSE;
		$sidebar_class = 'atb-no-sidebar';
	}
	if(!empty($atts['layout']) && isset($atts['layout']) && $atts['layout'] == 'horizontal'){
		$layout = 'horizontal';
	}else if(!empty($atts['layout']) && isset($atts['layout']) && $atts['layout'] == 'vertical'){
		$layout = 'vertical';
	}else {
		$layout = '';
	}

	// Stripe library
	require chauffeur_BASE_DIR .'/includes/vendor/stripe-new/autoload.php';

	$options = get_option( 'settings-page-atb', array() );
	//$content = $options['booking_form_content'];
	$stripe_result = chauffeur_stripe_payment($_POST);
	//if (isset($stripe_result['booking_reference']))
	
	$paymentMade = count($stripe_result) > 0;

	//if (count($stripe_result) > 0) // if payment is processed
	if ($paymentMade == true)
	{
		$authorization_reference = $stripe_result['authorization_reference'];
		$booking_reference = $stripe_result['booking_reference'];
		
		if (isset($stripe_result['return_authorization_reference']))
			$return_authorization_reference = $stripe_result['return_authorization_reference'];
		if (isset($stripe_result['return_booking_reference']))
			$return_booking_reference = $stripe_result['return_booking_reference'];

		/*$currTime = new DateTime("now");
		$fileName = get_home_path().'booking_ref_at_bookingpage_'.$currTime->format("Y-m-d__H_i_s-u").'.txt';
		$file = fopen($fileName, 'wb');
		fprintf($file, "authorization_reference: %s\n", $authorization_reference);
		fprintf($file, "booking_reference: %s\n", $booking_reference);
		if (isset($return_authorization_reference))
			fprintf($file, "return_authorization_reference: %s\n", $return_authorization_reference);
		if (isset($return_booking_reference))
			fprintf($file, "return_booking_reference: %s\n", $return_booking_reference);
		fclose($file);*/
		
	}
	
	global $chauffeur_data;
	
	
/*	if ($paymentMade != true)
	{
		//$chauffeur_data['booking-email']
		$currTime = new DateTime("now");
		$fileName = get_home_path().'debug_'.$currTime->format("Y-m-d__H_i_s-u").'.txt';
		$file = fopen($fileName, 'wb');
		fprintf($file, "booking-email: %s\n", $chauffeur_data['booking-email']);
		fprintf($file, "%s\n", esc_attr($chauffeur_data['booking-email']));
		fprintf($file, "email-sender-name: %s\n", $chauffeur_data['email-sender-name']);
		fclose($file);
		
		$headers = array('MIME-Version: 1.0', 
		'Content-Type: text/html; charset=UTF-8', 
		//'From: '.esc_attr($chauffeur_data['email-sender-name']),
		"From: ".esc_attr($chauffeur_data['email-sender-name'])." <".esc_attr($chauffeur_data['booking-email']).">",
		"Reply-To: " . esc_attr($chauffeur_data['booking-email'])
		);
		$content = 'test '.$currTime->format("Y-m-d__H_i_s-u");
		$email = 'Artem <artem.goncharenko.ua@gmail.com>';
		wp_mail($email, "test", $content, $headers);
		
	}*/
	
	// PayPal IPN
	$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    if (preg_match('/paypal\.com$/', $hostname)) {	
		$obj = New PayPal_IPN();
		$obj->ipn_response($_REQUEST);		
    }

	$output = '';
	
	// If the booking page referral is from an external form automatically go to booking step 2
	if ( isset($_POST['external_form'] ) ) {

		if($_POST['form_type'] == 'one_way') {
			
			$output .= '<div class="booking-step-wrapper clearfix">' . booking_steps('2') . '</div>';
			$output .= '<div class="booking-form-content b02 clearfix">' . booking_step_2("one_way") . '</div>';

		// Booking Form Step 2 
		} elseif($_POST['form_type'] == 'hourly') {

			$output .= '<div class="booking-step-wrapper clearfix">' . booking_steps('2') . '</div>';
			$output .= '<div class="booking-form-content b03 clearfix">' . booking_step_2("hourly") . '</div>';

		} elseif($_POST['form_type'] == 'flat') {

			$output .= '<div class="booking-step-wrapper clearfix">' . booking_steps('2') . '</div>';
			$output .= '<div class="booking-form-content b04 clearfix">' . booking_step_2("flat") . '</div>';

			}
	
	// Else just load booking step 1 as normal
	} else  {
		
		//$content = wpautop($content, true); // fix unclosed/unwanted paragraph tags in $content

		$output .= '<!-- BEGIN .booking-step-wrapper -->
		<div class="booking-step-wrapper clearfix">';
	
             if(isset($_REQUEST['payment_intent'])){
				  	$myoutput = chauffeur_3dstripe_payment($_REQUEST);
				  	//wp_redirect( get_site_url()."/thank-you/?item_number=".$myoutput['booking_id'], 301 );
					//exit();
					//var_dump($myoutput);
			 }else {
				if ( !empty($stripe_result) ) {
				$output .= booking_steps('4');
				} elseif(isset($_POST['pay_now']) && isset($_POST['payment-method'])) {
					
					if ( $_POST['payment-method'] == 'paypal' ) {
						$output .= booking_steps('3');
					} elseif ( $_POST['payment-method'] == 'stripe' ) {
						$output .= booking_steps('3');
					} else {
						$output .= booking_steps('4');
					}
				
				} else {
					$output .= booking_steps('1');
				}
			 }
			
		$output .= '<!-- END .booking-step-wrapper -->
		</div>';
		
		if ( !empty($stripe_result) ) {
			
			global $chauffeur_data;
			
			if ( $stripe_result["payment_status"] == 'success' ) {
			// display payment successful page
			
				/*if (isset($stripe_result['booking_reference']))
				{
					update_post_meta($stripe_result["booking_id"], 'chauffeur_payment_booking_reference', $stripe_result['booking_reference']);
					$currTime = new DateTime("now");
					$fileName = get_home_path().'booking_ref_at_last_page_1_'.$currTime->format("Y-m-d__H_i_s-u").'.txt';
					$file = fopen($fileName, 'wb');
					fprintf($file, "%s", $stripe_result['booking_reference']);
					fclose($file);
					
				}*/
				//if (count($stripe_result) > 0)
				if ($paymentMade == true)
				{
					/*$currTime = new DateTime("now");
					$fileName = get_home_path().'booking_ref_at_last_page_2_'.$currTime->format("Y-m-d__H_i_s-u").'.txt';
					$file = fopen($fileName, 'wb');
					fprintf($file, "booking_reference variable: %s\n", $booking_reference);
					//fprintf($file, "additional line\n");
					fprintf($file, "stripe_result[booking_id]:%d\n", $stripe_result["booking_id"]);
					fclose($file);*/
					
					update_post_meta($stripe_result["booking_id"], 'chauffeur_payment_authorization_reference', $authorization_reference);
					update_post_meta($stripe_result["booking_id"], 'chauffeur_payment_booking_reference', $booking_reference);
					
					if (isset($return_authorization_reference))
						update_post_meta($stripe_result["booking_id"], 'chauffeur_payment_return_authorization_reference', $return_authorization_reference);

					if (isset($return_booking_reference))
						update_post_meta($stripe_result["booking_id"], 'chauffeur_payment_return_booking_reference', $return_booking_reference);

					//send_booking_success_email($stripe_result["booking_id"]);
					//update_post_meta($stripe_result["booking_id"], 'chauffeur_payment_booking_reference', "some value");
				}

			
				$get_trip_type = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_trip_type',TRUE);
				$get_vehicle_name = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_item_name',TRUE);
				$get_pickup_address = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_pickup_address',TRUE);
				$get_pickup_via = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_pickup_via',TRUE);
				$get_dropoff_address = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_dropoff_address',TRUE);
				$get_pickup_date = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_pickup_date',TRUE);
				$get_pickup_time = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_pickup_time',TRUE);
				$get_trip_distance = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_trip_distance',TRUE);
				$get_trip_time = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_trip_time',TRUE);

				$get_flight_number = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_flight_number',TRUE);
				$get_first_journey_origin = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_first_journey_origin',TRUE);
				$get_first_journey_greet = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_first_journey_greet',TRUE);
				$get_additional_details = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_additional_info',TRUE);

				$get_num_passengers = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_num_passengers',TRUE);
				$get_num_bags = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_num_bags',TRUE);
				$get_first_name = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_first_name',TRUE);
				$get_last_name = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_last_name',TRUE);
				$get_payment_email = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_email',TRUE);
				$get_phone_num = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_phone_num',TRUE);
				
				//$get_payment_num_hours = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_num_hours',TRUE);
				
				// $get_full_pickup_address = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_full_pickup_address',TRUE);
				// $get_pickup_instructions = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_pickup_instructions',TRUE);
				// $get_full_dropoff_address = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_full_dropoff_address',TRUE);
				// $get_dropoff_instructions = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_dropoff_instructions',TRUE);

				

				$get_return_journey = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_journey',TRUE);

				if($get_return_journey == 'Return'){
					$get_return_address = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_address',TRUE);
					$get_return_pickup_via = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_pickup_via',TRUE);
					$get_return_dropoff = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_dropoff',TRUE);
					$get_return_date = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_date',TRUE);
					$get_return_time = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_time',TRUE);
					$get_return_trip_distance = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_trip_distance',TRUE);
					$get_return_trip_time = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_trip_time',TRUE);
					$get_return_flight_number = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_flight_number',TRUE);
					$get_return_journey_origin = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_journey_origin',TRUE);
					$get_return_journey_greet = get_post_meta($stripe_result["booking_id"],'chauffeur_payment_return_journey_greet',TRUE);
				}
				
				$output .= '<!-- BEGIN .full-booking-wrapper -->
				<div class="full-booking-wrapper full-booking-wrapper-3 clearfix">

					<h4>' . esc_html__('Payment Successful','chauffeur') . '</h4>
					<div class="title-block7"></div>

					<p>' . esc_attr($chauffeur_data['booking-thanks-message']) . '</p>

					<hr class="space7" />

					<h4>' . esc_html__('Trip Details','chauffeur') . '</h4>
					<div class="title-block7"></div>';

					$output .= '<p class="clearfix"><strong>' . esc_html__('Vehicle:','chauffeur') . '</strong> <span>' . $get_vehicle_name . '</span></p>';

					$output .= '<!-- BEGIN .clearfix -->
					<div class="clearfix">

						<!-- BEGIN .qns-one-half -->
						<div class="qns-one-half">

							<p class="clearfix"><strong>' . esc_html__('From:','chauffeur') . '</strong> <span>' . $get_pickup_address . '</span></p>';

							if( !empty($get_pickup_via) ){
								$get_pickup_via_arr = explode(' >>> ', $get_pickup_via);
								if(count($get_pickup_via_arr)) {
									$output .= '<p class="clearfix">';
									$output .= '<strong>'.esc_html__('Via','chauffeur').':</strong>';
										foreach($get_pickup_via_arr as $viapoint){
											$output .= '<span>'.$viapoint.'</span>';
										}
									$output .= '</p>';
								}
							}

							$output .= '<p class="clearfix"><strong>' . esc_html__('To:','chauffeur') . '</strong> <span>' . $get_dropoff_address . '</span></p>
							<p class="clearfix"><strong>' . esc_html__('Date:','chauffeur') . '</strong> <span>' . $get_pickup_date . '</span></p>
							<p class="clearfix"><strong>' . esc_html__('Pick Up Time:','chauffeur') . '</strong> <span>' . $get_pickup_time . '</span></p>
							<p class="clearfix"><strong>' . esc_html__('Return:','chauffeur') . '</strong> <span>' . $get_return_journey . '</span></p>

						<!-- END .qns-one-half -->
						</div>

						<!-- BEGIN .qns-one-half -->
						<div class="qns-one-half last-col">';

							$output .= '<p class="clearfix"><strong>' . esc_html__('Distance','chauffeur') . ':</strong> <span>' . $get_trip_distance . ' (' . $get_trip_time . ')</span></p>';

							$output .= '<p class="clearfix"><strong>' . esc_html__('Flight Number:','chauffeur') . '</strong> <span>' . $get_flight_number . '</span></p>
							<p class="clearfix"><strong>' . esc_html__('Origin:','chauffeur') . '</strong> <span>' . $get_first_journey_origin . '</span></p>
							<p class="clearfix"><strong>' . esc_html__('Meet & Greet Service:','chauffeur') . '</strong> <span>' . $get_first_journey_greet . '</span></p>

						<!-- END .qns-one-half -->
						</div>

					<!-- END .clearfix -->
					</div>

					<hr class="space2" />';

						if ( $get_return_journey == 'Return' ) {

							$output .= '<h4>' . esc_html__('Retrun Trip Details','chauffeur') . '</h4>
							<div class="title-block7"></div>

							<!-- BEGIN .clearfix -->
							<div class="clearfix">

								<!-- BEGIN .qns-one-half -->
								<div class="qns-one-half">
									
								<p class="clearfix"><strong>' . esc_html__('Pickup','chauffeur') . '</strong> <span>' . $get_return_address . '</span></p>';
								
								if( !empty($get_return_pickup_via) ) {
									$get_return_pickup_via_arr = explode(' >>> ', $get_return_pickup_via);
									if(count($get_return_pickup_via_arr)) {
										$output .= '<p class="clearfix">';
										$output .= '<strong>'.esc_html__('Via','chauffeur').':</strong>';
											foreach($get_return_pickup_via_arr as $return_viapoint){
												$output .= '<span>'.$return_viapoint.'</span>';
											}
										$output .= '</p>';
									}
								}
									
								$output .= '<p class="clearfix"><strong>' . esc_html__('Dropoff','chauffeur') .':</strong> <span>' . $get_return_dropoff . '</span></p>
								<p class="clearfix"><strong>' . esc_html__('Date','chauffeur') . ':</strong> <span>' . $get_return_date . '</span></p>
								<p class="clearfix"><strong>' . esc_html__('Time','chauffeur') . ':</strong> <span>' . $get_return_time . '</span></p>

								<!-- END .qns-one-half -->
								</div>

								<!-- BEGIN .qns-one-half -->
								<div class="qns-one-half last-col">

									<p class="clearfix"><strong>' . esc_html__('Distance','chauffeur') . ':</strong> <span>' . $get_return_trip_distance . ' (' . $get_return_trip_time . ')</span></p>	

									<p class="clearfix"><strong>' . esc_html__('Flight Number','chauffeur') . ':</strong> <span>' . $get_return_flight_number . '</span></p>

									<p class="clearfix"><strong>' . esc_html__('Origin','chauffeur') . ':</strong> <span>' . $get_return_journey_origin . '</span></p>

									<p class="clearfix"><strong>' . esc_html__('Meet & Greet Service','chauffeur') . ':</strong> <span>' .  $get_return_journey_greet . '</span></p>

								<!-- END .qns-one-half -->
								</div>

							<!-- END .clearfix -->
							</div>

							<hr class="space2" />';

						}

					$output .= '<h4>' . esc_html__('Passengers Details','chauffeur') . '</h4>
					<div class="title-block7"></div>

					<!-- BEGIN .clearfix -->
					<div class="clearfix">

						<!-- BEGIN .passenger-details-wrapper -->
						<div class="passenger-details-wrapper">

							<!-- BEGIN .clearfix -->
							<div class="clearfix">

								<!-- BEGIN .passenger-details-half -->
								<div class="passenger-details-half">

									<p class="clearfix"><strong>' . esc_html__('Passengers:','chauffeur') . '</strong> <span>' . $get_num_passengers . '</span></p>
									<p class="clearfix"><strong>' . esc_html__('Bags:','chauffeur') . '</strong> <span>' . $get_num_bags . '</span></p>

								<!-- END .passenger-details-half -->
								</div>

								<!-- BEGIN .passenger-details-half -->
								<div class="passenger-details-half last-col">

									<p class="clearfix"><strong>' . esc_html__('Name:','chauffeur') . '</strong> <span>' . $get_first_name . ' ' . $get_last_name . '</span></p>
									<p class="clearfix"><strong>' . esc_html__('Email:','chauffeur') . '</strong> <span>' . $get_payment_email . '</span></p>
									<p class="clearfix"><strong>' . esc_html__('Phone:','chauffeur') . '</strong> <span>' . $get_phone_num . '</span></p>

								<!-- END .passenger-details-half -->
								</div>

							<!-- END .clearfix -->
							</div>

						<!-- END .passenger-details-wrapper -->
						</div>

					<!-- END .clearfix -->
					</div>

				<!-- END .full-booking-wrapper -->
				</div>';
				 
			} else {
				
				$output .= '<!-- BEGIN .full-booking-wrapper -->
				<div class="full-booking-wrapper full-booking-wrapper-3 clearfix">

					<h4>' . esc_html__('Payment Failed','chauffeur') . '</h4>
					<div class="title-block7"></div>

					<p>' . esc_html__('Unfortunately we were not able to process your payment, please contact us at hello@test.com for assistance.','chauffeur') . '</p>

				<!-- END .full-booking-wrapper -->
				</div>';
				
			}
			
		// Load Payment
		} elseif(isset($_POST['pay_now']) && isset($_POST['payment-method'])) {
			
			// Get form data
			$num_passengers = $_POST['num-passengers'];
			$num_bags = $_POST['num-bags'];
			//$booking_reference = $_POST['booking_reference'];
			
			$first_name = $_POST['first-name'];
			$last_name = $_POST['last-name'];
			$email_address = $_POST['email-address'];
			$phone_number = $_POST['phone-number'];
			$additional_info = $_POST['additional-info'];
			$flight_number = $_POST['flight-number'];			
			$selected_vehicle_name = $_POST['selected-vehicle-name'];
			$selected_vehicle_price = $_POST['selected-vehicle-price'];

			$pickup_price = $_POST['pickup-price'];
			$return_price = $_POST['return-price'];

			$form_type = $_POST['form-type'];
			$pickup_address = $_POST['pickup-address'];
			$dropoff_address = $_POST['dropoff-address'];
			if( isset($_POST['pickup-via']) ){
				$pickup_via = $_POST['pickup-via'];
			}else{
				$pickup_via = '';
			}
			$pickup_date = $_POST['pickup-date'];
			$pickup_time = $_POST['pickup-time'];
			$first_journey_origin = $_POST['first-journey-origin'];
			if ( $_POST['first-journey-greet'] == 'true' ) {
				$first_journey_greet = esc_html__( 'Yes', 'chauffeur' );
			} else {
				$first_journey_greet = esc_html__( 'No', 'chauffeur' );
			}
			$first_trip_distance = $_POST['first-trip-distance'];
			$first_trip_time = $_POST['first-trip-time'];
			$num_hours = $_POST['num-hours'];
			
			$full_pickup_address = $_POST['full-pickup-address'];
			$pickup_instructions = $_POST['pickup-instructions'];
			$full_dropoff_address = $_POST['full-dropoff-address'];
			$dropoff_instructions = $_POST['dropoff-instructions'];
			
			if ($_POST['return-journey']) {
				
				if ( $_POST['return-journey'] == 'true' ) {
					$return_journey = esc_html__('Return','chauffeur');

					$return_address = $_POST['return-address'];
					if( isset($_POST['return-pickup-via']) ){
						$return_pickup_via = $_POST['return-pickup-via'];
					}else{
						$return_pickup_via = '';
					}
					$return_dropoff = $_POST['return-dropoff'];
					$return_date = $_POST['return-date'];
					$return_time = $_POST['return-time'];
					$return_flight_number = $_POST['return-flight-number'];
					$return_journey_origin = $_POST['return-journey-origin'];
					$return_trip_distance = $_POST['return-trip-distance'];
					$return_trip_time = $_POST['return-trip-time'];

					if ( $_POST['return-journey-greet'] == 'true' ) {
						$return_journey_greet = esc_html__( 'Yes', 'chauffeur' );
					} else {
						$return_journey_greet = esc_html__( 'No', 'chauffeur' );
					}

				} else {
					$return_journey = esc_html__('One Way','chauffeur');

					$return_address = '';
					$return_pickup_via = '';
					$return_dropoff = '';
					$return_date = '';
					$return_time = '';
					$return_flight_number = '';
					$return_journey_origin = '';
					$return_trip_distance = $_POST['return-trip-distance'];
					$return_trip_time = $_POST['return-trip-time'];
					$return_journey_greet = '';
				}
				
			} else {
				
				$return_journey = '';
			
			}
			
			// Booking query
			$add_booking_query = array(
				'post_title'    => $first_name . ' ' . $last_name . ' (' . $pickup_date . ' ' . esc_html__( 'at', 'chauffeur' ) . ' ' . $pickup_time . ')',
				'post_status'   => 'publish',
				'post_type'	    => 'payment'
			);

			// Insert booking
			$booking_id = wp_insert_post( $add_booking_query );
			
			// Insert custom fields
			update_post_meta($booking_id, 'chauffeur_payment_status', esc_html__( 'Unpaid', 'chauffeur' ) );
			update_post_meta($booking_id, 'atb-booking-status', 'pending');
			//update_post_meta($booking_id, 'chauffeur_payment_details', esc_html__( 'N/A', 'chauffeur' ) );
			update_post_meta($booking_id, 'chauffeur_payment_num_passengers', $num_passengers );
			update_post_meta($booking_id, 'chauffeur_payment_num_bags', $num_bags );
			update_post_meta($booking_id, 'chauffeur_payment_first_name', $first_name );
			update_post_meta($booking_id, 'chauffeur_payment_last_name', $last_name );
			update_post_meta($booking_id, 'chauffeur_payment_email', $email_address );
			update_post_meta($booking_id, 'chauffeur_payment_phone_num', $phone_number );
			update_post_meta($booking_id, 'chauffeur_payment_flight_number', $flight_number );
			update_post_meta($booking_id, 'chauffeur_payment_additional_info', $additional_info );
			update_post_meta($booking_id, 'chauffeur_payment_pickup_address', $pickup_address );
			update_post_meta($booking_id, 'chauffeur_payment_pickup_via', $pickup_via);
			update_post_meta($booking_id, 'chauffeur_payment_dropoff_address', $dropoff_address );
			update_post_meta($booking_id, 'chauffeur_payment_pickup_date', $pickup_date );
			update_post_meta($booking_id, 'chauffeur_payment_pickup_time', $pickup_time );
			update_post_meta($booking_id, 'chauffeur_payment_trip_distance', $first_trip_distance );
			update_post_meta($booking_id, 'chauffeur_payment_trip_time', $first_trip_time );
			update_post_meta($booking_id, 'chauffeur_payment_first_journey_origin', $first_journey_origin );
			update_post_meta($booking_id, 'chauffeur_payment_first_journey_greet', $first_journey_greet );
			update_post_meta($booking_id, 'chauffeur_payment_item_name', $selected_vehicle_name );
			update_post_meta($booking_id, 'chauffeur_payment_trip_type', $form_type );
			update_post_meta($booking_id, 'chauffeur_payment_return_journey', $return_journey );
			update_post_meta($booking_id, 'chauffeur_payment_return_address', $return_address );
			update_post_meta($booking_id, 'chauffeur_payment_return_pickup_via', $return_pickup_via );
			update_post_meta($booking_id, 'chauffeur_payment_return_dropoff', $return_dropoff );
			update_post_meta($booking_id, 'chauffeur_payment_return_date', $return_date );
			update_post_meta($booking_id, 'chauffeur_payment_return_time', $return_time );
			update_post_meta($booking_id, 'chauffeur_payment_return_trip_distance', $return_trip_distance );
			update_post_meta($booking_id, 'chauffeur_payment_return_trip_time', $return_trip_time );
			update_post_meta($booking_id, 'chauffeur_payment_return_flight_number', $return_flight_number );
			update_post_meta($booking_id, 'chauffeur_payment_return_journey_origin', $return_journey_origin );
			update_post_meta($booking_id, 'chauffeur_payment_return_journey_greet', $return_journey_greet );
			//update_post_meta($booking_id, 'chauffeur_payment_num_hours', $num_hours );
			update_post_meta($booking_id, 'chauffeur_payment_amount', $selected_vehicle_price );
			update_post_meta($booking_id, 'chauffeur_payment_amount_pickup', $pickup_price );
			update_post_meta($booking_id, 'chauffeur_payment_amount_return', $return_price );
			

			
			
			//update_post_meta($booking_id, 'chauffeur_payment_booking_reference', $booking_reference );
			
			// update_post_meta($booking_id, 'chauffeur_payment_full_pickup_address', $full_pickup_address );
			// update_post_meta($booking_id, 'chauffeur_payment_pickup_instructions', $pickup_instructions );
			// update_post_meta($booking_id, 'chauffeur_payment_full_dropoff_address', $full_dropoff_address );
			// update_post_meta($booking_id, 'chauffeur_payment_dropoff_instructions', $dropoff_instructions );
			
			// Load PayPal
			if ( $_POST['payment-method'] == 'paypal' ) {
				
				$data = array(
					'merchant_email'=> esc_attr($chauffeur_data['paypal-address']),
					'product_name'=> get_bloginfo('name'),
					'item_number'=> $booking_id,
					'amount'=> $selected_vehicle_price,
					'currency_code'=> esc_attr($chauffeur_data['paypal-currency']),
					'thanks_page'=> esc_attr($chauffeur_data['thanks-page-url']),
					'notify_url'=> esc_url($chauffeur_data['booking-page-url']),
					'cancel_url'=> "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
					'paypal_mode'=> true
				);

				$output .= '<div class="paypal-loader">' . esc_html__( 'PayPal is loading, please wait...', 'chauffeur' ) . '</div>';
				
				$output .= payment_form($data);
			
			// Load Stripe
			} elseif ($_POST['payment-method'] == 'stripe') {

				global $chauffeur_data;
				
				\Stripe\Stripe::setApiKey($chauffeur_data['stripe_secret_key']);
				$pubkey = $chauffeur_data['stripe_publishable_key'];

				$output .= ' <div class="payment-double-check-warning">
					<p class="stripe-review-payment">' . esc_html__( 'Please review the details before entering payment information.', 'chauffeur' ) . '</p>
				</div><div class="full-booking-wrapper-final"><div class="full-booking-wrapper full-booking-wrapper-3 clearfix">';
				$output .= '<h4>'. esc_html__('Trip Details','chauffeur') .'</h4>
				
				<div class="full-booking-wrapper-td-box">
					<div class="full-booking-wrapper-td">
					
						<div class="full-booking-wrapper-td-1">

							<p class="clearfix"><strong>' .esc_html__('From','chauffeur') .':</strong> <span>' . $pickup_address;

							if( !empty($full_pickup_address) ) {
								$output .= '(' . $full_pickup_address . ')';
							}
							$output .= '</span></p>';

							if(!empty($_POST['pickup-via'])) {
								$output .= '<p class="clearfix">';
								$output .= '<strong>'.esc_html__('Via','chauffeur').':</strong>';
								foreach($_POST['pickup-via'] as $viapoint){
									$output .= '<span>'.$viapoint.'</span>';
								}
								$output .= '</p>';
							}

							$output .= '<p class="clearfix"><strong>'. esc_html__('To','chauffeur') .':</strong> <span>'. $dropoff_address;
							if( !empty($full_dropoff_address) ) {
								$output .= '(' . $full_dropoff_address . ')';
							}
							$output .= '</span></p>';

							$output .= '<p class="clearfix"><strong>'. esc_html__('Date','chauffeur') .':</strong> <span>'. $pickup_date .'</span></p>';

							$output .= '<p class="clearfix"><strong>'. esc_html__('Pick Up Time','chauffeur').':</strong> <span>'. $pickup_time .'</span></p>';

							$output .= '<p class="clearfix"><strong>' . esc_html__('Return','chauffeur') . ':</strong> <span>' .  $return_journey . '</span></p>';

						$output .= '<!-- END .qns-one-half -->
						</div>

						<div class="full-booking-wrapper-td-2">';

							$output .= '<p class="clearfix"><strong>'. esc_html__('Vehicle','chauffeur') .':</strong> <span>'. $selected_vehicle_name .'</span></p>';

							$output .= '<p class="clearfix"><strong>'. esc_html__('Distance','chauffeur').':</strong> <span>'. $first_trip_distance .'('. $first_trip_time .')</span></p>';

							if ( !empty($flight_number) ) {
								$output .= '<p class="clearfix"><strong>'. esc_html__('Flight Number','chauffeur') .':</strong> <span>'. $flight_number .'</span></p>';
							}

							if ( !empty($first_journey_origin ) ) {
								$output .= '<p class="clearfix"><strong>'. esc_html__('Origin','chauffeur') .':</strong> <span>'. $first_journey_origin .'</span></p>';
							}

							$output .= '<p class="clearfix"><strong>'. esc_html__('Meet & Greet Service','chauffeur').':</strong> <span>'.  $first_journey_greet . '</span></p>';

							if ( !empty($pickup_instructions) ) {
								$output .= '<p class="clearfix"><strong>'. esc_html__('Pick Up Instructions','chauffeur') .':</strong> <span>'. $pickup_instructions .'</span></p>';
							}
							
							if ( !empty($dropoff_instructions) ) {
								$output .= '<p class="clearfix"><strong>'. esc_html__('Drop Off Instructions','chauffeur') .':</strong> <span>'. $dropoff_instructions .'</span></p>';
							}
							
							if ( !empty($full_pickup_address) ) {
								$output .= '<p class="clearfix"><strong>'. esc_html__('Full Pick Up Address','chauffeur') .':</strong> <span>'. $full_pickup_address .'</span></p>';
							}
							
							if ( !empty($full_dropoff_address) ) {
								$output .= '<p class="clearfix"><strong>'. esc_html__('Full Drop Off Address','chauffeur') .':</strong> <span>'. $full_dropoff_address .'</span></p>';
							}

						$output .= '<!-- END .qns-one-half -->
						</div>
					</div>
				</div>';

				if($return_journey == 'Return') {
					
					$output .= '<h4>'. esc_html__('Retrun Trip Details','chauffeur') .'</h4>

					<div class="full-booking-wrapper-td-box">
						<div class="full-booking-wrapper-td">
							<div class="full-booking-wrapper-td-1">';

								$output .= '<p class="clearfix"><strong>'. esc_html__('Pickup','chauffeur') .':</strong> <span>'. $return_address .'</span></p>';

								if(!empty($_POST['return-pickup-via'])) {
									$output .= '<p class="clearfix">';
									$output .= '<strong>'.esc_html__('Via','chauffeur').':</strong>';
									foreach($_POST['return-pickup-via'] as $return_viapoint){
										$output .= '<span>'.$return_viapoint.'</span>';
									}
									$output .= '</p>';
								}

								$output .= '<p class="clearfix"><strong>'. esc_html__('Dropoff','chauffeur') .':</strong> <span>'. $return_dropoff .'</span></p>';

								$output .= '<p class="clearfix"><strong>'. esc_html__('Date','chauffeur') .':</strong> <span>'. $return_date .'</span></p>';

								$output .= '<p class="clearfix"><strong>'. esc_html__('Time','chauffeur') .':</strong> <span>'. $return_time .'</span></p>';

							$output .= '<!-- END .qns-one-half -->
							</div>
				
							<div class="full-booking-wrapper-td-2">';

								$output .= '<p class="clearfix"><strong>'. esc_html__('Distance','chauffeur') .':</strong> <span>'. $return_trip_distance .'('. $return_trip_time .')</span></p>';

								$output .= '<p class="clearfix"><strong>'. esc_html__('Flight Number','chauffeur') .':</strong> <span>'. $return_flight_number .'</span></p>';

								$output .= '<p class="clearfix"><strong>'. esc_html__('Origin','chauffeur') .':</strong> <span>'. $return_journey_origin .'</span></p>';

								$output .= '<p class="clearfix"><strong>'. esc_html__('Meet & Greet Service','chauffeur') . ':</strong> <span>' .  $return_journey_greet . '</span></p>';

							$output .= '<!-- END .qns-one-half -->
							</div>
						</div>
					</div>';
				}

				$output .= '<h4 class="bt5">'. esc_html__('Passengers Details','chauffeur') .'</h4>
			
				<div class="full-booking-wrapper-td-box">
					<div class="full-booking-wrapper-td">
						<div class="full-booking-wrapper-td-1">';
				
							$output .= '<p class="clearfix"><strong>'. esc_html__('Passengers','chauffeur') .':</strong> <span>'. $num_passengers .'</span></p>';

							$output .= '<p class="clearfix"><strong>'. esc_html__('Bags','chauffeur') .':</strong> <span>'. $num_bags .'</span></p>';
				
							$output .= '<!-- END .qns-one-half -->
						</div>
				
						<div class="full-booking-wrapper-td-2">';
				
							$output .= '<p class="clearfix"><strong>'. esc_html__('Name','chauffeur') .':</strong> <span>'. $first_name .' '. $last_name .'</span></p>';

							$output .= '<p class="clearfix"><strong>'. esc_html__('Email','chauffeur') .':</strong> <span>'. $email_address .'</span></p>';

							$output .= '<p class="clearfix"><strong>'. esc_html__('Phone','chauffeur') .':</strong> <span>'. $phone_number .'</span></p>';

							$output .= '<!-- END .passenger-details-wrapper -->
						</div>
					</div>
				</div>
				</div>';
				
				
				$output .= '<form action="" method="POST" id="payment_form" class="total-price-display clearfix">
					<h4>Payment Information</h4>
					<div class="total-price-display-inside final-payment-step-attp">
					<div class="trip-amount"><p><span>Amount: </span> <strong style="color: #040404; background: #ffca0963; padding: 5px 10px; border-radius: 8px;">' . chauffeur_get_price($selected_vehicle_price) . '</strong></p></div>

					<div id="payment-response" class="hide"></div>';

					
					$output .= '<div id="paymentElement"> <h4><span id="atbLoader"></span> Loading Payment Form</h4></div>';
					$output .= '<input type="hidden" name="pay_now" value="true" />
					<input type="hidden" name="payment-method" value="stripe" />
					
					
					<input type="hidden" name="booking_reference" value="123" />	
					<input type="hidden" name="num-passengers" value="' . $_POST['num-passengers'] . '" />
					<input type="hidden" name="num-bags" value="' . $_POST['num-bags'] . '" />
					<input type="hidden" name="first-name" value="' . $_POST['first-name'] . '" />
					<input type="hidden" name="last-name" value="' . $_POST['last-name'] . '" />
					<input type="hidden" name="email-address" value="' . $_POST['email-address'] . '" />
					<input type="hidden" name="phone-number" value="' . $_POST['phone-number'] . '" />
					<input type="hidden" name="additional-info" value="' . $_POST['additional-info'] . '" />
					<input type="hidden" name="selected-vehicle-name" value="' . $_POST['selected-vehicle-name'] . '" />
					<input type="hidden" name="selected-vehicle-price" value="' . $_POST['selected-vehicle-price'] . '" />
					<input type="hidden" name="pickup-price" value="' . $_POST['pickup-price'] . '" />
					<input type="hidden" name="return-price" value="' . $_POST['return-price'] . '" />
					<input type="hidden" name="mtgt-price-p" value="' . $_POST['mtgt-price-p'] . '" />
					<input type="hidden" name="mtgt-price-r" value="' . $_POST['mtgt-price-r'] . '" />
					<input type="hidden" name="form-type" value="' . $_POST['form-type'] . '" />
					<input type="hidden" name="pickup-address" value="' . $_POST['pickup-address'] . '" />';
					
					if(!empty($_POST['pickup-via'])) {
						foreach($_POST['pickup-via'] as $viapoint){
							$output .= '<input type="hidden" name="pickup-via[]" value="'. $viapoint .'" />';
						}
					}
					if(isset($_POST['return-address'])){
						$ret_add = $_POST['return-address'];
					}else{
						$ret_add = '';						
					}
					if(isset($_POST['return-dropoff'])){
						$ret_dropoff = $_POST['return-dropoff'];
					}else{
						$ret_dropoff = '';						
					}
					if(isset($_POST['return-flight-number'])){
						$ret_fln = $_POST['return-flight-number'];
					}else{
						$ret_fln = '';						
					}
					if(isset($_POST['return-journey-origin'])){
						$ret_journey_origin = $_POST['return-journey-origin'];
					}else{
						$ret_journey_origin = '';						
					}
					if(isset($_POST['return-journey-greet'])){
						$ret_journey_greet = $_POST['return-journey-greet'];
					}else{
						$ret_journey_greet = '';						
					}

					$output .= '<input type="hidden" name="dropoff-address" value="' . $_POST['dropoff-address'] . '" />
					<input type="hidden" name="pickup-date" value="' . $_POST['pickup-date'] . '" />
					<input type="hidden" name="pickup-time" value="' . $_POST['pickup-time'] . '" />
					<input type="hidden" name="flight-number" value="' . $_POST['flight-number'] . '" />
					<input type="hidden" name="first-journey-origin" value="' . $_POST['first-journey-origin'] . '" />
					<input type="hidden" name="first-journey-greet" value="' . $_POST['first-journey-greet'] . '" />
					<input type="hidden" name="first-trip-distance" value="' . $_POST['first-trip-distance'] . '" />
					<input type="hidden" name="first-trip-time" value="' . $_POST['first-trip-time'] . '" />
					<input type="hidden" name="num-hours" value="' . $_POST['num-hours'] . '" />

					<input type="hidden" name="full-pickup-address" value="' . $_POST['full-pickup-address'] . '" />
					<input type="hidden" name="pickup-instructions" value="' . $_POST['pickup-instructions'] . '" />
					<input type="hidden" name="full-dropoff-address" value="' . $_POST['full-dropoff-address'] . '" />
					<input type="hidden" name="dropoff-instructions" value="' . $_POST['dropoff-instructions'] . '" />
					<input type="hidden" name="return-journey" value="' . $_POST['return-journey'] . '" />
					<input type="hidden" name="return-address" value="' . $ret_add . '" />';

					if(!empty($_POST['return-pickup-via'])) {
						foreach($_POST['return-pickup-via'] as $return_viapoint){
							$output .= '<input type="hidden" name="return-pickup-via[]" value="'. $return_viapoint .'" />';
						}
					}

					$output .= '<input type="hidden" name="return-dropoff" value="' . $ret_dropoff . '" />
					<input type="hidden" name="return-date" value="' . $_POST['return-date'] . '" />
					<input type="hidden" name="return-time" value="' . $_POST['return-time'] . '" />
					<input type="hidden" name="return-trip-distance" value="' . $_POST['return-trip-distance'] . '" />
					<input type="hidden" name="return-trip-time" value="' . $_POST['return-trip-time'] . '" />
					<input type="hidden" name="return-flight-number" value="' . $ret_fln . '" />
					<input type="hidden" name="return-journey-origin" value="' . $ret_journey_origin . '" />
					<input type="hidden" name="return-journey-greet" value="' . $ret_journey_greet . '" />

					<input type="hidden" name="booking_id" value="' . $booking_id . '" />

					<button type="submit" class="final-payment-button" disabled>
					<span id="button-text">
					' . esc_html__( 'Confirm & Pay', 'chauffeur' ) . '
					</span>
					<span id="atbLoader" class="hide">
					</span>
					</button>
					<img class="atb-stripe-partner" src="'. ATT_URL .'/assets/images/stripe-partner.png" width="100"/>

					</div>
				</form>
				</div>

				<script type="text/javascript" src="https://js.stripe.com/v3/"></script>

				<!-- TO DO : Place below JS code in js file and include that JS file -->
<script type="text/javascript">
    const stripe = Stripe("' . $pubkey . '");
    const paymentFrm = document.querySelector("#payment_form");

    let elements; // Define card elements
    initialize();
    let payment_intent_id;
    async function initialize() {
        var selectedvehicleprice = jQuery("input[name=selected-vehicle-price]").val();
        var emailaddress = jQuery("input[name=email-address]").val();
        var name = jQuery("input[name=first-name]").val() + jQuery("input[name=last-name]").val();

        checkStatus();
        async function checkStatus() {
            const clientSecret = new URLSearchParams(window.location.search).get(
                "payment_intent_client_secret"
            );

            const customerID = new URLSearchParams(window.location.search).get(
                "customer_id"
            );

            if (!clientSecret) {
                return;
            }

            const {
                paymentIntent
            } = await stripe.retrievePaymentIntent(clientSecret);

            if (paymentIntent) {
                switch (paymentIntent.status) {
                    case "succeeded":

                        // jQuery("#payment_form").submit();

                        break;
                    case "processing":
                        showMessage("Your payment is processing.");
                        setReinit();
                        break;
                    case "requires_payment_method":
                        showMessage("Your payment was not successful, please try again.");
                        setReinit();
                        break;
                    default:
                        showMessage("Something went wrong.");
                        setReinit();
                        break;
                }
            } else {
                showMessage("Something went wrong.");
                setReinit();
            }
        }
        paymentFrm.addEventListener("submit", handleSubmit);
        const {
            id,
            clientSecret
        } = await fetch("'.get_site_url().'/wp-json/endpoint/v1/stripe", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            //data : {action: "get_data"},
            body: JSON.stringify({
                request_type: "create_payment_intent",
                name: name,
                selectedvehicleprice: selectedvehicleprice,
                emailaddress: emailaddress
            }),
        }).then((r) => r.json());

        const appearance = {
            theme: "stripe",
            rules: {
                ".Label": {
                    fontWeight: "bold",
                    textTransform: "uppercase",
                }
            }
        };

        elements = stripe.elements({
            clientSecret,
            appearance
        });
        const paymentElement = elements.create("payment");
        paymentElement.mount("#paymentElement");
		document.querySelector(".final-payment-button").disabled = false;

        payment_intent_id = id;

    }


    async function handleSubmit(e) {
        var booking_id = jQuery("input[name=booking_id]").val();
        //var return_url = window.location.href;
        //return_url = return_url.replace("#", "");
		
		';
		$stripe_redirect_url = site_url().'/wp-json/endpoint/v2/stripe';

        $output .='e.preventDefault();
		setLoading(true);
		var return_url = "'.$stripe_redirect_url.'";
        var selectedvehicleprice = jQuery("input[name=selected-vehicle-price]").val();
        var emailaddress = jQuery("input[name=email-address]").val();
        var name = jQuery("input[name=first-name]").val() + " " + jQuery("input[name=last-name]").val();
        const {
            id,
            customer_id
        } = await fetch("'.get_site_url().'/wp-json/endpoint/v1/stripe", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                request_type: "create_customer",
                payment_intent_id: payment_intent_id,
                name: name,
                email: emailaddress
            }),
        }).then((r) => r.json());

        const {
            error
        } = await stripe.confirmPayment({
            elements,
            confirmParams: {
                // Make sure to change this to your payment completion page
                return_url: return_url + "?customer_id=" + customer_id + "&booking_id=" + booking_id,
            },
        });
		if (error.type === "card_error" || error.type === "validation_error") {
			showMessage(error.message);
		} else {
			showMessage("An unexpected error occured.");
		}
		setLoading(false);

    }
	function showMessage(messageText) {
		const messageContainer = document.querySelector("#payment-response");
	
		messageContainer.classList.remove("hide");
		messageContainer.textContent = messageText;
	
		setTimeout(function () {
		  messageContainer.classList.add("hide");
		  messageText.textContent = "";
		}, 4000);
	}
	// Show a spinner on payment submission
	function setLoading(isLoading) {
	  if (isLoading) {
		// Disable the button and show a spinner
		document.querySelector(".final-payment-button").disabled = true;
		document.querySelector(".final-payment-button #atbLoader").classList.remove("hide");
		document.querySelector(".final-payment-button #button-text").classList.add("hide");
	  } else {
		document.querySelector(".final-payment-button").disabled = false;
		document.querySelector("#atbLoader").classList.add("hide");
		document.querySelector(".final-payment-button #button-text").classList.remove("hide");
	  }
	}
</script>';
				
			// Load cash
			} elseif ($_POST['payment-method'] == 'cash') {
				
				$get_vehicle_name = get_post_meta($booking_id,'chauffeur_payment_item_name',TRUE);
				$get_pickup_address = get_post_meta($booking_id,'chauffeur_payment_pickup_address',TRUE);
				$get_dropoff_address = get_post_meta($booking_id,'chauffeur_payment_dropoff_address',TRUE);
				$get_pickup_date = get_post_meta($booking_id,'chauffeur_payment_pickup_date',TRUE);
				$get_pickup_time = get_post_meta($booking_id,'chauffeur_payment_pickup_time',TRUE);
				$get_num_passengers = get_post_meta($booking_id,'chauffeur_payment_num_passengers',TRUE);
				$get_num_bags = get_post_meta($booking_id,'chauffeur_payment_num_bags',TRUE);
				$get_first_name = get_post_meta($booking_id,'chauffeur_payment_first_name',TRUE);
				$get_last_name = get_post_meta($booking_id,'chauffeur_payment_last_name',TRUE);
				$get_phone_num = get_post_meta($booking_id,'chauffeur_payment_phone_num',TRUE);
				$get_trip_distance = get_post_meta($booking_id,'chauffeur_payment_trip_distance',TRUE);
				$get_trip_time = get_post_meta($booking_id,'chauffeur_payment_trip_time',TRUE);
				$get_flight_number = get_post_meta($booking_id,'chauffeur_payment_flight_number',TRUE);
				$get_additional_details = get_post_meta($booking_id,'chauffeur_payment_additional_info',TRUE);
				$get_trip_type = get_post_meta($booking_id,'chauffeur_payment_trip_type',TRUE);
				$get_payment_num_hours = get_post_meta($booking_id,'chauffeur_payment_num_hours',TRUE);
				$get_payment_email = get_post_meta($booking_id,'chauffeur_payment_email',TRUE);
				
				$get_full_pickup_address = get_post_meta($booking_id,'chauffeur_payment_full_pickup_address',TRUE);
				$get_pickup_instructions = get_post_meta($booking_id,'chauffeur_payment_pickup_instructions',TRUE);
				$get_full_dropoff_address = get_post_meta($booking_id,'chauffeur_payment_full_dropoff_address',TRUE);
				$get_dropoff_instructions = get_post_meta($booking_id,'chauffeur_payment_dropoff_instructions',TRUE);
				$get_return_journey = get_post_meta($booking_id,'chauffeur_payment_return_journey',TRUE);
				
				$amount = $selected_vehicle_price;
				
				$output .= cash_payment_complete($booking_id);
				update_post_meta($booking_id, 'chauffeur_payment_method', esc_html__('Cash','chauffeur') );
				/*
				// Send customer email
				include ( chauffeur_BASE_DIR . "/includes/templates/email-customer-booking.php");
				wp_mail($get_payment_email,$customer_email_subject,$customer_email_content,$customer_headers);

				// Send admin email
				include ( chauffeur_BASE_DIR . "/includes/templates/email-admin-booking.php");
				wp_mail($chauffeur_data['booking-email'],$admin_email_subject,$admin_email_content,$admin_headers);
				*/
				
			} } else {
		
		$output .= '<!-- BEGIN .clearfix -->
		<div class="booking-form-content b01 clearfix '.$sidebar_class.'">

			<!-- BEGIN .widget-booking-form-wrapper -->
			<div class="widget-booking-form-wrapper booking-step-1-form">

				<!-- BEGIN #booking-tabs -->
				<div id="booking-tabs">

					<ul class="nav clearfix">
						<li class="ifq_tab"><a id="tab1" href="#tab-one-way">' . esc_html__( 'INSTANT FARE QUOTE', 'chauffeur' ) . '</a></li>
						<li class="dnone"><a id="tab2 dnone" href="#tab-hourly">' . esc_html__( 'Hourly', 'chauffeur' ) . '</a></li>
						<li class="dnone"><a id="tab3" href="#tab-flat">' . esc_html__( 'Flat Rate', 'chauffeur' ) . '</a></li>
					</ul>

					<!-- BEGIN #tab-one-way -->
					<div id="tab-one-way">

						<!-- BEGIN .booking-form-1 -->
						<form class="booking-form-1 one-way-transfer-form">
							<div class="atb-box-346">
								<div class="atb-box-346-inside">
									<input type="text" name="pickup-address" id="pickup-address1" class="pickup-address" autocomplete="off" placeholder="' . esc_html__( 'Pick Up Address', 'chauffeur' ) . '" />
									<div class="pickup-via-input">
										<div class="via-wrapper" id="via-wrapper">
										</div>
										<div id="waypointsTotalCount" style="display: none">Something</div>
									</div>
								</div>
								<div class="atb-box-346-inside">
									<input type="text" name="dropoff-address" id="dropoff-address1" class="dropoff-address" autocomplete="off" placeholder="' . esc_html__( 'Drop Off Address', 'chauffeur' ) . '" />
									<div class="onward-add-via-box">
										<a id="onward-add-via" href="javascript:void(0);" class="add_button" title="Add waypoint">
											<img src="'. ATT_URL .'/assets/images/add-icon.png"/>  Add waypoint
										</a>
									</div>
								</div>
							</div>

							<div class="clear"></div>

							<div class="route-content">
								<div id="display-route-distance" class="left-col-distance"></div>
								<div id="display-route-time" class="right-col-time"></div>
							</div>
							<div class="clear"></div>

							<input type="hidden" name="route-distance-string" id="route-distance-string" />
							<input type="hidden" name="route-distance" id="route-distance" />
							<input type="hidden" name="route-time" id="route-time" />
							
							<div id="atbMap"></div>

							<input type="hidden" name="pickup-address-lat" id="pickup-address-lat" />
							<input type="hidden" name="pickup-address-lng" id="pickup-address-lng" />
							<input type="hidden" name="pickup-via-lat" id="pickup-via-lat" />
							<input type="hidden" name="pickup-via-lng" id="pickup-via-lng" />
							<input type="hidden" name="dropoff-address-lat" id="dropoff-address-lat" />
							<input type="hidden" name="dropoff-address-lng" id="dropoff-address-lng" />

							<input type="hidden" name="return-pickup-address-lat" id="return-pickup-address-lat" />
							<input type="hidden" name="return-pickup-address-lng" id="return-pickup-address-lng" />
							<input type="hidden" name="return-pickup-via-lat" id="return-pickup-via-lat" />
							<input type="hidden" name="return-pickup-via-lng" id="return-pickup-via-lng" />
							<input type="hidden" name="return-dropoff-address-lat" id="return-dropoff-address-lat" />
							<input type="hidden" name="return-dropoff-address-lng" id="return-dropoff-address-lng" />

							
							<div class="booking-form-time">
								<label>' . esc_html__( 'Pick Up Date & Time', 'chauffeur' ) . '</label>
							</div>

							<div class="booking-form-hour-min-wrap">
								<div class="booking-form-date">
									<input type="text" name="pickup-date" class="datepicker pickup-date1" value="" placeholder="' . esc_html__( 'Pick Up Date', 'chauffeur' ) . '" />
								</div>
								<div class="booking-form-hour">
									<div class="select-wrapper">
										
										<select name="time-hour" class="time-hour1">';
										$output .= time_input_hours();	
										$output .= '</select>
									</div>
								</div>

								<div class="booking-form-min">
									<div class="select-wrapper">
										
										<select name="time-min" class="time-min1" id="time-min1">
											<option value="00">' . esc_html__( '00', 'chauffeur' ) . '</option>
											<option value="05">' . esc_html__( '05', 'chauffeur' ) . '</option>
											<option value="10">' . esc_html__( '10', 'chauffeur' ) . '</option>
											<option value="15">' . esc_html__( '15', 'chauffeur' ) . '</option>
											<option value="20">' . esc_html__( '20', 'chauffeur' ) . '</option>
											<option value="25">' . esc_html__( '25', 'chauffeur' ) . '</option>
											<option value="30">' . esc_html__( '30', 'chauffeur' ) . '</option>
											<option value="35">' . esc_html__( '35', 'chauffeur' ) . '</option>
											<option value="40">' . esc_html__( '40', 'chauffeur' ) . '</option>
											<option value="45">' . esc_html__( '45', 'chauffeur' ) . '</option>
											<option value="50">' . esc_html__( '50', 'chauffeur' ) . '</option>
											<option value="55">' . esc_html__( '55', 'chauffeur' ) . '</option>
										</select>
									</div>
								</div>
							</div>
							<div class="clear"></div>

							<div class="booking-form-pasbags">
								<div class="booking-form-select-passengers">
									<label for="num-passengers">Passengers</label>
									<select name="num-passengers" id="num-passengers" class="num-passengers">
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
									</select>
								</div>
								<div class="booking-form-select-bags">
								<label for="num-bags">Bags</label>
									<select name="num-bags" id="num-bags" class="num-bags">
										<option value="0">0</option>
										<option value="1">1</option>
										<option value="2">2</option>
										<option value="3">3</option>
										<option value="4">4</option>
										<option value="5">5</option>
										<option value="6">6</option>
										<option value="7">7</option>
										<option value="8">8</option>
									</select>
								</div>
							</div>

							<div class="clear"></div>
							<div class="select-wrapper">
								
							 	<select name="return-journey" id="return-journey">
									<option value="false">' . esc_html__( 'One Way', 'chauffeur' ) . '</option>
									<option value="true">' . esc_html__( 'Return', 'chauffeur' ) . '</option>
								</select>
							</div>
							<input type="hidden" name="pcity" id="pcity">
							<input type="hidden" name="mwt-1" id="mwt-1">
							<input type="hidden" name="mwt-2" id="mwt-2">
							<input type="hidden" name="rcity" id="rcity">

							<div class="return-block" style="display: none;">
								<label class="return-journey-details-title"><span>RETURN JOURNEY DETAILS:</span></label>
								<div class="atb-box-346">
									<div class="atb-box-346-inside">
										<input type="text" name="return-address" id="return-address" class="return-address" placeholder="' . esc_html__( 'Return Pick Up Address', 'chauffeur' ) . '" />
										<div class="pickup-via-input">
											<div class="via_wrapper" id="return-via-wrapper">
											</div>
										</div>
									</div>

									<div class="atb-box-346-inside">
										<input type="text" name="return-dropoff" id="return-dropoff" class="return-dropoff" placeholder="' . esc_html__( 'Return Drop Off Address', 'chauffeur' ) . '" />
										<div class="onward-add-via-box">
											<a id="return-add-via" href="javascript:void(0);" class="add_button" title="Add waypoint">
												<img src="'. ATT_URL .'/assets/images/add-icon.png"/>  Add waypoint
											</a>
										</div>
									</div>
								</div>

								<div class="route-content">
									<div id="display-return-route-distance" class="left-col-distance"></div>
									<div id="display-return-route-time" class="right-col-time"></div>
								</div>
								<div class="clear"></div>
								
								<div id="atbReturnMap"></div>

								<input type="hidden" name="return-route-distance-string" id="return-route-distance-string" />
								<input type="hidden" name="return-route-distance" id="return-route-distance" />
								<input type="hidden" name="return-route-time" id="return-route-time" />


								<div class="booking-form-time">
								<label>' . esc_html__( 'Return Date & Time', 'chauffeur' ) . '</label>
								</div>

								<div class="booking-form-hour-min-wrap">
									<div class="booking-form-date">
										<input type="text" name="return-date" class="datepicker return-date1" value="" placeholder="' . esc_html__( 'Return Date', 'chauffeur' ) . '" />
									</div>
									<div class="booking-form-hour">
										<div class="select-wrapper">
											
											<select name="return-time-hour" class="return-time-hour1">';
											$output .= time_input_hours();	
											$output .= '</select>
										</div>
									</div>

									<div class="booking-form-min">
										<div class="select-wrapper">
											
											<select name="return-time-min" class="return-time-min1" id="return-time-min1">
												<option value="00">' . esc_html__( '00', 'chauffeur' ) . '</option>
												<option value="05">' . esc_html__( '05', 'chauffeur' ) . '</option>
												<option value="10">' . esc_html__( '10', 'chauffeur' ) . '</option>
												<option value="15">' . esc_html__( '15', 'chauffeur' ) . '</option>
												<option value="20">' . esc_html__( '20', 'chauffeur' ) . '</option>
												<option value="25">' . esc_html__( '25', 'chauffeur' ) . '</option>
												<option value="30">' . esc_html__( '30', 'chauffeur' ) . '</option>
												<option value="35">' . esc_html__( '35', 'chauffeur' ) . '</option>
												<option value="40">' . esc_html__( '40', 'chauffeur' ) . '</option>
												<option value="45">' . esc_html__( '45', 'chauffeur' ) . '</option>
												<option value="50">' . esc_html__( '50', 'chauffeur' ) . '</option>
												<option value="55">' . esc_html__( '55', 'chauffeur' ) . '</option>
											</select>
										</div>
									</div>
								</div>

							</div><!-- END .return-block -->
							<!-- END - Added by PG. -->

							<input type="hidden" name="form_type" value="one_way" />
							<input type="hidden" name="first_booking_step" class="first_booking_step" value="1" />

							<input type="hidden" name="action" value="contactform_action" />
							'.wp_nonce_field('ajax_contactform', '_acf_nonce1', true, false).'

							<button type="button" class="bookingbutton1">
				 				<span>' . esc_html__( 'Get My Quote', 'chauffeur' ) . '</span>
							</button>
							<div class="manage-booking-section">
							<div class="manage-booking-text">
								Already booked and need to make amendments?<br>
								Please click below to manage your existing booking.
							</div>
							<div class="manage-booking-button">
								<a href="'.site_url().'/manage-bookings/" target="_blank">Manage Your Bookings</a>
							</div>
						</div>

						<!-- END .booking-form-1 -->
						</form>

					<!-- END #tab-one-way -->
					</div>

					<!-- BEGIN #tab-hourly -->
					<div id="tab-hourly">

						<!-- BEGIN .booking-form-1 -->
						<form class="booking-form-1 hourly-service-form">

							<input type="text" name="pickup-address" id="pickup-address2" class="pickup-address" placeholder="' . esc_html__( 'Pick Up Address', 'chauffeur' ) . '" />

							<div class="one-third">
								<label>' . esc_html__( 'Trip Duration', 'chauffeur' ) . '</label>
							</div>

							<div class="two-thirds last-col">
								<div class="select-wrapper">
									
								 	<select name="num-hours" class="ch-num-hours">';
									
									if ($chauffeur_data['hourly-maximum']) {
										$hourly_maximum = $chauffeur_data['hourly-maximum'];
									} else {
										$hourly_maximum = '48';
									}
									
									foreach (range(1, $hourly_maximum) as $r) {
										$output .= '<option value="' . $r . '">' . $r . ' ' . esc_html__( 'Hour(s)', 'chauffeur') . '</option>';
									}
								
									$output .= '</select>
								</div>
							</div>

							<input type="text" name="dropoff-address" id="dropoff-address2" class="dropoff-address" placeholder="' . esc_html__( 'Drop Off Address', 'chauffeur' ) . '" />

							<div class="booking-form-time">
								<label>' . esc_html__( 'Pick Up Date & Time', 'chauffeur' ) . '</label>
							</div>
							<div class="booking-form-hour-min-wrap">
								<div class="booking-form-date">
									<input type="text" name="pickup-date" class="datepicker pickup-date2" value="" placeholder="' . esc_html__( 'Pick Up Date', 'chauffeur' ) . '" />
								</div>
								<div class="booking-form-hour">
									<div class="select-wrapper">
										
										<select name="time-hour" class="time-hour2">';
										$output .= time_input_hours();	
										$output .= '</select>
									</div>
								</div>

								<div class="booking-form-min">
									<div class="select-wrapper">
										
										<select name="time-min" class="time-min2">
											<option value="00">' . esc_html__( '00', 'chauffeur' ) . '</option>
											<option value="05">' . esc_html__( '05', 'chauffeur' ) . '</option>
											<option value="10">' . esc_html__( '10', 'chauffeur' ) . '</option>
											<option value="15">' . esc_html__( '15', 'chauffeur' ) . '</option>
											<option value="20">' . esc_html__( '20', 'chauffeur' ) . '</option>
											<option value="25">' . esc_html__( '25', 'chauffeur' ) . '</option>
											<option value="30">' . esc_html__( '30', 'chauffeur' ) . '</option>
											<option value="35">' . esc_html__( '35', 'chauffeur' ) . '</option>
											<option value="40">' . esc_html__( '40', 'chauffeur' ) . '</option>
											<option value="45">' . esc_html__( '45', 'chauffeur' ) . '</option>
											<option value="50">' . esc_html__( '50', 'chauffeur' ) . '</option>
											<option value="55">' . esc_html__( '55', 'chauffeur' ) . '</option>
										</select>
									</div>
								</div>
							</div>

							<input type="hidden" name="form_type" value="hourly" />
							<input type="hidden" name="first_booking_step" class="first_booking_step" value="1" />

							<input type="hidden" name="action" value="contactform_action" />
							'.wp_nonce_field('ajax_contactform', '_acf_nonce2', true, false).'

							<button type="button" class="bookingbutton1">
				 				<span>' . esc_html__( 'Reserve Now', 'chauffeur' ) . '</span>
							</button>

						<!-- END .booking-form-1 -->
						</form>

					<!-- END #tab-hourly -->
					</div>
					
					<!-- BEGIN #tab-flat -->
					<div id="tab-flat">

						<!-- BEGIN .booking-form-1 -->
						<form action="' . get_permalink( get_the_ID() ) . '" class="booking-form-1" method="post">

							<div class="booking-form-full">
								<div class="select-wrapper">
									
								 	<select name="flat-location">';
										
										global $post;
										global $wp_query;
										
										$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

										$args = array(
										'post_type' => 'flat_rate_trips',
										'posts_per_page' => '9999',
										'paged' => $paged,
										'order' => 'ASC',
										'orderby' => 'title'
										);

										$wp_query = new WP_Query( $args );
										if ($wp_query->have_posts()) :

											while($wp_query->have_posts()) :

												$wp_query->the_post();
												
												$chauffeur_flat_rate_trips_pick_up_name = get_post_meta($post->ID, 'chauffeur_flat_rate_trips_pick_up_name', true);
												$chauffeur_flat_rate_trips_drop_off_name = get_post_meta($post->ID, 'chauffeur_flat_rate_trips_drop_off_name', true);
												
												$output .= '<option value="' . get_the_ID() . '">';
												$output .= $chauffeur_flat_rate_trips_pick_up_name . ' > ' . $chauffeur_flat_rate_trips_drop_off_name;
												$output .= '</option>';

											endwhile;

										endif;
										wp_reset_query();
										
									$output .='</select>
								</div>
							</div>
							
							<div class="booking-form-full">
								<div class="select-wrapper">
									
								 	<select name="return-journey">
										<option value="false">' . esc_html__( 'One Way', 'chauffeur' ) . '</option>
										<option value="true">' . esc_html__( 'Return', 'chauffeur' ) . '</option>
									</select>
								</div>
							</div>
							
							<input type="text" name="pickup-date" class="datepicker pickup-date3" value="" placeholder="' . esc_html__('Pick Up Date','chauffeur') . '" />

							<div class="booking-form-time">
								<label>' . esc_html__('Pick Up Time','chauffeur') . '</label>
							</div>
							<div class="booking-form-hour-min-wrap">
								<div class="booking-form-hour">
									<div class="select-wrapper">
										
										<select name="time-hour" class="time-hour3">';
										$output .= time_input_hours();	
										$output .= '</select>
									</div>
								</div>

								<div class="booking-form-min">
									<div class="select-wrapper">
										
										<select name="time-min" class="time-min3">
											<option value="00">' . esc_html__( '00', 'chauffeur' ) . '</option>
											<option value="05">' . esc_html__( '05', 'chauffeur' ) . '</option>
											<option value="10">' . esc_html__( '10', 'chauffeur' ) . '</option>
											<option value="15">' . esc_html__( '15', 'chauffeur' ) . '</option>
											<option value="20">' . esc_html__( '20', 'chauffeur' ) . '</option>
											<option value="25">' . esc_html__( '25', 'chauffeur' ) . '</option>
											<option value="30">' . esc_html__( '30', 'chauffeur' ) . '</option>
											<option value="35">' . esc_html__( '35', 'chauffeur' ) . '</option>
											<option value="40">' . esc_html__( '40', 'chauffeur' ) . '</option>
											<option value="45">' . esc_html__( '45', 'chauffeur' ) . '</option>
											<option value="50">' . esc_html__( '50', 'chauffeur' ) . '</option>
											<option value="55">' . esc_html__( '55', 'chauffeur' ) . '</option>
										</select>
									</div>
								</div>
							</div>
							
							<input type="hidden" name="form_type" value="flat" />
							<input type="hidden" name="first_booking_step" class="first_booking_step" value="1" />
							
							<input type="hidden" name="action" value="contactform_action" />
							'.wp_nonce_field('ajax_contactform', '_acf_nonce2', true, false).'

							<button type="button" class="bookingbutton1">
				 				<span>' . esc_html__( 'Reserve Now', 'chauffeur' ) . '</span>
							</button>

						<!-- END .booking-form-1 -->
						</form>

					<!-- END #tab-flat -->
					</div>
					
				<!-- END #booking-tabs -->
				</div>

			<!-- END .widget-booking-form-wrapper -->
			</div>';


			if($sidebar == TRUE){
				$output .= '<div class="booking-step-intro">';

				if ( is_active_sidebar( 'widget-area-booking-form-content' ) ):
					ob_start();
					dynamic_sidebar('widget-area-booking-form-content');
					$output .= ob_get_contents();
					ob_end_clean();
				endif;
				$output .= '</div>';
			}

		$output .= '
		<!-- END .clearfix -->
		</div>';
	
	}
	
}
//$output .= "<script>document.addEventListener('contextmenu', event => event.preventDefault());</script>";
return $output;	
	
}


add_shortcode( 'booking_page', 'booking_page_shortcode' );

?>
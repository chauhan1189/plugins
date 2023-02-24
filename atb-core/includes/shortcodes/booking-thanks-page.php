<?php

function booking_thanks_page_shortcode( $atts, $content = null ) {
	
	global $chauffeur_data;
	
	ob_start();

    if ( isset($_REQUEST['item_number']) && 'publish' == get_post_status ( $_REQUEST['item_number'] ) ) {
		
		$item_number = $_REQUEST['item_number'];
		
		$get_vehicle_name = get_post_meta($item_number,'chauffeur_payment_item_name',TRUE);
		$get_pickup_address = get_post_meta($item_number,'chauffeur_payment_pickup_address',TRUE);
		$get_pickup_via = get_post_meta($item_number,'chauffeur_payment_pickup_via',TRUE);
		$get_dropoff_address = get_post_meta($item_number,'chauffeur_payment_dropoff_address',TRUE);
		$get_pickup_date = get_post_meta($item_number,'chauffeur_payment_pickup_date',TRUE);
		$get_pickup_time = get_post_meta($item_number,'chauffeur_payment_pickup_time',TRUE);
		$get_return_journey = get_post_meta($item_number,'chauffeur_payment_return_journey',TRUE);

		if ( $get_return_journey == 'Return' ) {
			$return_journey_text = esc_html__('Return','chauffeur');
		} else {
			$return_journey_text = esc_html__('One Way','chauffeur');
		}
		$get_num_passengers = get_post_meta($item_number,'chauffeur_payment_num_passengers',TRUE);
		$get_num_bags = get_post_meta($item_number,'chauffeur_payment_num_bags',TRUE);
		$get_first_name = get_post_meta($item_number,'chauffeur_payment_first_name',TRUE);
		$get_last_name = get_post_meta($item_number,'chauffeur_payment_last_name',TRUE);
		$get_phone_num = get_post_meta($item_number,'chauffeur_payment_phone_num',TRUE);
		$get_trip_distance = get_post_meta($item_number,'chauffeur_payment_trip_distance',TRUE);
		$get_trip_time = get_post_meta($item_number,'chauffeur_payment_trip_time',TRUE);
		$get_flight_number = get_post_meta($item_number,'chauffeur_payment_flight_number',TRUE);
		$get_first_journey_origin = get_post_meta($item_number,'chauffeur_payment_first_journey_origin',TRUE);
		$get_first_journey_greet = get_post_meta($item_number,'chauffeur_payment_first_journey_greet',TRUE);
		$get_additional_details = get_post_meta($item_number,'chauffeur_payment_additional_info',TRUE);
		$get_trip_type = get_post_meta($item_number,'chauffeur_payment_trip_type',TRUE);
		$get_payment_num_hours = get_post_meta($item_number,'chauffeur_payment_num_hours',TRUE);
		$get_payment_email = get_post_meta($item_number,'chauffeur_payment_email',TRUE);
		
		$get_full_pickup_address = get_post_meta($item_number,'chauffeur_payment_full_pickup_address',TRUE);
		$get_pickup_instructions = get_post_meta($item_number,'chauffeur_payment_pickup_instructions',TRUE);
		$get_full_dropoff_address = get_post_meta($item_number,'chauffeur_payment_full_dropoff_address',TRUE);
		$get_dropoff_instructions = get_post_meta($item_number,'chauffeur_payment_dropoff_instructions',TRUE);
		
		if($get_return_journey == 'Return'){
			$get_return_address = get_post_meta($item_number,'chauffeur_payment_return_address',TRUE);
			$get_return_via = get_post_meta($item_number,'chauffeur_payment_return_via',TRUE);
			$get_return_dropoff = get_post_meta($item_number,'chauffeur_payment_return_dropoff',TRUE);
			$get_return_date = get_post_meta($item_number,'chauffeur_payment_return_date',TRUE);
			$get_return_time = get_post_meta($item_number,'chauffeur_payment_return_time',TRUE);
			$get_return_flight_number = get_post_meta($item_number,'chauffeur_payment_return_flight_number',TRUE);
			$get_return_journey_origin = get_post_meta($item_number,'chauffeur_payment_return_journey_origin',TRUE);
			$get_return_journey_greet = get_post_meta($item_number,'chauffeur_payment_return_journey_greet',TRUE);
		}



		?>
		
		<!-- BEGIN .booking-step-wrapper -->
		<div class="booking-step-wrapper clearfix thankyou-page-booking-atb">

			<div class="step-wrapper clearfix">
				<div class="step-icon-wrapper">
					<div class="step-icon"><?php esc_html_e('1','chauffeur'); ?></div>
				</div>
				<div class="step-title"><?php esc_html_e('Trip Details','chauffeur'); ?></div>
			</div>

			<div class="step-wrapper clearfix">
				<div class="step-icon-wrapper">
					<div class="step-icon"><?php esc_html_e('2','chauffeur'); ?></div>
				</div>
				<div class="step-title"><?php esc_html_e('Select Vehicle','chauffeur'); ?></div>
			</div>

			<div class="step-wrapper clearfix">
				<div class="step-icon-wrapper">
					<div class="step-icon"><?php esc_html_e('3','chauffeur'); ?></div>
				</div>
				<?php if( $chauffeur_data['hide-pricing'] != '1' ) { ?>
					<div class="step-title"><?php esc_html_e('Enter Payment Details','chauffeur'); ?></div>
				<?php } else { ?>
					<div class="step-title"><?php esc_html_e('Review Details','chauffeur'); ?></div>
				<?php } ?>
			</div>

			<div class="step-wrapper qns-last clearfix">
				<div class="step-icon-wrapper">
					<div class="step-icon step-icon-current"><?php esc_html_e('4','chauffeur'); ?></div>
				</div>
				<div class="step-title"><?php esc_html_e('Confirmation','chauffeur'); ?></div>
			</div>

			<div class="step-line"></div>

		<!-- END .booking-step-wrapper -->
		</div>

		<div class="thankyou-page-booking-atb-main">
			<!-- BEGIN .full-booking-wrapper -->
			<div class="booking-thank-you-page-box-01">
				<img src="<?php echo ATT_URL;?>/assets/images/check.png" width="90"/>
				<h3><?php esc_html_e('Payment Successful','chauffeur'); ?></h3>
				<p>Your booking request is successfully placed. We have sent you a confirmation email which should arrive in your inbox shortly!</p>
			</div>
			<div class="full-booking-wrapper full-booking-wrapper-3 clearfix">

				<h4><?php esc_html_e('Trip Details','chauffeur'); ?></h4>

				<div class="full-booking-wrapper-td-box">
					<div class="full-booking-wrapper-td">
					<!-- BEGIN .qns-one-half -->
					<div class="full-booking-wrapper-td-1">

						<p class="clearfix"><strong><?php esc_html_e('From:','chauffeur'); ?></strong> <span><?php echo $get_pickup_address; ?></span></p>
						<?php
							if(!empty($get_pickup_via)){
								echo '<p class="clearfix"><strong>Via:</strong> <span>'.implode(', ', $get_pickup_via).'</span></p>';
							}
						?>
						<p class="clearfix"><strong><?php esc_html_e('To:','chauffeur'); ?></strong> <span><?php echo $get_dropoff_address; ?></span></p>
						<p class="clearfix"><strong><?php esc_html_e('Date:','chauffeur'); ?></strong> <span><?php echo $get_pickup_date; ?></span></p>
						<p class="clearfix"><strong><?php esc_html_e('Pick Up Time:','chauffeur'); ?></strong> <span><?php echo $get_pickup_time; ?></span></p>
						<p class="clearfix"><strong><?php esc_html_e('Return:','chauffeur'); ?></strong> <span><?php echo $return_journey_text; ?></span></p>

					<!-- END .qns-one-half -->
					</div>

					<!-- BEGIN .qns-one-half -->
					<div class="full-booking-wrapper-td-2">

						<p class="clearfix"><strong><?php esc_html_e('Vehicle:','chauffeur'); ?></strong> <span><?php echo $get_vehicle_name; ?></span></p>

						<?php if ($get_payment_num_hours != '') { ?>

							<p class="clearfix"><strong><?php esc_html_e('Hours','chauffeur'); ?>:</strong> <span><?php echo $get_payment_num_hours; ?></span></p>	

						<?php } else { ?>

							<p class="clearfix"><strong><?php esc_html_e('Distance','chauffeur'); ?>:</strong> <span><?php echo $get_trip_distance; ?> (<?php echo $get_trip_time; ?>)</span></p>	

						<?php } ?>

						<p class="clearfix"><strong><?php esc_html_e('Flight Number:','chauffeur'); ?></strong> <span><?php echo $get_flight_number; ?></span></p>
						<p class="clearfix"><strong><?php esc_html_e('Origin:','chauffeur'); ?></strong> <span><?php echo $get_first_journey_origin; ?></span></p>
						<p class="clearfix"><strong><?php esc_html_e('Meet & Greet Service:','chauffeur'); ?></strong> <span><?php echo $get_first_journey_greet; ?></span></p>

					<!-- END .qns-one-half -->
					</div>

					</div>
				</div>
				<?php
					if ( $get_return_journey == 'Return' ) { ?>

						<h4 class="bt5"><?php esc_html_e('Retrun Trip Details','chauffeur'); ?></h4>

						<!-- BEGIN .clearfix -->
						<div class="full-booking-wrapper-td-box">
							<div class="full-booking-wrapper-td">

								<div class="full-booking-wrapper-td-1">
									<p class="clearfix"><strong><?php esc_html_e('Pickup','chauffeur'); ?>:</strong> <span><?php echo $get_return_address ?></span></p>
									<?php
										if(!empty($get_return_via)){
											echo '<p class="clearfix"><strong>Via:</strong> <span>'.implode(', ', $get_return_via).'</span></p>';
										}
									?>
									<p class="clearfix"><strong><?php esc_html_e('Dropoff','chauffeur'); ?>:</strong> <span> <?php echo $get_return_dropoff; ?></span></p>
									<p class="clearfix"><strong><?php esc_html_e('Date','chauffeur'); ?>:</strong> <span><?php echo $get_return_date; ?></span></p>
									<p class="clearfix"><strong><?php esc_html_e('Time','chauffeur'); ?>:</strong> <span><?php echo $get_return_time; ?></span></p>
								</div>

								<div class="full-booking-wrapper-td-2">

									<p class="clearfix"><strong><?php esc_html_e('Flight Number','chauffeur'); ?>:</strong> <span><?php echo $get_return_flight_number; ?></span></p>

									<p class="clearfix"><strong><?php esc_html_e('Origin','chauffeur'); ?>:</strong> <span><?php echo $get_return_journey_origin; ?></span></p>

									<?php
									if ( $get_return_journey_greet == 'true' ) {
											$return_journey_greet_text = esc_html__('Yes, meet me in arrivals','chauffeur');
										} else {
											$return_journey_greet_text = esc_html__('No, I will call my driver','chauffeur');
										}

										echo '<p class="clearfix"><strong>' . esc_html__('Meet & Greet Service','chauffeur') . ':</strong> <span>' .  $return_journey_greet_text . '</span></p>';
									?>

								</div>

							</div>
						</div>

				<?php } ?>


				<h4 class="bt5"><?php esc_html_e('Passengers Details','chauffeur'); ?></h4>
				
					<div class="full-booking-wrapper-td-box">
						<div class="full-booking-wrapper-td">

							<div class="full-booking-wrapper-td-1">
								<p class="clearfix"><strong><?php esc_html_e('Passengers:','chauffeur'); ?></strong> <span><?php echo $get_num_passengers; ?></span></p>
								<p class="clearfix"><strong><?php esc_html_e('Bags:','chauffeur'); ?></strong> <span><?php echo $get_num_bags; ?></span></p>
							</div>

							<div class="full-booking-wrapper-td-2">
								<p class="clearfix"><strong><?php esc_html_e('Name:','chauffeur'); ?></strong> <span><?php echo $get_first_name . ' ' . $get_last_name; ?></span></p>
								<p class="clearfix"><strong><?php esc_html_e('Email:','chauffeur'); ?></strong> <span><?php echo $get_payment_email; ?></span></p>
								<p class="clearfix"><strong><?php esc_html_e('Phone:','chauffeur'); ?></strong> <span><?php echo $get_phone_num; ?></span></p>
							</div>

						</div>
					</div>

			</div>
		</div>

	<?php } else { ?>
	
		<h2 style="text-align: center; margin: 20px 0;"><?php esc_html_e('Invalid Request','chauffeur'); ?></h2>
		
	<?php }
	
	return ob_get_clean();
	
}

add_shortcode( 'booking_thanks_page', 'booking_thanks_page_shortcode' );

?>
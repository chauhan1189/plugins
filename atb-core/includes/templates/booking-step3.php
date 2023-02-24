<?php global $chauffeur_data; ?>

<?php
	$fileName = get_home_path().'booking-step3.txt';
	$file = fopen($fileName, 'wb');
	if (!empty($_POST['booking_reference']))
		fprintf($file, "%s", $_POST['booking_reference']);
	fclose($file);
?>


<!-- BEGIN .full-booking-wrapper -->
<div class="full-booking-wrapper full-booking-wrapper-3 clearfix">
	<h4><?php esc_html_e('Trip Details','chauffeur'); ?></h4>

	<div class="full-booking-wrapper-td-box">
		<div class="full-booking-wrapper-td">
			
			<div class="full-booking-wrapper-td-1">
				
				<?php if ($_POST['form_type'] == 'one_way') {
					$form_type_text = esc_html__('Distance','chauffeur');
				} elseif ($_POST['form_type'] == 'hourly') {
					$form_type_text = esc_html__('Hourly','chauffeur');
				} elseif ($_POST['form_type'] == 'flat') {
						$form_type_text = esc_html__('Flat Rate','chauffeur');
				} ?>
				
				<?php if ( $_POST['form_type'] == 'flat' ) {
					
					$pick_up_address = get_post_meta($_POST['flat-location'], 'chauffeur_flat_rate_trips_pick_up_name', true);
					$drop_off_address = get_post_meta($_POST['flat-location'], 'chauffeur_flat_rate_trips_drop_off_name', true);
					
				} else {
					
					$pick_up_address = $_POST['pickup-address'];
					$drop_off_address = $_POST['dropoff-address'];
					
				} ?>
				
				<p class="clearfix"><strong><?php esc_html_e('From','chauffeur'); ?>:</strong> <span><?php echo $pick_up_address; if( isset($_POST['full-pickup-address']) ) { echo '(' . $_POST['full-pickup-address'] . ')'; } ?></span></p>
				
				<?php 
				if(isset($_POST['pickup-via'])){
					$pick_up_via = $_POST['pickup-via'];
				}else{
					$pick_up_via = '';
				}
				if ( !empty($pick_up_via) ) { ?>
					<p class="clearfix">
						<strong><?php esc_html_e('Via','chauffeur'); ?>:</strong>
						<?php foreach($pick_up_via as $viapoint){ ?>
							<span><?php echo $viapoint; ?></span>
						<?php } ?>
					</p>
				<?php } ?>

				<p class="clearfix"><strong><?php esc_html_e('To','chauffeur'); ?>:</strong> <span><?php echo $drop_off_address; if( isset($_POST['full-dropoff-address']) ) { echo '(' . $_POST['full-dropoff-address'] . ')'; } ?></span></p>
				
				<p class="clearfix"><strong><?php esc_html_e('Date','chauffeur'); ?>:</strong> <span><?php echo $_POST["pickup-date"]; ?></span></p>

				<p class="clearfix"><strong><?php esc_html_e('Pick Up Time','chauffeur'); ?>:</strong> <span><?php echo $_POST["pickup-time"]; ?></span></p>
				
				<?php if ( $_POST['return-journey'] ) {
					
					if ( $_POST['return-journey'] == 'true' ) {
						$return_journey = esc_html__('Return','chauffeur');
					} else {
						$return_journey = esc_html__('One Way','chauffeur');
					}
				
					echo '<p class="clearfix"><strong>' . esc_html__('Return','chauffeur') . ':</strong> <span>' .  $return_journey . '</span></p>';
				
				} ?>
				
				
			
			</div>

			<div class="full-booking-wrapper-td-2">

				<p class="clearfix"><strong><?php esc_html_e('Vehicle','chauffeur'); ?>:</strong> <span><?php echo $_POST["selected-vehicle-name"]; ?></span></p>
				
				<?php if ($_POST['num-hours'] != '') { ?>
					
					<p class="clearfix"><strong><?php esc_html_e('Hours','chauffeur'); ?>:</strong> <span><?php echo $_POST['num-hours']; ?></span></p>	
					
				<?php } elseif ( $_POST['form_type'] != 'flat' ) { ?>
					
					<p class="clearfix"><strong><?php esc_html_e('Distance','chauffeur'); ?>:</strong> <span><?php echo $_POST['first-trip-distance']; ?> (<?php echo $_POST['first-trip-time']; ?>)</span></p>	
				
				<?php } ?>

				<?php if ( isset($_POST['flight-number']) ) { ?>
					<p class="clearfix"><strong><?php esc_html_e('Flight Number','chauffeur'); ?>:</strong> <span><?php echo $_POST["flight-number"]; ?></span></p>
				<?php } ?>
				
				<?php if ( isset($_POST['first-journey-origin']) ) { ?>
					<p class="clearfix"><strong><?php esc_html_e('Origin','chauffeur'); ?>:</strong> <span><?php echo $_POST["first-journey-origin"]; ?></span></p>
				<?php } ?>
				
				<?php
				if ( $_POST['first-journey-greet'] == 'true' ) {
						$first_journey_greet = esc_html__('Yes','chauffeur');
					} else {
						$first_journey_greet = esc_html__('No','chauffeur');
					}
				
					echo '<p class="clearfix"><strong>' . esc_html__('Meet & Greet Service','chauffeur') . ':</strong> <span>' .  $first_journey_greet . '</span></p>';
				?>
				
				<?php if ( isset($_POST['pickup-instructions']) ) { ?>
				
					<p class="clearfix"><strong><?php esc_html_e('Pick Up Instructions','chauffeur'); ?>:</strong> <span><?php echo $_POST["pickup-instructions"]; ?></span></p>
				
				<?php } ?>
				
				<?php if ( isset($_POST['dropoff-instructions']) ) { ?>
				
					<p class="clearfix"><strong><?php esc_html_e('Drop Off Instructions','chauffeur'); ?>:</strong> <span><?php echo $_POST["dropoff-instructions"]; ?></span></p>
				
				<?php } ?>
				
				<?php if ( isset($_POST['full-pickup-address']) ) { ?>
				
					<p class="clearfix"><strong><?php esc_html_e('Full Pick Up Address','chauffeur'); ?>:</strong> <span><?php echo $_POST["full-pickup-address"]; ?></span></p>
				
				<?php } ?>
				
				<?php if ( isset($_POST['full-dropoff-address']) ) { ?>
				
					<p class="clearfix"><strong><?php esc_html_e('Full Drop Off Address','chauffeur'); ?>:</strong> <span><?php echo $_POST["full-dropoff-address"]; ?></span></p>
				
				<?php } ?>

			
			</div>

		</div>
	</div>

	

	<?php
	if ( $_POST['return-journey'] == 'true' ) { ?>

		<h4 class="bt5"><?php esc_html_e('Retrun Trip Details','chauffeur'); ?></h4>

		<div class="full-booking-wrapper-td-box">
			<div class="full-booking-wrapper-td">

				<div class="full-booking-wrapper-td-1">
					<p class="clearfix"><strong><?php esc_html_e('Pickup','chauffeur'); ?>:</strong> <span> <?php echo $_POST['return-address'] ?></span></p>
					<?php $return_pick_up_via = $_POST['return-pickup-via']; ?>
						<?php if(!empty($return_pick_up_via)) { ?>
							<p class="clearfix">
								<strong><?php esc_html_e('Via','chauffeur'); ?>:</strong>
								<?php foreach($return_pick_up_via as $return_viapoint){ ?>
									<span><?php echo $return_viapoint; ?></span>
								<?php } ?>
							</p>
					<?php } ?>
					<p class="clearfix"><strong><?php esc_html_e('Dropoff','chauffeur'); ?>:</strong> <span> <?php echo $_POST['return-dropoff']; ?></span></p>
					<p class="clearfix"><strong><?php esc_html_e('Date','chauffeur'); ?>:</strong> <span><?php echo $_POST['return-date']; ?></span></p>
					<p class="clearfix"><strong><?php esc_html_e('Time','chauffeur'); ?>:</strong> <span><?php echo $_POST["return-time"]; ?></span></p>
				</div>

				<div class="full-booking-wrapper-td-2">
					<p class="clearfix"><strong><?php esc_html_e('Distance','chauffeur'); ?>:</strong> <span><?php echo $_POST['return-trip-distance']; ?> (<?php echo $_POST['return-trip-time']; ?>)</span></p>
					<p class="clearfix"><strong><?php esc_html_e('Flight Number','chauffeur'); ?>:</strong> <span><?php echo $_POST["return-flight-number"]; ?></span></p>
					<p class="clearfix"><strong><?php esc_html_e('Origin','chauffeur'); ?>:</strong> <span><?php echo $_POST["return-journey-origin"]; ?></span></p>
					<?php
					if ( $_POST['return-journey-greet'] == 'true' ) {
							$return_journey_greet = esc_html__('Yes','chauffeur');
						} else {
							$return_journey_greet = esc_html__('No','chauffeur');
						}

						echo '<p class="clearfix"><strong>' . esc_html__('Meet & Greet Service','chauffeur') . ':</strong> <span>' .  $return_journey_greet . '</span></p>';
					?>
				</div>

			</div>
		</div>

	<?php } ?>

	<h4 class="bt5"><?php esc_html_e('Passengers Details','chauffeur'); ?></h4>

	<div class="full-booking-wrapper-td-box">
		<div class="full-booking-wrapper-td">

			<div class="full-booking-wrapper-td-1">
				<p class="clearfix"><strong><?php esc_html_e('Passengers','chauffeur'); ?>:</strong> <span><?php echo $_POST["num-passengers"]; ?></span></p>
				<p class="clearfix"><strong><?php esc_html_e('Bags','chauffeur'); ?>:</strong> <span><?php echo $_POST["num-bags"]; ?></span></p>
			</div>
			
			<div class="full-booking-wrapper-td-2">
				<p class="clearfix"><strong><?php esc_html_e('Name','chauffeur'); ?>:</strong> <span><?php echo $_POST["first-name"]; ?> <?php echo $_POST["last-name"]; ?></span></p>
				<p class="clearfix"><strong><?php esc_html_e('Email','chauffeur'); ?>:</strong> <span><?php echo $_POST["email-address"]; ?></span></p>
				<p class="clearfix"><strong><?php esc_html_e('Phone','chauffeur'); ?>:</strong> <span><?php echo $_POST["phone-number"]; ?></span></p>
			</div>

		</div>
	</div>


<!-- END .full-booking-wrapper -->
</div>


<form class="total-price-display clearfix" method="post" action="#booking-form">
	<div class="total-price-display-inside">	
		<?php
		
		$flat_rate_surcharge = $chauffeur_data['surcharge-enable-flat-rate'];
		$distance_surcharge = $chauffeur_data['surcharge-enable-distance'];
		$hourly_surcharge = $chauffeur_data['surcharge-enable-hourly'];
		
		// Check if surcharge is enabled for form type
		if ( $_POST['form_type'] == 'flat' && $flat_rate_surcharge == 1 || $_POST['form_type'] == 'hourly' && $hourly_surcharge == 1 || $_POST['form_type'] == 'one_way' && $distance_surcharge == 1 ) {
			$use_surcharge = true;
		} else {
			$use_surcharge = false;
		}
		
		$total_booking_price = $_POST["selected-vehicle-price"];
		
		// If surcharge is enabled for form type add it to total
		if ( $use_surcharge == 1 ) {
			
			if( $chauffeur_data['booking-surcharge'] == 'percentage' ) {
				$total_booking_price = $chauffeur_data['surcharge-percentage'] * $total_booking_price / 100 + $total_booking_price;
			} elseif ( $chauffeur_data['booking-surcharge'] == 'flat-rate' ) {
				$total_booking_price = $total_booking_price + $chauffeur_data['surcharge-flat-rate'];
			} else {
				$total_booking_price = $total_booking_price;
			}
		
		
		} 
		// Else do not add surcharge to total
		else {	
			$total_booking_price = $total_booking_price;
		}
		
		if ( isset($_POST['first-journey-greet']) && $_POST['first-journey-greet'] == 'true') {
			$total_booking_price = $total_booking_price + $_POST['mtgt-price-p'];
		} 
		if ( isset($_POST['return-journey-greet']) && $_POST['return-journey-greet'] == 'true') {
			$total_booking_price = $total_booking_price + $_POST['mtgt-price-r'];
		}
		$uu = "ss";
		if(isset($_POST['first-journey-couponcode']) && !empty($_POST['first-journey-couponcode']))
		{
			$uu = "ss1";
			global $wpdb;
			$couponcode = $_POST['first-journey-couponcode'];
			$results=$wpdb->get_results("Select * from coupon Where coupon = '$couponcode'");
			if (count($results)> 0){
				$uu = "ss2";
				$discount = $results[0]->discount;
				$discount_type = $results[0]->discount_type;
				if($discount_type == "fixed")
				{
					$uu = "ss3";
					$total_booking_price = $total_booking_price - $discount;
				}elseif($discount_type == "percentage"){
					$uu = "ss4";
					$getpercent = $total_booking_price / $discount;
					$total_booking_price = $total_booking_price - $getpercent;
				}
			}
		}
		?>
		
		<input type="hidden" name="uu" value="<?php echo $uu; ?>" />
		<input type="hidden" name="booking_reference" value="<?php echo $_POST['booking_reference']; ?>" />
		<input type="hidden" name="num-passengers" value="<?php echo $_POST['num-passengers']; ?>" />
		<input type="hidden" name="num-bags" value="<?php echo $_POST['num-bags']; ?>" />
		<input type="hidden" name="first-name" value="<?php echo $_POST['first-name']; ?>" />
		<input type="hidden" name="last-name" value="<?php echo $_POST['last-name']; ?>" />
		<input type="hidden" name="email-address" value="<?php echo $_POST['email-address']; ?>" />
		<input type="hidden" name="phone-number" value="<?php echo $_POST['phone-number']; ?>" />
		<input type="hidden" name="flight-number" value="<?php echo $_POST['flight-number']; ?>" />
		<input type="hidden" name="additional-info" value="<?php echo isset($_POST['additional-info']) ? $_POST['additional-info'] : ''; ?>" />
		<input type="hidden" name="selected-vehicle-name" value="<?php echo $_POST['selected-vehicle-name']; ?>" />
		<input type="hidden" name="selected-vehicle-price" value="<?php echo number_format((float)$total_booking_price, 2, '.', ''); ?>" />
		<input type="hidden" name="atb-actual-price" value="<?php echo number_format((float)$total_booking_price, 2, '.', ''); ?>" />
		
		<input type="hidden" name="pickup-price" value="<?php echo $_POST['pickup-price']; ?>" />
		<input type="hidden" name="return-price" value="<?php echo $_POST['return-price']; ?>" />

		<input type="hidden" name="mtgt-price-p" value="<?php echo $_POST['mtgt-price-p']; ?>" />
		<input type="hidden" name="mtgt-price-r" value="<?php echo $_POST['mtgt-price-r']; ?>" />
		
		<input type="hidden" name="form-type" value="<?php echo $_POST['form_type']; ?>" />
		<input type="hidden" name="pickup-address" value="<?php echo $_POST['pickup-address']; ?>" />
		<?php if(!empty($pick_up_via)) { ?>
			<?php foreach($pick_up_via as $viapoint){ ?>
				<input type="hidden" name="pickup-via[]" value="<?php echo $viapoint; ?>" />
			<?php } ?>
		<?php } ?>
		<input type="hidden" name="dropoff-address" value="<?php echo $_POST['dropoff-address']; ?>" />
		<input type="hidden" name="pickup-date" value="<?php echo $_POST['pickup-date']; ?>" />
		<input type="hidden" name="pickup-time" value="<?php echo $_POST['pickup-time']; ?>" />
		<input type="hidden" name="first-journey-origin" value="<?php echo $_POST['first-journey-origin']; ?>" />
		<input type="hidden" name="first-journey-couponcode" value="<?php echo $_POST['first-journey-couponcode']; ?>" />
		<input type="hidden" name="first-journey-greet" value="<?php echo $_POST['first-journey-greet']; ?>" />
		<input type="hidden" name="first-trip-distance" value="<?php echo $_POST['first-trip-distance']; ?>" />
		<input type="hidden" name="first-trip-time" value="<?php echo $_POST['first-trip-time']; ?>" />
		<input type="hidden" name="num-hours" value="<?php echo $_POST['num-hours']; ?>" />
		<input type="hidden" name="flat-location" value="<?php if( isset($_POST['flat-location']) ) {echo $_POST['flat-location'];} ?>" />
		
		<input type="hidden" name="full-pickup-address" value="<?php echo isset($_POST['full-pickup-address']) ? $_POST['full-pickup-address'] : ''; ?>" />
		<input type="hidden" name="pickup-instructions" value="<?php echo isset($_POST['pickup-instructions']) ? $_POST['pickup-instructions'] : ''; ?>" />
		<input type="hidden" name="full-dropoff-address" value="<?php echo isset($_POST['full-dropoff-address']) ? $_POST['full-dropoff-address'] : ''; ?>" />
		<input type="hidden" name="dropoff-instructions" value="<?php echo isset($_POST['dropoff-instructions']) ? $_POST['dropoff-instructions'] : ''; ?>" />
		
		<?php if ( $_POST['return-journey'] ) { ?>
			<input type="hidden" name="return-journey" value="<?php if( isset($_POST['return-journey']) ) {echo $_POST['return-journey'];} ?>" />
		<?php } ?>

		<?php if ( $_POST['return-journey']  == true ) { ?>

			<?php if (isset($_POST['return-address'])) { ?>
				<input type="hidden" name="return-address" value="<?php if( isset($_POST['return-address']) ) { echo $_POST['return-address'];} ?>" />
			<?php } ?>

			<?php if(isset($return_pick_up_via)) { ?>
				<?php foreach($return_pick_up_via as $return_viapoint){ ?>
					<input type="hidden" name="return-pickup-via[]" value="<?php echo $return_viapoint; ?>" />
				<?php } ?>
			<?php } ?>

			<?php if ( isset($_POST['return-dropoff']) ) { ?>
				<input type="hidden" name="return-dropoff" value="<?php if( isset($_POST['return-dropoff']) ) { echo $_POST['return-dropoff'];} ?>" />
			<?php } ?>

			<input type="hidden" name="return-date" value="<?php echo $_POST['return-date']; ?>" />

			<input type="hidden" name="return-time" value="<?php echo $_POST["return-time"]; ?>" />

			<input type="hidden" name="return-trip-distance" value="<?php echo $_POST['return-trip-distance']; ?>" />
			<input type="hidden" name="return-trip-time" value="<?php echo $_POST['return-trip-time']; ?>" />
			
			<?php if ( isset($_POST['return-flight-number']) ) { ?>
				<input type="hidden" name="return-flight-number" value="<?php if( isset($_POST['return-flight-number']) ) { echo $_POST['return-flight-number'];} ?>" />
			<?php } ?>

			<?php if ( isset($_POST['return-journey-origin']) ) { ?>
				<input type="hidden" name="return-journey-origin" value="<?php if( isset($_POST['return-journey-origin']) ) { echo $_POST['return-journey-origin'];} ?>" />
			<?php } ?>

			<input type="hidden" name="return-journey-greet" value="<?php echo isset($_POST['return-journey-greet']) ? $_POST['return-journey-greet'] : ''; ?>" />

			<?php } ?>
		
		<?php if( $chauffeur_data['hide-pricing'] != '1' ) { ?>
		
		<div class="total-price-inner clearfix">
			<p><?php esc_html_e('Total Price','chauffeur'); ?>: <span class="atb-discounted-price" style="display: none;"></span> <span class="atb-actual-price"><?php echo chauffeur_get_price(number_format((float)$total_booking_price, 2, '.', '')); ?></span> 
				
				<?php if ( $use_surcharge == 1 ) { ?>
				
					<?php if( $chauffeur_data['booking-surcharge'] == 'percentage' ) { ?>

						<span>(<?php esc_html_e( 'Includes Surcharge of','chauffeur' ); ?> <?php echo $chauffeur_data['surcharge-percentage']; ?>%)</span>

					<?php } elseif ( $chauffeur_data['booking-surcharge'] == 'flat-rate' ) { ?>

						<span>(<?php esc_html_e( 'Includes Surcharge of','chauffeur' ); ?> <?php echo chauffeur_get_price($chauffeur_data['surcharge-flat-rate']); ?>)</span>

					<?php } ?>
				
				<?php } ?>
				
			</p>
			<p class="atb-coupon-notice" style="display: none;"></p>
		</div>
		
		<?php } ?>
		
		<div class="payment-options-section clearfix">
			
			<?php if( $chauffeur_data['hide-pricing'] != '1' ) { ?>
			
				<?php if( $chauffeur_data['enable-paypal'] == '1' ) {
					
					$paypal_check = '1';
					$stripe_check = '0';
					$cash_check = '0';
					
				} elseif ( $chauffeur_data['enable-paypal'] == '0' && $chauffeur_data['enable-stripe'] == '1' ) {
					
					$paypal_check = '0';
					$stripe_check = '1';
					$cash_check = '0';
					
				} else {
					
					$paypal_check = '0';
					$stripe_check = '0';
					$cash_check = '1';
					
				} ?>
			
				<?php if( $chauffeur_data['enable-paypal'] == '1' ) { ?>
					<div class="radio-wrapper clearfix">						
						<label><input type="radio" name="payment-method" value="paypal" <?php if( $paypal_check == '1' ) { echo 'checked="checked"'; } ?> /> <span><?php esc_html_e('Pay with PayPal','chauffeur'); ?></span></label>
						<div><img src="<?php echo plugins_url('../../assets/images/paypal.png', __FILE__); ?>"></div>
					</div>
				<?php } ?>
			
				<?php if( $chauffeur_data['enable-stripe'] == '1' ) { ?>
					<div class="radio-wrapper clearfix">
						<label><input type="radio" name="payment-method" value="stripe" <?php if( $stripe_check == '1' ) { echo 'checked="checked"'; } ?> /> <span><?php esc_html_e('Pay with Credit Card','chauffeur'); ?></span></label>
						<div><img src="<?php echo plugins_url('../../assets/images/stripe.png', __FILE__); ?>"></div>
					</div>
				<?php } ?>
			
				<?php if( $chauffeur_data['enable-cash'] == '1' ) { ?>
					<div class="radio-wrapper clearfix">
						<label><input type="radio" name="payment-method" value="cash" <?php if( $cash_check == '1' ) { echo 'checked="checked"'; } ?> /> <span><?php esc_html_e('Pay with Cash','chauffeur'); ?></span></label>
					</div>
				<?php } ?>
			
				<?php // If all payment gateways are disabled 
				if( $chauffeur_data['enable-paypal'] != '1' && $chauffeur_data['enable-stripe'] != '1' && $chauffeur_data['enable-cash'] != '1' ) { ?>	
					<input type="hidden" name="payment-method" value="cash" />
				<?php } ?>
			
				<button name="pay_now" id="pay_now" class="payment-button" type="submit">
					<?php esc_html_e('Proceed To Payment','chauffeur'); ?>
				</button>
			
			<?php } else { ?>
				
				<input type="hidden" name="payment-method" value="cash" />

				<button name="pay_now" id="pay_now" class="payment-button" type="submit">
					<?php esc_html_e('Proceed To Book','chauffeur'); ?>
				</button>
				
			<?php } ?>
			<div class="atb-coupon-box">
				<p>Do you have a Coupon Code? <a href="javascript:void(0);">Click to enter</a></p>
				<div class="atb-coupon-box-form-inside" style="display: none">
					<input type="text" name="atb_coupon" placeholder="Enter coupon code">
					<button type="button">APPLY</button>
				</div>
			</div>
		</div>
	</div>
</form>
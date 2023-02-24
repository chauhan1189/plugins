<?php 



if ( $_POST['form_type'] != 'flat' ) {

	if ( empty($_POST['route-distance']) ) {
		$invalid_address = true;
	} else {
		$outgoing_dist = $_POST['route-distance'];
		
		if( !empty($_POST['return-route-distance']) ) {
			$return_dist = $_POST['return-route-distance'];
		}

		$invalid_address = false;
	}

} else {
	$outgoing_dist = false;
}

if ( $invalid_address == true && $_POST['form_type'] != 'flat' ) { ?>

		<div class="msg fail clearfix space8"><p><?php esc_html_e('Sorry, we can\'t calculate the distance between the addresses you supplied, please','chauffeur'); ?> <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>"><?php esc_html_e('try a different address','chauffeur'); ?></a></p></div>
		

<?php } else {


	// function calculatePrice($distance_value_complete, $fixed_rate, $variable_rate)
	// {
	// 	$distance_completed = floatval($distance_value_complete);

	// 	static $minimum_distance = 2;
	// 	static $maximum_distance = 300;

	// 	if ($distance_completed >= $maximum_distance) {
	// 		return $distance_completed * $variable_rate['from_' . $maximum_distance . '_and_above'];;
	// 	}


	// 	if ($distance_completed < $minimum_distance) {
	// 		return $fixed_rate['upto_' . $minimum_distance . '_mile'];
	// 	}

	// 	static $fixed_distances = array(
	// 		4, 9, 14, 19, 25, 29, 35, 40, 45, 50, 56, 62, 70, 80, 90, 100, 
	// 		110, 120, 130, 140, 150, 160, 170, 180, 190, 200, 
	// 		210, 220, 230, 240, 250, 260, 270, 280, 290, 300
	// 	);

	// 	$last_distance = $minimum_distance;
	// 	foreach ($fixed_distances as $distance) {
	// 		if ($distance_completed < $distance) {
	// 			return $fixed_rate['from_' . $last_distance . '_to_' . $distance . '_miles'];
	// 		}
	// 		$last_distance = $distance;
	// 	}

	// 	// This should never happen, unless you modify $fixed_distances and forget to change the early return conditions
	// 	throw new \Exception(sprintf('Distance %f out of range', $distance_completed));
	// }
		
		global $post;
		global $wp_query;
		global $chauffeur_data;

		$args = array(
			'post_type' => 'fleet',
			'posts_per_page' => '9999',
			'order' => 'ASC',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'chauffeur_fleet_bag_capacity',
					'value' => $_POST['num-bags'],
					'compare' => '>='
				),		
				array(
					'key' => 'chauffeur_fleet_passenger_capacity',
					'value' => $_POST['num-passengers'],
					'compare' => '>='
				)
			)
		);

		$wp_query = new WP_Query( $args );
		if ($wp_query->have_posts()) : 
		?>

	<!-- BEGIN .clearfix -->
	<div class="booking-form-step2-main-wrap">

		<!-- BEGIN .select-vehicle-wrapper -->
		<div class="select-vehicle-wrapper">

			<h4><?php esc_html_e('Select Vehicle','chauffeur'); ?></h4>
			
			<div class="title-block7"></div>

			<?php
				if(!empty($_POST['pcity'])){
					$pick_up_city = strtolower($_POST['pcity']);
					$puc = 1;
				}else{
					$pick_up_city = strtolower($_POST['pickup-address']);
					$puc = '';
				}
				if(!empty($_POST['rcity'])){
					$return_city = strtolower($_POST['rcity']);
					$ruc = 1;
				}else{
					$return_city = strtolower($_POST['return-address']);
					$ruc = '';
				}
				
				function atb_check_city_v2($c_arr, $pick_up_city){
					foreach($c_arr as $key => $val){
						if(strpos($pick_up_city, strtolower($val)) !== false){
							return $key;
						}
					}
					return 'default';
				}
				usort($wp_query->posts, "sort_fleet");
				
				while($wp_query->have_posts()) :
				$wp_query->the_post(); 
				

				if( !empty(get_post_meta($post->ID, '_adv_rates_city', true)) ){
					$adv_rates_city = get_post_meta($post->ID, '_adv_rates_city', true);
				}else{
					$adv_rates_city = array();
				}
				
				$city_arr = array();
				$city_arr2 = array();
				$c_arr = array();

				foreach($adv_rates_city as $value){
					$cities = get_post_meta($value, 'city_name', true);
					foreach($cities as $city){
						$city = trim($city);
						$city = strtolower($city);
						$city_arr[$city] = $value;
						$city_arr2[] = $city;
						$c_arr[$value] = $city;
					}
				}
				

				if(!empty($puc)){
					if (in_array($pick_up_city, $city_arr2)) {
						$rate_id_p = $city_arr[$pick_up_city];
					} else {
						$rate_id_p = $city_arr['default'];
					}
				}else{
					$atb_check_city = atb_check_city_v2($c_arr, $pick_up_city);
					if ($atb_check_city == 'default') {
						$rate_id_p = $city_arr['default'];
					} else {
						$rate_id_p = $atb_check_city;
					}
				}

				if(!empty($ruc)){
					if (in_array($return_city, $city_arr2)) {
						$rate_id_r = $city_arr[$return_city];
					} else {
						$rate_id_r = $city_arr['default'];
					}
				}else{
					$atb_check_city = atb_check_city_v2($c_arr, $return_city);
					if ($atb_check_city == 'default') {
						$rate_id_r = $city_arr['default'];
					} else {
						$rate_id_r = $atb_check_city;
					}
				}

				$fixed_rate_p = get_post_meta($rate_id_p, 'fr_pricing', true);
				$variable_rate_p = get_post_meta($rate_id_p, 'vr_pricing', true);
				$extra_price = get_post_meta($rate_id_p, 'atb_gpi_price', true);

				$fixed_rate_r = get_post_meta($rate_id_r, 'fr_pricing', true);
				$variable_rate_r = get_post_meta($rate_id_r, 'vr_pricing', true);

				$mtgt_price_P = get_post_meta($rate_id_p, 'atb_mtgt_price', true);
				$mtgt_price_R = get_post_meta($rate_id_r, 'atb_mtgt_price', true);

				// Get Custom Fields
				$chauffeur_fleet_passenger_capacity = get_post_meta($post->ID, 'chauffeur_fleet_passenger_capacity', true);
				$chauffeur_fleet_bag_capacity = get_post_meta($post->ID, 'chauffeur_fleet_bag_capacity', true);
				//Added by PG.
				$chauffeur_fleet_short_description = get_post_meta($post->ID, 'chauffeur_fleet_short_description', true);
				//END - Added by PG.
				
				// If flat rate service selected
				if ( $_POST['form_type'] == 'flat' ) {
					
					// If return journey price x2
					if ( $_POST['return-journey'] == 'true' ) {
						$vehicle_price_calculate = get_post_meta($post->ID, 'chauffeur_'.$_POST['flat-location'], true);
						$vehicle_price = $vehicle_price_calculate * 2;
					} else {
						$vehicle_price = get_post_meta($post->ID, 'chauffeur_'.$_POST['flat-location'], true);
					}
				
				} elseif ( isset($_POST['num-hours']) ) {
					// If hourly service selected
					$vehicle_price = $_POST['num-hours'] * $chauffeur_fleet_price_per_hour;
				} else {
					// Else charge by the km
					
					//$fixed_rate = NULL;
					//$variable_rate = NULL;	
					/*
					$fixed_rate = array(
						'upto_1_mile' => get_post_meta($post->ID, 'chauffeur_fr_u1', true),
						'from_2_to_5_miles' => get_post_meta($post->ID, 'chauffeur_fr_25', true),
						'from_6_to_9_miles' => get_post_meta($post->ID, 'chauffeur_fr_69', true),
						'from_10_to_30_miles' => get_post_meta($post->ID, 'chauffeur_fr_1030', true)
					);
					$variable_rate = array(
						'from_31_to_50_miles' => get_post_meta($post->ID, 'chauffeur_vr_3150', true),
						'from_51_to_100_miles' => get_post_meta($post->ID, 'chauffeur_vr_51100', true),
						'from_101_to_150_miles' => get_post_meta($post->ID, 'chauffeur_vr_101150', true),
						'from_150_and_above' => get_post_meta($post->ID, 'chauffeur_vr_150', true)
					);
					*/
					if( isset($outgoing_dist) ) {

						if ( isset($return_dist) ) {	
							$vehicle_price = calculatePriceNewP($outgoing_dist, $fixed_rate_p, $variable_rate_p, $extra_price) + calculatePriceNewR($return_dist, $fixed_rate_r, $variable_rate_r, $extra_price);
							$vehicle_price_P = calculatePriceNewP($outgoing_dist, $fixed_rate_p, $variable_rate_p, $extra_price);
							$vehicle_price_R = calculatePriceNewR($return_dist, $fixed_rate_r, $variable_rate_r, $extra_price);
						} else {
							$vehicle_price = calculatePriceNewP($outgoing_dist, $fixed_rate_p, $variable_rate_p, $extra_price);
							$vehicle_price_P = calculatePriceNewP($outgoing_dist, $fixed_rate_p, $variable_rate_p, $extra_price);
							$vehicle_price_R = 0;
						}
						/*
						// Use minimum price
						if( $chauffeur_data['minimum-vehicle-price'] == 'minimum-vehicle-price-on' ) {
							if( $_POST['return-journey'] == 'true' ) {
								if ( $vehicle_price < 100 ) {
									$vehicle_price = 100;
								}
							} else {
								if ( $vehicle_price < 50 ) {
									$vehicle_price = 50;
								}
							}
						}
						*/
						
					} else {
						$vehicle_price = '0';
					}

				}

			?>

			<!-- BEGIN .vehicle-section -->
			<div class="vehicle-section clearfix " id="<?php the_ID(); ?>" mtgt-p="<?php echo ceil( $mtgt_price_P ); ?>" mtgt-r="<?php echo ceil( $mtgt_price_R ); ?>" price-p="<?php echo ceil( $vehicle_price_P ); ?>"  price-r="<?php echo ceil( $vehicle_price_R ); ?>" data-price="<?php echo ceil( $vehicle_price ); ?>" data-title="<?php the_title(); ?>" data-bags="<?php echo $chauffeur_fleet_bag_capacity; ?>" data-passengers="<?php echo $chauffeur_fleet_passenger_capacity; ?>">
				<div class="vehicle-thumbnail">
					<?php 				
						if( has_post_thumbnail() ) { ?>
							<a href="javascript:void(0);" rel="bookmark ss" title="<?php the_title_attribute(); ?>">
								<?php $src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'chauffeur-image-style10' ); ?>
								<?php echo '<img src="' . $src[0] . '" alt="" />'; ?>
							</a>
						<?php }	?>
				</div>
				<div class="vehicle-section-three-combine">
					<div class="vehicle-price-and-title-wrapper">	
						<div class="vehicle-title-and-price">
							<?php if( $chauffeur_data['hide-pricing'] != '1' ) { ?>						
								<p><?php the_title(); ?> <strong><?php echo chauffeur_get_price( ceil( $vehicle_price ) );?></strong></p>						
							<?php } else { ?>						
								<p><?php the_title(); ?></p>						
							<?php } ?>
						</div>

						<div class="vehicle-short-description">
							<small><?php echo $chauffeur_fleet_short_description; ?></small>
						</div>
					</div>
					<div class="vehicle-passenger-and-bags-limit">
						<a href="javascript:void(0);" class="vehicle-book-now-button">Select</a>
						<ul>
							<li class="vehicle-bag-limit" title="Bags Limit"><img src="<?php echo ATT_URL;?>assets/images/luggage.png"/> <?php echo $chauffeur_fleet_bag_capacity; ?></li>
							<li class="vehicle-passenger-limit" title="Seating Capacity"><img src="<?php echo ATT_URL;?>assets/images/passenger.png"/> <?php echo $chauffeur_fleet_passenger_capacity; ?></li>
						</ul>
					</div>
				</div>

			<!-- END .vehicle-section -->
			</div>

			<?php endwhile; ?>

		<!-- END .select-vehicle-wrapper -->
		</div>

		<?php else : ?>
			<p><?php esc_html_e('No vehicles have been added yet','chauffeur'); ?></p>
		<?php endif;

		wp_reset_query(); ?>

		<?php 

			// Set trip type
			if ($_POST['form_type'] == 'one_way') {
				$form_type_text = esc_html__('Distance','chauffeur');
			} elseif ($_POST['form_type'] == 'hourly') {
				$form_type_text = esc_html__('Hourly','chauffeur');
			} elseif ($_POST['form_type'] == 'flat') {
				$form_type_text = esc_html__('Flat Rate','chauffeur');
			}

		?>

			<!-- BEGIN .trip-details-wrapper -->
			<div class="trip-details-wrapper clearfix">
				<h4><?php esc_html_e('Trip Details','chauffeur'); ?></h4>

				<div class="trip-details-inside">
					<div class="title-block7"></div>
					<div class="trip-details-box">
						<!-- BEGIN .trip-details-wrapper-1 -->
						<div class="trip-details-wrapper-1">
							<div class="journey-details-subtitle journey-details-heading"> First Journey </div>

							<?php if ( $_POST['form_type'] == 'flat' ) {
								
								$pick_up_address = get_post_meta($_POST['flat-location'], 'chauffeur_flat_rate_trips_pick_up_name', true);
								$drop_off_address = get_post_meta($_POST['flat-location'], 'chauffeur_flat_rate_trips_drop_off_name', true);
								
							} else {
								
								$pick_up_address = $_POST['pickup-address'];
								$drop_off_address = $_POST['dropoff-address'];

								if(isset($_POST['pickup-via'])){
									$pick_up_via = $_POST['pickup-via'];
								}else{
									$pick_up_via = '';
								}
								
							} ?>
							
							<p class="clearfix"><strong><?php esc_html_e('From','chauffeur'); ?>:</strong> <span><?php echo $pick_up_address; ?></span></p>

							<?php if(!empty($pick_up_via)) { ?>
								<p class="clearfix">
									<strong><?php esc_html_e('Via','chauffeur'); ?>:</strong>
									<?php foreach($pick_up_via as $viapoint){ ?>
										<span><?php echo $viapoint; ?></span>
									<?php } ?>
								</p>
							<?php } ?>

							<p class="clearfix"><strong><?php esc_html_e('To','chauffeur'); ?>:</strong> <span><?php echo $drop_off_address; ?></span></p>

							<p class="clearfix"><strong><?php esc_html_e('Date','chauffeur'); ?>:</strong> <span><?php echo $_POST['pickup-date']; ?></span></p>

							<p class="clearfix"><strong><?php esc_html_e('Pick Up Time','chauffeur'); ?>:</strong> <span><?php echo time_output_hours($_POST['time-hour'],$_POST['time-min']); ?></span></p>
							
							<?php if ( isset($_POST['num-hours']) ) { ?>

								<p class="clearfix"><strong><?php esc_html_e('Hours','chauffeur'); ?>:</strong> <span><?php echo $_POST['num-hours']; ?></span></p>	

							<?php } ?>
								
							<?php if ( $_POST['return-journey']) {
								
							if ( $_POST['return-journey'] == 'true' ) {
								$return_journey = esc_html__('Return','chauffeur');
							} else {
								$return_journey = esc_html__('One Way','chauffeur');
							} ?>
							
							<p class="clearfix"><strong><?php esc_html_e('Return','chauffeur'); ?>:</strong> <span><?php echo $return_journey; ?></span></p>
							
							<?php } ?>
								
							<?php if ($_POST['form_type'] == 'one_way') { ?>
								<?php if ( $invalid_address == true ) { ?>

									<p class="clearfix"><strong><?php esc_html_e('Distance','chauffeur'); ?>:</strong> <span><?php esc_html_e('Invalid Address','chauffeur'); ?></span></p>

								<?php } else { ?>

									<p class="clearfix"><strong><?php esc_html_e('Distance','chauffeur'); ?>:</strong> <span><?php echo $_POST['route-distance-string']; ?> (<?php echo $_POST['route-time']; ?>)</span></p>

								<?php } ?>
							
							<?php } ?>
							
							<p class="clearfix"><strong><?php esc_html_e('Passenger','chauffeur'); ?>:</strong><span><?php echo $_POST['num-passengers']; ?></span></p>
							<p class="clearfix"><strong><?php esc_html_e('Bags','chauffeur'); ?>:</strong><span><?php echo $_POST['num-bags']; ?></span></p>
						</div>
						<!-- END .trip-details-wrapper-1 -->

						<!-- BEGIN .trip-details-wrapper-2 -->
						<div class="trip-details-wrapper-2">

							<?php
							if($_POST['return-journey'] == 'true') { ?>

								<div class="journey-details-subtitle journey-details-heading"> Return Trip Details </div>

								<?php $return_address = $_POST['return-address']; ?>
								<p class="clearfix"><strong><?php esc_html_e('Pickup','chauffeur'); ?>:</strong> <span> <?php echo $return_address; ?></span></p>

								<?php 
									if(isset($_POST['return-pickup-via'])){
										$return_pick_up_via = $_POST['return-pickup-via'];
									}else{
										$return_pick_up_via = '';
									}
									 ; ?>
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

								<p class="clearfix"><strong><?php esc_html_e('Time','chauffeur'); ?>:</strong> <span><?php echo time_output_hours($_POST['return-time-hour'],$_POST['return-time-min']); ?></span></p>

								<p class="clearfix"><strong><?php esc_html_e('Distance','chauffeur'); ?>:</strong><span><?php echo $_POST['return-route-distance-string']; ?> (<?php echo $_POST['return-route-time'] ?>)</span></p>

								<p class="clearfix"><strong><?php esc_html_e('Passenger','chauffeur'); ?>:</strong><span><?php echo $_POST['num-passengers']; ?></span></p>
								<p class="clearfix"><strong><?php esc_html_e('Bags','chauffeur'); ?>:</strong><span><?php echo $_POST['num-bags']; ?></span></p>

							<?php } ?>
							
							
							
						
						</div>
						<!-- END .trip-details-wrapper-2 -->
					</div>
				</div>
				
				<!-- BEGIN .booking-form-1 -->
				<form class="booking-form-1 form-step-2-main">
					
					<!-- START Passenger Details -->
					<h4 class="details-block-heading">Passenger Details</h4>
					<div class="booking-form-1-inside">
						<div class="details-block">
							<div class="attp-col-2">

								<div class="qns-one-half">
									<label><?php esc_html_e('First Name','chauffeur'); ?> <span>*</span></label>
									<input type="text" class="required-form-field" name="first-name" value="" />
								</div>
								
								<div class="qns-one-half last-col">
									<label><?php esc_html_e('Last Name','chauffeur'); ?> <span>*</span></label>
									<input type="text" class="required-form-field" name="last-name" value="" />
								</div>

							</div>
							
							<div class="attp-col-2">

								<div class="qns-one-half">
									<label><?php esc_html_e('Email Address','chauffeur'); ?> <span>*</span></label>
									<input type="text" class="required-form-field form-email-address" name="email-address" value="" />
								</div>

								<div class="qns-one-half last-col">
									<label><?php esc_html_e('Phone Number','chauffeur'); ?> <span>*</span></label>
									<input type="text" class="required-form-field form-phone-number" name="phone-number" value="" />
								</div>
								<input type="hidden" name="num-passengers" value="<?php if(isset($_POST['num-passengers'])){echo $_POST['num-passengers'];}?>">
								<input type="hidden" name="num-bags" value="<?php if(isset($_POST['num-bags'])){echo $_POST['num-bags'];}?>">
							</div>
						</div>
					</div>
					<!-- END Passenger Details -->
					
					<?php if ($_POST['form_type'] == 'flat') { ?>
						
						<div class="booking-form-1-inside">
							<!-- BEGIN .clearfix -->
							<div class="attp-col-2">
								<!-- BEGIN .qns-one-half -->
								<div class="qns-one-half">
									<label><?php esc_html_e('Full Pick Up Address','chauffeur'); ?> <span>*</span></label>
									<textarea cols="10" rows="2" class="required-form-field" name="full-pickup-address"></textarea>
								<!-- END .qns-one-half -->
								</div>

								<!-- BEGIN .qns-one-half -->
								<div class="qns-one-half last-col">									
									<label><?php esc_html_e('Full Drop Off Address','chauffeur'); ?></label>
									<textarea cols="10" rows="2" name="full-dropoff-address"></textarea>
								<!-- END .qns-one-half -->
								</div>
							<!-- END .clearfix -->
							</div>
						</div>
						
					<?php } ?>

						<!-- START Journey Details -->
						<h4 class="details-block-heading bt5">Journey Details</h4>						
						<div class="booking-form-1-inside">
							<div class="details-block">
							
								<!-- BEGIN .clearfix -->
								<div class="attp-col-2">

									<!-- BEGIN .qns-one-half -->
									<div class="qns-one-half">

										<label><?php esc_html_e('Flight Number','chauffeur'); ?></label>
										<input type="text" class="form-flight-number" name="flight-number" title="Please supply a service reference (e.g. *BA315*)" value="" />

									<!-- END .qns-one-half -->
									</div>

									<!-- BEGIN .qns-one-half -->
									<div class="qns-one-half last-col">

										<label><?php esc_html_e('Origin','chauffeur'); ?></label>
										<input type="text" class="first-journey-origin" name="first-journey-origin" title="Please indicate where you're arriving from (e.g. *Charles de Gualle*)" value="" />

									<!-- END .qns-one-half -->
									</div>

								<!-- END .clearfix -->
								</div>
							
								<label><?php esc_html_e('Meet & Greet Service','chauffeur'); ?></label>
								<div class="select-wrapper">
									
									<select name="first-journey-greet" id="first-journey-greet">
										<option value="false" selected><?php esc_html_e( 'No (+£0)', 'chauffeur' ); ?></option>
										<option value="true"><?php esc_html_e( 'Yes', 'chauffeur' ); ?></option>
									</select>
								</div>
							
							</div><!-- END .details-block -->
						</div>
						<!-- END Journey Details -->

					<?php if($_POST['return-journey'] == 'true') { ?>

						<!-- START Return Journey Details -->
						<h4 class="details-block-heading bt5">Return Journey Details</h4>	
						
						<div class="booking-form-1-inside">
							<div class="details-block">
								<!-- BEGIN .clearfix -->
								<div class="attp-col-2">

									<!-- BEGIN .qns-one-half -->
									<div class="qns-one-half">
										<label><?php esc_html_e('Flight Number','chauffeur'); ?></label>
										<input type="text" class="return-flight-number" name="return-flight-number" title="Please supply a service reference (e.g. *BA315*)" value="" />
									<!-- END .qns-one-half -->
									</div>

									<!-- BEGIN .qns-one-half -->
									<div class="qns-one-half last-col">
										<label><?php esc_html_e('Origin','chauffeur'); ?></label>
										<input type="text" class="return-journey-origin" name="return-journey-origin" title="Please indicate where you're arriving from (e.g. *Charles de Gualle*)" value="" />
									<!-- END .qns-one-half -->
									</div>

								<!-- END .clearfix -->
								</div>

								<label><?php esc_html_e('Meet & Greet Service','chauffeur'); ?></label>
								<div class="select-wrapper">
								
								<select name="return-journey-greet" id="return-journey-greet">
									<option value="false" selected><?php esc_html_e( 'No (+£0)', 'chauffeur' ); ?></option>
									<option value="true"><?php esc_html_e( 'Yes', 'chauffeur' ); ?></option>
								</select>
								</div>
							</div>
						</div>
						<!-- END Return Journey Details -->
					<?php } ?>
					<div class="booking-form-1-inside pt___0">	
						<input type="hidden" name="booking_reference" value="22" />
						<input type="hidden" class="selected-vehicle-name" name="selected-vehicle-name" value="" />
						<input type="hidden" class="selected-vehicle-price" name="selected-vehicle-price" value="" />
						<input type="hidden" class="pickup-price" name="pickup-price" value="" />
						<input type="hidden" class="return-price" name="return-price" value="" />

						<input type="hidden" class="mtgt-price-p" name="mtgt-price-p" value="" />
						<input type="hidden" class="mtgt-price-r" name="mtgt-price-r" value="" />
						
						<input type="hidden" class="selected-vehicle-bags" name="selected-vehicle-bags" value="" />
						<input type="hidden" class="selected-vehicle-passengers" name="selected-vehicle-passengers" value="" />
						
						<input type="hidden" name="form_type" value="<?php echo $_POST['form_type']; ?>" />	
						<input type="hidden" name="pickup-address" value="<?php if( isset($pick_up_address) ) {echo $pick_up_address;} ?>" />
						<input type="hidden" name="dropoff-address" value="<?php if( isset($drop_off_address) ) {echo $drop_off_address;} ?>" />
						<?php if(!empty($pick_up_via)) { ?>
							<?php foreach($pick_up_via as $viapoint){ ?>
								<input type="hidden" name="pickup-via[]" value="<?php echo $viapoint; ?>" />
							<?php } ?>
						<?php } ?>
						<input type="hidden" name="pickup-date" value="<?php echo $_POST['pickup-date']; ?>" />
						<input type="hidden" name="pickup-time" value="<?php echo time_output_hours($_POST['time-hour'],$_POST['time-min']); ?>" />
						<input type="hidden" name="first-trip-distance" value="<?php echo $_POST['route-distance-string']; ?>" />
						<input type="hidden" name="first-trip-time" value="<?php echo $_POST['route-time']; ?>" />
						
						<input type="hidden" name="currency-symbol" value="<?php echo $chauffeur_data['currency-symbol']; ?>" />
						<input type="hidden" name="num-hours" value="<?php if( isset($_POST['num-hours']) ) {echo $_POST['num-hours'];} ?>" />
						<input type="hidden" name="flat-location" value="<?php if( isset($_POST['flat-location']) ) {echo $_POST['flat-location'];} ?>" />
						
						<input type="hidden" name="return-journey" value="<?php echo $_POST['return-journey']; ?>" />

						<?php if ( $_POST['return-journey']  == true ) { ?>
							<?php if ( $_POST['return-address'] ) { ?>
								<input type="hidden" name="return-address" value="<?php if( isset($_POST['return-address']) ) { echo $_POST['return-address'];} ?>" />
							<?php } ?>

							<?php if(!empty($return_pick_up_via)) { ?>
								<?php foreach($return_pick_up_via as $return_viapoint){ ?>
									<input type="hidden" name="return-pickup-via[]" value="<?php echo $return_viapoint; ?>" />
								<?php } ?>
							<?php } ?>

							<?php if ( $_POST['return-dropoff'] ) { ?>
								<input type="hidden" name="return-dropoff" value="<?php if( isset($_POST['return-dropoff']) ) { echo $_POST['return-dropoff'];} ?>" />
							<?php } ?>

							<input type="hidden" name="return-date" value="<?php echo $_POST['return-date']; ?>" />

							<input type="hidden" name="return-time" value="<?php echo time_output_hours($_POST['return-time-hour'],$_POST['return-time-min']); ?>" />

							<input type="hidden" name="return-trip-distance" value="<?php echo $_POST['return-route-distance-string']; ?>" />
							<input type="hidden" name="return-trip-time" value="<?php echo $_POST['return-route-time']; ?>" />

						<?php } ?>
						
						<input type="hidden" class="booking-step-2-form" name="booking-step-2-form" value="1" />
						
						<input type="hidden" name="action" value="contactform_action" />
						<?php wp_nonce_field('ajax_contactform', '_acf_nonce', true, false); ?>
						
						<?php if ( !empty($chauffeur_data["terms_conditions"]) ) { ?>
							<div class="booking-terms-wrapper clearfix">	
								<input type="checkbox" id="terms_and_conditions" name="terms_and_conditions" value="1" class="fl terms_and_conditions">
								<label for="terms_and_conditions" class="fl"><?php esc_html_e('I have read and accept the', 'sohohotel_booking'); ?> <a href="#terms-conditions" data-gal="prettyPhoto"><?php esc_html_e('terms &amp; conditions', 'sohohotel_booking'); ?></a>.</label>
							</div>
						<?php } ?>
									
									
						<!-- BEGIN #terms-conditions -->
						<div id="terms-conditions" class="hide">

							<!-- BEGIN .lightbox-title -->
							<div class="lightbox-title">
								<h4 class="title-style4"><?php esc_html_e('Terms &amp; Conditions', 'sohohotel_booking'); ?><span class="title-block"></span></h4>
							<!-- END .lightbox-title -->
							</div>

							<!-- BEGIN .main-content -->
							<div class="main-content main-content-lightbox">

								<?php echo $chauffeur_data["terms_conditions"]; ?>

							<!-- END .page-content -->
							</div>

						<!-- END #terms-conditions -->
						</div>
						
						<?php if( isset($outgoing_dist) ) { ?> 

							<button type="button" class="bookingbutton1">
								<?php esc_html_e('Confirm &amp; Pay','chauffeur'); ?> <i class="fa fa-angle-right"></i>
							</button>

						<?php } elseif ($_POST['form_type'] == 'flat') { ?>
							
							<button type="button" class="bookingbutton1">
								<?php esc_html_e('Confirm &amp; Pay','chauffeur'); ?> <i class="fa fa-angle-right"></i>
							</button>

						<?php } else { ?>
							
							<p><?php esc_html_e('Sorry, the addresses you provided are invalid so we cannot proceed','chauffeur'); ?></p>
							
						<?php } ?>

					<!-- END .booking-form-1 -->
					</div>
				</form>

				<div class="alert-attp">Please enter required data.</div>

				
				
			</div>
			<!-- END .trip-details-wrapper -->

		<!-- END .clearfix -->
	</div>

<?php } ?>
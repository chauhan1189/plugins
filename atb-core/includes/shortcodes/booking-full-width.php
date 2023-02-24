<?php

function booking_full_width_shortcode( $atts, $content = null ) {
	
	global $chauffeur_data;
	
	$output = '	<!-- BEGIN .body-booking-form-wrapper -->
				<div class="body-booking-form-wrapper">

					<!-- BEGIN #booking-tabs-2 -->
					<div id="booking-tabs-2" class="clearfix">

						<ul class="nav clearfix">
							<li><a id="tab1" href="#tab-one-way">' . esc_html__('Distance','chauffeur') . '</a></li>
							<li><a id="tab2" href="#tab-hourly">' . esc_html__('Hourly','chauffeur') . '</a></li>
							<li><a id="tab3" href="#tab-flat">' . esc_html__('Flat Rate','chauffeur') . '</a></li>
						</ul>

						<!-- BEGIN .booking-tabs-2-panels-wrapper -->
						<div class="booking-tabs-2-panels-wrapper clearfix">

							<!-- BEGIN #tab-one-way -->
							<div id="tab-one-way">

								<!-- BEGIN .booking-form-2 -->
								<form id="formOneWay" action="' . esc_url($chauffeur_data['booking-page-url']) . '" class="booking-form-2" method="post">

									<div class="booking-form-input-1">
										<label>' . esc_html__('Pick Up Address','chauffeur') . '</label>

										<div class="icon-input-wrapper">
											<i class="fa fa-map-marker"></i>
											<input type="text" name="pickup-address" id="pickup-address1" class="pickup-address" value="" />
										</div>

									</div>

									<div class="booking-form-input-2">
										<label>' . esc_html__('Drop Off Address','chauffeur') . '</label>

										<div class="icon-input-wrapper">
											<i class="fa fa-map-marker"></i>
											<input type="text" name="dropoff-address" id="dropoff-address1" class="dropoff-address" value="" />
										</div>

									</div>
									
									<div class="booking-form-input-3">
										<label>' . esc_html__('Return','chauffeur') . '</label>
									
										<div class="select-wrapper">
											
										 	<select name="return-journey">
												<option value="false">' . esc_html__( 'One Way', 'chauffeur' ) . '</option>
												<option value="true">' . esc_html__( 'Return', 'chauffeur' ) . '</option>
											</select>
										</div>
									
									</div>
									
									<div class="booking-form-input-4">
										<label>' . esc_html__('Pick Up Date','chauffeur') . '</label>

										<div class="icon-input-wrapper">
											<i class="fa fa-calendar-o"></i>
											<input type="text" name="pickup-date" class="datepicker pickup-date1" />
										</div>

									</div>

									<div class="booking-form-input-5 clearfix">
										<label>' . esc_html__('Pick Up Time','chauffeur') . '</label>

										<div class="select-wrapper select-wrapper-hours">
											
										 	<select name="time-hour" class="time-hour1">';
											$output .= time_input_hours();	
											$output .= '</select>
										</div>

										<div class="select-wrapper select-wrapper-mins">
											
										 	<select name="time-min" class="time-min1">
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
									
									<input type="hidden" name="form_type" value="one_way" />
									<input type="hidden" name="external_form" value="true" />

									<button type="button" class="bookingbutton2">
						 				<span>' . esc_html__('Reserve Now','chauffeur') . '</span>
									</button>

								<!-- END .booking-form-2 -->
								</form>

							<!-- END #tab-one-way -->
							</div>

							<!-- BEGIN #tab-hourly -->
							<div id="tab-hourly">

								<!-- BEGIN .booking-form-3 -->
								<form id="formHourly" action="' . esc_url($chauffeur_data['booking-page-url']) . '" class="booking-form-3" method="post">

									<div class="booking-form-input-1">
										<label>' . esc_html__('Pick Up Address','chauffeur') . '</label>

										<div class="icon-input-wrapper">
											<i class="fa fa-map-marker"></i>
											<input type="text" name="pickup-address" id="pickup-address2" class="pickup-address" value="" />
										</div>

									</div>

									<div class="booking-form-input-2">
										<label>' . esc_html__('Trip Duration','chauffeur') . '</label>

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

									<div class="booking-form-input-3">
										<label>' . esc_html__('Drop Off Address','chauffeur') . '</label>

										<div class="icon-input-wrapper">
											<i class="fa fa-map-marker"></i>
											<input type="text" name="dropoff-address" id="dropoff-address2" class="dropoff-address" value="" />
										</div>

									</div>

									<div class="booking-form-input-4">
										<label>' . esc_html__('Pick Up Date','chauffeur') . '</label>

										<div class="icon-input-wrapper">
											<i class="fa fa-calendar-o"></i>
											<input type="text" name="pickup-date" class="datepicker pickup-date2" />
										</div>

									</div>

									<div class="booking-form-input-5 clearfix">
										<label>' . esc_html__('Pick Up Time','chauffeur') . '</label>

										<div class="select-wrapper select-wrapper-hours">
											
										 	<select name="time-hour" class="time-hour2">';
											$output .= time_input_hours();	
											$output .= '</select>
										</div>

										<div class="select-wrapper select-wrapper-mins">
											
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
									
									<input type="hidden" name="form_type" value="hourly" />
									<input type="hidden" name="external_form" value="true" />
									
									<button type="button" class="bookingbutton2">
						 				<span>' . esc_html__('Reserve Now','chauffeur') . '</span>
									</button>

								<!-- END .booking-form-3 -->
								</form>

							<!-- END #tab-hourly -->
							</div>
							
							<!-- BEGIN #tab-flat -->
							<div id="tab-flat">

								<!-- BEGIN .booking-form-1 -->
								<form id="formFlat" action="' . esc_url($chauffeur_data['booking-page-url']) . '" class="booking-form-2" method="post">

									<div class="booking-form-input-1">
									
										<label>' . esc_html__('Trip','chauffeur') . '</label>
									
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
									
									<div class="booking-form-input-2">
										
										<label>' . esc_html__('Return','chauffeur') . '</label>
									
										<div class="select-wrapper">
											
										 	<select name="return-journey">
												<option value="false">' . esc_html__( 'One Way', 'chauffeur' ) . '</option>
												<option value="true">' . esc_html__( 'Return', 'chauffeur' ) . '</option>
											</select>
										</div>
										
									</div>
									
									<div class="booking-form-input-3">
										<label>' . esc_html__('Pick Up Date','chauffeur') . '</label>

										<div class="icon-input-wrapper">
											<i class="fa fa-calendar-o"></i>
											<input type="text" name="pickup-date" class="datepicker pickup-date3" />
										</div>

									</div>

									<div class="booking-form-input-4 clearfix">
										<label>' . esc_html__('Pick Up Time','chauffeur') . '</label>

										<div class="select-wrapper select-wrapper-hours">
											
										 	<select name="time-hour" class="time-hour3">';
											$output .= time_input_hours();	
											$output .= '</select>
										</div>

										<div class="select-wrapper select-wrapper-mins">
											
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
									
									<input type="hidden" name="form_type" value="flat" />
									<input type="hidden" name="external_form" value="true" />
									
									<button type="button" class="bookingbutton2">
						 				<span>' . esc_html__('Reserve Now','chauffeur') . '</span>
									</button>

								<!-- END .booking-form-1 -->
								</form>

							<!-- END #tab-flat -->
							</div>

						<!-- END .booking-tabs-2-panels-wrapper -->
						</div>

					<!-- END #booking-tabs -->
					</div>

				<!-- END .body-booking-form-wrapper -->
				</div>';
	
	return $output;

}

add_shortcode( 'booking_full_width', 'booking_full_width_shortcode' );

?>
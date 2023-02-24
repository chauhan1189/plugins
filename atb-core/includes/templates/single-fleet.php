<?php get_header(); ?>

<div id="page-header" <?php echo wp_kses($chauffeur_page_header_image, $chauffeur_allowed_html_array); ?>>	
	
	<div class="page-header-inner">
		<h1><?php the_title(); ?></h1>
		<div class="title-block3"></div>
		<?php echo dimox_breadcrumbs();?>
	</div>
	
</div>

<!-- BEGIN .content-wrapper-outer -->
<div class="content-wrapper-outer clearfix">
	
	<?php if( $chauffeur_data['fleet-single-sidebar'] == 'chauffeur-fleet-sidebar-on' ) { ?>
	
		<!-- BEGIN .main-content -->
		<div class="main-content">
		
	<?php } else { ?>
		
		<!-- BEGIN .main-content -->
		<div class="main-content main-content-full">
		
	<?php } ?>
			
		<?php if ( post_password_required() ) {
			echo chauffeur_password_form();
		} else { ?>
				
			<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
					
				<?php the_content(); ?>
					
			<?php endwhile; ?>
			<?php endif; ?>

		<?php } ?>
			
	<!-- END .main-content -->
	</div>
	
	<?php if( $chauffeur_data['fleet-single-sidebar'] == 'chauffeur-fleet-sidebar-on' ) { ?>
		
	<!-- BEGIN .sidebar-content -->
	<div class="sidebar-content">

		<!-- BEGIN .widget-booking-form-wrapper -->
		<div class="widget-booking-form-wrapper">

			<!-- BEGIN #booking-tabs -->
			<div id="booking-tabs">

				<ul class="nav clearfix">
					<li><a id="tab1" href="#tab-one-way"><?php esc_html_e( 'Distance', 'chauffeur' ); ?></a></li>
					<li><a id="tab2" href="#tab-hourly"><?php esc_html_e( 'Hourly', 'chauffeur' ); ?></a></li>
					<li><a id="tab3" href="#tab-flat"><?php esc_html_e( 'Flat Rate', 'chauffeur' ); ?></a></li>
				</ul>

				<!-- BEGIN #tab-one-way -->
				<div id="tab-one-way">

					<!-- BEGIN .booking-form-1 -->	
					<form id="formOneWay" action="<?php echo esc_url($chauffeur_data['booking-page-url']); ?>" class="booking-form-1" method="post">

						<input type="text" name="pickup-address" id="pickup-address1" class="pickup-address" value="" placeholder="<?php esc_html_e('Pick Up Address', 'chauffeur'); ?>" />
						<input type="text" name="dropoff-address" id="dropoff-address1" class="dropoff-address" value="" placeholder="<?php esc_html_e('Drop Off Address', 'chauffeur'); ?>" />
						<input type="text" name="pickup-date" class="datepicker pickup-date1" value="" placeholder="<?php esc_html_e('Pick Up Date', 'chauffeur'); ?>" />

						<div class="booking-form-time">
							<label><?php esc_html_e('Pick Up Time', 'chauffeur'); ?></label>
						</div>

						<div class="booking-form-hour">
							<div class="select-wrapper">
								
							 	<select name="time-hour" class="time-hour1">
								<?php echo time_input_hours();	?>
								</select>
							</div>
						</div>

						<div class="booking-form-min">
							<div class="select-wrapper">
								
							 	<select name="time-min" class="time-min1">
									<option value="00"><?php esc_html_e('00', 'chauffeur'); ?></option>
									<option value="05"><?php esc_html_e('05', 'chauffeur'); ?></option>
									<option value="10"><?php esc_html_e('10', 'chauffeur'); ?></option>
									<option value="15"><?php esc_html_e('15', 'chauffeur'); ?></option>
									<option value="20"><?php esc_html_e('20', 'chauffeur'); ?></option>
									<option value="25"><?php esc_html_e('25', 'chauffeur'); ?></option>
									<option value="30"><?php esc_html_e('30', 'chauffeur'); ?></option>
									<option value="35"><?php esc_html_e('35', 'chauffeur'); ?></option>
									<option value="40"><?php esc_html_e('40', 'chauffeur'); ?></option>
									<option value="45"><?php esc_html_e('45', 'chauffeur'); ?></option>
									<option value="50"><?php esc_html_e('50', 'chauffeur'); ?></option>
									<option value="55"><?php esc_html_e('55', 'chauffeur'); ?></option>
								</select>
							</div>
						</div>
						
						<input type="hidden" name="form_type" value="one_way" />
						<input type="hidden" name="external_form" value="true" />
						
						<button type="button" class="bookingbutton2">
			 				<span><?php esc_html_e('Reserve Now', 'chauffeur'); ?></span>
						</button>

					<!-- END .booking-form-1 -->
					</form>

				<!-- END #tab-one-way -->
				</div>

				<!-- BEGIN #tab-hourly -->
				<div id="tab-hourly">

					<!-- BEGIN .booking-form-1 -->
					<form id="formHourly" action="<?php echo esc_url($chauffeur_data['booking-page-url']); ?>" class="booking-form-1" method="post">

						<input type="text" name="pickup-address" id="pickup-address2" class="pickup-address" value="" placeholder="<?php esc_html_e('Pick Up Address', 'chauffeur'); ?>" />

						<div class="one-third">
							<label><?php esc_html_e('Trip Duration', 'chauffeur'); ?></label>
						</div>

						<div class="two-thirds last-col">
							<div class="select-wrapper">
								
							 	<select name="num-hours" class="ch-num-hours">
									
									<?php 
									
									if ($chauffeur_data['hourly-maximum']) {
										$hourly_maximum = $chauffeur_data['hourly-maximum'];
									} else {
										$hourly_maximum = '48';
									}
									
									foreach (range(1, $hourly_maximum) as $r) {
										echo '<option value="' . $r . '">' . $r . ' ' . esc_html__( 'Hour(s)', 'chauffeur') . '</option>';
									}
								
									?>
									
								</select>
							</div>
						</div>

						<input type="text" name="dropoff-address" value="" id="dropoff-address2" class="dropoff-address" placeholder="<?php esc_html_e('Drop Off Address', 'chauffeur'); ?>" />
						<input type="text" name="pickup-date" class="datepicker pickup-date2" value="" placeholder="<?php esc_html_e('Pick Up Date', 'chauffeur'); ?>" />

						<div class="booking-form-time">
							<label><?php esc_html_e('Pick Up Time', 'chauffeur'); ?></label>
						</div>

						<div class="booking-form-hour">
							<div class="select-wrapper">
								
							 	<select name="time-hour" class="time-hour2">
								<?php echo time_input_hours();	?>
								</select>
							</div>
						</div>

						<div class="booking-form-min">
							<div class="select-wrapper">
								
							 	<select name="time-min" class="time-min2">
									<option value="00"><?php esc_html_e('00', 'chauffeur'); ?></option>
									<option value="05"><?php esc_html_e('05', 'chauffeur'); ?></option>
									<option value="10"><?php esc_html_e('10', 'chauffeur'); ?></option>
									<option value="15"><?php esc_html_e('15', 'chauffeur'); ?></option>
									<option value="20"><?php esc_html_e('20', 'chauffeur'); ?></option>
									<option value="25"><?php esc_html_e('25', 'chauffeur'); ?></option>
									<option value="30"><?php esc_html_e('30', 'chauffeur'); ?></option>
									<option value="35"><?php esc_html_e('35', 'chauffeur'); ?></option>
									<option value="40"><?php esc_html_e('40', 'chauffeur'); ?></option>
									<option value="45"><?php esc_html_e('45', 'chauffeur'); ?></option>
									<option value="50"><?php esc_html_e('50', 'chauffeur'); ?></option>
									<option value="55"><?php esc_html_e('55', 'chauffeur'); ?></option>
								</select>
							</div>
						</div>
						
						<input type="hidden" name="form_type" value="hourly" />
						<input type="hidden" name="external_form" value="true" />
						
						<button type="button" class="bookingbutton2">
			 				<span><?php esc_html_e('Reserve Now', 'chauffeur'); ?></span>
						</button>

					<!-- END .booking-form-1 -->
					</form>

				<!-- END #tab-hourly -->
				</div>
				
				<!-- BEGIN #tab-flat -->
				<div id="tab-flat">

					<!-- BEGIN .booking-form-1 -->
					<form id="formFlat" action="<?php echo esc_url($chauffeur_data['booking-page-url']); ?>" class="booking-form-1" method="post">
						
						<div class="booking-form-full">
							<div class="select-wrapper">
								
							 	<select name="flat-location">
									
									<?php global $post;
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
											
											echo '<option value="' . get_the_ID() . '">';
											echo $chauffeur_flat_rate_trips_pick_up_name . ' > ' . $chauffeur_flat_rate_trips_drop_off_name;
											echo '</option>';

										endwhile;

									endif;
									wp_reset_query(); ?>
									
								</select>
							</div>
						</div>
						
						<div class="booking-form-full">
							<div class="select-wrapper">
								
							 	<select name="return-journey">
									<option value="false"><?php esc_html_e( 'One Way', 'chauffeur' ); ?></option>
									<option value="true"><?php esc_html_e( 'Return', 'chauffeur' ); ?></option>
								</select>
							</div>
						</div>
						
						<input type="text" name="pickup-date" class="datepicker pickup-date3" value="" placeholder="<?php esc_html_e('Pick Up Date','chauffeur'); ?>" />

						<div class="booking-form-time">
							<label><?php esc_html_e('Pick Up Time','chauffeur'); ?></label>
						</div>

						<div class="booking-form-hour">
							<div class="select-wrapper">
								
							 	<select name="time-hour" class="time-hour3">
								<?php echo time_input_hours();	?>
								</select>
							</div>
						</div>

						<div class="booking-form-min">
							<div class="select-wrapper">
								
							 	<select name="time-min" class="time-min3">
									<option value="00"><?php esc_html_e('00', 'chauffeur'); ?></option>
									<option value="05"><?php esc_html_e('05', 'chauffeur'); ?></option>
									<option value="10"><?php esc_html_e('10', 'chauffeur'); ?></option>
									<option value="15"><?php esc_html_e('15', 'chauffeur'); ?></option>
									<option value="20"><?php esc_html_e('20', 'chauffeur'); ?></option>
									<option value="25"><?php esc_html_e('25', 'chauffeur'); ?></option>
									<option value="30"><?php esc_html_e('30', 'chauffeur'); ?></option>
									<option value="35"><?php esc_html_e('35', 'chauffeur'); ?></option>
									<option value="40"><?php esc_html_e('40', 'chauffeur'); ?></option>
									<option value="45"><?php esc_html_e('45', 'chauffeur'); ?></option>
									<option value="50"><?php esc_html_e('50', 'chauffeur'); ?></option>
									<option value="55"><?php esc_html_e('55', 'chauffeur'); ?></option>
								</select>
							</div>
						</div>
						
						<input type="hidden" name="form_type" value="flat" />
						<input type="hidden" name="external_form" value="true" />
						
						<button type="button" class="bookingbutton2">
			 				<span><?php esc_html_e('Reserve Now', 'chauffeur'); ?></span>
						</button>

					<!-- END .booking-form-1 -->
					</form>

				<!-- END #tab-flat -->
				</div>
				
			<!-- END #booking-tabs -->
			</div>

		<!-- END .widget-booking-form-wrapper -->
		</div>
		
		<?php if( $chauffeur_data['hide-pricing'] != '1' ) { ?>
		
		<!-- BEGIN .widget -->
		<div class="widget">
			<div class="widget-block"></div>

			<h3><?php esc_html_e( 'Pricing Options', 'chauffeur' ); ?></h3>

			<div class="pricing-options-widget">
				
				<?php $chauffeur_fleet_sidebar_icon_1 = get_post_meta($post->ID, 'chauffeur_fleet_sidebar_icon_1', true);
				$chauffeur_fleet_sidebar_title_1 = get_post_meta($post->ID, 'chauffeur_fleet_sidebar_title_1', true);
				$chauffeur_fleet_sidebar_content_1 = get_post_meta($post->ID, 'chauffeur_fleet_sidebar_content_1', true);
				
				$chauffeur_fleet_sidebar_icon_2 = get_post_meta($post->ID, 'chauffeur_fleet_sidebar_icon_2', true);
				$chauffeur_fleet_sidebar_title_2 = get_post_meta($post->ID, 'chauffeur_fleet_sidebar_title_2', true);
				$chauffeur_fleet_sidebar_content_2 = get_post_meta($post->ID, 'chauffeur_fleet_sidebar_content_2', true);
				
				$chauffeur_fleet_sidebar_icon_3 = get_post_meta($post->ID, 'chauffeur_fleet_sidebar_icon_3', true);
				$chauffeur_fleet_sidebar_title_3 = get_post_meta($post->ID, 'chauffeur_fleet_sidebar_title_3', true);
				$chauffeur_fleet_sidebar_content_3 = get_post_meta($post->ID, 'chauffeur_fleet_sidebar_content_3', true); ?>
				
				<ul>
					<?php if($chauffeur_fleet_sidebar_title_1) { ?><li class="hour-pricing-option"><i class="fa <?php echo $chauffeur_fleet_sidebar_icon_1; ?>"></i><strong><?php echo $chauffeur_fleet_sidebar_title_1; ?></strong> <?php echo $chauffeur_fleet_sidebar_content_1; ?></li><?php } ?>
					<?php if($chauffeur_fleet_sidebar_title_2) { ?><li class="day-pricing-option"><i class="fa <?php echo $chauffeur_fleet_sidebar_icon_2; ?>"></i><strong><?php echo $chauffeur_fleet_sidebar_title_2; ?></strong> <?php echo $chauffeur_fleet_sidebar_content_2; ?></li><?php } ?>
					<?php if($chauffeur_fleet_sidebar_title_3) { ?><li class="airport-pricing-option"><i class="fa <?php echo $chauffeur_fleet_sidebar_icon_3; ?>"></i><strong><?php echo $chauffeur_fleet_sidebar_title_3; ?></strong> <?php echo $chauffeur_fleet_sidebar_content_3; ?></li><?php } ?>
				</ul>

			</div>

		<!-- END .widget -->
		</div>
		
		<?php } ?>
		
	<!-- END .sidebar-content -->
	</div>
	
	<?php } ?>

<!-- BEGIN .content-wrapper-outer -->
</div>

<?php get_footer(); ?>
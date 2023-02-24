<?php get_header(); ?>


	<div class="booking-details-page">
			
        <?php if(!get_current_user_id()) {
        
            ob_start();
            $permalink = get_permalink();
            $message = __('Only logged in users can access this page. <a href="%1$s">Login</a> or <a href="%2$s">Register</a>.', "adverts");
            $parsed = sprintf($message, wp_login_url( $permalink ), wp_registration_url( $permalink ) );
            echo $parsed;
            $content = ob_get_clean();
            echo $content;
        }   
        
        else { ?>
				
            <?php if ( have_posts() ) : while ( have_posts() ) : the_post();
                    $author = get_the_author_meta( 'ID' );
                    if( $author == get_current_user_id() || current_user_can('administrator') ) {
                        the_content();
                        global $chauffeur_data;
                        $get_trip_type = get_post_meta($post->ID,'chauffeur_payment_trip_type',TRUE);
                        $get_vehicle_name = get_post_meta($post->ID,'chauffeur_payment_item_name',TRUE);
                        $get_pickup_address = get_post_meta($post->ID,'chauffeur_payment_pickup_address',TRUE);
                        $get_pickup_via = get_post_meta($post->ID,'chauffeur_payment_pickup_via',TRUE);
                        $get_dropoff_address = get_post_meta($post->ID,'chauffeur_payment_dropoff_address',TRUE);
                        $get_pickup_date = get_post_meta($post->ID,'chauffeur_payment_pickup_date',TRUE);
                        $get_pickup_time = get_post_meta($post->ID,'chauffeur_payment_pickup_time',TRUE);
                        
                        $get_trip_distance = get_post_meta($post->ID,'chauffeur_payment_trip_distance',TRUE);
                        $get_trip_time = get_post_meta($post->ID,'chauffeur_payment_trip_time',TRUE);
                        $get_flight_number = get_post_meta($post->ID,'chauffeur_payment_flight_number',TRUE);
                        $get_additional_details = get_post_meta($post->ID,'chauffeur_payment_additional_info',TRUE);
                        $get_first_journey_origin = get_post_meta($post->ID,'chauffeur_payment_first_journey_origin',TRUE);
                        $get_first_journey_greet = get_post_meta($post->ID,'chauffeur_payment_first_journey_greet',TRUE);
                        
                        $get_num_passengers = get_post_meta($post->ID,'chauffeur_payment_num_passengers',TRUE);
                        $get_num_bags = get_post_meta($post->ID,'chauffeur_payment_num_bags',TRUE);
                        $get_first_name = get_post_meta($post->ID,'chauffeur_payment_first_name',TRUE);
                        $get_last_name = get_post_meta($post->ID,'chauffeur_payment_last_name',TRUE);
                        $get_phone_num = get_post_meta($post->ID,'chauffeur_payment_phone_num',TRUE);
                        $get_payment_email = get_post_meta($post->ID,'chauffeur_payment_email',TRUE);

                        // $get_payment_num_hours = get_post_meta($post->ID,'chauffeur_payment_num_hours',TRUE);
                        // $get_full_pickup_address = get_post_meta($post->ID,'chauffeur_payment_full_pickup_address',TRUE);
                        // $get_pickup_instructions = get_post_meta($post->ID,'chauffeur_payment_pickup_instructions',TRUE);
                        // $get_full_dropoff_address = get_post_meta($post->ID,'chauffeur_payment_full_dropoff_address',TRUE);
                        // $get_dropoff_instructions = get_post_meta($post->ID,'chauffeur_payment_dropoff_instructions',TRUE);

                        $get_return_journey = get_post_meta($post->ID,'chauffeur_payment_return_journey',TRUE);

                        if($get_return_journey == 'Return'){
                            $get_return_address = get_post_meta($post->ID,'chauffeur_payment_return_address',TRUE);
                            $get_return_via = get_post_meta($post->ID,'chauffeur_payment_return_via',TRUE);
                            $get_return_dropoff = get_post_meta($post->ID,'chauffeur_payment_return_dropoff',TRUE);
                            $get_return_date = get_post_meta($post->ID,'chauffeur_payment_return_date',TRUE);
                            $get_return_time = get_post_meta($post->ID,'chauffeur_payment_return_time',TRUE);
                            $get_return_trip_distance = get_post_meta($post->ID,'chauffeur_payment_return_trip_distance',TRUE);
                            $get_return_trip_time = get_post_meta($post->ID,'chauffeur_payment_return_trip_time',TRUE);
                            $get_return_flight_number = get_post_meta($post->ID,'chauffeur_payment_return_flight_number',TRUE);
                            $get_return_journey_origin = get_post_meta($post->ID,'chauffeur_payment_return_journey_origin',TRUE);
                            $get_return_journey_greet = get_post_meta($post->ID,'chauffeur_payment_return_journey_greet',TRUE);
                        }
            ?>

                    <!-- BEGIN .full-booking-wrapper -->
                    <div class="full-booking-wrapper full-booking-wrapper-3 clearfix">

                        <h4><?php esc_html_e('Booking Details','chauffeur'); ?></h4>
                        
                        <div class="full-booking-wrapper-td-box">
                            <p class="booking-single-page-vehicle-name"><strong><?php esc_html_e('Vehicle:','chauffeur'); ?></strong> <span><?php echo $get_vehicle_name; ?></span></p>

                            <div class="full-booking-wrapper-td">
                                <div class="full-booking-wrapper-td-1">
                                    <p class="clearfix"><strong><?php esc_html_e('Booking ID:','chauffeur'); ?></strong> <span><?php echo "#".$post->ID; ?></span></p>                                    
                                    <p class="clearfix"><strong><?php esc_html_e('From:','chauffeur'); ?></strong> <span><?php echo $get_pickup_address; ?></span></p>
                                    <?php
                                        if(!empty($get_pickup_via)){
                                            echo '<p class="clearfix"><strong>Via:</strong> <span>'.implode(', ', $get_pickup_via).'</span></p>';
                                        }
                                    ?>
                                    <p class="clearfix"><strong><?php esc_html_e('To:','chauffeur'); ?></strong> <span><?php echo $get_dropoff_address; ?></span></p>
                                    <p class="clearfix"><strong><?php esc_html_e('Date:','chauffeur'); ?></strong> <span><?php echo $get_pickup_date; ?></span></p>
                                    <p class="clearfix"><strong><?php esc_html_e('Pick Up Time:','chauffeur'); ?></strong> <span><?php echo $get_pickup_time; ?></span></p>
                                    <p class="clearfix"><strong><?php esc_html_e('Return:','chauffeur'); ?></strong> <span><?php echo $get_return_journey ?></span></p>
                                </div>

                                <div class="full-booking-wrapper-td-2">
                                    <p class="clearfix"><strong><?php esc_html_e('Distance','chauffeur'); ?>:</strong> <span><?php echo $get_trip_distance; ?> (<?php echo $get_trip_time; ?>)</span></p>
                                    <p class="clearfix"><strong><?php esc_html_e('Flight Number','chauffeur'); ?>:</strong> <span><?php echo $get_flight_number; ?></span></p>
                                    <p class="clearfix"><strong><?php esc_html_e('Origin','chauffeur'); ?>:</strong> <span><?php echo $get_first_journey_origin; ?></span></p>
                                    <p class="clearfix"><strong><?php esc_html_e('Meet & Greet Service','chauffeur'); ?>:</strong> <span><?php echo $get_first_journey_greet; ?></span></p>
                                </div>
                            </div>
                        </div>

                        <?php if ( $get_return_journey == 'Return' ) { ?>

                            <h4 class="bt5"><?php esc_html_e('Retrun Trip Details','chauffeur'); ?></h4>
                            
                            <div class="full-booking-wrapper-td-box">
                                <div class="full-booking-wrapper-td-1">
                                    <div class="qns-one-half">
                                        <p class="clearfix"><strong><?php esc_html_e('Pickup','chauffeur') ?></strong><span><?php echo $get_return_address; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Via','chauffeur') ?>:</strong> <span><?php echo $get_return_via; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Dropoff','chauffeur') ?>:</strong> <span><?php echo $get_return_dropoff; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Date','chauffeur') ?>:</strong> <span><?php echo $get_return_date; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Time','chauffeur') ?>':</strong> <span><?php echo $get_return_time; ?></span></p>
                                    </div>

                                    <div class="full-booking-wrapper-td-2">
                                        <p class="clearfix"><strong><?php esc_html_e('Distance','chauffeur') ?>:</strong> <span><?php echo $get_return_trip_distance; ?> ( <?php echo $get_return_trip_time; ?>b)</span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Flight Number','chauffeur') ?>:</strong> <span><?php echo $get_return_flight_number; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Origin','chauffeur') ?>:</strong> <span><?php echo $get_return_journey_origin; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Meet & Greet Service','chauffeur') ?>:</strong> <span><?php echo $get_return_journey_greet; ?></span></p>
                                    </div>
                                </div>
                            </div>

                            <hr class="space2" />';

                        <?php } ?>

                        <h4 class="bt5"><?php esc_html_e('Passengers Details','chauffeur'); ?></h4>

                        <!-- BEGIN .clearfix -->
                        <div class="full-booking-wrapper-td-box">
                            <div class="full-booking-wrapper-td">
                                    <div class="full-booking-wrapper-td-1">
                                        <p class="clearfix"><strong><?php esc_html_e('Name:','chauffeur'); ?></strong> <span><?php echo $get_first_name . ' ' . $get_last_name; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Email:','chauffeur'); ?></strong> <span><?php echo $get_payment_email; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Phone:','chauffeur'); ?></strong> <span><?php echo $get_phone_num; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Passengers:','chauffeur'); ?></strong> <span><?php echo $get_num_passengers; ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Bags:','chauffeur'); ?></strong> <span><?php echo $get_num_bags; ?></span></p>
                                    </div>
                                    
                                    <div class="full-booking-wrapper-td-1">
                                        <p class="clearfix"><strong><?php esc_html_e('Payment Status:','chauffeur'); ?></strong> <span><?php echo get_post_meta($post->ID,'chauffeur_payment_status',TRUE); ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Payment Mode:','chauffeur'); ?></strong> <span><?php echo get_post_meta($post->ID,'chauffeur_payment_method',TRUE); ?></span></p>
                                        <p class="clearfix"><strong><?php esc_html_e('Total Amount:','chauffeur'); ?></strong> <span><?php echo $chauffeur_data['currency-symbol'].get_post_meta($post->ID,'chauffeur_payment_amount',TRUE); ?></span></p>
                                        <p style="display: block; border: none !important;"><?php esc_html_e('Additional Information:','chauffeur'); ?></p>
                                        <p style="display: block;"><?php echo $get_additional_details; ?></p>
                                    </div>
                            </div>
                        </div>
                    </div>


                <?php }
                else { 
                    echo "You don't have sufficient permissions to view this page";
                }		
			    endwhile;
			endif;
                      
        } ?>
        
			
	<!-- END .main-content -->
	</div>

<!-- BEGIN .content-wrapper-outer -->


<?php get_footer(); ?>

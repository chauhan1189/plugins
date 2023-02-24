<?php
/* Suppliers Page | Shortcode: [suppliers-dashboard] */


function suppliers_page_shortcode( $atts, $content = null ) {
    global $wp;
    $output = '';

    if ( isset($_GET['errors']) ) {
        $output .= '<div class="atb-form-error">'.$_GET['errors'].'</div>';
    } else if ( isset($_GET['success']) ) {
        $output .= '<div class="atb-form-success">'.$_GET['success'].'</div>';
    }
    $page_id = get_the_ID();
    if ( is_user_logged_in() ) {
        $current_user = wp_get_current_user();
        $user_login = $current_user->user_login;
        $user_login_id = $current_user->ID;

        $verification_status = get_user_meta($user_login_id, 'verification_status', TRUE);
        if($verification_status == 'verified'){

        if(isset($_GET['request_id'])){
            $request_id = $_GET['request_id'];
            $request_id_RT = '-R';
            if(str_contains($request_id, $request_id_RT)){
                $ret_j = 1;
            }else{
                $ret_j = 0;
            }
            if(str_contains($request_id, $request_id_RT) !== false){
                $request_id = str_replace($request_id_RT, '', $request_id);
                if ( 'publish' == get_post_status ( $request_id ) && get_post_type( $request_id ) == 'payment' && get_post_meta( $request_id, 'chauffeur_payment_status', true ) == 'Paid' && get_post_meta( $request_id, 'atb-booking-status', true ) == 'processing' && get_post_meta( $request_id, 'sp_show', true ) == 'yes'  && !empty(get_post_meta( $request_id, 'guide_amount', true ))) {
                    $return_via1 = get_post_meta($request_id, 'chauffeur_payment_return_pickup_via', TRUE);
                    $return_via = explode(PHP_EOL,$return_via1);
                    $output .= '
                        <div class="request-response-box-wrap">
                            <div class="request-response-box-breadcrumb">
                                <div>
                                    <span><a href="'.get_permalink($page_id).'">Quotation Requests</a></span> &#xbb; <span class="rrb-off-white">ATB-'.$request_id.'</span>
                                </div>
                                <div class="rrb-copy-btn-wrap">
                                    <button>Copy Details <img src="'. ATT_URL .'/assets/images/copy.svg" alt=""></button>
                                </div>
                            </div>
                            <div class="request-response-box-inside">
                                <div class="request-response-box-content">
                                    <div class="rrb-route-box">
                                        <p class="rrb-head">Route</p>
                                        <ul class="rrb-route-timeline">
                                            <li>
                                                <div>'.get_post_meta($request_id, 'chauffeur_payment_return_address', TRUE);
                                                if(get_post_meta($request_id, 'chauffeur_payment_return_journey_greet', TRUE) == 'Yes'){
                                                    $output .='<span class="meet-greet"><img src="'. ATT_URL .'/assets/images/user-plus.svg" alt=""> Meet & Greet required</span>';
                                                } 
                                                $output .='</div>
                                            </li>';
                                            if($return_via){
                                                foreach($return_via as $key => $val) {
                                                    $n = $key + 1;
                                                    $output .='<li class="waypoint"><div>'.$val.' <span>waypoint '.$n.'</span></div></li>';
                                                }
                                            }
                                            $output .='
                                            <li>
                                                <div>'.get_post_meta($request_id, 'chauffeur_payment_return_dropoff', TRUE).'</div>
                                            </li>
                                        </ul>
                                        <p>This route is roughly <b>'.get_post_meta($request_id, 'chauffeur_payment_return_trip_distance', TRUE).'</b></p>
                                    </div>
                                    <div class="rrb-action-box">
                                        <p class="rrb-head">Details</p>
                                        <ul>
                                            <li>
                                                <p class="sp-booking-time"> <img src="'. ATT_URL .'/assets/images/calendar.svg" alt=""> Pick up at: <b>'.get_post_meta($request_id, 'chauffeur_payment_return_date', TRUE).'</b> — <b>'.get_post_meta($request_id, 'chauffeur_payment_return_time', TRUE).'</b></p>
                                            </li>
                                            <li>
                                                <p class="sp-booking-location"> <img src="'. ATT_URL .'/assets/images/passengers.svg" alt=""> <b>'.get_post_meta($request_id, 'chauffeur_payment_num_passengers', TRUE).' Passenger(s)</b> with <b>';
                                                if(!empty(get_post_meta($request_id, 'chauffeur_payment_num_bags', TRUE))){
                                                    $output .= get_post_meta($request_id, 'chauffeur_payment_num_bags', TRUE);
                                                }else{
                                                    $output .=  0;
                                                }
                                            $output .=' bags</b></p>
                                                </li>
                                            <li>
                                                <p class="sp-booking-time"> <img src="'. ATT_URL .'/assets/images/disc.svg" alt=""> Vehicle: <b>'.get_post_meta($request_id, 'chauffeur_payment_item_name', TRUE).'</b> </p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="request-response-box-footer">
                                    <div class="rrb-footer-box-01">
                                        <h4>Important Notice</h4>
                                        <p>Do not do this transfer unless you receive a Job Confirmation email containing the full journey details (eg. passenger name, contact number, etc) and confirmation of your assignment.</p>
                                    </div>
                                    <div class="rrb-footer-box-02">
                                        <h4>Submit Price</h4>
                                        <p>Please submit your price into the box below (eg. £123.45). Please include any applicable VAT, tolls, parking charges, waiting time or similar additional fees. Please quote for the fastest route. </p>
                                        <p>As a guide, we are expecting prices of <b>£'.get_post_meta( $request_id, 'guide_amount_2', true ).'</b> or lower.</p>
                                    </div>
                                    <div class="rrb-footer-box-03">
                                        <form action="'.site_url().'/wp-json/suppliers/v1/submit" method="POST">
                                            <h4>Your Price</h4>
                                            <div class="price-input-box">
                                                <span class="rrb-pound-prefix">£</span>
                                                <input type="number" step="0.01" name="proposed_price" max="'.get_post_meta( $request_id, 'guide_amount_2', true ).'" autocomplete="off" id="proposed_price" placeholder="0.00" required="">
                                                <input type="hidden" name="booking_id" value="'.$request_id.'" required>
                                                <input type="hidden" name="user_login" value="'.$user_login.'" required>
                                                <input type="hidden" name="user_login_id" value="'.$user_login_id.'" required>
                                                <input type="hidden" name="return_journey" value="'.$ret_j.'" required>
                                                <input type="hidden" name="redirect" value="'.home_url(add_query_arg(array(), $wp->request)).'" required>
                                            </div>
                                            <div class="rrb-btn-group">
                                                <button type="submit" name="submit-supplier-data" class="rrb-accept-btn">Submit <img src="'. ATT_URL .'/assets/images/check-circle.svg" alt=""></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    ';
                }else{
                    $output .= '<div class="atb-form-error"> <i>Sorry, booking ID not valid.</i> <a style="color: #000; text-decoration: underline;" href="javascript:history.back();">Go Back</a></div>';
                }
            }else{
                if ( 'publish' == get_post_status ( $request_id ) && get_post_type( $request_id ) == 'payment' && get_post_meta( $request_id, 'chauffeur_payment_status', true ) == 'Paid' && get_post_meta( $request_id, 'atb-booking-status', true ) == 'processing' && get_post_meta( $request_id, 'sp_show', true ) == 'yes'  && !empty(get_post_meta( $request_id, 'guide_amount', true ))) {
                    $pickup_via1 = get_post_meta($request_id, 'chauffeur_payment_pickup_via', TRUE);
                    $pickup_via = explode(PHP_EOL,$pickup_via1);
                    $output .= '
                        <div class="request-response-box-wrap">
                            <div class="request-response-box-breadcrumb">
                                <div>
                                    <span><a href="'.get_permalink($page_id).'">Quotation Requests</a></span> &#xbb; <span class="rrb-off-white">ATB-'.$request_id.'</span>
                                </div>
                                <div class="rrb-copy-btn-wrap">
                                    <button>Copy Details <img src="'. ATT_URL .'/assets/images/copy.svg" alt=""></button>
                                </div>
                            </div>
                            <div class="request-response-box-inside">
                                <div class="request-response-box-content">
                                    <div class="rrb-route-box">
                                        <p class="rrb-head">Route</p>
                                        <ul class="rrb-route-timeline">
                                            <li>
                                                <div>'.get_post_meta($request_id, 'chauffeur_payment_pickup_address', TRUE);
                                                if(get_post_meta($request_id, 'chauffeur_payment_first_journey_greet', TRUE) == 'Yes'){
                                                    $output .='<span class="meet-greet"><img src="'. ATT_URL .'/assets/images/user-plus.svg" alt=""> Meet & Greet required</span>';
                                                } 
                                                $output .='</div>
                                            </li>';
                                            if($pickup_via){
                                                foreach($pickup_via as $key => $val) {
                                                    $n = $key + 1;
                                                    $output .='<li class="waypoint"><div>'.$val.' <span>waypoint '.$n.'</span></div></li>';
                                                }
                                            }
                                            $output .='
                                            <li>
                                                <div>'.get_post_meta($request_id, 'chauffeur_payment_dropoff_address', TRUE).'</div>
                                            </li>
                                        </ul>
                                        <p>This route is roughly <b>'.get_post_meta($request_id, 'chauffeur_payment_trip_distance', TRUE).'</b></p>
                                    </div>
                                    <div class="rrb-action-box">
                                        <p class="rrb-head">Details</p>
                                        <ul>
                                            <li>
                                                <p class="sp-booking-time"> <img src="'. ATT_URL .'/assets/images/calendar.svg" alt=""> Pick up at: <b>'.get_post_meta($request_id, 'chauffeur_payment_pickup_date', TRUE).'</b> — <b>'.get_post_meta($request_id, 'chauffeur_payment_pickup_time', TRUE).'</b></p>
                                            </li>
                                            <li>
                                                <p class="sp-booking-location"> <img src="'. ATT_URL .'/assets/images/passengers.svg" alt=""> <b>'.get_post_meta($request_id, 'chauffeur_payment_num_passengers', TRUE).' Passenger(s)</b> with <b>';
                                                if(!empty(get_post_meta($request_id, 'chauffeur_payment_num_bags', TRUE))){
                                                    $output .= get_post_meta($request_id, 'chauffeur_payment_num_bags', TRUE);
                                                }else{
                                                    $output .=  0;
                                                }
                                            $output .=' bags</b></p>
                                                </li>
                                            <li>
                                                <p class="sp-booking-time"> <img src="'. ATT_URL .'/assets/images/disc.svg" alt=""> Vehicle: <b>'.get_post_meta($request_id, 'chauffeur_payment_item_name', TRUE).'</b> </p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="request-response-box-footer">
                                    <div class="rrb-footer-box-01">
                                        <h4>Important Notice</h4>
                                        <p>Do not do this transfer unless you receive a Job Confirmation email containing the full journey details (eg. passenger name, contact number, etc) and confirmation of your assignment.</p>
                                    </div>
                                    <div class="rrb-footer-box-02">
                                        <h4>Submit Price</h4>
                                        <p>Please submit your price into the box below (eg. £123.45). Please include any applicable VAT, tolls, parking charges, waiting time or similar additional fees. Please quote for the fastest route. </p>
                                        <p>As a guide, we are expecting prices of <b>£'.get_post_meta( $request_id, 'guide_amount', true ).'</b> or lower.</p>
                                    </div>
                                    <div class="rrb-footer-box-03">
                                        <form action="'.site_url().'/wp-json/suppliers/v1/submit" method="POST">
                                            <h4>Your Price</h4>
                                            <div class="price-input-box">
                                                <span class="rrb-pound-prefix">£</span>
                                                <input type="number" step="0.01" name="proposed_price" max="'.get_post_meta( $request_id, 'guide_amount', true ).'" autocomplete="off" id="proposed_price" placeholder="0.00" required="">
                                                <input type="hidden" name="booking_id" value="'.$request_id.'" required>
                                                <input type="hidden" name="user_login" value="'.$user_login.'" required>
                                                <input type="hidden" name="user_login_id" value="'.$user_login_id.'" required>
                                                <input type="hidden" name="return_journey" value="0" required>
                                                <input type="hidden" name="redirect" value="'.home_url(add_query_arg(array(), $wp->request)).'" required>
                                            </div>
                                            <div class="rrb-btn-group">
                                                <button type="submit" name="submit-supplier-data" class="rrb-accept-btn">Submit <img src="'. ATT_URL .'/assets/images/check-circle.svg" alt=""></button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                    ';
                }else{
                    $output .= '<div class="atb-form-error"> <i>Sorry, booking ID not valid.</i> <a style="color: #000; text-decoration: underline;" href="javascript:history.back();">Go Back</a></div>';
                }
            }
        }
        
        $author_query = array(
            'post_type' => 'suppliers_portal',
            'post_status' => 'publish',
            'posts_per_page' => '-1',
            'author' => $current_user->ID
        );
        $author_posts = new WP_Query($author_query);

        $booking_arg = array(
            'post_type' => 'payment',
            'post_status' => 'publish',
            'posts_per_page' => '10',
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'chauffeur_payment_status',
					'value' => 'Paid',
					'compare' => '=='
				),		
				array(
					'key' => 'atb-booking-status',
					'value' => 'processing',
					'compare' => '=='
				)
			)
        );
        $bookings = new WP_Query($booking_arg);

        $output .= '<div class="atb-suppliers-dashboard-container">
        <div class="atb-suppliers-dashboard">
            <div class="atb-suppliers-data-table">
                <div class="atb-heading-container">
                    <h2>Invoice History</h2>
                </div>
                <div class="main-table-atb">
                <table class="suppliers_table" width="100%">';

                if($author_posts->have_posts()) :
                    $output .= '<tr>
                        <th>Date</th>
                        <th>Invoice Number</th>
                        <th>Journey Reference</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>';
                    while($author_posts->have_posts()) : $author_posts->the_post();
                    $post_id = get_the_id();
                    $booking_id = get_post_meta($post_id, 'atb_reference_number', TRUE);
                    $output .= '
                    <tr>
                        <td>'.get_the_date( 'F j, Y' ).'</td>
                        <td>'.get_post_meta($post_id, 'atb_invoice_number', TRUE).'</td><td style="font-weight: bold;">';

                        if(get_post_meta($post_id, 'atb_return_journey', true) == 1){
                            $output .= 'Pickup Date & Time: '.get_post_meta($booking_id,'chauffeur_payment_return_date',TRUE).' '.get_post_meta($booking_id,'chauffeur_payment_return_time',TRUE).'<br>';
                            $output .= 'From: '.get_post_meta($booking_id,'chauffeur_payment_return_address',TRUE).'<br>';
                            $output .= 'To: '.get_post_meta($booking_id,'chauffeur_payment_return_dropoff',TRUE).'<br>';
                            if(!empty(get_post_meta($booking_id,'chauffeur_payment_return_pickup_via',TRUE))){
                                $output .= 'Via: '.implode(', ', get_post_meta($booking_id,'chauffeur_payment_return_pickup_via',TRUE));
                            }
                            $output .= 'Flight number: '.get_post_meta($booking_id,'chauffeur_payment_return_flight_number',TRUE).'<br>';
                            $output .= 'Origin: '.get_post_meta($booking_id,'chauffeur_payment_return_journey_origin',TRUE).'<br>';
                            $output .= 'Meet & Greet: '.get_post_meta($booking_id,'chauffeur_payment_return_journey_greet',TRUE).'<br>';
                        }else{
                            $output .= 'Pickup Date & Time: '.get_post_meta($booking_id,'chauffeur_payment_pickup_date',TRUE).' '.get_post_meta($booking_id,'chauffeur_payment_pickup_time',TRUE).'<br>';
                            $output .= 'From: '.get_post_meta($booking_id,'chauffeur_payment_pickup_address',TRUE).'<br>';
                            $output .= 'To: '.get_post_meta($booking_id,'chauffeur_payment_dropoff_address',TRUE).'<br>';
                            if(!empty(get_post_meta($booking_id,'chauffeur_payment_pickup_via',TRUE))){
                                $output .= 'Via: '.implode(', ', get_post_meta($booking_id,'chauffeur_payment_pickup_via',TRUE));
                            }
                            $output .= 'Flight number: '.get_post_meta($booking_id,'chauffeur_payment_flight_number',TRUE).'<br>';
                            $output .= 'Origin: '.get_post_meta($booking_id,'chauffeur_payment_first_journey_origin',TRUE).'<br>';
                            $output .= 'Meet & Greet: '.get_post_meta($booking_id,'chauffeur_payment_first_journey_greet',TRUE).'<br>';
                        }

                        $output .= '</td>
                        <td>£'.get_post_meta($post_id, 'atb_proposed_price', TRUE).'</td>
                        <td>';
                        if(get_post_meta($post_id, 'atb_status', TRUE) == 'approved'){
                            $output .= '<p class="blinkText" style="text-transform: uppercase;margin-bottom: 0;display: inline-block;background: #f2ff0a;color: #000;padding: 4px 18px;border-radius: 15px;margin-top: 0;font-weight: bold;">upcoming</p>';
                        }else if(get_post_meta($post_id, 'atb_status', TRUE) == 'pending'){
                            $output .= '<p style="text-transform: uppercase;margin-bottom: 0;display: inline-block;background: #000000;color: #fff;padding: 4px 18px;border-radius: 15px;margin-top: 0;">'.get_post_meta($post_id, 'atb_status', TRUE).'</p>';
                        }else if(get_post_meta($post_id, 'atb_status', TRUE) == 'completed'){
                            $output .= '<p style="text-transform: uppercase;margin-bottom: 0;display: inline-block;background: #0ba84f;color: #fff;padding: 4px 18px;border-radius: 15px;margin-top: 0;">'.get_post_meta($post_id, 'atb_status', TRUE).'</p>';
                        }else {
                            $output .= '<p style="text-transform: uppercase;margin-bottom: 0;display: inline-block;background: #a80b0b;color: #fff;padding: 4px 18px;border-radius: 15px;margin-top: 0;">'.get_post_meta($post_id, 'atb_status', TRUE).'</p>';
                        }
                        $output .= '</td>
                    </tr>';
                endwhile; else :
                    $output .= '<p style="color: #fff;">No Data Found</p>';
                endif;
                $output .= '</table>
                </div>
            </div>
        </div>';

        $output .= '
        <div class="atb-sp-booking-list">
        <h2 style="text-align: center;">Quotation Requests</h2>';
        
        $sp_args = array(
            'post_type' => 'suppliers_portal',
            'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_query' => array(
				array(
					'key' => 'atb_user',
					'value' => $user_login,
					'compare' => '=='
				)
			)
        );
        $sp_posts = get_posts($sp_args);

        $sp_id_array = array();
        foreach($sp_posts as $val){
            $post_id = $val->ID;
            $sp_id_array[] = get_post_meta($post_id, 'atb_reference_number', TRUE);
        }
        
        if($bookings->have_posts()) :
            while($bookings->have_posts()) : $bookings->the_post();
            $post_id = get_the_id();
            if(!empty(get_post_meta($post_id, 'chauffeur_payment_pickup_via', TRUE))) {
                $p_via1 = get_post_meta($post_id, 'chauffeur_payment_pickup_via', TRUE);
                $p_via = explode(PHP_EOL,$p_via1);
                $pickup_via = ' via <b>'.implode(" ",$p_via).'</b> ';
            }else{
                $pickup_via = '';
            }
            if(!empty(get_post_meta($post_id, 'chauffeur_payment_return_pickup_via', TRUE))) {
                $r_via1 = get_post_meta($post_id, 'chauffeur_payment_return_pickup_via', TRUE);
                $r_via = explode(PHP_EOL,$r_via1);
                $return_via = ' via <b>'.implode(" ",$r_via).'</b> ';
            }else{
                $return_via = '';
            }
            $pickup_date = get_post_meta($post_id, 'chauffeur_payment_pickup_date', TRUE);
            $pickup_time = get_post_meta($post_id, 'chauffeur_payment_pickup_time', TRUE);
            
            $pickup_datetime = $pickup_date.''.$pickup_time;
            $today = date('d/m/Y H:i');

            $dateLatest = DateTime::createFromFormat('d/m/Y H:i', $pickup_datetime);
            $dateOld = DateTime::createFromFormat('d/m/Y H:i', $today );
            global $chauffeur_data;
            
            if($dateLatest > $dateOld){
                $interval = $dateLatest->diff($dateOld);
                $hours    = ($interval->days * 24) + $interval->h
                          + ($interval->i / 60) + ($interval->s / 3600);
                $dateLatestNew = $dateLatest->format('Y-m-d H:i');
                $expire_date = date('d/m/Y H:i', strtotime($dateLatestNew .' -3 day'));
                if((int)$hours > (int)$chauffeur_data['sp-hours-before-booking-minimum'] /60 ){
                    $output .= '
                        <div class="atb-sp-bookings">
                            <ul>
                                <li>
                                    <p class="sp-booking-number">ATB-'.$post_id.'</p>
                                </li>
                                <li>
                                    <p class="sp-booking-time"> <img src="'. ATT_URL .'/assets/images/calendar.svg" alt=""> Pick up at: <b>'.get_post_meta($post_id, 'chauffeur_payment_pickup_date', TRUE).'</b> — <b>'.get_post_meta($post_id, 'chauffeur_payment_pickup_time', TRUE).'</b> </p>
                                </li>
                                <li>
                                    <p class="sp-booking-location"> <img src="'. ATT_URL .'/assets/images/map-pin.svg" alt=""> From: <b>'.get_post_meta($post_id, 'chauffeur_payment_pickup_address', TRUE).'</b> to <b>'.get_post_meta($post_id, 'chauffeur_payment_dropoff_address', TRUE).$pickup_via.'</b> by <b>'.get_post_meta($post_id, 'chauffeur_payment_item_name', TRUE).'</b></p>
                                </li>
                                <li>
                                    <p class="sp-booking-expiry"> <img src="'. ATT_URL .'/assets/images/clock.svg" alt=""> Expires on <b>'.$expire_date.'</b></p>
                                </li>
                            </ul>';
                    if(in_array($post_id, $sp_id_array)){
                        $output .= ' <div class="atb-sp-bookings-button">
                            <a href="javascript:void(0)" class="atb-sp-booking-button-disabled">Already Submitted</a>
                        </div>'; 
                    }else{
                        $output .= '
                        <div class="atb-sp-bookings-button">
                            <a href="'.get_permalink($page_id).'?request_id='.$post_id.'">Submit Price</a>
                        </div>';
                    }
                    $output .= '</div>
                    ';
                    if(get_post_meta($post_id, 'chauffeur_payment_return_journey', TRUE) == 'Return'){
                        $output .= '
                            <div class="atb-sp-bookings">
                                <ul>
                                    <li>
                                        <p class="sp-booking-number">ATB-'.$post_id.'-R</p>
                                    </li>
                                    <li>
                                        <p class="sp-booking-time"> <img src="'. ATT_URL .'/assets/images/calendar.svg" alt=""> Pick up at: <b>'.get_post_meta($post_id, 'chauffeur_payment_return_date', TRUE).' — '.get_post_meta($post_id, 'chauffeur_payment_return_time', TRUE).'</b> </p>
                                    </li>
                                    <li>
                                        <p class="sp-booking-location"> <img src="'. ATT_URL .'/assets/images/map-pin.svg" alt=""> From: <b>'.get_post_meta($post_id, 'chauffeur_payment_return_address', TRUE).'</b> to <b>'.get_post_meta($post_id, 'chauffeur_payment_return_dropoff', TRUE).$return_via.'</b> by <b>'.get_post_meta($post_id, 'chauffeur_payment_item_name', TRUE).'</b></p>
                                    </li>
                                    <li>
                                        <p class="sp-booking-expiry"> <img src="'. ATT_URL .'/assets/images/clock.svg" alt=""> Expires on <b>'.$expire_date.'</b></p>
                                    </li>
                                </ul>
                                <div class="atb-sp-bookings-button">
                                    <a href="'.get_permalink($page_id).'?request_id='.$post_id.'-R">Submit Price</a>
                                </div>
                            </div>
                        ';
                    }
                }
            }
        endwhile; else :
            $output .= '<p style="color: #fff;">No Data Found</p>';
        endif;
        $output .= '</div></div>';

        }else if($verification_status == 'unverified'){
            $output .= '
            <div class="atb-sp-booking-list">
                <h3 style="text-align: center;">You have successfully registered on our suppliers portal. Your uploaded document is under approval. We will inform you via email with the verification status within 24-48 hours.</h2>
            </div>';
        }else if($verification_status == 'rejected'){
            $output .= '
            <div class="atb-sp-booking-list">
                <h3 style="text-align: center;">We re unable to verify your uploaded document, please upload a different document or call us directly: 03300 109 709</h2>
                <form action="'.site_url().'/wp-json/suppliers/v1/auth" method="post" enctype="multipart/form-data">
                    <div class="atb-form-control">
                        <label for="verification_document" style="color: #000;">Upload a Operators licence (PDF/Doc/Docx/JPG/PNG)</label>
                        <input type="file" name="verification_document" id="verification_document" style="color: #fff;"  accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/msword, application/pdf, image/*" required>
                        <input type="hidden" name="redirect" value="'.home_url(add_query_arg(array(), $wp->request)).'" required>
                        <input type="hidden" name="user_id" value="'.get_current_user_id().'" required>
                    </div>
                    <div class="atb-form-button">
                        <button type="submit" name="reupload-submit">Upload</button>
                    </div>
                </form>
            </div>';
        }else{

        }
    } else {
        if(isset($_GET['register'])){
            $output .= '
            <div class="atb-suppliers-form--wrap">
                <div class="atb-heading-container">
                    <h2>Register as a Supplier</h2>
                </div>
                <form action="'.site_url().'/wp-json/suppliers/v1/auth" method="POST" id="atb-supplier-register" enctype="multipart/form-data">
                    <div class="atb-form-control-column">
                        <div class="atb-form-control">
                            <label for="firstname">First name</label>
                            <input type="text" name="firstname" id="firstname" placeholder="Enter firstname" required>
                        </div>
                        <div class="atb-form-control">
                            <label for="lastname">Last name</label>
                            <input type="text" name="lastname" id="lastname" placeholder="Enter lastname" required>
                        </div>
                    </div>
                    <div class="atb-form-control">
                        <label for="email">Email address</label>
                        <input type="email" name="email" id="email" placeholder="Enter a valid email" required>
                    </div>
                    <div class="atb-form-control">
                        <label for="phone">Phone number</label>
                        <input type="text" name="phone" id="phone" placeholder="Enter a valid phone number" required>
                    </div>
                    <div class="atb-form-control">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" placeholder="Enter a strong password" required>
                    </div>
                    <div class="atb-form-control">
                        <label for="confirm_password">Confirm password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder="Re-enter the password" required>
                    </div>
                    <div class="atb-form-control">
                        <label for="company_name">Company name</label>
                        <input type="text" name="company_name" id="company_name" placeholder="Enter company name" required>
                    </div>
                    <div class="atb-form-control">
                        <label for="company_number">Company number</label>
                        <input type="text" name="company_number" id="company_number" placeholder="Enter company number" required>
                    </div>
                    <div class="atb-form-control">
                        <label for="vat_number">VAT number</label>
                        <input type="text" name="vat_number" id="vat_number" placeholder="Enter VAT number" required>
                        <input type="hidden" name="redirect" value="'.home_url(add_query_arg(array(), $wp->request)).'" required>
                    </div>
                    <div class="atb-form-control">
                        <label for="verification_document">Operators licence (PDF/Doc/Docx/JPG/PNG)</label>
                        <input type="file" name="verification_document" id="verification_document" style="color: #fff;"  accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/msword, application/pdf, image/*" required>
                    </div>
                    <div class="atb-form-button">
                        <button type="submit" name="register-submit">Register</button>
                    </div>
                    <div class="atb-form-others">
                        <a href="'.home_url(add_query_arg(array(), $wp->request)).'">Login to your account</a>
                    </div>
                </form>
            </div>';
        }else{
            $output .= '
            <div class="atb-suppliers-form--wrap">
                <div class="atb-heading-container">
                    <h2>Login to Supplier Portal</h2>
                </div>
                <form action="'.site_url().'/wp-json/suppliers/v1/auth" method="POST" id="atb-supplier-login">
                    <div class="atb-form-control">
                        <label for="email">Email address</label>
                        <input type="email" name="email" id="email" placeholder="Enter your registered email address" required>
                    </div>
                    <div class="atb-form-control">
                        <label for="password">Password <a href="'.wp_lostpassword_url().'" class="atb_fp_link" target="_blank">forgot password?</a> </label>
                        <input type="password" name="password" id="password" placeholder="Enter your password" required>
                        <input type="hidden" name="redirect" value="'.home_url(add_query_arg(array(), $wp->request)).'" required>
                    </div>
                    <div class="atb-form-button">
                        <button type="submit" name="login-submit">Login</button>
                    </div>
                    <div class="atb-form-others">
                        <a href="'.home_url(add_query_arg(array(), $wp->request)).'?register">Register as a Supplier</a>
                    </div>
                </form>
            </div>';
        }
    }

    return $output;

}
add_shortcode( 'suppliers-dashboard', 'suppliers_page_shortcode' );
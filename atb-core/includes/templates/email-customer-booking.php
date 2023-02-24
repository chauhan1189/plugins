<?php

// Email message
$customer_email_content = '';
//$customer_email_content .= esc_attr($chauffeur_data['customer-booking-email-content']);
$customer_email_content .= '<p>Dear' . $get_first_name . ', <br>
							Thank you for choosing to book your trip with Airport Taxi Bookings. Details of your booking can be seen below.</p>';
if ( $exists ) {
	$customer_email_content .= '<p>A user account is already registered in our site with the email ' . $get_payment_email . '</br>. You can login and check your booking details here: http://www.airporttaxibooking.co.uk/manage-bookings/ </p>';
}
else {
	$customer_email_content .= '<p>A new user account has been registered for you. The login details are as follows: <br>
	username: ' . $get_payment_email . '<br>
	password: ' . $user_password . '<br>
	You can login and check your booking details here: http://www.airporttaxibooking.co.uk/manage-bookings/ </p>'; 
}
$customer_email_content .= '<ul>';

$customer_email_content .= '<li><strong>' . esc_html__('Booking ID','chauffeur') . ': </strong>' . $booking_id . '</li>';
$customer_email_content .= '<li><strong>' . esc_html__('Name','chauffeur') . ': </strong>' . $get_first_name . ' ' . $get_last_name . '</li>';
$customer_email_content .= '<li><strong>' . esc_html__('Phone Number','chauffeur') . ': </strong>' . $get_phone_num . '</li>';
$customer_email_content .= '<li><strong>' . esc_html__('Email','chauffeur') . ': </strong>' . $get_payment_email . '</li>';
$customer_email_content .= '<li><strong>' . esc_html__('Vehicle','chauffeur') . ': </strong>' . $get_vehicle_name . '</li>';
$customer_email_content .= '<li><strong>' . esc_html__('Passengers','chauffeur') . ': </strong>' . $get_num_passengers . '</li>';
$customer_email_content .= '<li><strong>' . esc_html__('Bags','chauffeur') . ': </strong>' . $get_num_bags . '</li>';
$customer_email_content .= '<li><strong>' . esc_html__('Pickup Address','chauffeur') . ': </strong>' . $get_pickup_address . '</li>';

if( !empty($get_pickup_via) ){
	$customer_email_content .= '<li><strong>' . esc_html__('Pickup Via','chauffeur') . ': </strong>' . $get_pickup_via . '</li>';
}

$customer_email_content .= '<li><strong>' . esc_html__('Dropoff Address','chauffeur') . ': </strong>' . $get_dropoff_address . '</li>';

$customer_email_content .= '<li><strong>' . esc_html__('Pickup Time','chauffeur') . ': </strong>' . $get_pickup_date . ' ' . esc_html__('at','chauffeur') . ' ' . $get_pickup_time . '</li>';

// $customer_email_content .= '<li><strong>' . esc_html__('Full Pickup Address','chauffeur') . ': </strong>' . $get_full_pickup_address . '</li>';

// $customer_email_content .= '<li><strong>' . esc_html__('Pickup Instructions','chauffeur') . ': </strong>' . $get_pickup_instructions . '</li>';

// $customer_email_content .= '<li><strong>' . esc_html__('Full Dropoff Address','chauffeur') . ': </strong>' . $get_full_dropoff_address . '</li>';

// $customer_email_content .= '<li><strong>' . esc_html__('Dropoff Instructions','chauffeur') . ': </strong>' . $get_dropoff_instructions . '</li>';

if ( $get_trip_distance != '' ) {
	$customer_email_content .= '<li><strong>' . esc_html__('Estimated Distance','chauffeur') . ': </strong>' . $get_trip_distance . ' (' . $get_trip_time . ')</li>';
}

$customer_email_content .= '<li><strong>' . esc_html__('Flight Number','chauffeur') . ': </strong>' . $get_flight_number . '</li>';

$customer_email_content .= '<li><strong>' . esc_html__('Journey Origin','chauffeur') . ': </strong>' . $get_first_journey_origin . '</li>';

$customer_email_content .= '<li><strong>' . esc_html__('Meet & Greet','chauffeur') . ': </strong>' . $get_first_journey_greet . '</li>';

if ( $get_additional_details != '' ) {
	$customer_email_content .= '<li><strong>' . esc_html__('Additional Details','chauffeur') . ': </strong>' . $get_additional_details . '</li>';
}

// if ( $get_payment_num_hours != '' ) {
// 	$customer_email_content .= '<li><strong>' . esc_html__('Hours','chauffeur') . ': </strong>' . $get_payment_num_hours . '</li>';
// }

if ( $get_return_journey != '' ) {
	$customer_email_content .= '<li><strong>' . esc_html__('Return','chauffeur') . ': </strong>' . $get_return_journey . '</li>';
}

if($get_return_journey == 'Return'){

	$customer_email_content .= '</ul><br />';

	$customer_email_content .= '<p><strong>' . esc_html__('Return Trip Details','chauffeur') . ':</strong></p>';
	$customer_email_content .= '<ul>';

	$customer_email_content .= '<li><strong>' . esc_html__('Return Address','chauffeur') . ': </strong>' . $get_return_address . '</li>';
	$customer_email_content .= '<li><strong>' . esc_html__('Return Via','chauffeur') . ': </strong>' . $get_return_pickup_via . '</li>';
	$customer_email_content .= '<li><strong>' . esc_html__('Return Dropoff','chauffeur') . ': </strong>' . $get_return_dropoff . '</li>';
	$customer_email_content .= '<li><strong>' . esc_html__('Return Date & Time','chauffeur') . ': </strong>' . $get_return_date . ' ' . esc_html__('at','chauffeur') . ' ' . $get_return_time . '</li>';

	if ( $get_return_trip_distance != '' ) {
		$customer_email_content .= '<li><strong>' . esc_html__('Estimated Return Distance','chauffeur') . ': </strong>' . $get_return_trip_distance . ' (' . $get_return_trip_time . ')</li>';
	}
	$customer_email_content .= '<li><strong>' . esc_html__('Return Flight Number','chauffeur') . ': </strong>' . $get_return_flight_number . '</li>';

	$customer_email_content .= '<li><strong>' . esc_html__('Return Journey Origin','chauffeur') . ': </strong>' . $get_return_journey_origin . '</li>';

	$customer_email_content .= '<li><strong>' . esc_html__('Meet & Greet','chauffeur') . ': </strong>' . $get_return_journey_greet . '</li>';

}

$customer_email_content .= '</ul><br />';

$customer_email_content .= '<p><strong>' . esc_html__('Payment Details','chauffeur') . ':</strong></p>';
$customer_email_content .= '<ul>';

if( $chauffeur_data['hide-pricing'] != '1' ) {
	$customer_email_content .= '<li><strong>' . esc_html__('Amount','chauffeur') . ': </strong>' . chauffeur_get_price($amount) . '</li>';
}

$customer_email_content .= '</ul>';
$customer_email_content .= 'Thanks,</br>
							Airport Taxi Booking';

// Email Subject
$customer_email_subject = esc_attr($chauffeur_data['customer-booking-email-subject']);

// Email Headers
$customer_headers = "MIME-Version: 1.0\r\n";
$customer_headers .= "Content-type: text/html; charset=UTF-8\r\n";
$customer_headers .= "From: " . esc_attr($chauffeur_data['email-sender-name']) . " <" . esc_attr($chauffeur_data['booking-email']) . ">" . "\r\n" . "Reply-To: " . esc_attr($chauffeur_data['booking-email']);

?>
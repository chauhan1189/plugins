<?php
// Email message
$admin_email_content = '';
$admin_email_content .= esc_attr($chauffeur_data['admin-booking-email-content']);
$admin_email_content .= '<br>';
$admin_email_content .= '<strong>' . esc_html__('Vehicle','chauffeur') . ': </strong>' . $get_vehicle_name;

$admin_email_content .= '<br /><ul>';
$admin_email_content .= '<li><strong>' . esc_html__('Name','chauffeur') . ': </strong>' . $get_first_name . ' ' . $get_last_name . '</li>';
$admin_email_content .= '<li><strong>' . esc_html__('Phone Number','chauffeur') . ': </strong>' . $get_phone_num . '</li>';
$admin_email_content .= '<li><strong>' . esc_html__('Email','chauffeur') . ': </strong>' . $get_payment_email . '</li>';
$admin_email_content .= '<li><strong>' . esc_html__('Passengers','chauffeur') . ': </strong>' . $get_num_passengers . '</li>';
$admin_email_content .= '<li><strong>' . esc_html__('Bags','chauffeur') . ': </strong>' . $get_num_bags . '</li>';
$admin_email_content .= '<li><strong>' . esc_html__('Pickup Address','chauffeur') . ': </strong>' . $get_pickup_address . '</li>';
if( !empty($get_pickup_via) ){
	$admin_email_content .= '<li><strong>' . esc_html__('Pickup Via','chauffeur') . ': </strong>' . $get_pickup_via . '</li>';
}
$admin_email_content .= '<li><strong>' . esc_html__('Dropoff Address','chauffeur') . ': </strong>' . $get_dropoff_address . '</li>';
$admin_email_content .= '<li><strong>' . esc_html__('Pickup Time','chauffeur') . ': </strong>' . $get_pickup_date . ' ' . esc_html__('at','chauffeur') . ' ' . $get_pickup_time . '</li>';

// $admin_email_content .= '<li><strong>' . esc_html__('Full Pickup Address','chauffeur') . ': </strong>' . $get_full_pickup_address . '</li>';

// $admin_email_content .= '<li><strong>' . esc_html__('Pickup Instructions','chauffeur') . ': </strong>' . $get_pickup_instructions . '</li>';

// $admin_email_content .= '<li><strong>' . esc_html__('Full Dropoff Address','chauffeur') . ': </strong>' . $get_full_dropoff_address . '</li>';

// $admin_email_content .= '<li><strong>' . esc_html__('Dropoff Instructions','chauffeur') . ': </strong>' . $get_dropoff_instructions . '</li>';

if ( $get_trip_distance != '' ) {
	$admin_email_content .= '<li><strong>' . esc_html__('Estimated Distance','chauffeur') . ': </strong>' . $get_trip_distance . ' (' . $get_trip_time . ')</li>';
}

$admin_email_content .= '<li><strong>' . esc_html__('Flight Number','chauffeur') . ': </strong>' . $get_flight_number . '</li>';

$admin_email_content .= '<li><strong>' . esc_html__('Journey Origin','chauffeur') . ': </strong>' . $get_first_journey_origin . '</li>';

$admin_email_content .= '<li><strong>' . esc_html__('Meet & Greet','chauffeur') . ': </strong>' . $get_first_journey_greet . '</li>';

if ( $get_additional_details != '' ) {
	$admin_email_content .= '<li><strong>' . esc_html__('Additional Details','chauffeur') . ': </strong>' . $get_additional_details . '</li>';
}

// if ( $get_payment_num_hours != '' ) {
// 	$admin_email_content .= '<li><strong>' . esc_html__('Hours','chauffeur') . ': </strong>' . $get_payment_num_hours . '</li>';
// }

if ( $get_return_journey != '' ) {
	$admin_email_content .= '<li><strong>' . esc_html__('Return','chauffeur') . ': </strong>' . $get_return_journey . '</li>';
}

if($get_return_journey == 'Return'){

	$admin_email_content .= '</ul><br />';

	$admin_email_content .= '<p><strong>' . esc_html__('Return Trip Details','chauffeur') . ':</strong></p>';
	$admin_email_content .= '<ul>';

	$admin_email_content .= '<li><strong>' . esc_html__('Return Address','chauffeur') . ': </strong>' . $get_return_address . '</li>';
	$admin_email_content .= '<li><strong>' . esc_html__('Return Via','chauffeur') . ': </strong>' . $get_return_pickup_via . '</li>';
	$admin_email_content .= '<li><strong>' . esc_html__('Return Dropoff','chauffeur') . ': </strong>' . $get_return_dropoff . '</li>';
	$admin_email_content .= '<li><strong>' . esc_html__('Return Date & Time','chauffeur') . ': </strong>' . $get_return_date . ' ' . esc_html__('at','chauffeur') . ' ' . $get_return_time . '</li>';

	if ( $get_return_trip_distance != '' ) {
		$admin_email_content .= '<li><strong>' . esc_html__('Estimated Return Distance','chauffeur') . ': </strong>' . $get_return_trip_distance . ' (' . $get_return_trip_time . ')</li>';
	}
	$admin_email_content .= '<li><strong>' . esc_html__('Return Flight Number','chauffeur') . ': </strong>' . $get_return_flight_number . '</li>';

	$admin_email_content .= '<li><strong>' . esc_html__('Return Journey Origin','chauffeur') . ': </strong>' . $get_return_journey_origin . '</li>';

	$admin_email_content .= '<li><strong>' . esc_html__('Meet & Greet','chauffeur') . ': </strong>' . $get_return_journey_greet . '</li>';

}

$admin_email_content .= '</ul>';

$admin_email_content .= '<p>' . esc_html__('Payment Details','chauffeur') . ':</p>';
$admin_email_content .= '<ul>';

if( $chauffeur_data['hide-pricing'] != '1' ) {
	$admin_email_content .= '<li><strong>' . esc_html__('Amount','chauffeur') . ': </strong>' . chauffeur_get_price($amount) . '</li>';
}

$admin_email_content .= '</ul>';

// Email Subject
$admin_email_subject = esc_attr($chauffeur_data['admin-booking-email-subject']);

// Email Headers
$admin_headers = "MIME-Version: 1.0\r\n";
$admin_headers .= "Content-type: text/html; charset=UTF-8\r\n";
$admin_headers .= "From: " . esc_attr($chauffeur_data['email-sender-name']) . " <" . esc_attr($chauffeur_data['booking-email']) . ">" . "\r\n" . "Reply-To: " . esc_attr($payer_email);
?>
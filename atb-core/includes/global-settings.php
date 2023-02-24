<?php

$att_settings_options = get_option( 'settings-page-atb', array() );
$booking_threshold_time = trim($att_settings_options['booking_threshold_time']);
$sp_booking_threshold_time = trim($att_settings_options['sp_booking_threshold_time']);

$att_settings_array = array(
  'google-map-api-key'  => $att_settings_options['google_maps_api_key'],
  'google-map-api-key-2'  => $att_settings_options['google_maps_api_key_2'],
  'enable-stripe'  => 1,
  'stripe_secret_key'  => $att_settings_options['stripe_secret_key'],
  'stripe_publishable_key'  => $att_settings_options['stripe_publishable_key'],
  'stripe-currency'  => $att_settings_options['stripe_currency'],
  'booking-email'  => $att_settings_options['bookings_email_address'],
  'hourly-minimum' => '1',
  'hourly-maximum' => '48',
  'hours-before-booking-minimum' => $booking_threshold_time,
  'sp-hours-before-booking-minimum' => $sp_booking_threshold_time,
  'max-selectable-bags' => '8',
  'max-selectable-passengers' => '8',
  'disable-distance' => '0',
  'disable-hourly' => '1',
  'disable-flat-rate' => '1',
  'time-format' => '24hr',
  'hide-pricing' => '0',
  'terms_conditions' => '',
  'google-limit-country' => 'GB',
  'enable-paypal' => '0',
  'surcharge-enable-flat-rate' => '0',
  'surcharge-enable-distance' => '0',
  'surcharge-enable-hourly' => '0',
  'enable-cash' => '0',
  'paypal-currency' => 'USD',
  'paypal-sandbox' => '',
  'paypal-address' => '',
  'currency-symbol' => '£',
  'currency-symbol-position' => 'before',
  'minimum-vehicle-price' => 'minimum-vehicle-price-on',
  'booking-thanks-message' => 'Thanks for booking! We have sent you a confirmation email which should arrive in your inbox shortly!',
  'email-sender-name' => 'Airport Taxi',
);

$data = json_encode($att_settings_array);

$data1 = '
{
    "last_tab": "",
    "site-layout-style": "right-sidebar",
    "site-header-style": "chauffeur-header-left-align",
    "site-footer-style": "chauffeur-footer-left-align",
    "fleet-single-sidebar": "chauffeur-fleet-sidebar-on",
    "top-left-text": "",
    "top-right-link-text1": "",
    "top-right-link-url1": "",
    "top-right-link-text2": "",
    "top-right-link-url2": "",
    "top-right-link-text3": "",
    "top-right-link-url3": "",
    "header-text-title-1": "AIRPORT TAXI BOOKINGS",
    "header-text-1": "AIRPORT TAXIS YOUR WAY",
    "header-text-title-2": "",
    "header-text-2": "",
    "header-text-title-3": "",
    "header-text-3": "",
    "top-right-button-text": "",
    "top-right-button-url": "",
    "top-right-button-link-target": "0",
    "google-map-api-key": "AIzaSyBxXDkCSBPquzn_3-Ddzkm8KeVc11P2YqQ",
    "google-api-language": "en",
    "footer-message": "\\u00a9 2017 Airport Taxi Bookings. All Rights Reserved",
    "fleet_slug": "",
    "testimonials_slug": "",
    "hourly-minimum": "1",
    "hourly-maximum": "48",
    "hours-before-booking-minimum": "60",
    "max-selectable-bags": "50",
    "max-selectable-passengers": "50",
    "disable-distance": "0",
    "disable-hourly": "1",
    "disable-flat-rate": "1",
    "currency-symbol": "\\u00a3",
    "currency-symbol-position": "before",
    "datepicker-format": "dd\\/mm\\/yy",
    "time-format": "24hr",
    "google-distance-matrix-unit": "imperial",
    "remove-vehicle-link": "1",
    "booking-surcharge": "none",
    "surcharge-percentage": "",
    "surcharge-flat-rate": "",
    "surcharge-enable-distance": "0",
    "surcharge-enable-hourly": "0",
    "surcharge-enable-flat-rate": "0",
    "google-limit-country": "GB",
    "enable-paypal": "0",
    "enable-stripe": "1",
    "enable-cash": "0",
    "hide-pricing": "0",
    "booking-email": "booking@wp.website-factory.org",
    "booking-page-url": "'.site_url().'/booking",
    "thanks-page-url": "'.site_url().'/thank-you",
    "customer-booking-email-subject": "Thanks for booking with Chauffeur!",
    "customer-booking-email-content": "Thanks, we have received your booking and will get back to you shortly!",
    "admin-booking-email-subject": "A New Vehicle Booking Has Been Placed",
    "admin-booking-email-content": "Please see below for details of the booking",
    "email-sender-name": "Airport Taxi",
    "booking-thanks-message": "Thanks for booking! We have sent you a confirmation email which should arrive in your inbox shortly!",
    "terms_conditions": "",
    "paypal-currency": "USD",
    "paypal-sandbox": "",
    "paypal-address": "",
    "stripe-currency": "GBP",
    "stripe-testmode": "on",
    "stripe_secret_key": "sk_test_ofNzfwOM6p2IfLyby0rBNbuH00FDxHOZi5",
    "stripe_publishable_key": "pk_test_KqOjr0z7i3JLDvwbetCtRofP005q1bfAQc",
    "logo-image": {
      "url": "https:\\/\\/wp.website-factory.org\\/airporttaxibooking\\/wp-content\\/uploads\\/2022\\/06\\/Untitled-design-1-e1656254924652.png",
      "id": "11354",
      "height": "141",
      "width": "250",
      "thumbnail": "https:\\/\\/wp.website-factory.org\\/airporttaxibooking\\/wp-content\\/uploads\\/2022\\/06\\/Untitled-design-1-e1656254924652-150x141.png"
    },
    "main-color": "#FFCA09",
    "main-color-overlay": "#E7AEB4",
    "secondary-color": "#2F3033",
    "secondary-color-border": "#3B3B3B",
    "secondary-color-text": "#838383",
    "google_font_name_1": "\'Open Sans\', sans-serif",
    "google_font_url_1": "Open Sans:300,300italic,400,400italic,600,700,700italic",
    "google_font_name_2": "\'Source Sans Pro\', sans-serif",
    "google_font_url_2": "Source Sans Pro:400,200,200italic,300,300italic,400italic,600,600italic,700,700italic,900,900italic",
    "page-header-image": {
      "url": "",
      "id": "",
      "height": "",
      "width": "",
      "thumbnail": ""
    },
    "custom_js": "",
    "social-link-target": "1",
    "facebook-icon": "",
    "flickr-icon": "",
    "googleplus-icon": "",
    "instagram-icon": "",
    "linkedin-icon": "",
    "pinterest-icon": "",
    "skype-icon": "",
    "soundcloud-icon": "",
    "tripadvisor-icon": "",
    "tumblr-icon": "",
    "twitter-icon": "",
    "vimeo-icon": "",
    "vine-icon": "",
    "yelp-icon": "",
    "youtube-icon": "",
    "REDUX_LAST_SAVE": 1657121992,
    "REDUX_LAST_COMPILER": 1657121992
  }
';

$chauffeur_data = json_decode($data, TRUE);

add_action( 'after_setup_theme', 'add_image_size_chauffeur_image_style10' );
function add_image_size_chauffeur_image_style10() {
    add_image_size( 'chauffeur-image-style10', 110, 70, true );
}


/* Custom Widget Area */

function register_atb_custom_widget_area() {
  register_sidebar(
  array(
  'id' => 'widget-area-booking-form-content',
  'name' => esc_html__( 'Booking Form Content', '' ),
  'description' => esc_html__( 'A new widget area made to show booking form content', '' ),
  'before_widget' => '<div id="%1$s" class="widget %2$s">',
  'after_widget' => '</div>',
  'before_title' => '<div class="widget-title-holder"><h3 class="widget-title">',
  'after_title' => '</h3></div>'
  )
  );
}
add_action( 'widgets_init', 'register_atb_custom_widget_area' );

add_shortcode( 'bfc-content', 'wphelp_custom_sidebar' );
function wphelp_custom_sidebar(){
  if ( is_active_sidebar( 'widget-area-booking-form-content' ) ):
    dynamic_sidebar( 'widget-area-booking-form-content' );
  endif;
}
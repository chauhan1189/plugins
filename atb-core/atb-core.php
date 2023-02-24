<?php

/*
  Plugin Name: Airport Taxi Booking - Core
  Plugin URI: http://iamavi.com
  Description: Custom plugin for taxi booking.
  Version: 2.0.8
  Author: Avijit H.
  Author URI: http://iamavi.com
*/

define( 'ATT_PATH', plugin_dir_path( __FILE__ ));
define( 'ATT_URL', plugin_dir_url( __FILE__ ) );

include(ATT_PATH . 'includes/stripe-payment-endpoint.php');
include(ATT_PATH . 'includes/stripe-payment-redirect.php');
include(ATT_PATH . 'includes/suppliers-portal-auth.php');
include(ATT_PATH . 'includes/suppliers-portal-insert.php');
include(ATT_PATH . 'includes/global-settings.php');
include(ATT_PATH . 'includes/plugin-settings-page.php');
include(ATT_PATH . 'includes/autocab/mha-autocab-chauffeur.php');
include(ATT_PATH . 'includes/shortcodes/suppliers-page.php');
include(ATT_PATH . 'includes/post-types/suppliers-portal.php');
include(ATT_PATH . 'includes/post-types/new-rates.php');
include(ATT_PATH . 'includes/post-types/min-wait-time.php');
include(ATT_PATH . 'includes/post-types/coupons.php');

require_once(ABSPATH . 'wp-admin/includes/file.php');

/* Register Session */
function register_session(){
	if( !session_id()) 
	session_start();
}
add_action('init','register_session');

function getCurrentLocalTime(){
	return new DateTime("now", new DateTimeZone("Europe/London"));
}

/* ----------------------------------------------------------------------------

   Load Language Files

---------------------------------------------------------------------------- */
function chauffeur_init() {
	load_plugin_textdomain( 'chauffeur', false, dirname(plugin_basename( __FILE__ ))  . '/languages/' ); 
}
add_action('init', 'chauffeur_init');


/* Create Suppliers User Role */
add_role(
    'supplier', //  System name of the role.
    __( 'Supplier'  ),
    array(
        'read'  => false,
        'delete_posts'  => false,
        'delete_published_posts' => false,
        'edit_posts'   => false,
        'publish_posts' => false,
        'upload_files'  => false,
        'edit_pages'  => false,
        'edit_published_pages'  =>  false,
        'publish_pages'  => false,
        'delete_published_pages' => false,
    )
);

/* ----------------------------------------------------------------------------

   Load Files

---------------------------------------------------------------------------- */
if ( ! defined( 'chauffeur_BASE_FILE' ) )
    define( 'chauffeur_BASE_FILE', __FILE__ );

if ( ! defined( 'chauffeur_BASE_DIR' ) )
    define( 'chauffeur_BASE_DIR', dirname( chauffeur_BASE_FILE ) );

if ( ! defined( 'chauffeur_PLUGIN_URL' ) )
    define( 'chauffeur_PLUGIN_URL', plugin_dir_url( __FILE__ ) );



/* ----------------------------------------------------------------------------

   Plugin Activation

---------------------------------------------------------------------------- */
function chauffeur_shortcodes_activation() {}
register_activation_hook(__FILE__, 'chauffeur_shortcodes_activation');

function chauffeur_shortcodes_deactivation() {}
register_deactivation_hook(__FILE__, 'chauffeur_shortcodes_deactivation');



/* ----------------------------------------------------------------------------

   Load JS

---------------------------------------------------------------------------- */
add_action('wp_enqueue_scripts', 'chauffeur_shortcodes_scripts');
function chauffeur_shortcodes_scripts() {
	
    global $post;
	global $chauffeur_data;
	
	
	
	/* +Flat Rate +Hourly +Distance */
	if ( $chauffeur_data['disable-flat-rate'] == 0 && $chauffeur_data['disable-hourly'] == 0 && $chauffeur_data['disable-distance'] == 0 ) {
		$disable_booking_options_js = "var chauffeur_active_tab = 'distance';";
	}



	/* +Flat Rate +Hourly -Distance */
	if ( $chauffeur_data['disable-flat-rate'] == 0 && $chauffeur_data['disable-hourly'] == 0 && $chauffeur_data['disable-distance'] == 1 ) {
		$disable_booking_options_js = "jQuery(document).ready(function($) { 
			$( '#booking-tabs' ).tabs({ active: 1 });
			$( '#booking-tabs-2' ).tabs({ active: 1 });
		});
		var chauffeur_active_tab = 'hourly';";
	}



	/* +Distance -Hourly +Flat Rate */
	if ( $chauffeur_data['disable-flat-rate'] == 0 && $chauffeur_data['disable-hourly'] == 1 && $chauffeur_data['disable-distance'] == 0  ) {
		$disable_booking_options_js = "jQuery(document).ready(function($) { 
			$( '#booking-tabs' ).tabs({ active: 3 });
			$( '#booking-tabs-2' ).tabs({ active: 3 });
		});
		var chauffeur_active_tab = 'distance';";
	}



	/* +Distance +Hourly -Flat Rate */
	if ( $chauffeur_data['disable-flat-rate'] == 1 && $chauffeur_data['disable-hourly'] == 0 && $chauffeur_data['disable-distance'] == 0  ) {
		$disable_booking_options_js = "jQuery(document).ready(function($) { 
			$( '#booking-tabs' ).tabs({ active: 3 });
			$( '#booking-tabs-2' ).tabs({ active: 3 });
		});
		var chauffeur_active_tab = 'distance';";
	}



	/* +Distance -Hourly -Flat Rate */
	if ( $chauffeur_data['disable-flat-rate'] == 1 && $chauffeur_data['disable-hourly'] == 1 && $chauffeur_data['disable-distance'] == 0  ) {
		$disable_booking_options_js = "jQuery(document).ready(function($) { 
			$( '#booking-tabs' ).tabs({ active: 3 });
			$( '#booking-tabs-2' ).tabs({ active: 3 });
		});
		var chauffeur_active_tab = 'distance';";
	}



	/* +Hourly -Distance -Flat Rate */
	if ( $chauffeur_data['disable-flat-rate'] == 1 && $chauffeur_data['disable-hourly'] == 0 && $chauffeur_data['disable-distance'] == 1  ) {
		$disable_booking_options_js = "jQuery(document).ready(function($) { 
			$( '#booking-tabs' ).tabs({ active: 1 });
			$( '#booking-tabs-2' ).tabs({ active: 1 });
		});
		var chauffeur_active_tab = 'hourly';

		jQuery(document).ready(function($) {
			setTimeout(function(){
				$('#booking-tabs a[href=\"#tab-hourly\"]').trigger('click');
			});
		});

		jQuery(document).ready(function($) {
			setTimeout(function(){
				$('#booking-tabs-2 a[href=\"#tab-hourly\"]').trigger('click');
			});
		});";
	}



	/* +Flat Rate -Hourly -Distance */
	if ( $chauffeur_data['disable-flat-rate'] == 0 && $chauffeur_data['disable-hourly'] == 1 && $chauffeur_data['disable-distance'] == 1  ) {
		$disable_booking_options_js = "jQuery(document).ready(function($) { 
			$( '#booking-tabs' ).tabs({ active: 2 });
			$( '#booking-tabs-2' ).tabs({ active: 2 });
		});
		var chauffeur_active_tab = 'flat_rate';

		jQuery(document).ready(function($) {
			setTimeout(function(){
				$('#booking-tabs a[href=\"#tab-flat\"]').trigger('click');
			});
		});

		jQuery(document).ready(function($) {
			setTimeout(function(){
				$('#booking-tabs-2 a[href=\"#tab-flat\"]').trigger('click');
			});
		});";
	}
	
	if (empty($disable_booking_options_js)) {
		$disable_booking_options_js = '';
	}
	
	$GoogleMapApiKey = $chauffeur_data['google-map-api-key'];
	//$GoogleMapApiKey = 'AIzaSyChwcKtJscESB7Cvw8gZbaQp11ZUfmS1RQ';
	//$GoogleMapApiKey = 'AIzaSyBxXDkCSBPquzn_3-Ddzkm8KeVc11P2YqQ';
	// var_dump($GoogleMapApiKey);
	// die();
	wp_enqueue_script('jquery');
	
	if ( !empty($chauffeur_data['google-map-api-key']) ) {
		//wp_register_script('googleMap', 'http://maps.google.com/maps/api/js?key='.$GoogleMapApiKey);
		//wp_enqueue_script('googleMap');
	}
	
	if ( !empty($chauffeur_data['datepicker-format']) ) {
		$chauffeur_datepicker_format = $chauffeur_data['datepicker-format'];
	} else {
		$chauffeur_datepicker_format = "dd/mm/yy";
	}
	
	if ( !empty($chauffeur_data['google-map-api-key']) ) {
		
		if ( !empty($chauffeur_data['google-api-language']) ) {
			wp_register_script('googlesearch', 'https://maps.googleapis.com/maps/api/js?key=' . $GoogleMapApiKey . '&libraries=places&mode=driving&language='.$chauffeur_data['google-api-language']);
			wp_enqueue_script('googlesearch');
		} else {
			wp_register_script('googlesearch', 'https://maps.googleapis.com/maps/api/js?key=' . $GoogleMapApiKey . '&libraries=places&mode=driving');
			wp_enqueue_script('googlesearch');
		}
		
	}
	
	if ( $chauffeur_data['hours-before-booking-minimum'] == '60' ) {
		$hours_before_booking_minimum = '1';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '120' ) {
		$hours_before_booking_minimum = '2';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '180' ) {
		$hours_before_booking_minimum = '3';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '240' ) {
		$hours_before_booking_minimum = '4';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '300' ) {
		$hours_before_booking_minimum = '5';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '360' ) {
		$hours_before_booking_minimum = '6';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '420' ) {
		$hours_before_booking_minimum = '7';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '480' ) {
		$hours_before_booking_minimum = '8';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '540' ) {
		$hours_before_booking_minimum = '9';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '600' ) {
		$hours_before_booking_minimum = '10';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '660' ) {
		$hours_before_booking_minimum = '11';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '720' ) {
		$hours_before_booking_minimum = '12';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '780' ) {
		$hours_before_booking_minimum = '13';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '840' ) {
		$hours_before_booking_minimum = '14';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '900' ) {
		$hours_before_booking_minimum = '15';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '960' ) {
		$hours_before_booking_minimum = '16';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '1020' ) {
		$hours_before_booking_minimum = '17';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '1080' ) {
		$hours_before_booking_minimum = '18';
	}	elseif ( $chauffeur_data['hours-before-booking-minimum'] == '1140' ) {
		$hours_before_booking_minimum = '19';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '1200' ) {
		$hours_before_booking_minimum = '20';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '1260' ) {
 		$hours_before_booking_minimum = '21';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '1320' ) {
		$hours_before_booking_minimum = '22';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '1380' ) {
		$hours_before_booking_minimum = '23';
	} elseif ( $chauffeur_data['hours-before-booking-minimum'] == '1440' ) {
		$hours_before_booking_minimum = '24';
	}
	
	if ( $chauffeur_data['terms_conditions'] ) {
		$terms_and_conditions = 'true';
	} else {
		$terms_and_conditions = 'false';
	}
	
	wp_register_script('chauffeur-custom', plugins_url('assets/js/script-9325.js', __FILE__));
	wp_enqueue_script('chauffeur-custom');

	$path_vars_array = array(
		'stylesheet_url' => get_stylesheet_directory_uri(),
		'image_dir_path' => ATT_URL .'/assets/images/'
	);
	
	wp_localize_script('chauffeur-custom', 'path_vars', $path_vars_array );
	
	wp_add_inline_script( 'chauffeur-custom', "
	
	var AJAX_URL = '" . AJAX_URL . "';
	var chauffeur_pickup_dropoff_error = '" . esc_html__('Please enter a pick up and drop off location','chauffeur') . "';
	var chauffeur_valid_email = '" . esc_html__('Please enter a valid email address111','chauffeur') . "';
	var chauffeur_valid_phone = '" . esc_html__('Please enter a valid phone number (numbers only and no spaces)','chauffeur') . "';
	var chauffeur_valid_bags = '" . esc_html__('Number of bags selected exceeds vehicle limit','chauffeur') . "';
	var chauffeur_valid_passengers = '" . esc_html__('Number of passengers selected exceeds vehicle limit','chauffeur') . "';
	var chauffeur_select_vehicle = '" . esc_html__('Please select a vehicle','chauffeur') . "';
	var chauffeur_complete_required = '" . esc_html__('Please complete all the required form fields marked with a *','chauffeur') . "';
	var chauffeur_autocomplete = '" . esc_html__('Please select pickup and drop off addresses using the Google autocomplete suggestion','chauffeur') . "';
	var chauffeur_terms = '" . esc_html__('You must accept the terms and conditions before placing your booking', 'chauffeur') . "';
	var chauffeur_terms_set = '" . $terms_and_conditions . "';
	
	var ch_minimum_hourly_alert = '" . esc_html__('The minimum hourly booking is','chauffeur') . " " . $chauffeur_data['hourly-minimum'] . " " . esc_html__('hours','chauffeur') . "';
	
	var chauffeur_min_time_before_booking_error = '" . esc_html__('Sorry we do not accept same day online bookings less than','chauffeur') . " " . $hours_before_booking_minimum . " " . esc_html__('hour(s) in advance of the pick up time','chauffeur') . "';
	
	var LOADING_IMAGE = '" . chauffeur_PLUGIN_URL . "assets/images/loading.gif';
	var chauffeur_datepicker_format = '" . $chauffeur_datepicker_format . "';
	
	" . $disable_booking_options_js );
	
	if ($chauffeur_data['google-limit-country'] == '') {
		wp_add_inline_script( 'chauffeur-custom', "var Google_AutoComplete_Country = 'ALL_COUNTRIES';");		
	} else {	
		wp_add_inline_script( 'chauffeur-custom', "var Google_AutoComplete_Country = '" . $chauffeur_data['google-limit-country'] . "';");
	}
	
	if ($chauffeur_data['hours-before-booking-minimum'] == '') {
		wp_add_inline_script( 'chauffeur-custom', "var hours_before_booking_minimum = '2000';");		
	} else {	
		wp_add_inline_script( 'chauffeur-custom', "var hours_before_booking_minimum = '" . $chauffeur_data['hours-before-booking-minimum'] . "';");
	}
	
	if ($chauffeur_data['hourly-minimum'] == '') {
		wp_add_inline_script( 'chauffeur-custom', "var hourly_minimum = '1';");		
	} else {	
		wp_add_inline_script( 'chauffeur-custom', "var hourly_minimum = '" . $chauffeur_data['hourly-minimum'] . "';");
	}
	
	wp_register_script('fontawesomemarkers', plugins_url('assets/js/fontawesome-markers.min.js', __FILE__));
	wp_enqueue_script('fontawesomemarkers');
	
	wp_enqueue_script( array( 'jquery-ui-core', 'jquery-ui-tabs', 'jquery-effects-core' ) );

}



/* ----------------------------------------------------------------------------

   WPML

---------------------------------------------------------------------------- */
global $sitepress;
if ( !empty($sitepress) ){
	define('AJAX_URL', admin_url('admin-ajax.php?lang=' . $sitepress->get_current_language()));
} else {
	define('AJAX_URL', admin_url('admin-ajax.php'));
}



/* ----------------------------------------------------------------------------

   Load CSS

---------------------------------------------------------------------------- */
add_action('wp_enqueue_scripts', 'chauffeur_shortcodes_styles');
function chauffeur_shortcodes_styles() {

	wp_enqueue_script( array('jquery-ui-core', 'jquery-ui-datepicker', 'jquery-ui-accordion', 'jquery-ui-tabs', 'jquery-effects-core') );
	wp_enqueue_style( 'toastr-style', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css', "");
	wp_enqueue_script( 'toastr-js', 'https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js', array(), NULL, true );

	wp_register_style('style', plugins_url('assets/css/style-4527.css', __FILE__));
    wp_enqueue_style('style');

}



/* ----------------------------------------------------------------------------

   Load Shortcodes

---------------------------------------------------------------------------- */
include 'includes/shortcodes/booking-image-background.php';
include 'includes/shortcodes/booking-full-width.php';
include 'includes/shortcodes/booking-page.php';
include 'includes/shortcodes/booking-widget.php';
include 'includes/shortcodes/booking-thanks-page.php';
include 'includes/shortcodes/fleet-carousel.php';
include 'includes/shortcodes/fleet-page.php';
include 'includes/shortcodes/testimonials-carousel.php';
include 'includes/shortcodes/testimonials-page.php';
include 'includes/shortcodes/call-to-action-small.php';
include 'includes/shortcodes/call-to-action-large.php';
include 'includes/shortcodes/icon-text.php';
include 'includes/shortcodes/video-text.php';
include 'includes/shortcodes/video-thumbnail.php';
include 'includes/shortcodes/news-carousel.php';
include 'includes/shortcodes/gallery.php';
include 'includes/shortcodes/title.php';
include 'includes/shortcodes/news-page.php';
include 'includes/shortcodes/link-blocks.php';
include 'includes/shortcodes/socialmedia.php';
include 'includes/shortcodes/googlemap.php';
include 'includes/shortcodes/contactdetails.php';
include 'includes/shortcodes/button.php';
include 'includes/shortcodes/message.php';
include 'includes/shortcodes/service-rates-page.php';
//include 'includes/shortcodes/booking-debug.php';

/** Added by PG. */
include 'includes/shortcodes/booking-manage.php';
/** END - Added by PG. */



/* ----------------------------------------------------------------------------

   Load Post Types

---------------------------------------------------------------------------- */
//include 'includes/post-types/testimonials.php';
include 'includes/post-types/fleet.php';
//include 'includes/post-types/rates.php';
include 'includes/post-types/payments.php';
//include 'includes/post-types/flat-rate-trips.php';



/* ----------------------------------------------------------------------------

   Load Template Chooser

---------------------------------------------------------------------------- */
add_filter( 'template_include', 'chauffeur_spt_template_chooser');
function chauffeur_spt_template_chooser( $template ) {
 
    if ( is_search() ) {
		
		return $template;
		
	} else {
		
		$post_id = get_the_ID();

		if ( get_post_type( $post_id ) == 'fleet' ) {
			return chauffeur_spt_get_template_hierarchy( 'single-fleet' );
		} elseif ( get_post_type( $post_id ) == 'testimonial' ) {
			return chauffeur_spt_get_template_hierarchy( 'single-testimonials' );
		} elseif ( get_post_type( $post_id ) == 'rates' ) {
			return chauffeur_spt_get_template_hierarchy( 'single-rates' );
		} elseif ( get_post_type( $post_id ) == 'payment' ) {
			return chauffeur_spt_get_template_hierarchy( 'single-payment' );
		} elseif ( get_post_type( $post_id ) == 'flat_rate_trips' ) {
			return chauffeur_spt_get_template_hierarchy( 'single-flat-rate-trips' );
		} else {
			return $template;
		}
		
	}

}



/* ----------------------------------------------------------------------------

   Select Template

---------------------------------------------------------------------------- */
add_filter( 'template_include', 'chauffeur_spt_template_chooser' );
function chauffeur_spt_get_template_hierarchy( $template ) {
 
	if ( is_search() ) {
		
		$file = chauffeur_BASE_DIR . '/includes/templates/' . $template;
		return apply_filters( 'chauffeur_template_' . $template, $file );
	
	} else {

    	$template_slug = rtrim( $template, '.php' );
	    $template = $template_slug . '.php';

	    if ( $theme_file = locate_template( array( 'includes/templates/' . $template ) ) ) {
	        $file = $theme_file;
	    }
	    else {
	        $file = chauffeur_BASE_DIR . '/includes/templates/' . $template;
	    }

	    return apply_filters( 'chauffeur_template_' . $template, $file );
	
	}

}



/* ----------------------------------------------------------------------------

   Select Taxonomy Template

---------------------------------------------------------------------------- */
add_filter('template_include', 'qns_taxonomy_template');
function qns_taxonomy_template( $template ){

	if( is_tax('yacht_charter-type')){
  		$template = chauffeur_BASE_DIR .'/includes/templates/taxonomy-yacht-categories.php';
 	}  

	if( is_tax('yacht_sales-type')){
  		$template = chauffeur_BASE_DIR .'/includes/templates/taxonomy-yacht-categories.php';
 	}
  	
	return $template;

}



/* ----------------------------------------------------------------------------

   AJAX Booking Form

---------------------------------------------------------------------------- */
function contactform_add_script() {
	wp_localize_script( 'contactform-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_enqueue_scripts', 'contactform_add_script');



/* ----------------------------------------------------------------------------

   AJAX Booking Form Callback

---------------------------------------------------------------------------- */
function ajax_contactform_action_callback() {
	
	// Booking Form Step 2 
	if($_POST['form_type'] == 'one_way') {
		
		$booking_step_wrapper = booking_steps('2');
		$booking_form_content = booking_step_2("one_way");
	
	// Booking Form Step 2 
	} elseif($_POST['form_type'] == 'hourly') {
		
		$booking_step_wrapper = booking_steps('2');
		$booking_form_content = booking_step_2("hourly");
		
	} elseif($_POST['form_type'] == 'flat') {

		$booking_step_wrapper = booking_steps('2');
		$booking_form_content = booking_step_2("hourly");

	}
	
	// Booking Form Step 3
	if( isset($_POST['selected-vehicle-name']) ) {
		
		$booking_step_wrapper = booking_steps('3');
		$booking_form_content = booking_step_3();
		
	}
	
	$resp = array('booking_step_wrapper' => $booking_step_wrapper, 'booking_form_content' => $booking_form_content);
	header( "Content-Type: application/json" );
	echo json_encode($resp);
	die();
	
}
add_action( 'wp_ajax_contactform_action', 'ajax_contactform_action_callback' );
add_action( 'wp_ajax_nopriv_contactform_action', 'ajax_contactform_action_callback' );

/* ----------------------------------------------------------------------------

   AJAX Coupon Code Callback

---------------------------------------------------------------------------- */
function ajax_atb_coupon_action_callback() {
	$coupon = $_POST['coupon'];
	$price = $_POST['price'];

	$args = array(
		'post_type' => 'atb-coupons',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'meta_query' => array(
			array(
				'key' => 'atb_coupon_code',
				'value' => trim($coupon),
				'compare' => '==',
			)
		)
	);

	$wp_query = new WP_Query( $args );

	if ($wp_query->have_posts()){
		foreach($wp_query->posts as $post){
			$post_id = $post->ID;
			$discount_type = get_post_meta($post_id, 'atb_coupon_discount_type', true);
			$coupon_amount = get_post_meta($post_id, 'atb_coupon_amount', true);
			if($discount_type == 'percentage'){
				$discounted_price = $price - ($price * ($coupon_amount / 100));
				$coupon_data = $coupon_amount . '% OFF';
			}else{
				$discounted_price = $price - $coupon_amount;
				$coupon_data = '£' . $coupon_amount . ' OFF';
			}
			if($discounted_price > 0){
				$json_data = array(
					"valid"=> 'yes',
					"coupon"=> 'Coupon Applied: ' . $coupon . ' ('.$coupon_data.')',
					"price"=> $discounted_price
				);
				echo json_encode($json_data);
			}else{
				$json_data = array(
					"valid"=> 'no',
					"coupon"=> 'Coupon code is not valid.',
					"price"=> ''
				);
				echo json_encode($json_data);
			}
		}
		wp_reset_query();
	}else{
		$json_data = array(
			"valid"=> 'no',
			"coupon"=> 'Coupon code is not valid.',
			"price"=> ''
		);
		echo json_encode($json_data);
	}
	die();
	
}
add_action( 'wp_ajax_atb_coupon_action', 'ajax_atb_coupon_action_callback' );
add_action( 'wp_ajax_nopriv_atb_coupon_action', 'ajax_atb_coupon_action_callback' );



/* ----------------------------------------------------------------------------

   Minimum Wait Time Callback

---------------------------------------------------------------------------- */
function ajax_atb_min_wait_times_callback() {

	// Default Time
	
	global $chauffeur_data;

	if($_POST['city']){
		$city = $_POST['city'];
		$args = array(
			'post_type' => 'min-wait-time',
			'posts_per_page' => -1
		);
		$html = '';
		$city_lists = get_posts( $args );
		foreach ( $city_lists as $post ) {
			$post_id = $post->ID;
			$cities = get_post_meta($post_id, 'city_name', TRUE);
			foreach($cities as $city){
				$city = trim($city);
				$city = strtolower($city);
				$city_arr[$city] = get_post_meta($post_id, 'atb_min_time_box', TRUE);
				$city_arr2[] = $city;
				$c_arr[get_post_meta($post_id, 'atb_min_time_box', TRUE)] = $city;
			}
		}
		if (in_array($city, $city_arr2)) {
			$min_wait_time1 = $city_arr[$city];
		} else {
			$min_wait_time1 = $chauffeur_data['hours-before-booking-minimum'];
		}
		if(!isset($min_wait_time1) || empty($min_wait_time1)){
			$min_wait_time1 = $chauffeur_data['hours-before-booking-minimum'];
		}
	}else{
		$min_wait_time1 = $chauffeur_data['hours-before-booking-minimum'];
	}

	if ( $min_wait_time1 == '60' ) {
		$hours_before_booking_minimum = '1 hour';
	} elseif ( $min_wait_time1 == '120' ) {
		$hours_before_booking_minimum = '2 hours';
	} elseif ( $min_wait_time1 == '180' ) {
		$hours_before_booking_minimum = '3 hours';
	} elseif ( $min_wait_time1 == '240' ) {
		$hours_before_booking_minimum = '4 hours';
	} elseif ( $min_wait_time1 == '300' ) {
		$hours_before_booking_minimum = '5 hours';
	} elseif ( $min_wait_time1 == '360' ) {
		$hours_before_booking_minimum = '6 hours';
	} elseif ( $min_wait_time1 == '420' ) {
		$hours_before_booking_minimum = '7 hours';
	} elseif ( $min_wait_time1 == '480' ) {
		$hours_before_booking_minimum = '8 hours';
	} elseif ( $min_wait_time1 == '540' ) {
		$hours_before_booking_minimum = '9 hours';
	} elseif ( $min_wait_time1 == '600' ) {
		$hours_before_booking_minimum = '10 hours';
	} elseif ( $min_wait_time1 == '660' ) {
		$hours_before_booking_minimum = '11 hours';
	} elseif ( $min_wait_time1 == '720' ) {
		$hours_before_booking_minimum = '12 hours';
	} elseif ( $min_wait_time1 == '780' ) {
		$hours_before_booking_minimum = '13 hours';
	} elseif ( $min_wait_time1 == '840' ) {
		$hours_before_booking_minimum = '14 hours';
	} elseif ( $min_wait_time1 == '900' ) {
		$hours_before_booking_minimum = '15 hours';
	} elseif ( $min_wait_time1 == '960' ) {
		$hours_before_booking_minimum = '16 hours';
	} elseif ( $min_wait_time1 == '1020' ) {
		$hours_before_booking_minimum = '17 hours';
	} elseif ( $min_wait_time1 == '1080' ) {
		$hours_before_booking_minimum = '18 hours';
	}	elseif ( $min_wait_time1 == '1140' ) {
		$hours_before_booking_minimum = '19 hours';
	} elseif ( $min_wait_time1 == '1200' ) {
		$hours_before_booking_minimum = '20 hours';
	} elseif ( $min_wait_time1 == '1260' ) {
 		$hours_before_booking_minimum = '21 hours';
	} elseif ( $min_wait_time1 == '1320' ) {
		$hours_before_booking_minimum = '22 hours';
	} elseif ( $min_wait_time1 == '1380' ) {
		$hours_before_booking_minimum = '23 hours';
	} elseif ( $min_wait_time1 == '1440' ) {
		$hours_before_booking_minimum = '24 hours';
	}
	
	$json_data = array(
		"data1"=> $min_wait_time1,
		"data2"=> $hours_before_booking_minimum
	);
	echo json_encode($json_data);

	die();
	
}
add_action( 'wp_ajax_atb_min_wait_times', 'ajax_atb_min_wait_times_callback' );
add_action( 'wp_ajax_nopriv_atb_min_wait_times', 'ajax_atb_min_wait_times_callback' );


/* ----------------------------------------------------------------------------

   Load Booking Form Steps Template

---------------------------------------------------------------------------- */
function booking_steps($step) {
	
	ob_start();
	include 'includes/templates/booking-steps.php';
	return ob_get_clean();
	
}



/* ----------------------------------------------------------------------------

   Load Booking Form Step 2 Template

---------------------------------------------------------------------------- */
function booking_step_2($type) {
	
	ob_start();
	include 'includes/templates/booking-step2.php';
	return ob_get_clean();
	
}



/* ----------------------------------------------------------------------------

   Load Booking Form Step 3 Template

---------------------------------------------------------------------------- */
function booking_step_3() {
	
	ob_start();
	include 'includes/templates/booking-step3.php';
	return ob_get_clean();
	
}



/* ----------------------------------------------------------------------------

   Get Google Map Coordinates

---------------------------------------------------------------------------- */
function get_coordinates($address_string) {
	
	global $chauffeur_data;
	
	$address = urlencode($address_string);
	$url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&key=" . $chauffeur_data['google-map-api-key-2'];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	curl_close($ch);
	$response_a = json_decode($response);
	$status = $response_a->status;
	
	if ( $status == 'ZERO_RESULTS' ) {
		return FALSE;
	} else {
		$return = array('lat' => $response_a->results[0]->geometry->location->lat, 'long' => $long = $response_a->results[0]->geometry->location->lng);
		return $return;
	}

}



/* ----------------------------------------------------------------------------

   Get Google Map Coordinates (DEBUG)

---------------------------------------------------------------------------- */
function get_coordinates_debug($address_string) {
	
	global $chauffeur_data;
	
	$address = urlencode($address_string);
	$url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&key=" . $chauffeur_data['google-map-api-key'];
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	curl_close($ch);
	$response_a = json_decode($response);
	$status = $response_a->status;
	
	return $response;

}



/* ----------------------------------------------------------------------------

   Get Google Map Driving Distance

---------------------------------------------------------------------------- */
function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
   
	global $chauffeur_data;

	if ( $chauffeur_data['google-distance-matrix-unit'] == 'imperial' ) {
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&key=" . $chauffeur_data['google-map-api-key'];
	} else {
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&key=" . $chauffeur_data['google-map-api-key'];
	}
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	curl_close($ch);
	$response_a = json_decode($response, true);
	
	if ( isset($response_a['rows'][0]['elements'][0]['distance']['text']) ) {
		$dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
	} else {
		$dist = false;
	}
	
	if ( isset($response_a['rows'][0]['elements'][0]['duration']['text']) ) {
		$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
		
		/* Added BY PG */
		$time_sec = $response_a['rows'][0]['elements'][0]['duration']['value'];
		/* END - Added BY PG */
	} else {
		$time = false;
	}
	
	/* Commented BY PG
	return array('distance' => $dist, 'time' => $time); */

	/* Added BY PG */
	return array('distance' => $dist, 'time' => $time, 'time-sec' => $time_sec);
	/* END - Added BY PG */

}



/* ----------------------------------------------------------------------------

   Get Google Map Driving Distance (Debug)

---------------------------------------------------------------------------- */
function GetDrivingDistance_debug($lat1, $lat2, $long1, $long2) {
   
	global $chauffeur_data;

	if ( $chauffeur_data['google-distance-matrix-unit'] == 'imperial' ) {
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?units=imperial&origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&key=" . $chauffeur_data['google-map-api-key'];
	} else {
		$url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&key=" . $chauffeur_data['google-map-api-key'];
	}
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	curl_close($ch);
	$response_a = json_decode($response, true);
	
	if ( isset($response_a['rows'][0]['elements'][0]['distance']['text']) ) {
		$dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
	} else {
		$dist = false;
	}
	
	if ( isset($response_a['rows'][0]['elements'][0]['duration']['text']) ) {
		$time = $response_a['rows'][0]['elements'][0]['duration']['text'];
	} else {
		$time = false;
	}
	
	return $response;

}



/* ----------------------------------------------------------------------------

   Payment Form

---------------------------------------------------------------------------- */
function payment_form( $data) {
	
	global $chauffeur_data;
	
	if ($chauffeur_data['paypal-sandbox'] == 'true') {
		define('SSL_P_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
		define('SSL_SAND_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
	} else {
		define('SSL_P_URL', 'https://www.paypal.com/cgi-bin/webscr');
		define('SSL_SAND_URL', 'https://www.paypal.com/cgi-bin/webscr');
	}
	
	$action = '';
	// Is this a test transaction? 
	$action = ($data['paypal_mode']) ? SSL_SAND_URL : SSL_URL;

	$form = '';
	$form .= '<form name="frm_payment_method" action="' . $action . '" method="post">';
	$form .= '<input type="hidden" name="business" value="' . $data['merchant_email'] . '" />';
	// Instant Payment Notification & Return Page Details /
	$form .= '<input type="hidden" name="notify_url" value="' . $data['notify_url'] . '" />';
	$form .= '<input type="hidden" name="cancel_return" value="' . $data['cancel_url'] . '" />';
	$form .= '<input type="hidden" name="return" value="' . $data['thanks_page'] . '" />';
	$form .= '<input type="hidden" name="rm" value="2" />';
	// Configures Basic Checkout Fields -->
	$form .= '<input type="hidden" name="lc" value="" />';
	$form .= '<input type="hidden" name="no_shipping" value="1" />';
	$form .= '<input type="hidden" name="no_note" value="1" />';
	// <input type="hidden" name="custom" value="localhost" />-->
	$form .= '<input type="hidden" name="currency_code" value="' . $data['currency_code'] . '" />';
	$form .= '<input type="hidden" name="page_style" value="paypal" />';
	$form .= '<input type="hidden" name="charset" value="utf-8" />';
	$form .= '<input type="hidden" name="item_name" value="' . $data['product_name'] . '" />';
	$form .= '<input type="hidden" name="item_number" value="' . $data['item_number'] . '" />';
	$form .= '<input type="hidden" value="_xclick" name="cmd"/>';
	$form .= '<input type="hidden" name="amount" value="' . $data['amount'] . '" />';
			
	$form .= '</form>';
	$form .= '<script>';
	$form .= 'setTimeout("document.frm_payment_method.submit()", 0);';
	$form .= '</script>';
	return $form;
	
}



/* ----------------------------------------------------------------------------

   PayPal IPN

---------------------------------------------------------------------------- */
class PayPal_IPN{
	
	function payment_ipn($im_debut_ipn) {
		
		global $chauffeur_data;
		
		if ($chauffeur_data['paypal-sandbox'] == 'true') {
			define('SSL_P_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
			define('SSL_SAND_URL', 'https://www.sandbox.paypal.com/cgi-bin/webscr');
		} else {
			define('SSL_P_URL', 'https://www.paypal.com/cgi-bin/webscr');
			define('SSL_SAND_URL', 'https://www.paypal.com/cgi-bin/webscr');
		}
		
		$hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		if (!preg_match('/paypal\.com$/', $hostname)) {
			
			$ipn_status = 'Validation post isn\'t from PayPal';
			if ($im_debut_ipn == true) {
				// mail test
			}
			return false;
		
		}
		
		// parse the paypal URL
		$paypal_url = ($_REQUEST['test_ipn'] == 1) ? SSL_SAND_URL : SSL_P_URL;
		$url_parsed = parse_url($paypal_url);
		
		$post_string = '';
		foreach ($_REQUEST as $field => $value) {
			$post_string .= $field . '=' . urlencode(stripslashes($value)) . '&';
		}
		$post_string.="cmd=_notify-validate"; // append ipn command
		// get the correct paypal url to post request to
		$paypal_mode_status = $im_debut_ipn; //get_option('im_sabdbox_mode');
		if ($chauffeur_data['paypal-sandbox'] == 'true')
			$fp = fsockopen('ssl://www.sandbox.paypal.com', "443", $err_num, $err_str, 60);
		else
			$fp = fsockopen('ssl://www.paypal.com', "443", $err_num, $err_str, 60);
		
		$ipn_response = '';
		
		if (!$fp) {
			// could not open the connection.  If loggin is on, the error message
			// will be in the log.
			$ipn_status = "fsockopen error no. $err_num: $err_str";
			if ($im_debut_ipn == true) {
				echo 'fsockopen fail';
			}
			return false;
		} else {
			// Post the data back to paypal
			fputs($fp, "POST $url_parsed[path] HTTP/1.1\r\n");
			fputs($fp, "Host: $url_parsed[host]\r\n");
			fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
			fputs($fp, "Content-length: " . strlen($post_string) . "\r\n");
			fputs($fp, "Connection: close\r\n\r\n");
			fputs($fp, $post_string . "\r\n\r\n");

			// loop through the response from the server and append to variable
			while (!feof($fp)) {
				$ipn_response .= fgets($fp, 1024);
			}
			fclose($fp); // close connection
		}
		
		// Invalid IPN transaction.  Check the $ipn_status and log for details.
		if (!preg_match("/VERIFIED/s", $ipn_response)) {
			$ipn_status = 'IPN Validation Failed';
			if ($im_debut_ipn == true) {
				echo 'Validation fail';
				print_r($_REQUEST);
			}
			return false;
		} else {
			$ipn_status = "IPN VERIFIED";
			if ($im_debut_ipn == true) {
				echo 'SUCCESS';
			}
			return true;
		}
		
	}
	
	function ipn_response($request) {
		
		$im_debut_ipn=true;
		if ($this->payment_ipn($im_debut_ipn)) {
			
			// if paypal sends a response code back let's handle it        
			if ($im_debut_ipn == true) {
				$sub = 'PayPal IPN Debug Email Main';
				$msg = print_r($request, true);
				$aname = 'infotuts';
				//mail send
			}
			
			// process the membership since paypal gave us a valid +
			$this->insert_data($request);
		}
	}
	
	function issetCheck($post,$key) {
		
		if(isset($post[$key])){
			$return=$post[$key];
		} else {
			$return='';
		}
		return $return;
	
	}
	
	function insert_data($request) {
		
		global $chauffeur_data;
		
		$post=$request;
		$item_name=$this->issetCheck($post,'item_name');
		$item_number=$this->issetCheck($post,'item_number');
		$amount=$this->issetCheck($post,'mc_gross');
		$currency=$this->issetCheck($post,'mc_currency');
		$payer_email=$this->issetCheck($post,'payer_email');
		$first_name=$this->issetCheck($post,'first_name');
		$last_name=$this->issetCheck($post,'last_name');
		$country=$this->issetCheck($post,'residence_country');
		$txn_id=$this->issetCheck($post,'txn_id');
		$txn_type=$this->issetCheck($post,'txn_type');
		$payment_status=$this->issetCheck($post,'payment_status');
		$payment_type=$this->issetCheck($post,'payment_type');
		$payer_id=$this->issetCheck($post,'payer_id');
		$create_date=date('Y-m-d H:i:s');
		$payment_date=date('Y-m-d H:i:s');
		
		/*$payment_details = esc_html__('Amount Paid','chauffeur') . ': ' . $amount . ' (' . $currency . ')
' . esc_html__('Email','chauffeur') . ': ' . $payer_email . '
' . esc_html__('Name','chauffeur') . ': ' . $first_name . ' ' . $last_name . '
' . esc_html__('Country','chauffeur') . ': ' . $country . '
' . esc_html__('Payment Status','chauffeur') . ': ' . $payment_status . '
' . esc_html__('Payment ID','chauffeur') . ': ' . $payer_id . '
' . esc_html__('Payment Date','chauffeur') . ': ' . $payment_date;*/
		
		$get_vehicle_name = get_post_meta($item_number,'chauffeur_payment_item_name',TRUE);
		$get_pickup_address = get_post_meta($item_number,'chauffeur_payment_pickup_address',TRUE);
		$get_dropoff_address = get_post_meta($item_number,'chauffeur_payment_dropoff_address',TRUE);
		$get_pickup_date = get_post_meta($item_number,'chauffeur_payment_pickup_date',TRUE);
		$get_pickup_time = get_post_meta($item_number,'chauffeur_payment_pickup_time',TRUE);
		$get_num_passengers = get_post_meta($item_number,'chauffeur_payment_num_passengers',TRUE);
		$get_num_bags = get_post_meta($item_number,'chauffeur_payment_num_bags',TRUE);
		$get_first_name = get_post_meta($item_number,'chauffeur_payment_first_name',TRUE);
		$get_last_name = get_post_meta($item_number,'chauffeur_payment_last_name',TRUE);
		$get_phone_num = get_post_meta($item_number,'chauffeur_payment_phone_num',TRUE);
		$get_trip_distance = get_post_meta($item_number,'chauffeur_payment_trip_distance',TRUE);
		$get_trip_time = get_post_meta($item_number,'chauffeur_payment_trip_time',TRUE);
		$get_flight_number = get_post_meta($item_number,'chauffeur_payment_flight_number',TRUE);
		$get_additional_details = get_post_meta($item_number,'chauffeur_payment_additional_info',TRUE);
		$get_trip_type = get_post_meta($item_number,'chauffeur_payment_trip_type',TRUE);
		$get_payment_num_hours = get_post_meta($item_number,'chauffeur_payment_num_hours',TRUE);
		$get_payment_email = get_post_meta($item_number,'chauffeur_payment_email',TRUE);
		
		$get_full_pickup_address = get_post_meta($item_number,'chauffeur_payment_full_pickup_address',TRUE);
		$get_pickup_instructions = get_post_meta($item_number,'chauffeur_payment_pickup_instructions',TRUE);
		$get_full_dropoff_address = get_post_meta($item_number,'chauffeur_payment_full_dropoff_address',TRUE);
		$get_dropoff_instructions = get_post_meta($item_number,'chauffeur_payment_dropoff_instructions',TRUE);
		
		$get_return_journey = get_post_meta($item_number,'chauffeur_payment_return_journey',TRUE);
		
		
		// Send customer email
		include ( chauffeur_BASE_DIR . "/includes/templates/email-customer-booking.php");
		wp_mail($get_payment_email,$customer_email_subject,$customer_email_content,$customer_headers);
		/*
		// Send admin email
		include ( chauffeur_BASE_DIR . "/includes/templates/email-admin-booking.php");
		wp_mail($chauffeur_data['booking-email'],$admin_email_subject,$admin_email_content,$admin_headers);
		*/
		// Update booking data
		update_post_meta($item_number, 'chauffeur_payment_status', esc_html__('Paid','chauffeur') );
		//update_post_meta($item_number, 'chauffeur_payment_details', $payment_details );
		update_post_meta($item_number, 'chauffeur_payment_method', esc_html__('PayPal','chauffeur') );
	
	}

}



/* ----------------------------------------------------------------------------

   Cash Payment Complete

---------------------------------------------------------------------------- */

function cash_payment_complete($booking_id) {
	
	global $chauffeur_data;
	
	ob_start();

    if ( isset($booking_id) ) {
		
		$item_number = $booking_id;
		
		$get_vehicle_name = get_post_meta($item_number,'chauffeur_payment_item_name',TRUE);
		$get_pickup_address = get_post_meta($item_number,'chauffeur_payment_pickup_address',TRUE);
		$get_dropoff_address = get_post_meta($item_number,'chauffeur_payment_dropoff_address',TRUE);
		$get_pickup_date = get_post_meta($item_number,'chauffeur_payment_pickup_date',TRUE);
		$get_pickup_time = get_post_meta($item_number,'chauffeur_payment_pickup_time',TRUE);
		$get_num_passengers = get_post_meta($item_number,'chauffeur_payment_num_passengers',TRUE);
		$get_num_bags = get_post_meta($item_number,'chauffeur_payment_num_bags',TRUE);
		$get_first_name = get_post_meta($item_number,'chauffeur_payment_first_name',TRUE);
		$get_last_name = get_post_meta($item_number,'chauffeur_payment_last_name',TRUE);
		$get_phone_num = get_post_meta($item_number,'chauffeur_payment_phone_num',TRUE);
		$get_trip_distance = get_post_meta($item_number,'chauffeur_payment_trip_distance',TRUE);
		$get_trip_time = get_post_meta($item_number,'chauffeur_payment_trip_time',TRUE);
		$get_flight_number = get_post_meta($item_number,'chauffeur_payment_flight_number',TRUE);
		$get_additional_details = get_post_meta($item_number,'chauffeur_payment_additional_info',TRUE);
		$get_trip_type = get_post_meta($item_number,'chauffeur_payment_trip_type',TRUE);
		$get_payment_num_hours = get_post_meta($item_number,'chauffeur_payment_num_hours',TRUE);
		$get_payment_email = get_post_meta($item_number,'chauffeur_payment_email',TRUE);
		
		$get_full_pickup_address = get_post_meta($item_number,'chauffeur_payment_full_pickup_address',TRUE);
		$get_pickup_instructions = get_post_meta($item_number,'chauffeur_payment_pickup_instructions',TRUE);
		$get_full_dropoff_address = get_post_meta($item_number,'chauffeur_payment_full_dropoff_address',TRUE);
		$get_dropoff_instructions = get_post_meta($item_number,'chauffeur_payment_dropoff_instructions',TRUE);
		
		 ?>

		<!-- BEGIN .full-booking-wrapper -->
		<div class="full-booking-wrapper full-booking-wrapper-3 clearfix">

			<h4><?php esc_html_e('Booking Successful','chauffeur'); ?></h4>
			<div class="title-block7"></div>

			<p><?php echo esc_attr($chauffeur_data['booking-thanks-message']); ?></p>

			<hr class="space7" />

			<h4><?php esc_html_e('Trip Details','chauffeur'); ?></h4>
			<div class="title-block7"></div>

			<!-- BEGIN .clearfix -->
			<div class="clearfix">

				<!-- BEGIN .qns-one-half -->
				<div class="qns-one-half">
					
					<?php if ($get_trip_type == 'one_way') {
						$form_type_text = esc_html__('Distance','chauffeur');
					} elseif ($get_trip_type == 'hourly') {
						$form_type_text = esc_html__('Hourly','chauffeur');
					} elseif ($get_trip_type == 'flat') {
						$form_type_text = esc_html__('Flat Rate','chauffeur');
					} ?>
					
					<p class="clearfix"><strong><?php esc_html_e('Service:','chauffeur'); ?></strong> <span><?php echo $form_type_text; ?></span></p>
					
					<?php if ( $get_trip_type == 'flat' ) {

						$pick_up_address = get_post_meta($_POST['flat-location'], 'chauffeur_flat_rate_trips_pick_up_name', true);
						$drop_off_address = get_post_meta($_POST['flat-location'], 'chauffeur_flat_rate_trips_drop_off_name', true);

					} else {

						$pick_up_address = $_POST['pickup-address'];
						$drop_off_address = $_POST['dropoff-address'];

					} ?>

					<p class="clearfix"><strong><?php esc_html_e('From','chauffeur'); ?>:</strong> <span><?php echo $pick_up_address; if( $_POST['full-pickup-address'] ) { echo '(' . $_POST['full-pickup-address'] . ')'; } ?></span></p>
					<p class="clearfix"><strong><?php esc_html_e('To','chauffeur'); ?>:</strong> <span><?php echo $drop_off_address; if( $_POST['full-dropoff-address'] ) { echo '(' . $_POST['full-dropoff-address'] . ')'; } ?></span></p>
					
					<p class="clearfix"><strong><?php esc_html_e('Vehicle:','chauffeur'); ?></strong> <span><?php echo $get_vehicle_name; ?></span></p>
					
					<?php if ( $_POST['return-journey']) {
				
						if ( $_POST['return-journey'] == 'true' ) {
							$return_journey = esc_html__('Return','chauffeur');
						} else {
							$return_journey = esc_html__('One Way','chauffeur');
						}
			
						echo '<p class="clearfix"><strong>' . esc_html__('Return','chauffeur') . ':</strong> <span>' .  $return_journey . '</span></p>';
			
					} ?>
			
					<?php if ( $_POST['flight-number'] ) { ?>
			
						<p class="clearfix"><strong><?php esc_html_e('Flight Number','chauffeur'); ?>:</strong> <span><?php echo $_POST["flight-number"]; ?></span></p>
			
					<?php } ?>
					
				<!-- END .qns-one-half -->
				</div>

				<!-- BEGIN .qns-one-half -->
				<div class="qns-one-half last-col">

					<p class="clearfix"><strong><?php esc_html_e('Date:','chauffeur'); ?></strong> <span><?php echo $get_pickup_date; ?></span></p>

					<?php if ($_POST['num-hours'] != '') { ?>

						<p class="clearfix"><strong><?php esc_html_e('Hours','chauffeur'); ?>:</strong> <span><?php echo $_POST['num-hours']; ?></span></p>	

					<?php } elseif ( $get_trip_type != 'flat' ) { ?>

						<p class="clearfix"><strong><?php esc_html_e('Distance','chauffeur'); ?>:</strong> <span><?php echo $_POST['first-trip-distance']; ?> (<?php echo $_POST['first-trip-time']; ?>)</span></p>	

					<?php } ?>

					<p class="clearfix"><strong><?php esc_html_e('Pick Up Time:','chauffeur'); ?></strong> <span><?php echo $get_pickup_time; ?></span></p>
					
					<?php if ( $_POST['pickup-instructions'] ) { ?>

						<p class="clearfix"><strong><?php esc_html_e('Pick Up Instructions','chauffeur'); ?>:</strong> <span><?php echo $_POST["pickup-instructions"]; ?></span></p>

					<?php } ?>

					<?php if ( $_POST['dropoff-instructions'] ) { ?>

						<p class="clearfix"><strong><?php esc_html_e('Drop Off Instructions','chauffeur'); ?>:</strong> <span><?php echo $_POST["dropoff-instructions"]; ?></span></p>

					<?php } ?>
					
					<?php if ( $_POST['full-pickup-address'] ) { ?>
			
						<p class="clearfix"><strong><?php esc_html_e('Full Pick Up Address','chauffeur'); ?>:</strong> <span><?php echo $_POST["full-pickup-address"]; ?></span></p>
			
					<?php } ?>
			
					<?php if ( $_POST['full-dropoff-address'] ) { ?>
			
						<p class="clearfix"><strong><?php esc_html_e('Full Drop Off Address','chauffeur'); ?>:</strong> <span><?php echo $_POST["full-dropoff-address"]; ?></span></p>
			
					<?php } ?>
					
					<?php if ( $get_trip_type != 'flat' ) { ?>
					
					<p class="clearfix"><strong><?php esc_html_e('Route Estimate','chauffeur'); ?>:</strong> <span><a href="https://maps.google.com/maps?saddr=<?php echo $get_pickup_address; ?>&amp;daddr=<?php echo $get_dropoff_address; ?>&amp;ie=UTF8&amp;z=11&amp;layer=t&amp;t=m&amp;iwloc=A&amp;output=embed?iframe=true&amp;width=640&amp;height=480" data-gal="prettyPhoto[gallery]" class="view-map-button"><?php esc_html_e('View Map','chauffeur'); ?></a></span></p>

					<?php } ?>

				<!-- END .qns-one-half -->
				</div>

			<!-- END .clearfix -->
			</div>

			<hr class="space2" />

			<h4><?php esc_html_e('Passengers Details','chauffeur'); ?></h4>
			<div class="title-block7"></div>

			<!-- BEGIN .clearfix -->
			<div class="clearfix">

				<!-- BEGIN .passenger-details-wrapper -->
				<div class="passenger-details-wrapper">

					<!-- BEGIN .clearfix -->
					<div class="clearfix">

						<!-- BEGIN .passenger-details-half -->
						<div class="passenger-details-half">

							<p class="clearfix"><strong><?php esc_html_e('Passengers:','chauffeur'); ?></strong> <span><?php echo $get_num_passengers; ?></span></p>
							<p class="clearfix"><strong><?php esc_html_e('Bags:','chauffeur'); ?></strong> <span><?php echo $get_num_bags; ?></span></p>

						<!-- END .passenger-details-half -->
						</div>

						<!-- BEGIN .passenger-details-half -->
						<div class="passenger-details-half last-col">

							<p class="clearfix"><strong><?php esc_html_e('Name:','chauffeur'); ?></strong> <span><?php echo $get_first_name . ' ' . $get_last_name; ?></span></p>
							<p class="clearfix"><strong><?php esc_html_e('Email:','chauffeur'); ?></strong> <span><?php echo $get_payment_email; ?></span></p>
							<p class="clearfix"><strong><?php esc_html_e('Phone:','chauffeur'); ?></strong> <span><?php echo $get_phone_num; ?></span></p>

						<!-- END .passenger-details-half -->
						</div>

					<!-- END .clearfix -->
					</div>

				<!-- END .passenger-details-wrapper -->
				</div>

				<!-- BEGIN .passenger-details-wrapper -->
				<div class="passenger-details-wrapper additional-information-wrapper last-col">

					<p class="clearfix"><strong><?php esc_html_e('Additional Information:','chauffeur'); ?></strong> <span><?php echo $get_additional_details; ?></span></p>

				<!-- END .passenger-details-wrapper -->
				</div>

			<!-- END .clearfix -->
			</div>

		<!-- END .full-booking-wrapper -->
		</div>

	<?php } else { ?>
	
		<p><?php esc_html_e('Invalid Request','chauffeur'); ?></p>
		
	<?php }
	
	return ob_get_clean();
	
}



/* ----------------------------------------------------------------------------

   Time Input

---------------------------------------------------------------------------- */

function time_input_hours() {
	
	global $chauffeur_data;
	$output = '';
	
	if ($chauffeur_data['time-format'] == '12hr') {
		
		$output .= '<option value="01">' . esc_html__( '1am', 'chauffeur' ) . '</option>
		<option value="02">' . esc_html__( '2am', 'chauffeur' ) . '</option>
		<option value="03">' . esc_html__( '3am', 'chauffeur' ) . '</option>
		<option value="04">' . esc_html__( '4am', 'chauffeur' ) . '</option>
		<option value="05">' . esc_html__( '5am', 'chauffeur' ) . '</option>
		<option value="06">' . esc_html__( '6am', 'chauffeur' ) . '</option>
		<option value="07">' . esc_html__( '7am', 'chauffeur' ) . '</option>
		<option value="08">' . esc_html__( '8am', 'chauffeur' ) . '</option>
		<option value="09">' . esc_html__( '9am', 'chauffeur' ) . '</option>
		<option value="10">' . esc_html__( '10am', 'chauffeur' ) . '</option>
		<option value="11">' . esc_html__( '11am', 'chauffeur' ) . '</option>
		<option value="12">' . esc_html__( '12pm', 'chauffeur' ) . '</option>
		<option value="13">' . esc_html__( '1pm', 'chauffeur' ) . '</option>
		<option value="14">' . esc_html__( '2pm', 'chauffeur' ) . '</option>
		<option value="15">' . esc_html__( '3pm', 'chauffeur' ) . '</option>
		<option value="16">' . esc_html__( '4pm', 'chauffeur' ) . '</option>
		<option value="17">' . esc_html__( '5pm', 'chauffeur' ) . '</option>
		<option value="18">' . esc_html__( '6pm', 'chauffeur' ) . '</option>
		<option value="19">' . esc_html__( '7pm', 'chauffeur' ) . '</option>
		<option value="20">' . esc_html__( '8pm', 'chauffeur' ) . '</option>
		<option value="21">' . esc_html__( '9pm', 'chauffeur' ) . '</option>
		<option value="22">' . esc_html__( '10pm', 'chauffeur' ) . '</option>
		<option value="23">' . esc_html__( '11pm', 'chauffeur' ) . '</option>
		<option value="00">' . esc_html__( '12am', 'chauffeur' ) . '</option>';
		
	} else {
		
		$output .= '<option value="01">' . esc_html__( '01', 'chauffeur' ) . '</option>
		<option value="02">' . esc_html__( '02', 'chauffeur' ) . '</option>
		<option value="03">' . esc_html__( '03', 'chauffeur' ) . '</option>
		<option value="04">' . esc_html__( '04', 'chauffeur' ) . '</option>
		<option value="05">' . esc_html__( '05', 'chauffeur' ) . '</option>
		<option value="06">' . esc_html__( '06', 'chauffeur' ) . '</option>
		<option value="07">' . esc_html__( '07', 'chauffeur' ) . '</option>
		<option value="08">' . esc_html__( '08', 'chauffeur' ) . '</option>
		<option value="09">' . esc_html__( '09', 'chauffeur' ) . '</option>
		<option value="10">' . esc_html__( '10', 'chauffeur' ) . '</option>
		<option value="11">' . esc_html__( '11', 'chauffeur' ) . '</option>
		<option value="12">' . esc_html__( '12', 'chauffeur' ) . '</option>
		<option value="13">' . esc_html__( '13', 'chauffeur' ) . '</option>
		<option value="14">' . esc_html__( '14', 'chauffeur' ) . '</option>
		<option value="15">' . esc_html__( '15', 'chauffeur' ) . '</option>
		<option value="16">' . esc_html__( '16', 'chauffeur' ) . '</option>
		<option value="17">' . esc_html__( '17', 'chauffeur' ) . '</option>
		<option value="18">' . esc_html__( '18', 'chauffeur' ) . '</option>
		<option value="19">' . esc_html__( '19', 'chauffeur' ) . '</option>
		<option value="20">' . esc_html__( '20', 'chauffeur' ) . '</option>
		<option value="21">' . esc_html__( '21', 'chauffeur' ) . '</option>
		<option value="22">' . esc_html__( '22', 'chauffeur' ) . '</option>
		<option value="23">' . esc_html__( '23', 'chauffeur' ) . '</option>
		<option value="00">' . esc_html__( '00', 'chauffeur' ) . '</option>';
		
	}
	
	return $output;
	
}



/* ----------------------------------------------------------------------------

   Time Output

---------------------------------------------------------------------------- */

function time_output_hours($hour,$min) {
	
	global $chauffeur_data;
	$output = '';
	
	if ($chauffeur_data['time-format'] == '12hr') {
		
		if($hour == '01') {
			$hour_output = '1';
			$unit = 'am';
		} elseif ($hour == '02') {
			$hour_output = '2';
			$unit = 'am';
		} elseif ($hour == '03') {
			$hour_output = '3';
			$unit = 'am';
		} elseif ($hour == '04') {
			$hour_output = '4';
			$unit = 'am';
		} elseif ($hour == '05') {
			$hour_output = '5';
			$unit = 'am';
		} elseif ($hour == '06') {
			$hour_output = '6';
			$unit = 'am';
		} elseif ($hour == '07') {
			$hour_output = '7';
			$unit = 'am';
		} elseif ($hour == '08') {
			$hour_output = '8';
			$unit = 'am';
		} elseif ($hour == '09') {
			$hour_output = '9';
			$unit = 'am';
		} elseif ($hour == '10') {
			$hour_output = '10';
			$unit = 'am';
		} elseif ($hour == '11') {
			$hour_output = '11';
			$unit = 'am';
		} elseif ($hour == '12') {
			$hour_output = '12';
			$unit = 'pm';
		} elseif ($hour == '13') {
			$hour_output = '1';
			$unit = 'pm';
		} elseif ($hour == '14') {
			$hour_output = '2';
			$unit = 'pm';
		} elseif ($hour == '15') {
			$hour_output = '3';
			$unit = 'pm';
		} elseif ($hour == '16') {
			$hour_output = '4';
			$unit = 'pm';
		} elseif ($hour == '17') {
			$hour_output = '5';
			$unit = 'pm';
		} elseif ($hour == '18') {
			$hour_output = '6';
			$unit = 'pm';
		} elseif ($hour == '19') {
			$hour_output = '7';
			$unit = 'pm';
		} elseif ($hour == '20') {
			$hour_output = '8';
			$unit = 'pm';
		} elseif ($hour == '21') {
			$hour_output = '9';
			$unit = 'pm';
		} elseif ($hour == '22') {
			$hour_output = '10';
			$unit = 'pm';
		} elseif ($hour == '23') {
			$hour_output = '11';
			$unit = 'pm';
		} elseif ($hour == '00') {
			$hour_output = '12';
			$unit = 'am';
		}
		
		$output = $hour_output . ':' . $min . $unit;
		
	} else {
		
		$output = $hour . ':' . $min;
		
	}
	
	return $output;
	
}



/* ----------------------------------------------------------------------------

   Time Output

---------------------------------------------------------------------------- */

function chauffeur_get_price($price) {

	global $chauffeur_data;
	
	if ($chauffeur_data['currency-symbol-position'] == 'before') {
		return $chauffeur_data['currency-symbol'] . $price;
	} else {
		return $price . $chauffeur_data['currency-symbol'];
	}
	
}



/* ----------------------------------------------------------------------------

   Stripe Payment

---------------------------------------------------------------------------- */
function chauffeur_3dstripe_payment($data) {	
	global $chauffeur_data;
	$data_array = array();
	//$amount_cents = str_replace(".","",$data["selected-vehicle-price"]);  // Chargeble amount
	$invoiceid = $data["booking_id"];                      // Invoice ID
	$description = "Invoice #" . $invoiceid . " - " . $invoiceid;
    $result = "success";
		
		
		$data_array["booking_id"] = $data["booking_id"];
		$data_array["payment_status"] = $result;
		
		// If payment is successful add details in database
		if ( $result == 'success' ) {
			
			$item_number = $data_array["booking_id"];
			//$amount = $data["selected-vehicle-price"];
			
			/*$get_trip_type = get_post_meta($item_number,'chauffeur_payment_trip_type',TRUE);
			$get_vehicle_name = get_post_meta($item_number,'chauffeur_payment_item_name',TRUE);
			$get_pickup_address = get_post_meta($item_number,'chauffeur_payment_pickup_address',TRUE);
			$get_pickup_via = get_post_meta($item_number,'chauffeur_payment_pickup_via',FALSE);
			// var_dump($get_pickup_via);
			// die();
			$get_dropoff_address = get_post_meta($item_number,'chauffeur_payment_dropoff_address',TRUE);
			$get_pickup_date = get_post_meta($item_number,'chauffeur_payment_pickup_date',TRUE);
			$get_pickup_time = get_post_meta($item_number,'chauffeur_payment_pickup_time',TRUE);
			$get_trip_distance = get_post_meta($item_number,'chauffeur_payment_trip_distance',TRUE);
			$get_trip_time = get_post_meta($item_number,'chauffeur_payment_trip_time',TRUE);
			$get_flight_number = get_post_meta($item_number,'chauffeur_payment_flight_number',TRUE);
			$get_first_journey_origin = get_post_meta($item_number,'chauffeur_payment_first_journey_origin',TRUE);
			$get_first_journey_greet = get_post_meta($item_number,'chauffeur_payment_first_journey_greet',TRUE);
			$get_additional_details = get_post_meta($item_number,'chauffeur_payment_additional_info',TRUE);

			$get_num_passengers = get_post_meta($item_number,'chauffeur_payment_num_passengers',TRUE);
			$get_num_bags = get_post_meta($item_number,'chauffeur_payment_num_bags',TRUE);
			$get_first_name = get_post_meta($item_number,'chauffeur_payment_first_name',TRUE);
			$get_last_name = get_post_meta($item_number,'chauffeur_payment_last_name',TRUE);
			$get_phone_num = get_post_meta($item_number,'chauffeur_payment_phone_num',TRUE);
			$get_payment_email = get_post_meta($item_number,'chauffeur_payment_email',TRUE);
			*/
			
			// $get_payment_num_hours = get_post_meta($item_number,'chauffeur_payment_num_hours',TRUE);
			// $get_full_pickup_address = get_post_meta($item_number,'chauffeur_payment_full_pickup_address',TRUE);
			// $get_pickup_instructions = get_post_meta($item_number,'chauffeur_payment_pickup_instructions',TRUE);
			// $get_full_dropoff_address = get_post_meta($item_number,'chauffeur_payment_full_dropoff_address',TRUE);
			// $get_dropoff_instructions = get_post_meta($item_number,'chauffeur_payment_dropoff_instructions',TRUE);

			$get_first_name = get_post_meta($item_number,'chauffeur_payment_first_name',TRUE);
			$get_last_name = get_post_meta($item_number,'chauffeur_payment_last_name',TRUE);

			$get_payment_email = get_post_meta($item_number,'chauffeur_payment_email',TRUE);
			$exists = email_exists( $get_payment_email );
			if ( $exists ) {
				$user_id = $exists;
				//That E-mail is registered to user number '$exists';
			} else {
				//That E-mail doesn\'t belong to any registered users on this site
				// Generate the password and create the user
				$user_password = wp_generate_password( 12, false );
				$user_id = wp_create_user( $get_payment_email, $user_password, $get_payment_email );
				
				// Set the nickname
				wp_update_user(
					array(
						'ID'          =>    $user_id,
						'nickname'    =>    $get_first_name,
						'display_name'=>    $get_first_name
					)
				);

				// Set the role
				$user = new WP_User( $user_id );
				$user->set_role( 'subscriber' );
			}

			if( is_int( $user_id ) ) {
				wp_update_post( array( 
					"ID" => $item_number,
					"post_author" => $user_id
				) );
			}

			// Send customer email
			// include ( chauffeur_BASE_DIR . "/includes/templates/email-customer-booking.php");
			// wp_mail($get_payment_email,$customer_email_subject,$customer_email_content,$customer_headers);

			// Send admin email
			// include ( chauffeur_BASE_DIR . "/includes/templates/email-admin-booking.php");
			// wp_mail($chauffeur_data['booking-email'],$admin_email_subject,$admin_email_content,$admin_headers);
			
			// Update booking data
			update_post_meta($data_array["booking_id"], 'chauffeur_payment_status', esc_html__('Paid','chauffeur') );
			update_post_meta($data_array["booking_id"], 'chauffeur_payment_method', esc_html__('Stripe','chauffeur') );
			update_post_meta($data_array["booking_id"], 'chauffeur_payment_payment_reference', $data["payment_intent"] );
			update_post_meta($data_array["booking_id"], 'atb-booking-status', 'processing' );
			
			$RawAmount = get_post_meta($data_array["booking_id"], 'chauffeur_payment_amount_pickup', TRUE );
			$RawAmount2 = get_post_meta($data_array["booking_id"], 'chauffeur_payment_amount_return', TRUE );

			$perc = 20;
			$perc_amount = ($perc / 100) * $RawAmount;
			$sp_amount = $RawAmount - $perc_amount;

			$perc_amount2 = ($perc / 100) * $RawAmount2;
			$sp_amount2 = $RawAmount2 - $perc_amount2;

			update_post_meta($data_array["booking_id"], 'sp_show', 'yes' );
			update_post_meta($data_array["booking_id"], 'guide_amount', $sp_amount );
			update_post_meta($data_array["booking_id"], 'guide_amount_2', $sp_amount2 );

			// Send Email to User/ Admin
            send_booking_success_email_new($data_array["booking_id"]);

			// Send email to Suppliers
			new_order_email_send_to_all_verified_suppliers($data_array["booking_id"]);

			
			// Automatic Authorize it in cabe
				// die();
			/*$dtWhen=$get_pickup_date." ".$get_pickup_time;
			$strContactName=$get_first_name." ".$get_last_name;
			$strContactPhone=$get_phone_num;
			$strContactEmail=$get_payment_email;
			$iRidePassengers=$get_num_passengers;
			$iRideBags=$get_num_bags;
			$strRideVehicle=$get_vehicle_name;
			$strFromAddress=$get_pickup_address;
			$strToAddress=$get_dropoff_address;
			$start=get_coordinates($strFromAddress);
			$strFromLat=$start['lat'];
			$strFromLong=$start['long'];

			$to=get_coordinates($strToAddress);
			$strToLat=$to['lat'];
			$strToLong=$to['long'];


			$dateString=str_replace("/", "-", $dtWhen);
			$date = strtotime($dateString);
			$dtWhen= DateTime::createFromFormat('U', $date);
			

			$ret = sendToCabe("mha-autocab-chauffeur",$dtWhen,$strContactName, $strContactPhone, $strContactEmail, $iRidePassengers, $iRideBags, $strRideVehicle, $strFromAddress, $strFromLat, $strFromLong, $strToAddress, $strToLat, $strToLong, $get_pickup_via);
			*/
			
			$ret = book_there_journey($item_number);
			
			$data_array['authorization_reference'] = strval($ret->AuthorizationReference);
			$data_array['booking_reference'] = strval($ret->BookingReference);
			
			$get_return_journey = get_post_meta($item_number,'chauffeur_payment_return_journey',TRUE);

			if($get_return_journey == 'Return'){
				$get_return_address = get_post_meta($item_number,'chauffeur_payment_return_address',TRUE);
				$get_return_pickup_via = get_post_meta($item_number,'chauffeur_payment_return_pickup_via',TRUE);
				$get_return_dropoff = get_post_meta($item_number,'chauffeur_payment_return_dropoff',TRUE);
				$get_return_date = get_post_meta($item_number,'chauffeur_payment_return_date',TRUE);
				$get_return_time = get_post_meta($item_number,'chauffeur_payment_return_time',TRUE);
				$get_return_trip_distance = get_post_meta($item_number,'chauffeur_payment_return_trip_distance',TRUE);
				$get_return_trip_time = get_post_meta($item_number,'chauffeur_payment_return_trip_time',TRUE);
				$get_return_flight_number = get_post_meta($item_number,'chauffeur_payment_return_flight_number',TRUE);
				$get_return_journey_origin = get_post_meta($item_number,'chauffeur_payment_return_journey_origin',TRUE);
				$get_return_journey_greet = get_post_meta($item_number,'chauffeur_payment_return_journey_greet',TRUE);
			}

			if($get_return_journey == 'Return'){
				$ret = book_return_journey($item_number);
				$data_array['return_authorization_reference'] = strval($ret->AuthorizationReference);
				$data_array['return_booking_reference'] = strval($ret->BookingReference);
			}

			// die();
		}
			
		//return $data_array;

	return $data_array;
}
function chauffeur_stripe_payment($data) {
	
	global $chauffeur_data;
	$data_array = array();
	
	\Stripe\Stripe::setApiKey($chauffeur_data['stripe_secret_key']);
	$pubkey = $chauffeur_data['stripe_publishable_key'];

	if(isset($data['stripeToken']))
	{
		$amount_cents = str_replace(".","",$data["selected-vehicle-price"]);  // Chargeble amount
		$invoiceid = $data["booking_id"];                      // Invoice ID
		$description = "Invoice #" . $invoiceid . " - " . $invoiceid;

		try {
			
			global $chauffeur_data;

			$charge = Stripe_Charge::create(array(		 
				   "amount" => $amount_cents,
				 // "amount" => "1000",
				  "currency" => $chauffeur_data['stripe-currency'],
				  "source" => $data['stripeToken'],
				  "description" => $description,
				  "receipt_email" => $data["email-address"])	  
			);

			if ($charge->card->address_zip_check == "fail") {
				throw new Exception("zip_check_invalid");
			} else if ($charge->card->address_line1_check == "fail") {
				throw new Exception("address_check_invalid");
			} else if ($charge->card->cvc_check == "fail") {
				throw new Exception("cvc_check_invalid");
			}
			// Payment has succeeded, no exceptions were thrown or otherwise caught				

			$result = "success";

		} catch(Stripe_CardError $e) {			

		$error = $e->getMessage();
			$result = "declined";

		} catch (Stripe_InvalidRequestError $e) {
			$result = "declined";		  
		} catch (Stripe_AuthenticationError $e) {
			$result = "declined";
		} catch (Stripe_ApiConnectionError $e) {
			$result = "declined";
		} catch (Stripe_Error $e) {
			$result = "declined";
		} catch (Exception $e) {

			if ($e->getMessage() == "zip_check_invalid") {
				$result = "declined";
			} else if ($e->getMessage() == "address_check_invalid") {
				$result = "declined";
			} else if ($e->getMessage() == "cvc_check_invalid") {
				$result = "declined";
			} else {
				$result = "declined";
			}		  
		}
		
		$data_array["booking_id"] = $data["booking_id"];
		$data_array["payment_status"] = $result;
		
		// If payment is successful add details in database
		if ( $result == 'success' ) {
			
			$item_number = $data_array["booking_id"];
			//$amount = $data["selected-vehicle-price"];
			
			/*$get_trip_type = get_post_meta($item_number,'chauffeur_payment_trip_type',TRUE);
			$get_vehicle_name = get_post_meta($item_number,'chauffeur_payment_item_name',TRUE);
			$get_pickup_address = get_post_meta($item_number,'chauffeur_payment_pickup_address',TRUE);
			$get_pickup_via = get_post_meta($item_number,'chauffeur_payment_pickup_via',FALSE);
			// var_dump($get_pickup_via);
			// die();
			$get_dropoff_address = get_post_meta($item_number,'chauffeur_payment_dropoff_address',TRUE);
			$get_pickup_date = get_post_meta($item_number,'chauffeur_payment_pickup_date',TRUE);
			$get_pickup_time = get_post_meta($item_number,'chauffeur_payment_pickup_time',TRUE);
			$get_trip_distance = get_post_meta($item_number,'chauffeur_payment_trip_distance',TRUE);
			$get_trip_time = get_post_meta($item_number,'chauffeur_payment_trip_time',TRUE);
			$get_flight_number = get_post_meta($item_number,'chauffeur_payment_flight_number',TRUE);
			$get_first_journey_origin = get_post_meta($item_number,'chauffeur_payment_first_journey_origin',TRUE);
			$get_first_journey_greet = get_post_meta($item_number,'chauffeur_payment_first_journey_greet',TRUE);
			$get_additional_details = get_post_meta($item_number,'chauffeur_payment_additional_info',TRUE);

			$get_num_passengers = get_post_meta($item_number,'chauffeur_payment_num_passengers',TRUE);
			$get_num_bags = get_post_meta($item_number,'chauffeur_payment_num_bags',TRUE);
			$get_first_name = get_post_meta($item_number,'chauffeur_payment_first_name',TRUE);
			$get_last_name = get_post_meta($item_number,'chauffeur_payment_last_name',TRUE);
			$get_phone_num = get_post_meta($item_number,'chauffeur_payment_phone_num',TRUE);
			$get_payment_email = get_post_meta($item_number,'chauffeur_payment_email',TRUE);
			*/
			
			// $get_payment_num_hours = get_post_meta($item_number,'chauffeur_payment_num_hours',TRUE);
			// $get_full_pickup_address = get_post_meta($item_number,'chauffeur_payment_full_pickup_address',TRUE);
			// $get_pickup_instructions = get_post_meta($item_number,'chauffeur_payment_pickup_instructions',TRUE);
			// $get_full_dropoff_address = get_post_meta($item_number,'chauffeur_payment_full_dropoff_address',TRUE);
			// $get_dropoff_instructions = get_post_meta($item_number,'chauffeur_payment_dropoff_instructions',TRUE);

			$get_payment_email = get_post_meta($item_number,'chauffeur_payment_email',TRUE);
			$exists = email_exists( $get_payment_email );
			if ( $exists ) {
				$user_id = $exists;
				//That E-mail is registered to user number '$exists';
			} else {
				//That E-mail doesn\'t belong to any registered users on this site
				// Generate the password and create the user
				$user_password = wp_generate_password( 12, false );
				$user_id = wp_create_user( $get_payment_email, $user_password, $get_payment_email );
				
				// Set the nickname
				wp_update_user(
					array(
						'ID'          =>    $user_id,
						'nickname'    =>    $get_first_name,
						'display_name'=>    $get_first_name
					)
				);

				// Set the role
				$user = new WP_User( $user_id );
				$user->set_role( 'subscriber' );
			}

			if( is_int( $user_id ) ) {
				wp_update_post( array( 
					"ID" => $item_number,
					"post_author" => $user_id
				) );
			}

			// Send customer email
			// include ( chauffeur_BASE_DIR . "/includes/templates/email-customer-booking.php");
			// wp_mail($get_payment_email,$customer_email_subject,$customer_email_content,$customer_headers);

			// Send admin email
			// include ( chauffeur_BASE_DIR . "/includes/templates/email-admin-booking.php");
			// wp_mail($chauffeur_data['booking-email'],$admin_email_subject,$admin_email_content,$admin_headers);
			
			// Update booking data
			update_post_meta($data_array["booking_id"], 'chauffeur_payment_status', esc_html__('Paid','chauffeur') );
			update_post_meta($data_array["booking_id"], 'chauffeur_payment_method', esc_html__('Stripe','chauffeur') );

			
			// Automatic Authorize it in cabe
				// die();
			/*$dtWhen=$get_pickup_date." ".$get_pickup_time;
			$strContactName=$get_first_name." ".$get_last_name;
			$strContactPhone=$get_phone_num;
			$strContactEmail=$get_payment_email;
			$iRidePassengers=$get_num_passengers;
			$iRideBags=$get_num_bags;
			$strRideVehicle=$get_vehicle_name;
			$strFromAddress=$get_pickup_address;
			$strToAddress=$get_dropoff_address;
			$start=get_coordinates($strFromAddress);
			$strFromLat=$start['lat'];
			$strFromLong=$start['long'];

			$to=get_coordinates($strToAddress);
			$strToLat=$to['lat'];
			$strToLong=$to['long'];


			$dateString=str_replace("/", "-", $dtWhen);
			$date = strtotime($dateString);
			$dtWhen= DateTime::createFromFormat('U', $date);
			

			$ret = sendToCabe("mha-autocab-chauffeur",$dtWhen,$strContactName, $strContactPhone, $strContactEmail, $iRidePassengers, $iRideBags, $strRideVehicle, $strFromAddress, $strFromLat, $strFromLong, $strToAddress, $strToLat, $strToLong, $get_pickup_via);
			*/
			
			$ret = book_there_journey($item_number);
			
			$data_array['authorization_reference'] = strval($ret->AuthorizationReference);
			$data_array['booking_reference'] = strval($ret->BookingReference);
			
			$get_return_journey = get_post_meta($item_number,'chauffeur_payment_return_journey',TRUE);

			if($get_return_journey == 'Return'){
				$get_return_address = get_post_meta($item_number,'chauffeur_payment_return_address',TRUE);
				$get_return_pickup_via = get_post_meta($item_number,'chauffeur_payment_return_pickup_via',TRUE);
				$get_return_dropoff = get_post_meta($item_number,'chauffeur_payment_return_dropoff',TRUE);
				$get_return_date = get_post_meta($item_number,'chauffeur_payment_return_date',TRUE);
				$get_return_time = get_post_meta($item_number,'chauffeur_payment_return_time',TRUE);
				$get_return_trip_distance = get_post_meta($item_number,'chauffeur_payment_return_trip_distance',TRUE);
				$get_return_trip_time = get_post_meta($item_number,'chauffeur_payment_return_trip_time',TRUE);
				$get_return_flight_number = get_post_meta($item_number,'chauffeur_payment_return_flight_number',TRUE);
				$get_return_journey_origin = get_post_meta($item_number,'chauffeur_payment_return_journey_origin',TRUE);
				$get_return_journey_greet = get_post_meta($item_number,'chauffeur_payment_return_journey_greet',TRUE);
			}

			if($get_return_journey == 'Return'){
				$ret = book_return_journey($item_number);
				$data_array['return_authorization_reference'] = strval($ret->AuthorizationReference);
				$data_array['return_booking_reference'] = strval($ret->BookingReference);
			}

			// die();
		}
			
		//return $data_array;
		
	}
	return $data_array;
}

function book_there_journey($post_id)
{
	$get_pickup_date = get_post_meta($post_id,'chauffeur_payment_pickup_date',TRUE);
	$get_pickup_time = get_post_meta($post_id,'chauffeur_payment_pickup_time',TRUE);
	$dtWhen=$get_pickup_date." ".$get_pickup_time;
	
	$get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
	$get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
	$strContactName=$get_first_name." ".$get_last_name;
	
	$strContactPhone=get_post_meta($post_id,'chauffeur_payment_phone_num',TRUE);
	
	$strContactEmail=get_post_meta($post_id,'chauffeur_payment_email',TRUE);

	$iRidePassengers=get_post_meta($post_id,'chauffeur_payment_num_passengers',TRUE);
	$iRideBags=get_post_meta($post_id,'chauffeur_payment_num_bags',TRUE);
	$strRideVehicle=get_post_meta($post_id,'chauffeur_payment_item_name',TRUE);
	$strFromAddress=get_post_meta($post_id,'chauffeur_payment_pickup_address',TRUE);
	
	$get_pickup_via = get_post_meta($post_id, 'chauffeur_payment_pickup_via', TRUE);
	
	$start=get_coordinates($strFromAddress);
	$strFromLat=$start['lat'];
	$strFromLong=$start['long'];
	$strToAddress=get_post_meta($post_id,'chauffeur_payment_dropoff_address',TRUE);
	$to=get_coordinates($strToAddress);
	$strToLat=$to['lat'];
	$strToLong=$to['long'];
	
	$fileName = get_home_path().'my_debug.txt';
	$file = fopen($fileName, 'wb');
	$format = "M d Y H:i:s";
	fprintf($file, "original dtWhen: %s\n", $dtWhen);
	$dateString=str_replace("/", "-", $dtWhen);
	fprintf($file, "dateString: %s\n", $dateString);

	$date = new DateTime($dateString);
	
	fprintf($file, "date: %s\n", $date->format($format));
	$date = new DateTime($dateString, new DateTimeZone('Europe/London'));
	fprintf($file, "date: %s\n", $date->format($format));

	//$date = strtotime($dateString);
	//$dtWhen= DateTime::createFromFormat('U', $date, new DateTimeZone('Europe/London'));
	$dtWhen = new DateTime($dateString, new DateTimeZone('Europe/London')); 
	
	
	//$currTime = new DateTime("now");
	//fprintf($file, "%s\n", date($format, time()));
	//fprintf($file, "%s\n", date($format, localtime()));
	
	//fprintf($file, gmdate("M d Y H:i:s", mktime(0, 0, 0, 1, 1, 1998)));
	//fprintf($file, gmdate("M d Y H:i:s", time()));
	//fprintf($file, DateTime::createFromFormat('U', $date, new DateTimeZone('Europe/London'))->format('Y-m-d__H_i_s'));
	//fprintf($file, "\n");
	fclose($file);	
	
	$ret = sendToCabe("mha-autocab-chauffeur", $dtWhen, $strContactName, $strContactPhone, $strContactEmail, $iRidePassengers, $iRideBags, $strRideVehicle, $strFromAddress, $strFromLat, $strFromLong, $strToAddress, $strToLat, $strToLong, $get_pickup_via);
	return $ret;
}

function book_return_journey($post_id)
{
	$get_return_address = get_post_meta($post_id,'chauffeur_payment_return_address',TRUE);
	$get_return_pickup_via = get_post_meta($post_id,'chauffeur_payment_return_pickup_via',TRUE);
	$get_return_dropoff = get_post_meta($post_id,'chauffeur_payment_return_dropoff',TRUE);
	$get_return_date = get_post_meta($post_id,'chauffeur_payment_return_date',TRUE);
	$get_return_time = get_post_meta($post_id,'chauffeur_payment_return_time',TRUE);
	$get_return_trip_distance = get_post_meta($post_id,'chauffeur_payment_return_trip_distance',TRUE);
	$get_return_trip_time = get_post_meta($post_id,'chauffeur_payment_return_trip_time',TRUE);
	$get_return_flight_number = get_post_meta($post_id,'chauffeur_payment_return_flight_number',TRUE);
	$get_return_journey_origin = get_post_meta($post_id,'chauffeur_payment_return_journey_origin',TRUE);
	$get_return_journey_greet = get_post_meta($post_id,'chauffeur_payment_return_journey_greet',TRUE);
	$get_vehicle_name = get_post_meta($post_id,'chauffeur_payment_item_name',TRUE);
	$get_num_bags = get_post_meta($post_id,'chauffeur_payment_num_bags',TRUE);
	$get_num_passengers = get_post_meta($post_id,'chauffeur_payment_num_passengers',TRUE);
	$get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
	$get_phone_num = get_post_meta($post_id,'chauffeur_payment_phone_num',TRUE);
	$get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
	$get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
	
	$dtWhen = $get_return_date." ".$get_return_time;
	$strContactName = $get_first_name." ".$get_last_name;
	$strContactPhone = $get_phone_num;
	$strContactEmail = $get_payment_email;
	$iRidePassengers = $get_num_passengers;
	$iRideBags = $get_num_bags;
	$strRideVehicle = $get_vehicle_name;
	$strFromAddress = $get_return_address;
	$strToAddress = $get_return_dropoff;
	
	$start=get_coordinates($strFromAddress);
	$strFromLat=$start['lat'];
	$strFromLong=$start['long'];

	$to=get_coordinates($strToAddress);
	$strToLat=$to['lat'];
	$strToLong=$to['long'];


	$dateString=str_replace("/", "-", $dtWhen);
	//$date = strtotime($dateString);
	//$dtWhen = DateTime::createFromFormat('U', $date, new DateTimeZone('Europe/London'));
	$dtWhen = new DateTime($dateString, new DateTimeZone('Europe/London')); 

	$ret = sendToCabe("mha-autocab-chauffeur", $dtWhen, $strContactName, $strContactPhone, $strContactEmail, $iRidePassengers, $iRideBags, $strRideVehicle, $strFromAddress, $strFromLat, $strFromLong, $strToAddress, $strToLat, $strToLong, $get_return_pickup_via);
	return $ret;
}

function sendToCabe($strCallRef,$dtWhen,$strContactName, $strContactPhone, $strContactEmail, $iRidePassengers, $iRideBags, $strRideVehicle, $strFromAddress, $strFromLat, $strFromLong, $strToAddress, $strToLat, $strToLong,$pickup_via){

	return callBookingAvailability($strCallRef,$dtWhen,$strContactName, $strContactPhone, $strContactEmail, $iRidePassengers, $iRideBags, $strRideVehicle, $strFromAddress, $strFromLat, $strFromLong, $strToAddress, $strToLat, $strToLong,$pickup_via);
}

function callBookingAvailability($strCallRef,$dtWhen,$strContactName, $strContactPhone, $strContactEmail, $iRidePassengers, $iRideBags, $strRideVehicle,$strFromAddress, $strFromLat, $strFromLong,$strToAddress, $strToLat, $strToLong, $pickup_via)
	{
		try
		{
			// $dtWhen=strtotime($dtWhen);
			// $dtWhen2=$dtWhen;


			// $dtWhen->setTimezone(new DateTimeZone('Europe/London'));
			// $strWhen = $dtWhen->format("c");
			// var_dump($strWhen);
			// echo "<hr>";
			// // $dtWhen->setTimezone(new DateTimeZone('Europe/London'));
			// $strWhen2 = $dtWhen2->format("c");
			// var_dump($strWhen2);
			// die();
			// $strWhen = $dtWhen;

			//$strWhen = $dtWhen->modify('-1 hours')->format("c");
			$strWhen = $dtWhen->format("c");
			if ( empty($strWhen) )
			{
				$m_strLastError = "Invalid booking travel time";
				return [];
			}
			
			$viasxml="";

			if(isset($pickup_via) && !empty($pickup_via)){
				//$pickup_via1 = explode(PHP_EOL, $pickup_via);
				$pickup_via1 = $pickup_via;
			}else{
				$pickup_via1 = '';
			}

			#removed old validation - count($pickup_via)>0
			if(!empty($pickup_via1)){
				$vias = [];
				foreach ($pickup_via1 as $key => $value) {
					$vias[$key]['data'] = $value;
					$vias[$key]['lat'] = get_coordinates($value)['lat'];
					$vias[$key]['long'] = get_coordinates($value)['long'];
				}
				
				$viasxml="<Vias>";
				foreach ($vias as $key => $value) {
					$viasxml.="<Via>";
					$viasxml.="<Type>Address</Type>";
					$viasxml.="<Data>".$value['data']."</Data>";
					$viasxml.="<Coordinate>";
					$viasxml.="<Latitude>".$value['lat']."</Latitude>";
					$viasxml.="<Longitude>".$value['long']."</Longitude>";
					$viasxml.="</Coordinate>";
					$viasxml.="</Via>";
				}
				$viasxml.="</Vias>";
			}			
			
			$payload = "<AgentBookingAvailabilityRequest>".getPayloadAgent($strCallRef)."
						<BookingParameters>
							<Source>Autocab Chauffeur</Source>
							<BookingTime>".$strWhen."</BookingTime>
							<PassengerDetails>
								<Name>".toSafeXml($strContactName)."</Name>
								<TelephoneNumber>".toSafeXml($strContactPhone)."</TelephoneNumber>
								<EmailAddress>".toSafeXml($strContactEmail)."</EmailAddress>
							</PassengerDetails>
							<Ride Type='Passenger'>
								<Count>".intVal($iRidePassengers)."</Count>
								<Luggage>".intVal($iRideBags)."</Luggage>
								<Facilities></Facilities>
								<DriverType>Any</DriverType>
								<VehicleType>".toSafeXml($strRideVehicle)."</VehicleType>
							</Ride>
							<Journey>
								<From>
									<Type>Address</Type>
									<Data>".toSafeXml($strFromAddress)."</Data>
									<Coordinate>
										<Latitude>".toSafeXml($strFromLat)."</Latitude>
										<Longitude>".toSafeXml($strFromLong)."</Longitude>
									</Coordinate>
								</From>
								<To>
									<Type>Address</Type>
									<Data>".toSafeXml($strToAddress)."</Data>
									<Coordinate>
										<Latitude>".toSafeXml($strToLat)."</Latitude>
										<Longitude>".toSafeXml($strToLong)."</Longitude>
									</Coordinate>
								</To>
								".$viasxml."
							</Journey>
						</BookingParameters>
					</AgentBookingAvailabilityRequest>";
					// echo "<hr/>Data:<pre>".htmlentities($payload)."</pre>";
					// die();
					$ret	=	call( $payload);
					
					if (( !empty($ret) ) ) {
						
						$ret = callBookingAuthorization("mha-autocab-chauffeur",$ret->AvailabilityReference,"bookin1",$strContactName,$strContactPhone,$strContactEmail,"");
						// echo "<hr/>THIS:<pre>".print_r($ret,1)."</pre>";
						
						//$doc = new DOMDocument();
						//$doc->loadXML($strResponse);
						//if ($doc != FALSE)
						//{
							//update_post_meta($booking_id, 'chauffeur_payment_booking_reference', $strResponse->BookingReference );
						//}
						
						$currTime = new DateTime("now");
						$fileName = get_home_path().'debug'.$currTime->format("Y-m-d__H_i_s-u").'.txt';
						//$file = fopen($fileName, 'wb');
						//fprintf($file, "authorization reference obtained from API %s\n", strval($ret->AuthorizationReference));
						//fprintf($file, "booking reference obtained from API %s\n", strval($ret->BookingReference));
						//if (empty($data_array['booking_id']))
							//fprintf($file, "data_array[booking_id] is empty\n");
						//else
						//{
							//fprintf($file, "booking ID is %s\n", $data_array['booking_id']);
							
							//$updateRes = update_post_meta($data_array['booking_id'], 'chauffeur_payment_booking_reference', $ret->BookingReference);
							//fprintf($file, "updateRes = %d\n", $updateRes);
						//}
						//fprintf($file, "xml:\n");
						
						$dom=new DOMDocument;
						$dom->loadXML($ret->asXML());
						
						//fprintf($file, "%s", $dom->saveXML());
							
						//fclose($file);
						
						//$data_array['booking_reference'] = $ret->BookingReference;
					}
					return $ret;
		}
		catch (Exception $e)
		{
		}
		return [];
	}

function callBookingAuthorization(	$strCallRef,$strAvailabilityRef,$strAgentBookingRef,$strContactName, $strContactPhone, $strContactEmail, $strDriverNote="",$strNotifyAlertEvents="",$strNotifyAlertMethod="None"
)
{
	try
	{
		$payload = 
			"<AgentBookingAuthorizationRequest>
			".getPayloadAgent($strCallRef)."
				<AvailabilityReference>".toSafeXml($strAvailabilityRef)."</AvailabilityReference>
				<AgentBookingReference>".toSafeXml($strAgentBookingRef)."</AgentBookingReference>
				<Passengers>
					<PassengerDetails IsLead='true'>
						<Name>".toSafeXml($strContactName)."</Name>
						<TelephoneNumber>".toSafeXml($strContactPhone)."</TelephoneNumber>
						<EmailAddress>".toSafeXml($strContactEmail)."</EmailAddress>
					</PassengerDetails>
				</Passengers>
				<DriverNote>".toSafeXml($strDriverNote)."</DriverNote>
				<Notifications>
					<VendorEvents>".toSafeXml($strNotifyAlertEvents)."</VendorEvents>
					<AlertMethod>".toSafeXml($strNotifyAlertMethod)."</AlertMethod>
				</Notifications>
			</AgentBookingAuthorizationRequest>
				";
		//echo "<hr/>Data:<pre>".htmlentities($payload)."</pre>";
		return call($payload);
	}
	catch (Exception $e)
	{
	}
	return [];
}

function getPayloadAgent( $strCallRef )
{	
	//add timezone here
	//$now = new DateTime("now");
	//$now = date("c");
	$now = getCurrentLocalTime()->format("c");
	return 
	"<Agent Id='20068'>
		<Password>bLwg9JJ793gvVCb3UP6HxKpD</Password>
		<Reference>".toSafeXml($strCallRef)."</Reference>
		<Time>".$now."</Time>
	</Agent>
	<Vendor Id='72992' />";
}

function toSafeXml( $str )
{
	return htmlspecialchars( $str, ENT_XML1 | ENT_QUOTES );
}

function call( $payload )
{
	//$doc = new DOMDocument();
	//$doc->loadXML($payload);
	//$currTime = new DateTime("now");
	//$xmlFileName = dirname(__FILE__).'/../../../'.$currTime->format("Y-m-d__H_i_s-u");
	//$ending = '_good';
	
	$result = '';
	
	try
	{			
		//echo "<hr/>Data:<pre>".htmlentities($payload)."</pre>";
		
		// CAutocabChauffeur::DoLog( true, __METHOD__, __LINE__, ">>>PAYLOAD=\n".print_r($payload,1) );
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://cxs.autocab.net/api/agent/AgentBookingAvailabilityRequest");
		//curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: text/xml',
			'Content-Length: ' . strlen($payload))
		);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		$result = curl_exec($ch);
		curl_close($ch);
		
		if (empty($result))
		{
			//$ending = '_bad';
			//$xmlFileName = $xmlFileName.'_bad';
		}
		else
		{
			//$xmlFileName = $xmlFileName.'_good';
			//$xmlFileName .= '.xml';
			//$doc->save($xmlFileName);
			
			$xml = new SimpleXMLElement($result, LIBXML_NOERROR | LIBXML_NOWARNING);
			if ( !empty($xml) )
			{
				// CAutocabChauffeur::DoLog( true, __METHOD__, __LINE__, "<<<RESULT=\n".print_r($xml,1) );
				//$xmlFileName .= $ending;
				//$xmlFileName .= '.xml';
				//$doc->save($xmlFileName);					
				return $xml;
			}
		}
	}
	catch (Exception $e)
	{
		//$ending = '_bad';
		
		if ( !empty($result) )
		{
			// $this->m_strLastError = $result;
			//trim(print_r($result));
		}
		else
		{
			//trim(print_r($e->getMessage(),1));
		}
		// $this->m_strLastException	=	$e;
		// CAutocabChauffeur::DoLog( true, __METHOD__, __LINE__, "<<<EXCEPTION=\n".print_r($e,1) );
	}
	//$xmlFileName .= $ending;
	//$xmlFileName .= '.xml';
	//$doc->save($xmlFileName);
	return [];
	//return $result;
}
function trip_details_table($post_id){
	$get_pickup_date = get_post_meta($post_id,'chauffeur_payment_pickup_date',TRUE);
	$get_pickup_time = get_post_meta($post_id,'chauffeur_payment_pickup_time',TRUE);
	$strFromAddress = get_post_meta($post_id,'chauffeur_payment_pickup_address',TRUE);
	$strToAddress = get_post_meta($post_id,'chauffeur_payment_dropoff_address',TRUE);
	$pickup_via = get_post_meta($post_id,'chauffeur_payment_pickup_via',TRUE);

	$get_return_date = get_post_meta($post_id,'chauffeur_payment_return_date',TRUE);
	$get_return_time = get_post_meta($post_id,'chauffeur_payment_return_time',TRUE);
	$get_return_address = get_post_meta($post_id,'chauffeur_payment_return_address',TRUE);
	$get_return_dropoff = get_post_meta($post_id,'chauffeur_payment_return_dropoff',TRUE);
	$return_pickup_via = get_post_meta($post_id,'chauffeur_payment_return_pickup_via',TRUE);

	$vehicle = get_post_meta($post_id,'chauffeur_payment_item_name',TRUE);
	$passenger = get_post_meta($post_id,'chauffeur_payment_num_passengers',TRUE);
	
	$output = '<br>
	<table id="trip-details">
		<tbody>
			<tr>
				<th scope="row">Time:</th>
				<td>'.$get_pickup_date.' - '.$get_pickup_time.'</td>
			</tr>
			<tr>
				<th scope="row">From:</th>
				<td>'.$strFromAddress.'</td>
			</tr>
			<tr>
				<th scope="row">To:</th>
				<td>'.$strToAddress.'</td>
			</tr>';
		if(!empty($pickup_via)){
			$output .= '
			<tr>
				<th scope="row">Via:</th>
				<td>'.implode(', ', $pickup_via).'</td>
			</tr>';
		}else{
			$output .= '';
		}
	$output .= '
			<tr>
				<th scope="row">Vehicle</th>
				<td>'.$vehicle.'</td>
			</tr>
			<tr>
				<th scope="row">Vehicle</th>
				<td>'.$passenger.'</td>
			</tr>
		</tbody>
	</table><br>';
	
	$get_return_journey = get_post_meta($post_id,'chauffeur_payment_return_journey',TRUE);

	if(strcasecmp($get_return_journey, 'Return')==0) {
		$output .= '<p style="text-align: center;font-weight:bold;">Return trip details:</p>
		<table id="trip-details">
			<tbody>
				<tr>
					<th scope="row">Time:</th>
					<td>'.$get_return_date.' - '.$get_return_time.'</td>
				</tr>
				<tr>
					<th scope="row">From:</th>
					<td>'.$get_return_address.'</td>
				</tr>
				<tr>
					<th scope="row">To:</th>
					<td>'.$get_return_dropoff.'</td>
				</tr>';
				if(!empty($return_pickup_via)){
					$output .= '
					<tr>
						<th scope="row">Via:</th>
						<td>'.implode(', ', $return_pickup_via).'</td>
					</tr>';
				}else{
					$output .= '';
				}
			$output .= '</tbody>
			</table><br>';
	}else{
		$output .= '';
	}
	return $output;
}

function supplier_details_table($user_id, $booking_id, $sp_id){
    $get_pickup_date = get_post_meta($booking_id,'chauffeur_payment_pickup_date',TRUE);
	$get_pickup_time = get_post_meta($booking_id,'chauffeur_payment_pickup_time',TRUE);
	$strFromAddress = get_post_meta($booking_id,'chauffeur_payment_pickup_address',TRUE);
	$strToAddress = get_post_meta($booking_id,'chauffeur_payment_dropoff_address',TRUE);
	$pickup_via = get_post_meta($booking_id,'chauffeur_payment_pickup_via',TRUE);

	$get_return_date = get_post_meta($booking_id,'chauffeur_payment_return_date',TRUE);
	$get_return_time = get_post_meta($booking_id,'chauffeur_payment_return_time',TRUE);
	$get_return_address = get_post_meta($booking_id,'chauffeur_payment_return_address',TRUE);
	$get_return_dropoff = get_post_meta($booking_id,'chauffeur_payment_return_dropoff',TRUE);
	$return_pickup_via = get_post_meta($booking_id,'chauffeur_payment_return_pickup_via',TRUE);

	$passengers = get_post_meta($booking_id,'chauffeur_payment_num_passengers',TRUE);
	$vehicle = get_post_meta($booking_id,'chauffeur_payment_item_name',TRUE);

	$fname = get_post_meta($booking_id,'chauffeur_payment_first_name',TRUE);
	$lname = get_post_meta($booking_id,'chauffeur_payment_last_name',TRUE);
	$email = get_post_meta($booking_id,'chauffeur_payment_email',TRUE);
	$phone = get_post_meta($booking_id,'chauffeur_payment_phone_num',TRUE);

	$return_journey = get_post_meta($sp_id, 'atb_return_journey', true);
	
	$get_return_journey = get_post_meta($booking_id,'chauffeur_payment_return_journey',TRUE);

	$user_info = get_userdata($user_id);
	$get_first_name = $user_info->first_name;
	$get_last_name = $user_info->last_name;
	$sup_email = $user_info->user_email;
	$sup_phone_number = get_user_meta( $user_id, 'supplier_phone_number' , true );

    $output = '';

	if(strcasecmp($get_return_journey, 'Return') == 0 && $return_journey == 1){
        $output = '
            <p>We would like to confirm that your taxi booking for <b>'.$get_return_date.' - '.$get_return_time.'</b> has been assigned to <b>'.$get_first_name.' '.$get_last_name.'</b>. Please find below the booking details:</p>
            <p>
                Booking Reference Number: <b>#'.$booking_id.'-RET</b> <br>
                Pickup Date and Time: <b>'.$get_return_date.' - '.$get_return_time.'</b> <br>
                Pickup Location: <b>'.$get_return_address.'</b> <br>
                Drop-off Location: <b>'.$get_return_dropoff.'</b> <br>
                Number of Passengers: <b>'.$passengers.'</b> <br>
                Vehicle Type: <b>'.$vehicle.'</b> <br>
            </p>
            <p>The contact details of the assigned supplier are as follows:</p>
            <p>
                Supplier Name: <b>'.$get_first_name.' '.$get_last_name.'</b> <br>
                Phone Number: <b>'.$sup_phone_number.'</b> <br>
                Email: <b>'.$sup_email.'</b> <br>
            </p>
            <p>We are confident that '.$get_first_name.' '.$get_last_name.' will provide you with a safe and comfortable journey to your destination.</p>
            <p>However, if you encounter any issues with your booking or need to make changes, please do not hesitate to contact us at <a href="tel:03300109709">03300 109 709</a>, and we will be happy to assist you.</p>
            <p>Thank you for choosing our taxi booking service.</p>
        ';
    }else{
        $output = '
            <p>We would like to confirm that your taxi booking for <b>'.$get_pickup_date.' - '.$get_pickup_time.'</b> has been assigned to <b>'.$get_first_name.' '.$get_last_name.'</b>. Please find below the booking details:</p>
            <p>
                Booking Reference Number: <b>#'.$booking_id.'</b> <br>
                Pickup Date and Time: <b>'.$get_pickup_date.' - '.$get_pickup_time.'</b> <br>
                Pickup Location: <b>'.$strFromAddress.'</b> <br>
                Drop-off Location: <b>'.$strToAddress.'</b> <br>
                Number of Passengers: <b>'.$passengers.'</b> <br>
                Vehicle Type: <b>'.$vehicle.'</b> <br>
            </p>
            <p>The contact details of the assigned supplier are as follows:</p>
            <p>
                Supplier Name: <b>'.$get_first_name.' '.$get_last_name.'</b> <br>
                Phone Number: <b>'.$sup_phone_number.'</b> <br>
                Email: <b>'.$sup_email.'</b> <br>
            </p>
            <p>We are confident that '.$get_first_name.' '.$get_last_name.' will provide you with a safe and comfortable journey to your destination.</p>
            <p>However, if you encounter any issues with your booking or need to make changes, please do not hesitate to contact us at <a href="tel:03300109709">03300 109 709</a>, and we will be happy to assist you.</p>
            <p>Thank you for choosing our taxi booking service.</p>
        ';
    }
	return $output;
}

function booking_details_table_sp($booking_id, $sp_id){
	$get_pickup_date = get_post_meta($booking_id,'chauffeur_payment_pickup_date',TRUE);
	$get_pickup_time = get_post_meta($booking_id,'chauffeur_payment_pickup_time',TRUE);
	$strFromAddress = get_post_meta($booking_id,'chauffeur_payment_pickup_address',TRUE);
	$strToAddress = get_post_meta($booking_id,'chauffeur_payment_dropoff_address',TRUE);
	$pickup_via = get_post_meta($booking_id,'chauffeur_payment_pickup_via',TRUE);

	$get_return_date = get_post_meta($booking_id,'chauffeur_payment_return_date',TRUE);
	$get_return_time = get_post_meta($booking_id,'chauffeur_payment_return_time',TRUE);
	$get_return_address = get_post_meta($booking_id,'chauffeur_payment_return_address',TRUE);
	$get_return_dropoff = get_post_meta($booking_id,'chauffeur_payment_return_dropoff',TRUE);
	$return_pickup_via = get_post_meta($booking_id,'chauffeur_payment_return_pickup_via',TRUE);

	$fname = get_post_meta($booking_id,'chauffeur_payment_first_name',TRUE);
	$lname = get_post_meta($booking_id,'chauffeur_payment_last_name',TRUE);
	$email = get_post_meta($booking_id,'chauffeur_payment_email',TRUE);
	$phone = get_post_meta($booking_id,'chauffeur_payment_phone_num',TRUE);

	$return_journey = get_post_meta($sp_id, 'atb_return_journey', true);
	
	$get_return_journey = get_post_meta($booking_id,'chauffeur_payment_return_journey',TRUE);

    if(empty($pickup_via)){
        $pickup_via = '--';
    }
    if(empty($return_pickup_via)){
        $return_pickup_via = '';
    }

	$output = '';
	if(strcasecmp($get_return_journey, 'Return') == 0 && $return_journey == 1){
		$output .= '
        <p style="color: #000;">
            Customer Name: '.$fname.' '.$lname.' <br>
            Contact Number: '.$phone.' <br>
            Email Address: '.$email.' <br>
            Pick-up Address: '.$get_return_address.' <br>';
            if(!empty($return_pickup_via)){
                $output .= 'Additional Pick-ups: '.implode(', ', $return_pickup_via).' <br>';
            }
            $output .= 'Destination Address: '.$get_return_dropoff.' <br>
            Pick-up Date and Time: '.$get_return_date.' - '.$get_return_time.' <br>
        </p>

        <p style="color: #000;">
            Flight Number: '.get_post_meta($booking_id,'chauffeur_payment_return_flight_number',TRUE).' <br>
            Meet and Greet: '.get_post_meta($booking_id,'chauffeur_payment_return_journey_greet',TRUE).' <br>
            Bid Price: '.get_post_meta($sp_id, 'atb_proposed_price', true).' <br>
        </p>';
	}else{
        $output .= '
        <p style="color: #000;">
            Customer Name: '.$fname.' '.$lname.' <br>
            Contact Number: '.$phone.' <br>
            Email Address: '.$email.' <br>
            Pick-up Address: '.$strFromAddress.' <br>';
            if(!empty($pickup_via)){
                $output .= 'Additional Pick-ups: '.implode(', ', $pickup_via).' <br>';
            }
            $output .= 'Destination Address: '.$strToAddress.' <br>
            Pick-up Date and Time: '.$get_pickup_date.' - '.$get_pickup_time.' <br>
        </p>

        <p style="color: #000;">
            Flight Number: '.get_post_meta($booking_id,'chauffeur_payment_return_flight_number',TRUE).' <br>
            Meet and Greet: '.get_post_meta($booking_id,'chauffeur_payment_return_journey_greet',TRUE).' <br>
            Bid Price: '.get_post_meta($sp_id, 'atb_proposed_price', true).' <br>
        </p>';
	}
	return $output;
}


function suppliers_details_table($post_id){
	$atb_invoice_number = get_post_meta($post_id,'atb_invoice_number',TRUE);
	$atb_reference_number = get_post_meta($post_id,'atb_reference_number',TRUE);
	$atb_proposed_price = get_post_meta($post_id,'atb_proposed_price',TRUE);
	
	$output = '<br>
	<table id="trip-details">
		<tbody>
			<tr>
				<th scope="row">Invoice Number:</th>
				<td>'.$atb_invoice_number.'</td>
			</tr>
			<tr>
				<th scope="row">Reference Number:</th>
				<td>'.$atb_reference_number.'</td>
			</tr>
			<tr>
				<th scope="row">Proposed Price:</th>
				<td>£'.$atb_proposed_price.'</td>
			</tr>
		</tbody>
	</table><br>';

	return $output;
}

function send_booking_success_email_new($post_id){
	$get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
	$get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);

	$get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
	$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);


	$options = get_option( 'email-templates-settings-att', array() );
	$subject = str_replace('{{booking_id}}', '#'.$post_id, $options['booking_email_success_subject']);

	$email_data1 = str_replace('{{first_name}}', $get_first_name, $options['booking_email_success_user']);

	$vars1 = array(
		'trip_details' => trip_details_table($post_id),
	);
	foreach($vars1 as $key => $value) {
		$email_data = str_replace('{{'.$key.'}}', $value, $email_data1);
	}

	$vars = array(
		'msg' => wpautop($email_data)
	);


	ob_start();
	include(ATT_PATH . '/includes/emails/main-template.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	foreach($vars as $key => $value) {
		$email_content = str_replace('{{'.$key.'}}', $value, $email_content);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($email, $subject, $email_content, $headers);

	
	/* Send email to Admin */

	$admin_email = get_option('admin_email');

	$subject1 = str_replace('{{booking_id}}', '#'.$post_id, 'New booking request received - {{booking_id}}');

	$email_data11 = '<p>Hi Admin,</p>
	<p>A new booking request received, details are as follows:</p>
	{{trip_details}}';

	$vars11 = array(
		'trip_details' => trip_details_table($post_id),
	);
	foreach($vars11 as $key => $value) {
		$email_data1 = str_replace('{{'.$key.'}}', $value, $email_data11);
	}

	$vars111 = array(
		'msg' => $email_data1
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template-admin.php');
	$email_content11 = ob_get_contents();
	ob_end_clean();
	foreach($vars111 as $key => $value) {
		$email_content1 = str_replace('{{'.$key.'}}', $value, $email_content11);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($admin_email, $subject1, $email_content1, $headers);
}

function suppliers_email_send($user_id, $post_id){
	/* Send email to user */
	$user_info = get_userdata($user_id);
	$get_first_name = $user_info->first_name;
	$get_last_name = $user_info->last_name;

	$get_payment_email = $user_info->user_email;
	$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);


	$options = get_option( 'email-templates-settings-att', array() );
	$invoice_number = get_post_meta($post_id, 'atb_invoice_number', TRUE );
	$subject = str_replace('{{invoice_id}}', '#'.$invoice_number, $options['suppliers_submission_email_subject']);

	$email_data1 = str_replace('{{first_name}}', $get_first_name, $options['suppliers_submission_email_body']);

	$vars1 = array(
		'suppliers_details' => suppliers_details_table($post_id),
	);
	foreach($vars1 as $key => $value) {
		$email_data = str_replace('{{'.$key.'}}', $value, $email_data1);
	}

	$vars = array(
		'msg' => wpautop($email_data)
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	foreach($vars as $key => $value) {
		$email_content = str_replace('{{'.$key.'}}', $value, $email_content);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($email, $subject, $email_content, $headers);
	
	/* Send email to Admin */

	$admin_email = get_option('admin_email');

	$subject1 = str_replace('{{invoice_id}}', '#'.$invoice_number, 'New submission on suppliers portal - {{invoice_id}}');

	$email_data11 = '<p>Hi Admin,</p>
	<p>A new submission on suppliers portal, details are as follows:</p>
	{{suppliers_details}}';

	$vars11 = array(
		'suppliers_details' => suppliers_details_table($post_id),
	);
	foreach($vars11 as $key => $value) {
		$email_data1 = str_replace('{{'.$key.'}}', $value, $email_data11);
	}

	$vars111 = array(
		'msg' => $email_data1
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template-admin.php');
	$email_content11 = ob_get_contents();
	ob_end_clean();
	foreach($vars111 as $key => $value) {
		$email_content1 = str_replace('{{'.$key.'}}', $value, $email_content11);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($admin_email, $subject1, $email_content1, $headers);

}


function suppliers_verification_completed_email_send($user_id){
	/* Send email to user */
	$user_info = get_userdata($user_id);
	$get_first_name = $user_info->first_name;
	$get_last_name = $user_info->last_name;

	$get_payment_email = $user_info->user_email;
	$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);


	$options = get_option( 'email-templates-settings-att', array() );
	
	$subject = $options['suppliers_submission_verifiation_completed_subject'];

	$email_data1 = str_replace('{{first_name}}', $get_first_name, $options['suppliers_submission_verifiation_completed_email_body']);

	$vars1 = array(
		'sp_link' => '<a href="'.get_permalink(11458).'">Login to Suppliers Portal</a>',
	);
	foreach($vars1 as $key => $value) {
		$email_data = str_replace('{{'.$key.'}}', $value, $email_data1);
	}

	$vars = array(
		'msg' => wpautop($email_data)
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	foreach($vars as $key => $value) {
		$email_content = str_replace('{{'.$key.'}}', $value, $email_content);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($email, $subject, $email_content, $headers);
	
}
function suppliers_verification_rejected_email_send($user_id){
	/* Send email to user */
	$user_info = get_userdata($user_id);
	$get_first_name = $user_info->first_name;
	$get_last_name = $user_info->last_name;

	$get_payment_email = $user_info->user_email;
	$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);


	$options = get_option( 'email-templates-settings-att', array() );
	
	$subject = $options['suppliers_submission_verifiation_rejected_subject'];

	$email_data1 = str_replace('{{first_name}}', $get_first_name, $options['suppliers_submission_verifiation_rejected_email_body']);

	$vars1 = array(
		'sp_link' => '<a href="'.get_permalink(11458).'">Upload a Different Document</a>',
	);
	foreach($vars1 as $key => $value) {
		$email_data = str_replace('{{'.$key.'}}', $value, $email_data1);
	}

	$vars = array(
		'msg' => wpautop($email_data)
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	foreach($vars as $key => $value) {
		$email_content = str_replace('{{'.$key.'}}', $value, $email_content);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($email, $subject, $email_content, $headers);
	
}

function suppliers_reupload_email_send($user_id){
	/* Send email to Admin */
	$user_info = get_userdata($user_id);
	$get_first_name = $user_info->first_name;
	$get_last_name = $user_info->last_name;

	$get_payment_email = $user_info->user_email;

	$admin_email = get_option('admin_email');

	$subject1 = 'Suppliers Document Reuploaded by - '.$get_first_name.' '.$get_last_name.' - '.$get_payment_email;

	$email_data12 = '
	<p>A new document re-submission on suppliers portal, details are as follows:</p><br>
	<p><b>Name:</b> '.$get_first_name.' '.$get_last_name.'</p>
	<p><b>Email:</b> '.$get_payment_email.'</p>
	<p><b>Company name:</b> '.get_user_meta($user_id, 'company_name', true).'</p>
	<p><b>Company number:</b> '.get_user_meta($user_id, 'company_number', true).'</p>
	<p><b>VAT number:</b> '.get_user_meta($user_id, 'vat_number', true).'</p>
	<p><b>Verification Document:</b> '.get_user_meta($user_id, 'verification_document', true).'</p>
	<a href="'.admin_url('user-edit.php?user_id='.$user_id).'#spi">Take Action</a>
	';

	$vars111 = array(
		'msg' => $email_data12
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template-admin.php');
	$email_content11 = ob_get_contents();
	ob_end_clean();
	foreach($vars111 as $key => $value) {
		$email_content1 = str_replace('{{'.$key.'}}', $value, $email_content11);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($admin_email, $subject1, $email_content1, $headers);

}

function new_order_email_send_to_all_verified_suppliers($order_id){
	/* Send email to user */
	$args = array(
        'role'    => 'supplier',
        'orderby' => 'user_nicename',
        'order'   => 'ASC',
		'meta_query' => array(
			array(
				'key' => 'verification_status',
				'value' => 'verified',
				'compare' => '==',
			)
		)
    );
    $users = get_users( $args );
	foreach ( $users as $user ) {
		$user_id = $user->ID;
        //$email = $user->user_email;

		$user_info = get_userdata($user_id);
		$get_first_name = $user_info->first_name;
		$get_last_name = $user_info->last_name;
	
		$get_payment_email = $user_info->user_email;
		$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);
	
		$options = get_option( 'email-templates-settings-att', array() );
		
		$subject = $options['suppliers_submission_order_notification_subject'].' #'.$order_id;
	
		$email_data = str_replace('{{first_name}}', $get_first_name, $options['suppliers_submission_order_notification_body']);
	
		$vars = array(
			'msg' => wpautop($email_data)
		);
	
		ob_start();
		include(ATT_PATH . '/includes/emails/main-template.php');
		$email_content = ob_get_contents();
		ob_end_clean();
		foreach($vars as $key => $value) {
			$email_content = str_replace('{{'.$key.'}}', $value, $email_content);
		}
		$headers = array('Content-Type: text/html; charset=UTF-8');
	
		wp_mail($email, $subject, $email_content, $headers);
    }
}

function suppliers_bid_accepted_email_send($user_id, $sp_id, $booking_id){
	/* Send email to user */
	$user_info = get_userdata($user_id);
	$get_first_name = $user_info->first_name;
	$get_last_name = $user_info->last_name;

	$get_payment_email = $user_info->user_email;
	$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);

	$options = get_option( 'email-templates-settings-att', array() );
	
	$subject = $options['suppliers_submission_order_approved_subject'] . ' #' .$booking_id;

	$email_data1 = str_replace('{{first_name}}', $get_first_name, $options['suppliers_submission_order_approved_body']);

	$vars1 = array(
		'booking_details' => booking_details_table_sp($booking_id, $sp_id),
	);

	foreach($vars1 as $key => $value) {
		$email_data = str_replace('{{'.$key.'}}', $value, $email_data1);
	}

	$vars = array(
		'msg' => wpautop($email_data)
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	foreach($vars as $key => $value) {
		$email_content = str_replace('{{'.$key.'}}', $value, $email_content);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($email, $subject, $email_content, $headers);
	
}
function suppliers_confirmation_email_send_to_customer($user_id, $sp_id, $booking_id){
	/* Send email to customer */
	$email = get_post_meta( $booking_id, 'chauffeur_payment_email' , true );
	$user = get_user_by( 'email', $email );
	$customer_id = $user->ID;

	$user_info = get_userdata($customer_id);
	$get_first_name = $user_info->first_name;
	$get_last_name = $user_info->last_name;

	$get_payment_email = $user_info->user_email;

	$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);

	$options = get_option( 'email-templates-settings-att', array() );
	
	$subject = $options['suppliers_confirmation_subject'] . ' #' .$booking_id;

	$email_data1 = str_replace('{{first_name}}', $get_first_name, $options['suppliers_confirmation_body']);

	$vars1 = array(
		'sp_details' => supplier_details_table($user_id, $booking_id, $sp_id),
	);

	foreach($vars1 as $key => $value) {
		$email_data = str_replace('{{'.$key.'}}', $value, $email_data1);
	}

	$vars = array(
		'msg' => wpautop($email_data)
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	foreach($vars as $key => $value) {
		$email_content = str_replace('{{'.$key.'}}', $value, $email_content);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($email, $subject, $email_content, $headers);
}

function get_total_suppliers_bid($post_id){
	$count = 0;
	$query_args = array(
		'post_type' => 'suppliers_portal',
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' => 'atb_reference_number',
				'value' => $post_id,
				'compare' => '=',
			)
		)
	);

	$custom_query = new WP_Query($query_args);
	if ($custom_query->have_posts()) {
		$count = $custom_query->found_posts;
	}
	return $count;
}

function suppliers_bid_cancelled_email_send($user_id, $sp_id, $booking_id){
	/* Send email to user */
	$user_info = get_userdata($user_id);
	$get_first_name = $user_info->first_name;
	$get_last_name = $user_info->last_name;

	$get_payment_email = $user_info->user_email;
	$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);

	$options = get_option( 'email-templates-settings-att', array() );
	
	$subject = $options['suppliers_submission_order_cancelled_subject'] . ' #' .$booking_id;

	$email_data1 = str_replace('{{first_name}}', $get_first_name, $options['suppliers_submission_order_cancelled_body']);

	$vars1 = array(
		'sp_link' => '<a href="'.get_permalink(11458).'">Login to Suppliers Portal</a>',
	);

	foreach($vars1 as $key => $value) {
		$email_data = str_replace('{{'.$key.'}}', $value, $email_data1);
	}

	$vars = array(
		'msg' => wpautop($email_data)
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	foreach($vars as $key => $value) {
		$email_content = str_replace('{{'.$key.'}}', $value, $email_content);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($email, $subject, $email_content, $headers);
	
}

function suppliers_registration_email_send($user_id){
	/* Send email to user */
	$user_info = get_userdata($user_id);
	$get_first_name = $user_info->first_name;
	$get_last_name = $user_info->last_name;

	$get_payment_email = $user_info->user_email;
	$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);


	$options = get_option( 'email-templates-settings-att', array() );
	
	$subject = $options['suppliers_submission_verifiation_pending_subject'];

	$email_data = str_replace('{{first_name}}', $get_first_name, $options['suppliers_submission_verifiation_pending_email_body']);

	$vars = array(
		'msg' => wpautop($email_data)
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template.php');
	$email_content = ob_get_contents();
	ob_end_clean();
	foreach($vars as $key => $value) {
		$email_content = str_replace('{{'.$key.'}}', $value, $email_content);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($email, $subject, $email_content, $headers);
	
	/* Send email to Admin */

	$admin_email = get_option('admin_email');

	$subject1 = 'New registration on suppliers portal - '.$get_first_name.' '.$get_last_name.' - '.$get_payment_email;

	$email_data12 = '
	<p>A new registration on suppliers portal, details are as follows:</p><br>
	<p><b>Name:</b> '.$get_first_name.' '.$get_last_name.'</p>
	<p><b>Email:</b> '.$get_payment_email.'</p>
	<p><b>Company name:</b> '.get_user_meta($user_id, 'company_name', true).'</p>
	<p><b>Company number:</b> '.get_user_meta($user_id, 'company_number', true).'</p>
	<p><b>VAT number:</b> '.get_user_meta($user_id, 'vat_number', true).'</p>
	<p><b>Verification Document:</b> '.get_user_meta($user_id, 'verification_document', true).'</p>
	<a href="'.admin_url('user-edit.php?user_id='.$user_id).'#spi">Take Action</a>
	';

	$vars111 = array(
		'msg' => $email_data12
	);

	ob_start();
	include(ATT_PATH . '/includes/emails/main-template-admin.php');
	$email_content11 = ob_get_contents();
	ob_end_clean();
	foreach($vars111 as $key => $value) {
		$email_content1 = str_replace('{{'.$key.'}}', $value, $email_content11);
	}
	$headers = array('Content-Type: text/html; charset=UTF-8');

	wp_mail($admin_email, $subject1, $email_content1, $headers);

}

function get_customer_email_string($get_first_name, $get_last_name, $get_payment_email)
{
	return $get_first_name." ".$get_last_name." <".$get_payment_email.">";
}

function letter_ending()
{
	global $chauffeur_data;
	return 'Thanks,<br>'.esc_attr($chauffeur_data['email-sender-name']);
}

function get_mail_headers()
{
	global $chauffeur_data;

	$headers = array('MIME-Version: 1.0', 
	'Content-Type: text/html; charset=UTF-8', 
	"From: ".esc_attr($chauffeur_data['email-sender-name'])." <".esc_attr($chauffeur_data['booking-email']).">",
	"Reply-To: " . esc_attr($chauffeur_data['booking-email'])
	);
		
	return $headers;
}


// function to sort fleet posts. 
// it is used on booking page step #2 where user selects a vehicle
// and at price calculator
function sort_fleet($a, $b)
{
	$v1 = get_post_meta($a->ID, 'chauffeur_fleet_order');
	$v2 = get_post_meta($b->ID, 'chauffeur_fleet_order');
	if ($v1 < $v2)
		return -1;
	else if ($v1 == $v2)
		return 0;
	else
		return 1;
}


//////////////////// code for price calculator available for website administrators ////////////////////
//require_once(dirname(__FILE__).'/settings_form.php');

function chauffer_add_admin_menu(){
	add_submenu_page(
		'plugin-settings-atb',
		'Taxi price calculator',
		'Price Calculator',
		'manage_options',
		'taxi-price-calculator',
		'chauffer_display_settings_page'
	);
	add_submenu_page(
		'plugin-settings-atb',
		'Coupons',
		'Coupons',
		'manage_options',
		'edit.php?post_type=atb-coupons'
	);
}
add_action('admin_menu','chauffer_add_admin_menu');

function calculatePrice($distance_value_complete, $fixed_rate, $variable_rate){
	
	$distance_completed = floatval($distance_value_complete);
	
	static $minimum_distance = 2;
	static $maximum_distance = 300;


	$price=0;
	//fixed Pricing
	if($distance_completed<=1){
		$price=$fixed_rate['upto_1_mile'];
	}
	if($distance_completed>=1 && $distance_completed<=5){
		$price=$fixed_rate['from_2_to_5_miles'];
	}
	if($distance_completed>=5 && $distance_completed<=9){
		$price=$fixed_rate['from_6_to_9_miles'];
	}
	if($distance_completed>=9 && $distance_completed<=30){
		$price=$fixed_rate['from_10_to_30_miles'];
	}

	if($distance_completed>30){
		$price=$fixed_rate['from_10_to_30_miles'];
		$extraDistance=$distance_completed-30;
		$extraPrice=0;
		//calculation of price on the basis of 30 KM 
		if($extraDistance>=1){
			$extraDistance1 = $extraDistance-20;
			
			if($extraDistance1<=0){
			 $extraPrice=$extraDistance*$variable_rate['from_31_to_50_miles'];
			}else {
				$extraPrice=20*$variable_rate['from_31_to_50_miles'];
				
			}
			$extraDistance=$extraDistance1;
		}
		
		if($extraDistance>=1){
			$extraDistance1 = $extraDistance-50;
			if($extraDistance1<=0){
			$extraPrice1=$extraDistance*$variable_rate['from_51_to_100_miles'];
			}else {
				$extraPrice1=50*$variable_rate['from_51_to_100_miles'];
				
			}
			$extraPrice = $extraPrice+$extraPrice1;
			$extraDistance=$extraDistance1;
		}
		if($extraDistance>=1){
			$extraDistance1 = $extraDistance-50;
			if($extraDistance1<=0){
			$extraPrice1=$extraDistance*$variable_rate['from_101_to_150_miles'];
			}else {
				$extraPrice1=50*$variable_rate['from_101_to_150_miles'];
				
			}
			$extraDistance=$extraDistance1;
			$extraPrice = $extraPrice+$extraPrice1;
		}
		if($extraDistance>=1){
			$extraDistance1 = $extraDistance-50;
			if($extraDistance1<=0){
			$extraPrice1=$extraDistance*$variable_rate['from_150_and_above'];
			}else {
				$extraPrice1=50*$variable_rate['from_150_and_above'];
			//	$extraDistance=$extraDistance1;
			}
			$extraPrice = $extraPrice+$extraPrice1;
		}
		

		/*
		if($extraDistance<=1){
			$extraPrice=$extraDistance*$variable_rate['from_31_to_50_miles'];
		}
		if($extraDistance>=1 && $extraDistance<=5){
			$extraPrice=$extraDistance*$variable_rate['from_51_to_100_miles'];
		}
		if($extraDistance>=5 && $extraDistance<=9){
			$extraPrice=$extraDistance*$variable_rate['from_101_to_150_miles'];
		}
		if($extraDistance>=9 && $extraDistance<=30){
			$extraPrice=$extraDistance*$variable_rate['from_150_and_above'];
		}*/
		
		$price=$price+$extraPrice;
		
		
	}
	
	return $price;

	// This should never happen, unless you modify $fixed_distances and forget to change the early return conditions
	throw new \Exception(sprintf('Distance %f out of range', $distance_completed));
}

function calculatePriceNewP($outgoing_dist, $fixed_rate_p, $variable_rate_p, $extra_price){
	
	$distance_completed = floatval($outgoing_dist);
	
	static $minimum_distance = 2;
	static $maximum_distance = 300;

	$price = 0;
	$high_to = array();
	$min_price = array();
	foreach($fixed_rate_p as $key => $value){
		if(strlen($value['from']) > 0 && $value['to'] && $value['price']){
			if($distance_completed >= $value['from'] && $distance_completed <= $value['to']){
				$price = $value['price'];
			}
			$high_to[] = $value['to'];
			$min_price[] = $value['price'];
		}
    }
	$high__to = max($high_to);

	if($distance_completed > $high__to){
		foreach($fixed_rate_p as $key => $value){
			if($high__to == $value['to']){
				$price = $value['price'];
			}
		}
		$extraDistance = $distance_completed - $high__to;
		$extraPrice = array();

		if($extraDistance >= 1){
			foreach($variable_rate_p as $key => $value){
				if($value['from'] && $value['to'] && $value['price']){
					$diff1 = $value['to'] - $value['from'];
					$diff = $diff1 + 1;
					$extraDistance1 = $extraDistance - $diff;
					if($extraDistance1 <= 0){
						$extraPrice[] = $extraDistance * $value['price'];
					} else {
						$extraPrice[] = $diff * $value['price'];
					}
					$extraDistance = $extraDistance1;
				}
			}
		}
		$extraPriceNew = array_filter($extraPrice, function($value) {
			return $value > 0;
		});
		$price = $price + array_sum($extraPriceNew);
	}
	
	if($distance_completed == 0){
		$price = min($min_price);
	}
	if(isset($extra_price) && !empty($extra_price)){
		$price  = $price + ($price * ($extra_price / 100));
	}
	return $price;
}
function calculatePriceNewR($distance_return, $fixed_rate_r, $variable_rate_r, $extra_price){
	
	$distance_completed = floatval($distance_return);
	
	static $minimum_distance = 2;
	static $maximum_distance = 300;

	$price = 0;
	$high_to = array();
	$min_price = array();
	foreach($fixed_rate_r as $key => $value){
		if(strlen($value['from']) > 0 && $value['to'] && $value['price']){
			if($distance_completed >= $value['from'] && $distance_completed <= $value['to']){
				$price = $value['price'];
			}
			$high_to[] = $value['to'];
			$min_price[] = $value['price'];
		}
    }
	$high__to = max($high_to);

	if($distance_completed > $high__to){
		foreach($fixed_rate_r as $key => $value){
			if($high__to == $value['to']){
				$price = $value['price'];
			}
		}
		$extraDistance = $distance_completed - $high__to;
		$extraPrice = array();

		if($extraDistance >= 1){
			foreach($variable_rate_r as $key => $value){
				if($value['from'] && $value['to'] && $value['price']){
					$diff1 = $value['to'] - $value['from'];
					$diff = $diff1 + 1;
					$extraDistance1 = $extraDistance - $diff;
					if($extraDistance1 <= 0){
						$extraPrice[] = $extraDistance * $value['price'];
					} else {
						$extraPrice[] = $diff * $value['price'];
					}
					$extraDistance = $extraDistance1;
				}
			}
		}
		$extraPriceNew = array_filter($extraPrice, function($value) {
			return $value > 0;
		});
		$price = $price + array_sum($extraPriceNew);
	}
	
	if($distance_completed == 0){
		$price = min($min_price);
	}
	if(isset($extra_price) && !empty($extra_price)){
		$price  = $price + ($price * ($extra_price / 100));
	}
	return $price;
}

function add_variable($label, $name, $price_array)
{
	/*$var = 0.0;
	if (isset($_POST[$name]))
	{
		$var = trim($_POST[$name]);
		if (is_float($var)==true)
		{
			$price_array[$name] = $var;
		}
	}*/
	//$var = load_one_fleet_price_from_post_variable($name, $price_array);
	
	//if ($var == 0.0)
	$var = $price_array[$name];
	$s = '';
	//$s .= '<div style="display:inline-block;">';
	$s .= '<div class="priceRow">';
		$s .= '<label>'.$label.'</label><br>';
		$s .= '<input type="text" name="'.$name.'" value="'.$var.'"></input><br>';
	$s .= '</div>';	
	return $s;
}

function display_fleet_rates_controls($post_id, $fixed_rate, $variable_rate)
{
	$s = '';
	$s .= '<h2>Fixed Rate</h2>';
	$s .= '<style>.priceRow{display:inline-block; margin-right:5px;}</style>';

	$s .= '<div>';
	$s .= add_variable('Up to 1 mile', 'upto_1_mile', $fixed_rate);
	$s .= add_variable('From 2 to 5 miles', 'from_2_to_5_miles', $fixed_rate);
	$s .= add_variable('From 6 to 9 miles', 'from_6_to_9_miles', $fixed_rate);
	$s .= add_variable('From 10 to 30 miles', 'from_10_to_30_miles', $fixed_rate);
	$s .= '</div>';

	$s .= '<h2>Variable Rate</h2>';
	$s .= '<div>';
	$s .= add_variable('From 31 to 50 miles', 'from_31_to_50_miles', $variable_rate);
	$s .= add_variable('From 51 to 100 miles', 'from_51_to_100_miles', $variable_rate);
	$s .= add_variable('From 101 to 150 miles', 'from_101_to_150_miles', $variable_rate);
	$s .= add_variable('From 150 and above', 'from_150_and_above', $variable_rate);
	$s .= '</div>';
	
	$s .= '<br><input type="submit" name="btnSaveFleetRates" class="button-primary" value="Save Fleet Rates"'.buttonSavePricesClickJavaScript().'/><br>';
	
	return $s;
}

// function checks if $_POST array contains a variable for specified name and returns its value
// if the variable is not floating point value, then appropriace array element is returned from $price_array
function load_one_fleet_price_from_post_variable($name, $price_array)
{
	$var = 0.0;
	if (isset($_POST[$name]))
	{
		$var = trim($_POST[$name]);
		/*if (is_float($var)==true)
		{
			$price_array[$name] = $var;
		}*/
	}
	return $var;
}

function assign_fleet_price_from_post_variable($name, &$price_array)
{
	$price_array[$name] = load_one_fleet_price_from_post_variable($name, $price_array);
}

function load_fleet_prices_from_post_variables(&$fixed_rate, &$variable_rate)
{
	assign_fleet_price_from_post_variable('upto_1_mile',$fixed_rate);
	assign_fleet_price_from_post_variable('from_2_to_5_miles',$fixed_rate);
	assign_fleet_price_from_post_variable('from_6_to_9_miles',$fixed_rate);
	assign_fleet_price_from_post_variable('from_10_to_30_miles',$fixed_rate);
	assign_fleet_price_from_post_variable('from_31_to_50_miles',$variable_rate);
	assign_fleet_price_from_post_variable('from_51_to_100_miles',$variable_rate);
	assign_fleet_price_from_post_variable('from_101_to_150_miles',$variable_rate);
	assign_fleet_price_from_post_variable('from_150_and_above',$variable_rate);
}

// function saves arrays to database
function save_fleet_prices($post_id, $fixed_rate, $variable_rate)
{
	update_field('fixed_rate', $fixed_rate,  $post_id);
	update_field('variable_rate', $variable_rate,  $post_id);
}


function buttonSelectCarClickJavaScript()
{
	$s = 'onclick="document.getElementById(\'form_mode\').value=0; ';
	$s .= 'document.forms.form_taxi_prices.submit();"';
	return $s;
}

function buttonSavePricesClickJavaScript()
{
	$s = 'onclick="document.getElementById(\'form_mode\').value=1; ';
	$s .= 'document.forms.form_taxi_prices.submit();"';
	return $s;
}

// this function displays taxi price calculator and taxi rates prices on settings page
// the function works in few modes:
// mode 0 (default mode): prices are taken from database
// mode 1: is used to save prices to database, prices are taken from $_POST variables
function chauffer_display_settings_page() {
	$form_mode = 0;
	if (isset($_POST['form_mode'])==true)
		$form_mode = $_POST['form_mode'];
	
	$s = '';
	$s .= '<style>';
	$s .= '.div1 select, input { width:200px;}';
	$s .= '</style>';
	$s .= '<div class="div1">';
		$s .= '<h1>Taxi price calculator</h2>';
		$s .= '<form id="form_taxi_prices" method="post" action="'.$_SERVER["REQUEST_URI"].'">';
		$s .= '<label>form mode: '.$form_mode.'</label>';
		$args = array(
			'post_type' => 'fleet',
			'posts_per_page' => '9999',
			'order' => 'ASC'
		);		
		$wp_query = new WP_Query( $args );
		$s .= '<p><label>Vehicle</label><br>';
		
		$vehicle = '';
		if (isset($_POST['vehicle'])){
			$vehicle = $_POST['vehicle'];
		}
		if ($form_mode == 1)
		{
			load_fleet_prices_from_post_variables($fixed_rate, $variable_rate);
			//$s .= '<p><label style="color:blue">value = '.$fixed_rate['test'].'</label></p>';
		}

		
		$s .= '<select name="vehicle" '; 
		if (strlen($vehicle) > 0)
			$s .= 'value="'.$vehicle.'" ';
		$s .= ' >';
		
		
		if ($wp_query->have_posts()){
			usort($wp_query->posts, "sort_fleet"); // sort vehicles by chauffer_fleet_order metadata
			$post_id = 0;
			$s .= '<option selected disabled>Please select</option>';
			foreach($wp_query->posts as $p)
			{
				$s .= '<option';
				if ($p->ID == $vehicle || $vehicle == '')
				{
					$vehicle = $p->post_title;
					$s .= ' selected ';
					if ($form_mode != 1){
						/*
						$fixed_rate = array(
							'upto_1_mile' => get_post_meta($p->ID, 'chauffeur_fr_u1', true),
							'from_2_to_5_miles' => get_post_meta($p->ID, 'chauffeur_fr_25', true),
							'from_6_to_9_miles' => get_post_meta($p->ID, 'chauffeur_fr_69', true),
							'from_10_to_30_miles' => get_post_meta($p->ID, 'chauffeur_fr_1030', true)
						);
						$variable_rate = array(
							'from_31_to_50_miles' => get_post_meta($p->ID, 'chauffeur_vr_3150', true),
							'from_51_to_100_miles' => get_post_meta($p->ID, 'chauffeur_vr_51100', true),
							'from_101_to_150_miles' => get_post_meta($p->ID, 'chauffeur_vr_101150', true),
							'from_150_and_above' => get_post_meta($p->ID, 'chauffeur_vr_150', true)
						);
						*/
						//$message_for_textarea = 'message_for_textarea';
					}
					$post_id = $p->ID;
				}
				$s .= ' value="'.$p->ID.'">'.$p->post_title.'</option>';
			}
			$s .= '</select>';
			
			wp_reset_query();
		}
		$s .= '<input type="button" name="btnSelectCar" class="button-primary" style="margin-left:10px" value="Select Car"'.buttonSelectCarClickJavaScript().'/>';
		$s .= '</p>';
		
		if (isset($_POST['vehicle'])){
			$fleet_id = $_POST['vehicle'];
		}else{
			$fleet_id = '';					
		}

		if( !empty(get_post_meta($fleet_id, '_adv_rates_city', true)) ){
			$adv_rates_city = get_post_meta($fleet_id, '_adv_rates_city', true);
		}else{
			$adv_rates_city = array();
		}
		
		$args = array(
			'post_type' => 'pricing',
			'posts_per_page' => -1
		);
		$s .= '<label style="display: block;font-weight: bold;margin-bottom: 10px;margin-top: 15px;">Select Rates</label>';
		$get_adv_rates = get_posts( $args );

		if(isset($_POST['fleet_rate'])){
			$rate_id = $_POST['fleet_rate'];
		}else{
			$rate_id = '';
		}
		foreach ( $get_adv_rates as $post ) {
			$post_id = $post->ID;
			$city = get_post_meta($post_id, 'city_name', TRUE);
			
			if( !empty(get_post_meta($post_id, '_atb_vehicles', true)) ){
				$atb_vehicles = get_post_meta($post_id, '_atb_vehicles', true);
			}else{
				$atb_vehicles = '';
			}
			$s .= '<label for="adv_rates_city_'.$post_id.'"';
			if($fleet_id == $atb_vehicles){
				$s .= 'style="display: block;margin: 15px 0;"';
			}else{
				$s .= 'style="display: none;"';
			}
			$s .= '><input type="radio" name="fleet_rate" id="adv_rates_city_'.$post_id.'" value="'.$post_id.'"';
			if($rate_id == $post_id){
				$s .= 'checked="checked"';
			}else{
				$s .= '';
			}
			$s .= '>'.get_the_title($post_id).' <i style="background: #d2d2d2;padding: 2px 10px;color: #000;">['.implode(', ', $city).']</i></label>';
		}
		$s .= '<p><label>Outgoing distance, miles</label><br>';
		$distance_outgoing = '';
		if (isset($_POST['distance_outgoing']))
			$distance_outgoing = $_POST['distance_outgoing'];
		$s .= '<input type="number" step="any" name="distance_outgoing" value="'.$distance_outgoing.'" placeholder="Enter miles"></input><br>';
		$s .= '</p>';
		
		$s .= '<p><label>Return distance (optional), miles</label><br>';
		$distance_return = '';
		if (isset($_POST['distance_return']))
			$distance_return = $_POST['distance_return'];
		$s .= '<input type="number" step="any" name="distance_return" value="'.$distance_return.'" placeholder="Enter miles"></input><br>';
		$s .= '</p>';
		$s .= '<input type="submit" name="btnCalcPrice" class="button-primary" value="Calculate"/><br>';
		$s .= '<p><label>Estimated price</label><br>';

		if (isset($_POST['fleet_rate'])) {
			$fixed_rate = get_post_meta($rate_id, 'fr_pricing', true);
			$variable_rate = get_post_meta($rate_id, 'vr_pricing', true);

			if (strlen($distance_return) > 0 && isset($_POST['fleet_rate'])) {
				$price_return = calculatePriceNewR($distance_return, $fixed_rate, $variable_rate);
			}else{
				$price_return = 0;
			}
	
			if (strlen($distance_outgoing) > 0 && isset($_POST['fleet_rate'])) {
				$price_pickup = calculatePriceNewP($distance_outgoing, $fixed_rate, $variable_rate);
			}else{
				$price_pickup = 0;
			}
		}


		//$s .= '<input type="text" value="'.$price.'" disabled></p>';
		if(isset($price_pickup)){
			$s .= '<h4>Outgoing Price: '.$price_pickup.'</h4>';
		}
		if(isset($price_return)){
			$s .= '<h4>Return Price: '.$price_return.'</h4>';
		}
		$s .= '<input type="hidden" id="form_mode" name="form_mode" value="0"></p>';

		$s .= '<hr>';
		//$s .= display_fleet_rates_controls($post_id, $fixed_rate, $variable_rate);

		
		$fr_pricing = get_post_meta($rate_id, 'fr_pricing', true);
		$vr_pricing = get_post_meta($rate_id, 'vr_pricing', true);
		if(is_array($fr_pricing)){
			$s .= '<label style="display: block;font-weight: bold;">Fixed Rate - '.get_the_title($fleet_id).'</label>';
			foreach($fr_pricing as $key => $value){
				if(!empty($value['price'])){
					$s .= '<div class="adv-rates-col ';
					if(!empty($value['price'])){
						$s .= 'bg-f1f1f1';
					}
					$s .= '"><div class="adv-rates-col-block-01">
							<input type="number" name="fr_pricing['.$key.'][from]" value="';
							if(isset($value['from'])){
								$s .= $value['from'];
							}else{
								$s .= '';
							}
							$s .= '">
							to
							<input type="number" name="fr_pricing['.$key.'][to]" value="';
							if(isset($value['to'])){
								$s .= $value['to'];
							}else{
								$s .= '';
							}
							$s .= '">
							miles
						</div>
						<div class="adv-rates-col-block-02">
							<input type="number" name="fr_pricing['.$key.'][price]" placeholder="Enter Price" value="';					
							if(isset($value['price'])){
								$s .= $value['price'];
							}else{
								$s .= '';
							}
							$s .= '">
						</div>
					</div>';
				}
			}
		}
		if(is_array($vr_pricing)){
			$s .= '<label style="display: block;font-weight: bold;">Variable Rates - '.get_the_title($fleet_id).'</label>';
			foreach($vr_pricing as $key => $value){
				if(!empty($value['price'])){
					$s .= '<div class="adv-rates-col ';
					if(!empty($value['price'])){
						$s .= 'bg-f1f1f1';
					}
					$s .= '"><div class="adv-rates-col-block-01">
							<input type="number" name="fr_pricing['.$key.'][from]" value="';
							if(isset($value['from'])){
								$s .= $value['from'];
							}else{
								$s .= '';
							}
							$s .= '">
							to
							<input type="number" name="fr_pricing['.$key.'][to]" value="';
							if(isset($value['to'])){
								$s .= $value['to'];
							}else{
								$s .= '';
							}
							$s .= '">
							miles
						</div>
						<div class="adv-rates-col-block-02">
							<input type="number" name="fr_pricing['.$key.'][price]" placeholder="Enter Price" value="';					
							if(isset($value['price'])){
								$s .= $value['price'];
							}else{
								$s .= '';
							}
							$s .= '">
						</div>
					</div>';
				}
			}
			$s .= '<br><a href="'.get_edit_post_link( $rate_id ).'" class="button-primary" target="_blank">Edit This Rates</a>';
		}
		if ($form_mode == 1)
		{
			save_fleet_prices($post_id, $fixed_rate, $variable_rate);
			$s .= '<p><label style="background-color:#dfd">Prices are saved</label></p>';
		}
		$s .= '</form>';
	$s .= '</div>';
	$s .= '
	<style>
	input[type=number]::-webkit-inner-spin-button, 
	input[type=number]::-webkit-outer-spin-button { 
		-webkit-appearance: none;
		-moz-appearance: none;
		appearance: none;
		margin: 0; 
	}
	.bg-f1f1f1 {
		background: #f1f1f1;
	}
	.adv-rates-main-wrap {
		width: 100%;
		text-align: center;
	}
	.adv-rates-col {
		//width: 22%;
		border: 1px solid #c8c8c8;
		display: inline-block;
		margin: 10px;
	}
	.adv-rates-col-block-01 {
		display: flex;
		gap: 5px;
		justify-content: center;
		border-bottom: 1px solid #c8c8c8;
		padding: 10px;
		align-items: center;
	}
	.adv-rates-col-block-01 input{
		width: 55px;
		text-align: center;
		background: #ffffffa1;
		border: none;
		pointer-events: none;
	}
	.adv-rates-col-block-02 input {
		text-align: center;
		background: #ffffffa1;
		border: none;
		pointer-events: none;
	}
	.adv-rates-col-block-02 {
		padding: 10px;
	}
	.chauffeur-field-wrapper input, .chauffeur-field-wrapper textarea {
		background-color: #fff;
		line-height: 100%;
		margin: 0;
		outline: 0 none;
		padding: 10px 8px;
		width: calc(100% - 16px);
	}
	.chauffeur-field-wrapper {
		margin-bottom: 15px;
	}
	.chauffeur-field-wrapper label {
		font-size: 13px;
		font-weight: bold;
		margin: 0 0 5px 0;
		display: block;
	}
</style>
	';
	echo $s;
}

# Add Supplier field 
add_action( 'show_user_profile', 'crf_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'crf_show_extra_profile_fields' );

function crf_show_extra_profile_fields( $user ) {
	$supplier_phone_number = get_the_author_meta( 'supplier_phone_number', $user->ID );
	$company_name = get_the_author_meta( 'company_name', $user->ID );
	$company_number = get_the_author_meta( 'company_number', $user->ID );
	$vat_number = get_the_author_meta( 'vat_number', $user->ID );
	$verification_document = get_the_author_meta( 'verification_document', $user->ID );
	$verification_status = get_the_author_meta( 'verification_status', $user->ID );
	?>
	<h3 id="spi"><?php esc_html_e( 'Supplier Personal Information', 'crf' ); ?></h3>

	<table class="form-table">
		<tr>
			<th><label for="supplier_phone_number"><?php esc_html_e( 'Supplier Phone Number', 'wordpress' ); ?></label></th>
			<td>
				<input type="text"
			       id="supplier_phone_number"
			       name="supplier_phone_number"
			       value="<?php echo esc_attr( $supplier_phone_number ); ?>"
			       class="regular-text"
				/>
			</td>
		</tr>
		<tr>
			<th><label for="company_name"><?php esc_html_e( 'Company name', 'wordpress' ); ?></label></th>
			<td>
				<input type="text"
			       id="company_name"
			       name="company_name"
			       value="<?php echo esc_attr( $company_name ); ?>"
			       class="regular-text"
				/>
			</td>
		</tr>
		<tr>
			<th><label for="company_number"><?php esc_html_e( 'Company number', 'wordpress' ); ?></label></th>
			<td>
				<input type="text"
			       id="company_number"
			       name="company_number"
			       value="<?php echo esc_attr( $company_number ); ?>"
			       class="regular-text"
				/>
			</td>
		</tr>
		<tr>
			<th><label for="vat_number"><?php esc_html_e( 'VAT number', 'wordpress' ); ?></label></th>
			<td>
				<input type="text"
			       id="vat_number"
			       name="vat_number"
			       value="<?php echo esc_attr( $vat_number ); ?>"
			       class="regular-text"
				/>
			</td>
		</tr>
		<tr>
			<th><label for="verification_document"><?php esc_html_e( 'Verification Document', 'wordpress' ); ?></label></th>
			<td>
				<input type="text"
			       id="verification_document"
			       name="verification_document"
			       value="<?php echo esc_attr( $verification_document ); ?>"
			       class="regular-text"
				/>
			</td>
		</tr>
		<tr>
			<th><label for="verification_status"><?php esc_html_e( 'Verification Status', 'wordpress' ); ?></label></th>
			<td>
				<select name="verification_status" id="verification_status">
					<option value="unverified" <?php if($verification_status == 'unverified'){echo 'selected';}?>>Unverified</option>
					<option value="verified" <?php if($verification_status == 'verified'){echo 'selected';}?>>Verified</option>
					<option value="rejected" <?php if($verification_status == 'rejected'){echo 'selected';}?>>Rejected</option>
				</select>
			</td>
		</tr>
	</table>
	<?php
}

add_action( 'personal_options_update', 'crf_update_profile_fields' );
add_action( 'edit_user_profile_update', 'crf_update_profile_fields' );

function crf_update_profile_fields( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}
	update_user_meta( $user_id, 'supplier_phone_number', trim( $_POST['supplier_phone_number'] ) );
	update_user_meta( $user_id, 'company_name', trim( $_POST['company_name'] ) );
	update_user_meta( $user_id, 'company_number', trim( $_POST['company_number'] ) );
	update_user_meta( $user_id, 'vat_number', trim( $_POST['vat_number'] ) );
	update_user_meta( $user_id, 'verification_document', trim( $_POST['verification_document'] ) );

	$old_meta = get_user_meta( $user_id, 'verification_status' , true );

	$user_meta = get_userdata($user_id);
    $user_roles = $user_meta->roles;
	if(in_array('supplier', $user_roles)){
		if($_POST['verification_status'] == 'verified' && $old_meta != $_POST['verification_status']){
			#send_email
			suppliers_verification_completed_email_send($user_id);
		}else if($_POST['verification_status'] == 'rejected'){
			#send_email
			suppliers_verification_rejected_email_send($user_id);
		}
		update_user_meta( $user_id, 'verification_status', $_POST['verification_status']);			
	}
}
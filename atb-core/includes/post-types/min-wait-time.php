<?php

function create_post_type_min_wait_time() {
	register_post_type('min-wait-time',
		array(
			'labels' => array(
				'name' => esc_html__( 'Min Booking Time', 'chauffeur' ),
                'singular_name' => esc_html__( 'Min Booking Time', 'chauffeur' ),
				'add_new' => esc_html__('Add New', 'chauffeur' ),
				'add_new_item' => esc_html__('Add New' , 'chauffeur' ),
				'edit_item' => esc_html__('Edit' , 'chauffeur' ),
			),
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'rewrite'=> false,
        'exclude_from_search' => true,
        'show_in_menu' => false,
        'has_archive' => false,
		/*'menu_position' => 5,
		'menu_icon' => 'dashicons-admin-post',*/
		'rewrite' => array(
			'slug' => esc_html__('min-wait-time','chauffeur')
		), 
        'supports' => array( 'title', 'author')
	));
}

add_action( 'init', 'create_post_type_min_wait_time' );

// Add the Meta Box  
function atb_min_time_box() {
    add_meta_box( 
        'atb_min_time_box_view',
        esc_html__('Minimum time for bookings','chauffeur'), 
        'atb_min_time_box_render',
        'min-wait-time',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'atb_min_time_box');

function atb_min_time_box_render() {
    ?>
        <select name="atb_min_time_box" id="atb_min_time_box">
            <option selected value="">---Default---</option>
            <option value="60" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '60'){echo 'selected';}?>>1 Hour</option>
            <option value="120" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '120'){echo 'selected';}?>>2 Hours</option>
            <option value="180" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '180'){echo 'selected';}?>>3 Hours</option>
            <option value="240" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '240'){echo 'selected';}?>>4 Hours</option>
            <option value="300" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '300'){echo 'selected';}?>>5 Hours</option>
            <option value="360" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '360'){echo 'selected';}?>>6 Hours</option>
            <option value="420" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '420'){echo 'selected';}?>>7 Hours</option>
            <option value="480" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '480'){echo 'selected';}?>>8 Hours</option>
            <option value="540" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '540'){echo 'selected';}?>>9 Hours</option>
            <option value="600" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '600'){echo 'selected';}?>>10 Hours</option>
            <option value="660" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '660'){echo 'selected';}?>>11 Hours</option>
            <option value="720" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '720'){echo 'selected';}?>>12 Hours</option>
            <option value="1440" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '1440'){echo 'selected';}?>>24 Hours</option>
            <option value="2880" <?php if(get_post_meta(get_the_ID(), 'atb_min_time_box', true) == '2880'){echo 'selected';}?>>48 Hours</option>
        </select>
    <?php
}

// Add the Meta Box  
function add_meta_box_city_list() {
    add_meta_box( 
        'pricing_meta_box_city',
        esc_html__('City List','chauffeur'), 
        'show_meta_box_city_list',
        'min-wait-time',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_meta_box_city_list');

// The Callback  
function show_meta_box_city_list() {
	global $post;
	// Use nonce for verification
	echo '<input type="hidden" name="min_wait_time__meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
?>
    <div class="chauffeur-field-wrapper field-padding clearfix" style="margin-top: 15px;" style="">
        <div class="four-fifths">
            <?php 
                $meta_city_name = get_post_meta($post->ID, 'city_name', true);
                if(is_array($meta_city_name) || is_object($meta_city_name)){
                    $meta_city_name = implode("\n",$meta_city_name);
                }
            ?>
            <div><i>Enter city name one by one, press enter to jump into next line.</i></div>
            <textarea name="city_name" id="city_name" placeholder="Enter City Name (Example: London)" cols="30" rows="10" style="width: 100%;"><?php echo !empty($meta_city_name) ? $meta_city_name : ''; ?></textarea>
        </div>
        <?php //echo get_post_meta($post->ID, 'city_name', true);?>
    </div>

    <hr>
    <h4>City Look Up</h4>
    <div id="locationField" style="margin-bottom: 15px;">
        <input id="autocomplete" placeholder="Enter address here.." type="text" style="width: 100%"></input>
    </div>
    <table id="address" style="border: 1px solid #bebebe;padding: 15px;background: #f7f7f7;">
        <tr>
            <td class="label">City</td>
            <td class="wideField" colspan="3">
                <input class="field" id="locality"></input>
            </td>
        </tr>
        <tr>
            <td class="label">State</td>
            <td class="slimField">
                <input class="field" id="administrative_area_level_1"></input>
            </td>
        </tr>
    </table>
    <script src='https://maps.googleapis.com/maps/api/js?key=AIzaSyBxXDkCSBPquzn_3-Ddzkm8KeVc11P2YqQ&#038;libraries=places&#038;mode=driving&#038;ver=6.0.1' id='googlesearch-js'></script>
    <script>
        jQuery("#autocomplete").on('focus', function () {
            geolocate();
        });

        var placeSearch, autocomplete;
        var componentForm = {locality: 'long_name', administrative_area_level_1: 'short_name',};


        function initialize() {
        autocomplete = new google.maps.places.Autocomplete(
            document.getElementById('autocomplete'), {
            types: ['geocode'],
            componentRestrictions: {
            country: 'uk' } });
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                fillInAddress();
            });
        }

        function fillInAddress() {
            var place = autocomplete.getPlace();

            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                }
            }window.CP.exitedLoop(0);
        }

        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                var geolocation = new google.maps.LatLng(
                position.coords.latitude, position.coords.longitude);


                autocomplete.setBounds(new google.maps.LatLngBounds(geolocation, geolocation));
                });
            }
        }
        initialize();
    </script>
<?php
}

// Save the Data  
function save_min_wait_time_post_types($post_id, $post, $update) {  
    global $suppliers_meta_fields;  
  	
    // Get our form field
    if(isset( $_POST['atb_vehicles_rates'] )) {
        
        $new_atb_vehicles = $_POST['atb_vehicles_rates'];
        $old_meta_atb_vehicles = get_post_meta($post->ID, '_atb_vehicles', true);
        // Update post meta
        if(!empty($old_meta_atb_vehicles)){
            update_post_meta($post->ID, '_atb_vehicles', $new_atb_vehicles);
        } else {
            add_post_meta($post->ID, '_atb_vehicles', $new_atb_vehicles, true);
        }
    }

	$post_data = '';
	
	if(isset($_POST['min_wait_time__meta_box_nonce'])) {
		$post_data = $_POST['min_wait_time__meta_box_nonce'];
	}

    // verify nonce  
    if (!wp_verify_nonce($post_data, basename(__FILE__)))  
        return $post_id;

    // check autosave  
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)  
        return $post_id;

    // check permissions  
    if ('page' == $_POST['post_type']) {  
        if (!current_user_can('edit_page', $post_id))  
            return $post_id;  
        } elseif (!current_user_can('edit_post', $post_id)) {  
            return $post_id;  
    }  
  
    //update_post_meta($post_id, 'city_name', $_POST['city_name']);

    $expld = explode(PHP_EOL, $_POST['city_name']);
    update_post_meta($post_id, 'city_name', $expld);

    update_post_meta($post_id, 'atb_mtgt_price', $_POST['atb_mtgt_price']);
    update_post_meta($post_id, 'atb_gpi_price', $_POST['atb_gpi_price']);
    update_post_meta($post_id, 'atb_min_time_box', $_POST['atb_min_time_box']);
    
    foreach ($_POST['fr_pricing'] as $key => $value) {
        $fr_pricing[$key] = $value;
    }
    foreach ($_POST['vr_pricing'] as $key => $value) {
        $vr_pricing[$key] = $value;
    }

    update_post_meta($post_id, 'fr_pricing', $fr_pricing);
    update_post_meta($post_id, 'vr_pricing', $vr_pricing);
}
add_action('save_post', 'save_min_wait_time_post_types', 10, 3);

add_filter('manage_min-wait-time_posts_columns', 'atb_min_wait_time_head');
function atb_min_wait_time_head( $defaults ) {
    $defaults['city_name']    = 'City';
    $defaults['atb_min_time_box']    = 'Wait Time';
    return $defaults;
}

add_action( 'manage_min-wait-time_posts_custom_column', 'atb_min_wait_time_table_content', 10, 2 );
function atb_min_wait_time_table_content( $column_name, $post_id ) {
    if ($column_name == 'city_name') {
        $cities = get_post_meta( $post_id, 'city_name', true );
        echo ' <i style="background: #d2d2d2;padding: 2px 10px;color: #000;">['.implode(', ', $cities).']</i>';
    }
    if ($column_name == 'atb_min_time_box') {
        $atb_min_time_box = get_post_meta( $post_id, 'atb_min_time_box', true );
        $atb_min_time_box = trim($atb_min_time_box);
        if ( $atb_min_time_box == '60' ) {
            $hours_before_booking_minimum = '1 Hour';
        } elseif ( $atb_min_time_box == '120' ) {
            $hours_before_booking_minimum = '2 Hours';
        } elseif ( $atb_min_time_box == '180' ) {
            $hours_before_booking_minimum = '3 Hours';
        } elseif ( $atb_min_time_box == '240' ) {
            $hours_before_booking_minimum = '4 Hours';
        } elseif ( $atb_min_time_box == '300' ) {
            $hours_before_booking_minimum = '5 Hours';
        } elseif ( $atb_min_time_box == '360' ) {
            $hours_before_booking_minimum = '6 Hours';
        } elseif ( $atb_min_time_box == '420' ) {
            $hours_before_booking_minimum = '7 Hours';
        } elseif ( $atb_min_time_box == '480' ) {
            $hours_before_booking_minimum = '8 Hours';
        } elseif ( $atb_min_time_box == '540' ) {
            $hours_before_booking_minimum = '9 Hours';
        } elseif ( $atb_min_time_box == '600' ) {
            $hours_before_booking_minimum = '10 Hours';
        } elseif ( $atb_min_time_box == '660' ) {
            $hours_before_booking_minimum = '11 Hours';
        } elseif ( $atb_min_time_box == '720' ) {
            $hours_before_booking_minimum = '12 Hours';
        } elseif ( $atb_min_time_box == '780' ) {
            $hours_before_booking_minimum = '13 Hours';
        } elseif ( $atb_min_time_box == '840' ) {
            $hours_before_booking_minimum = '14 Hours';
        } elseif ( $atb_min_time_box == '900' ) {
            $hours_before_booking_minimum = '15 Hours';
        } elseif ( $atb_min_time_box == '960' ) {
            $hours_before_booking_minimum = '16 Hours';
        } elseif ( $atb_min_time_box == '1020' ) {
            $hours_before_booking_minimum = '17 Hours';
        } elseif ( $atb_min_time_box == '1080' ) {
            $hours_before_booking_minimum = '18 Hours';
        }	elseif ( $atb_min_time_box == '1140' ) {
            $hours_before_booking_minimum = '19 Hours';
        } elseif ( $atb_min_time_box == '1200' ) {
            $hours_before_booking_minimum = '20 Hours';
        } elseif ( $atb_min_time_box == '1260' ) {
             $hours_before_booking_minimum = '21 Hours';
        } elseif ( $atb_min_time_box == '1320' ) {
            $hours_before_booking_minimum = '22 Hours';
        } elseif ( $atb_min_time_box == '1380' ) {
            $hours_before_booking_minimum = '23 Hours';
        } elseif ( $atb_min_time_box == '1440' ) {
            $hours_before_booking_minimum = '24 Hours';
        }


        if($atb_min_time_box){
            echo '<p style="font-size:15px;font-weight: bold;color: #6fc8c8;">' . $hours_before_booking_minimum . '</p>';
        }else{
            echo 'Default';
        }
    }
}

<?php

function create_post_type_new_rates() {
	
	register_post_type('pricing', 
		array(
			'labels' => array(
				'name' => esc_html__( 'Rates', 'chauffeur' ),
                'singular_name' => esc_html__( 'Rates', 'chauffeur' ),
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
			'slug' => esc_html__('pricing','chauffeur')
		), 
        'supports' => array( 'title', 'author')
	));
}

add_action( 'init', 'create_post_type_new_rates' );

// Add the Meta Box  
function atb_mtgt_price_box() {
    add_meta_box( 
        'atb_mtgt_price_box_view',
        esc_html__('Meet & Greet Price','chauffeur'), 
        'atb_mtgt_price_box_render',
        'pricing',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'atb_mtgt_price_box');

function atb_mtgt_price_box_render() {
    ?>
        <input type="number" name="atb_mtgt_price" id="atb_mtgt_price" value="<?php if(get_post_meta(get_the_ID(), 'atb_mtgt_price', true)){echo get_post_meta(get_the_ID(), 'atb_mtgt_price', true);}else{echo 10;} ?>" placeholder="Enter Meet & Greet Price">
    <?php
}
// Add the Meta Box  
function atb_global_price_increment_box() {
    add_meta_box( 
        'atb_global_price_increment_box_view',
        esc_html__('Price Increment (%)','chauffeur'), 
        'atb_global_price_increment_box_render',
        'pricing',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'atb_global_price_increment_box');

function atb_global_price_increment_box_render() {
    ?>
        <input type="number" name="atb_gpi_price" id="atb_gpi_price" value="<?php if(get_post_meta(get_the_ID(), 'atb_gpi_price', true)){echo get_post_meta(get_the_ID(), 'atb_gpi_price', true);}else{echo '';} ?>" placeholder="%">
    <?php
}
// Add the Meta Box  
function atb_show_fleet_list() {
    add_meta_box( 
        'atb_show_fleet_lists',
        esc_html__('Vehicles','chauffeur'), 
        'atb_show_fleet_lists_show',
        'pricing',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'atb_show_fleet_list');

function atb_show_fleet_lists_show() {
    if( !empty(get_post_meta(get_the_ID(), '_atb_vehicles', true)) ){
        $atb_vehicles = get_post_meta(get_the_ID(), '_atb_vehicles', true);
    }else{
        $atb_vehicles = '';
    }
    $args = array(
        'post_type' => 'fleet',
        'posts_per_page' => -1
    );
    $get_vehicles_rates = get_posts( $args );
    foreach ( $get_vehicles_rates as $post ) {
        $post_id = $post->ID;
        ?>
        <label for="atb_vehicles_<?php echo $post_id;?>">
            <input type="radio" name="atb_vehicles_rates" id="atb_vehicles_<?php echo $post_id;?>" value="<?php echo $post_id;?>" <?php if($atb_vehicles == $post_id){echo 'checked="checked"';}else{echo '';} ?> > <?php echo get_the_title($post_id);?>
        </label><br>
        <?php
    }
}
/*
add_action( 'save_post', 'save__atb_vehicles' );
function save__atb_vehicles(){
}
*/
// Add the Meta Box  
function atb_add_suppliers_meta_box_rates_city() {
    add_meta_box( 
        'pricing_meta_box_city',
        esc_html__('Rate Type','chauffeur'), 
        'show_new_rates_meta_box_city',
        'pricing',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'atb_add_suppliers_meta_box_rates_city');

// The Callback  
function show_new_rates_meta_box_city() {
	global $post;
	// Use nonce for verification
	echo '<input type="hidden" name="nrates__meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
?>
    <label for="Default">
        <input type="radio" name="atb_rate_type" id="Default" value="default" <?php if(empty(get_post_meta($post->ID, 'city_name', true)) || get_post_meta($post->ID, 'city_name', true) == 'Default'){echo 'checked';}?>> Default
    </label>
    <br>
    <label for="city_based">
        <input type="radio" name="atb_rate_type" id="city_based" value="city_based" <?php if(!empty(get_post_meta($post->ID, 'city_name', true)) && get_post_meta($post->ID, 'city_name', true) != 'Default'){echo 'checked';}?>> City Based
    </label>

    <div class="chauffeur-field-wrapper field-padding clearfix" style="margin-top: 15px;" style="">
        <div class="four-fifths">
            <?php 
                $meta_city_name = get_post_meta($post->ID, 'city_name', true);
                if(is_array($meta_city_name) || is_object($meta_city_name)){
                    $meta_city_name = implode("\n",$meta_city_name);
                }
            ?>
            <i>Enter city name one by one, press enter to jump into next line.</i>
            <textarea name="city_name" id="city_name" placeholder="Enter City Name (Example: London)" cols="30" rows="10"><?php echo !empty($meta_city_name) ? $meta_city_name : ''; ?></textarea>
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
        jQuery( document ).ready(function($) {
            var def_city_name = $('textarea[name=city_name]').val();
            if(def_city_name == 'Default' || def_city_name == ''){
                $("textarea[name=city_name]").hide();
                $('#city_name').val('Default');
                //$("#city_name").prop('disabled', true);
            }
            $('input[type=radio][name=atb_rate_type]').on('change', function() {
                var atb_rate_type = this.value;
                var old_city_name = $('#city_name').val();
                if(atb_rate_type == 'default'){
                $("textarea[name=city_name]").hide();
                    $('#city_name').val('Default');
                }else{
                    $("textarea[name=city_name]").show();
                    $('#city_name').val('');                 
                }
            });
        });
        
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
/*
    $fr_pricing = get_post_meta($post->ID, 'fr_pricing', true);

    //echo json_encode($fr_pricing);
    $distance_completed = 8;
    $price = 0;

    
    foreach($fr_pricing as $key => $value){
        if($distance_completed >= $value['from'] && $distance_completed <= $value['to']){
            $price = $value['price'];
        }
    }

    echo $price;
    */
}

// Add the Meta Box  
function atb_add_suppliers_meta_box_rates() {
    add_meta_box( 
        'pricing_meta_box_fr',
        esc_html__('Fixed Rates','chauffeur'), 
        'show_new_rates_meta_box_fr',
        'pricing',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'atb_add_suppliers_meta_box_rates');

// The Callback  
function show_new_rates_meta_box_fr() {
	global $post;
	// Use nonce for verification
	echo '<input type="hidden" name="nrates__meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
?>

    <div class="adv-rates-main-wrap">
        <?php
            if(!empty(get_post_meta($post->ID, 'fr_pricing', true))){
                $fr_pricing = get_post_meta($post->ID, 'fr_pricing', true);
            }else{
                $fr_pricing = array(
                    '1' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '2' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '3' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '4' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '5' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '6' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '7' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '8' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '9' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '10' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                );
            }
            foreach($fr_pricing as $key => $value){
        ?>
        <div class="adv-rates-col <?php if(!empty($value['price'])){echo 'bg-f1f1f1';}?>">
            <div class="adv-rates-col-block-01">
                <input type="number" name="fr_pricing[<?php echo $key;?>][from]" value="<?php echo isset($value['from']) ? $value['from'] : ''; ?>">
                to
                <input type="number" name="fr_pricing[<?php echo $key;?>][to]" value="<?php echo isset($value['to']) ? $value['to'] : ''; ?>">
                miles
            </div>
            <div class="adv-rates-col-block-02">
                <input type="number" name="fr_pricing[<?php echo $key;?>][price]" placeholder="Enter Price" value="<?php echo isset($value['price']) ? $value['price'] : ''; ?>">
            </div>
        </div>
        <?php
        }
        ?>
    </div>
    
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
<?php
}
// Add the Meta Box  
function atb_add_suppliers_meta_box_rates_02() {
    add_meta_box( 
        'pricing_meta_box_vr',
        esc_html__('Variable Rates','chauffeur'), 
        'show_new_rates_meta_box_vr',
        'pricing',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'atb_add_suppliers_meta_box_rates_02');

// The Callback  
function show_new_rates_meta_box_vr() {
	global $post;
	// Use nonce for verification
	echo '<input type="hidden" name="nrates__meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
?>

<div class="adv-rates-main-wrap">
        <?php
            if(!empty(get_post_meta($post->ID, 'vr_pricing', true))){
                $vr_pricing = get_post_meta($post->ID, 'vr_pricing', true);
            }else{
                $vr_pricing = array(
                    '1' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '2' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '3' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '4' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '5' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '6' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '7' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '8' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '9' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                    '10' => array(
                        'from' => '',
                        'to' => '',
                        'price' => ''
                    ),
                );
            }
                foreach($vr_pricing as $key => $value){
        ?>
        <div class="adv-rates-col <?php if(!empty($value['price'])){echo 'bg-f1f1f1';}?>">
            <div class="adv-rates-col-block-01">
                <input type="number" name="vr_pricing[<?php echo $key;?>][from]" value="<?php echo isset($value['from']) ? $value['from'] : ''; ?>">
                to
                <input type="number" name="vr_pricing[<?php echo $key;?>][to]" value="<?php echo isset($value['to']) ? $value['to'] : ''; ?>">
                miles
            </div>
            <div class="adv-rates-col-block-02">
                <input type="number" name="vr_pricing[<?php echo $key;?>][price]" placeholder="Enter Price" step="any" value="<?php echo isset($value['price']) ? $value['price'] : ''; ?>">
            </div>
        </div>
        <?php
        }
        ?>
    </div>
<?php
}


// Save the Data  
function save_new_rates_pricing_meta($post_id, $post, $update) {  
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
	
	if(isset($_POST['nrates__meta_box_nonce'])) {
		$post_data = $_POST['nrates__meta_box_nonce'];
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
    
    foreach ($_POST['fr_pricing'] as $key => $value) {
        $fr_pricing[$key] = $value;
    }
    foreach ($_POST['vr_pricing'] as $key => $value) {
        $vr_pricing[$key] = $value;
    }

    update_post_meta($post_id, 'fr_pricing', $fr_pricing);
    update_post_meta($post_id, 'vr_pricing', $vr_pricing);
}
add_action('save_post', 'save_new_rates_pricing_meta', 10, 3);

add_filter('manage_pricing_posts_columns', 'atb_pricing_table_head');
function atb_pricing_table_head( $defaults ) {
    $defaults['city_name']    = 'City';
    $defaults['atb_gpi_price']    = 'Price Increment';
    return $defaults;
}

add_action( 'manage_pricing_posts_custom_column', 'atb_pricing_table_content_rates', 10, 2 );
function atb_pricing_table_content_rates( $column_name, $post_id ) {
    if ($column_name == 'city_name') {
        $cities = get_post_meta( $post_id, 'city_name', true );
        echo ' <i style="background: #d2d2d2;padding: 2px 10px;color: #000;">['.implode(', ', $cities).']</i>';
    }
    if ($column_name == 'atb_gpi_price') {
        $atb_gpi_price = get_post_meta( $post_id, 'atb_gpi_price', true );
        if($atb_gpi_price){
            echo '<p style="font-size:15px;font-weight: bold;color: #0b50ff;">' . $atb_gpi_price . '%</p>';
        }else{
            echo 'Unset';
        }
    }
}

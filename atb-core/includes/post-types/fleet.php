<?php
if ( !defined( 'ABSPATH' ) ) exit;

function create_post_type_fleet() {
	
	register_post_type('fleet', 
		array(
			'labels' => array(
				'name' => esc_html__( 'Fleet', 'chauffeur' ),
                'singular_name' => esc_html__( 'Fleet', 'chauffeur' ),
				'add_new' => esc_html__('Add Vehicle', 'chauffeur' ),
				'add_new_item' => esc_html__('Add New Vehicle' , 'chauffeur' )
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
			'slug' => esc_html__('fleet','chauffeur')
		), 
		'supports' => array( 'title','thumbnail')
	));
}

add_action( 'init', 'create_post_type_fleet' );


function fleet_type() {	
    register_taxonomy( esc_html__('fleet-type','chauffeur'), 'fleet', array( 'hierarchical' => true, 'label' => esc_html__('Vehicle Type','chauffeur'), 'query_var' => true, 'rewrite' => true ) );
}
add_action( 'init', 'fleet_type' );

/* -------------------------------------------------------------

	Custom Fixed Rate & Variable rate
	
------------------------------------------------------------- */

// Add the Meta Box  
function add_meta_box_adv_rates() {
    add_meta_box( 
        'meta_box_rates_adv',
        esc_html__('Advanced Rate Rules','chauffeur'), 
        'show__meta_box_adv_rates',
        'fleet',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_meta_box_adv_rates');

function show__meta_box_adv_rates() {
    if( !empty(get_post_meta(get_the_ID(), '_adv_rates_city', true)) ){
        $adv_rates_city = get_post_meta(get_the_ID(), '_adv_rates_city', true);
    }else{
        $adv_rates_city = array();
    }
    $fleet_id = get_the_ID();
    $args = array(
        'post_type' => 'pricing',
        'posts_per_page' => -1
    );
    echo '<label style="display: block;font-weight: bold;margin-bottom: 10px;margin-top: 15px;">Select Rates</label>';
    $get_adv_rates = get_posts( $args );
    foreach ( $get_adv_rates as $post ) {
        $post_id = $post->ID;
        $city = get_post_meta($post_id, 'city_name', TRUE);
        if( !empty(get_post_meta($post_id, '_atb_vehicles', true)) ){
            $atb_vehicles = get_post_meta($post_id, '_atb_vehicles', true);
        }else{
            $atb_vehicles = '';
        }
        ?>
        <label for="adv_rates_city_<?php echo $post_id;?>" <?php if($fleet_id == $atb_vehicles){}else{echo 'style="display: none;"';}?>>
            <input type="checkbox" name="adv_rates_city[]" id="adv_rates_city_<?php echo $post_id;?>" value="<?php echo $post_id;?>" <?php echo (in_array($post_id, $adv_rates_city)) ? 'checked="checked"' : ''; ?> > <?php echo get_the_title($post_id);?>
            <?php 
            $meta = get_post_meta($post_id, 'city_name', true);
            if(is_array($meta) || is_object($meta)){
                echo '<i style="background: #d2d2d2;padding: 2px 10px;color: #000;">['.implode(", ",$meta).']</i>';
            }?>
            <br>
        </label>
        <?php
    }
}
add_action( 'save_post', 'save_adv_rates_meta_box' );
function save_adv_rates_meta_box(){
    global $post;
    // Get our form field
    if(isset( $_POST['adv_rates_city'] ))
    {
        $new_adv_rates_city = $_POST['adv_rates_city'];
        $old_meta_adv_rates_city = get_post_meta($post->ID, '_adv_rates_city', true);
        // Update post meta
        if(!empty($old_meta_adv_rates_city)){
            update_post_meta($post->ID, '_adv_rates_city', $new_adv_rates_city);
        } else {
            add_post_meta($post->ID, '_adv_rates_city', $new_adv_rates_city, true);
        }
    }
}
// Add the Meta Box  
function add_fleet0_meta_box() {  
    add_meta_box(  
        'add_fleet0_meta_box', // $id  
        esc_html__('Old Rates (Deactivated)','chauffeur'), // $title  
        'show_fleet0_meta_box', // $callback  
        'fleet', // $page  
        'normal', // $context  
        'high'); // $priority  
}  
add_action('add_meta_boxes', 'add_fleet0_meta_box');

// Field Array  
$prefix = 'chauffeur_';
$fleet0_meta_fields_1 = array(
	array(  
        'label'=> esc_html__('upto 1 mile','chauffeur'),
        'id'    => $prefix.'fr_u1',
    ),
	array(  
        'label'=> esc_html__('from 2 to 5 miles','chauffeur'),
        'id'    => $prefix.'fr_25',
    ),
	array(  
        'label'=> esc_html__('from 6 to 9 miles','chauffeur'),
        'id'    => $prefix.'fr_69',
    ),
	array(  
        'label'=> esc_html__('from 10 to 30 miles','chauffeur'),
        'id'    => $prefix.'fr_1030',
    ),
);
$fleet0_meta_fields_2 = array(
	array(  
        'label'=> esc_html__('from 31 to 50 miles','chauffeur'),
        'id'    => $prefix.'vr_3150',
    ),
	array(  
        'label'=> esc_html__('from 51 to 100 miles','chauffeur'),
        'id'    => $prefix.'vr_51100',
    ),
	array(  
        'label'=> esc_html__('from 101 to 150 miles','chauffeur'),
        'id'    => $prefix.'vr_101150',
    ),
	array(  
        'label'=> esc_html__('from 150 and above','chauffeur'),
        'id'    => $prefix.'vr_150',
    ),
);

// The Callback  
function show_fleet0_meta_box() {
	global $fleet0_meta_fields_1, $fleet0_meta_fields_2, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="fleet0_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

    ?>
    <div style="opacity:0.4; pointer-events: none;">
    <style>
        .attp_tbl {
            border: #DFDFDF solid 1px;
            background: #fff;
            border-spacing: 0;
            border-radius: 0;
            table-layout: auto;
            padding: 0;
            margin: 0;
            width: 100%;
            clear: both;
        }
        .attp_tbl > thead > tr > th:first-child {
            border-left-width: 0;
        }
        .attp_tbl > thead > tr > th {
            border-color: #E1E1E1;
            border-width: 0 0 1px 1px;
        }
        .attp-input-wrap {
            position: relative;
            overflow: hidden;
        }
        .attp-field input[type="text"], .attp-field input[type="password"], .attp-field input[type="number"], .attp-field input[type="search"], .attp-field input[type="email"], .attp-field input[type="url"], .attp-field textarea, .attp-field select {
            width: 100%;
            padding: 3px 5px;
            resize: none;
            margin: 0;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            font-size: 14px;
            line-height: 1.4;
        }
        .attp_tbl > tbody > tr > th, .attp_tbl > thead > tr > th, .attp_tbl > tbody > tr > td, .attp_tbl > thead > tr > td {
            padding: 8px;
            vertical-align: top;
            background: #fff;
            text-align: left;
            border-style: solid;
            font-weight: normal;
        }
        .attp_tbl > tbody > tr > td {
            border-color: #EDEDED;
            border-width: 1px 0 0 1px;
        }
        .attp_tbl > tbody > tr > td:first-child {
            border-left-width: 0;
        }
        .attp_tbl > tbody > tr:first-child > td {
            border-top-width: 0;
        }
        .attp_hr{
            margin: 20px 0;
            border-color: #d7d7d7;
            border-width: 1px;
            border-top: 0;
        }
    </style>
        <label for="" style="font-weight: bold;margin: 15px 0;display: block;">Fixed Rate</label>
        <table class="attp_tbl">
            <thead>
                <tr>
                    <?php foreach ($fleet0_meta_fields_1 as $field) {?>
                        <th style="width: 25%;">
                            <label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr class="attp-row">
                    <?php foreach ($fleet0_meta_fields_1 as $field) {$meta = get_post_meta($post->ID, $field['id'], true);?>
                        <td class="attp-field" >
                            <div class="attp-input">
                                <div class="attp-input-wrap">
                                    <input type="number" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" value="<?php echo !empty($meta) ? $meta : ''; ?>" step="0.01">
                                </div>
                            </div>
                        </td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>

        <hr class="attp_hr">

        
        <label for="" style="font-weight: bold;margin: 15px 0;display: block;">Variable Rate</label>
        <table class="attp_tbl">
            <thead>
                <tr>
                    <?php foreach ($fleet0_meta_fields_2 as $field) {?>
                        <th style="width: 25%;">
                            <label for="<?php echo $field['id']; ?>"><?php echo $field['label']; ?></label>					
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr class="attp-row">
                    <?php foreach ($fleet0_meta_fields_2 as $field) { $meta = get_post_meta($post->ID, $field['id'], true); ?>
                        <td class="attp-field" >
                            <div class="attp-input">
                                <div class="attp-input-wrap">
                                    <input type="number" id="<?php echo $field['id']; ?>" name="<?php echo $field['id']; ?>" value="<?php echo !empty($meta) ? $meta : ''; ?>" step="0.01">
                                </div>
                            </div>
                        </td>
                    <?php } ?>
                </tr>
            </tbody>
        </table>
        </div>
    <?php
}
// Save the Data  
function save_fleet0_meta($post_id) {  
    global $fleet0_meta_fields_1, $fleet0_meta_fields_2;  
  	
	$post_data = '';
	
	if(isset($_POST['fleet0_meta_box_nonce'])) {
		$post_data = $_POST['fleet0_meta_box_nonce'];
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
  
    // loop through fields and save the data  
    foreach ($fleet0_meta_fields_1 as $field) {
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];  
        if ($new && $new != $old) {  
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } 
    foreach ($fleet0_meta_fields_2 as $field) {
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];  
        if ($new && $new != $old) {  
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    }// end foreach  

}  
add_action('save_post', 'save_fleet0_meta');


/* -------------------------------------------------------------

	Vehicle Details
	
------------------------------------------------------------- */

// Add the Meta Box  
function add_fleet1_meta_box() {  
    add_meta_box(  
        'fleet1_meta_box', // $id  
        esc_html__('Vehicle Details','chauffeur'), // $title  
        'show_fleet1_meta_box', // $callback  
        'fleet', // $page  
        'normal', // $context  
        'high'); // $priority  
}  
add_action('add_meta_boxes', 'add_fleet1_meta_box');



// Field Array  
$prefix = 'chauffeur_';  
$fleet1_meta_fields = array(  	
	array(  
        'label'=> esc_html__('Short Description','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'fleet_short_description',  
        'type'  => 'textarea'
    ),
	array(  
        'label'=> esc_html__('Passenger Capacity','chauffeur'),  
        'desc'  => 'e.g. 2',  
        'id'    => $prefix.'fleet_passenger_capacity',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Bag Capacity','chauffeur'),  
        'desc'  => 'e.g. 2',  
        'id'    => $prefix.'fleet_bag_capacity',  
        'type'  => 'text'
    )
);



// The Callback  
function show_fleet1_meta_box() {
	global $fleet1_meta_fields, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="fleet1_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	foreach ($fleet1_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);

		switch($field['type']) {
			
			// text
			case 'text':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo !empty($meta) ? $meta : ''; ?>"><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
			case 'textarea':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><textarea rows="4" cols="130" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>"><?php echo !empty($meta) ? $meta : ''; ?></textarea><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
		} //end switch
   } // end foreach
}



// Save the Data  
function save_fleet1_meta($post_id) {  
    global $fleet1_meta_fields;  
  	
	$post_data = '';
	
	if(isset($_POST['fleet1_meta_box_nonce'])) {
		$post_data = $_POST['fleet1_meta_box_nonce'];
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
  
    // loop through fields and save the data  
    foreach ($fleet1_meta_fields as $field) {  
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];  
        if ($new && $new != $old) {  
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } // end foreach  
}  
add_action('save_post', 'save_fleet1_meta');



/* -------------------------------------------------------------

	Sidebar Content
	
------------------------------------------------------------- */

// Add the Meta Box  
function add_fleet4_meta_box() {  
    add_meta_box(  
        'fleet4_meta_box', // $id  
        esc_html__('Sidebar Content','chauffeur'), // $title  
        'show_fleet4_meta_box', // $callback  
        'fleet', // $page  
        'normal', // $context  
        'high'); // $priority  
}  
add_action('add_meta_boxes', 'add_fleet4_meta_box');



// Field Array  
$prefix = 'chauffeur_';  
$fleet4_meta_fields = array(  	
	array(  
        'label'=> esc_html__('Sidebar Section Icon 1','chauffeur'),  
        'desc'  => esc_html__('Please use Font Awesome icon codes e.g. "fa-calendar": http://fontawesome.io/cheatsheet/', 'chauffeur'),  
        'id'    => $prefix.'fleet_sidebar_icon_1',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Sidebar Section Title 1','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'fleet_sidebar_title_1',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Sidebar Section Content 1','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'fleet_sidebar_content_1',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Sidebar Section Icon 2','chauffeur'),  
        'desc'  => esc_html__('Please use Font Awesome icon codes e.g. "fa-calendar": http://fontawesome.io/cheatsheet/', 'chauffeur'),  
        'id'    => $prefix.'fleet_sidebar_icon_2',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Sidebar Section Title 2','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'fleet_sidebar_title_2',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Sidebar Section Content 2','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'fleet_sidebar_content_2',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Sidebar Section Icon 3','chauffeur'),  
        'desc'  => esc_html__('Please use Font Awesome icon codes e.g. "fa-calendar": http://fontawesome.io/cheatsheet/', 'chauffeur'),  
        'id'    => $prefix.'fleet_sidebar_icon_3',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Sidebar Section Title 3','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'fleet_sidebar_title_3',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Sidebar Section Content 3','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'fleet_sidebar_content_3',  
        'type'  => 'text'
    ),
);



// The Callback  
function show_fleet4_meta_box() {
	global $fleet4_meta_fields, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="fleet4_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	foreach ($fleet4_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);

		switch($field['type']) {
			
			// text
			case 'text':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo !empty($meta) ? $meta : ''; ?>"><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
			case 'textarea':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><textarea rows="4" cols="130" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>"><?php echo !empty($meta) ? $meta : ''; ?></textarea><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
		} //end switch
   } // end foreach
}



// Save the Data  
function save_fleet4_meta($post_id) {  
    global $fleet4_meta_fields;  
  	
	$post_data = '';
	
	if(isset($_POST['fleet4_meta_box_nonce'])) {
		$post_data = $_POST['fleet4_meta_box_nonce'];
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
  
    // loop through fields and save the data  
    foreach ($fleet4_meta_fields as $field) {  
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];  
        if ($new && $new != $old) {  
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } // end foreach  
}  
add_action('save_post', 'save_fleet4_meta');



/* -------------------------------------------------------------

	Price Details
	
------------------------------------------------------------- */

// Add the Meta Box  
function add_fleet2_meta_box() {  
    add_meta_box(  
        'fleet2_meta_box', // $id  
        esc_html__('Hourly &amp; Distance Pricing','chauffeur'), // $title  
        'show_fleet2_meta_box', // $callback  
        'fleet', // $page  
        'normal', // $context  
        'high'); // $priority  
}  
add_action('add_meta_boxes', 'add_fleet2_meta_box');



// Field Array  
$prefix = 'chauffeur_';  
$fleet2_meta_fields = array(  	
	array(  
        'label'=> esc_html__('"From" Price','chauffeur'),  
        'desc'  => 'Only enter numerical values, e.g. 10.00 for $10.00 - This is the lowest price the vehicle can be hired for',  
        'id'    => $prefix.'fleet_price_from',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Price Per Kilometer or Mile (choose km or miles in Theme Options > Payments in the "Distance Measurement Unit" field)','chauffeur'),  
        'desc'  => 'Only enter numerical values, e.g. 10.00 for $10.00',  
        'id'    => $prefix.'fleet_price_per_mile',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Price Per Hour','chauffeur'),  
        'desc'  => 'Only enter numerical values, e.g. 10.00 for $10.00',  
        'id'    => $prefix.'fleet_price_per_hour',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Price Per Day','chauffeur'),  
        'desc'  => 'Only enter numerical values, e.g. 10.00 for $10.00',  
        'id'    => $prefix.'fleet_price_per_day',  
        'type'  => 'text'
    ),
);



// The Callback  
function show_fleet2_meta_box() {
	global $fleet2_meta_fields, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="fleet2_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	foreach ($fleet2_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);

		switch($field['type']) {
			
			// text
			case 'text':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo !empty($meta) ? $meta : ''; ?>"><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
			case 'textarea':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><textarea rows="4" cols="130" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>"><?php echo !empty($meta) ? $meta : ''; ?></textarea><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
		} //end switch
   } // end foreach
}



// Save the Data  
function save_fleet2_meta($post_id) {  
    global $fleet2_meta_fields;  
  	
	$post_data = '';
	
	if(isset($_POST['fleet2_meta_box_nonce'])) {
		$post_data = $_POST['fleet2_meta_box_nonce'];
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
  
    // loop through fields and save the data  
    foreach ($fleet2_meta_fields as $field) {  
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];  
        if ($new && $new != $old) {  
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } // end foreach  
}  
add_action('save_post', 'save_fleet2_meta');



/* -------------------------------------------------------------

	Flat Price Details
	
------------------------------------------------------------- */

// Add the Meta Box  
function add_fleet3_meta_box() {  
    add_meta_box(  
        'fleet3_meta_box', // $id  
        esc_html__('Flat Rate Pricing','chauffeur'), // $title  
        'show_fleet3_meta_box', // $callback  
        'fleet', // $page  
        'normal', // $context  
        'high'); // $priority  
}  
add_action('add_meta_boxes', 'add_fleet3_meta_box');



// Create price meta box fields array
function create_price_meta_box_fields() {
	
	global $post;
	global $wp_query;
	global $fleet3_meta_fields;

	$args = array(
	'post_type' => 'flat_rate_trips',
	'posts_per_page' => '9999',
	'order' => 'ASC',
	'orderby' => 'title'
	);
	
	$prefix = 'chauffeur_'; 
	$fleet3_meta_fields = array();
	$count = -1;
	
	$myposts1 = get_posts( $args );
	foreach ( $myposts1 as $post1 ) : setup_postdata( $post1 );
		
		$count++;
		
		$chauffeur_flat_rate_trips_pick_up_name = get_post_meta( $post1->ID, 'chauffeur_flat_rate_trips_pick_up_name', true );
		$chauffeur_flat_rate_trips_drop_off_name = get_post_meta( $post1->ID, 'chauffeur_flat_rate_trips_drop_off_name', true );

		$fleet3_meta_fields[$count]['label'] = esc_html__('Price For','chauffeur') . ' ' . $chauffeur_flat_rate_trips_pick_up_name . ' > ' . $chauffeur_flat_rate_trips_drop_off_name;
		$fleet3_meta_fields[$count]['desc'] = esc_html__('Only enter numerical values, e.g. 10.00 for $10.00','chauffeur');
		$fleet3_meta_fields[$count]['id'] =  $prefix . $post1->ID;
		$fleet3_meta_fields[$count]['type'] = 'text';
		
	endforeach; 
	wp_reset_postdata();
	
}



// The Callback  
function show_fleet3_meta_box() {
	
	create_price_meta_box_fields();
	
	global $fleet3_meta_fields;
	global $post;
	
	// Use nonce for verification
	echo '<input type="hidden" name="fleet3_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	foreach ($fleet3_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);

		switch($field['type']) {
			
			// text
			case 'text':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo !empty($meta) ? $meta : ''; ?>"><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
			case 'textarea':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><textarea rows="4" cols="130" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>"><?php echo !empty($meta) ? $meta : ''; ?></textarea><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
		} //end switch
   } // end foreach
}



// Save the Data  
function save_fleet3_meta($post_id) { 
	
	create_price_meta_box_fields();
	
    global $fleet3_meta_fields;  
  	
	$post_data = '';
	
	if(isset($_POST['fleet3_meta_box_nonce'])) {
		$post_data = $_POST['fleet3_meta_box_nonce'];
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
  
    // loop through fields and save the data  
    foreach ($fleet3_meta_fields as $field) {  
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];  
        if ($new && $new != $old) {  
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } // end foreach  
}  
add_action('save_post', 'save_fleet3_meta');


//----------------------------------------------------------
// Add the Meta Box  
function add_fleet5_meta_box() {  
    add_meta_box(  
        'fleet5_meta_box', // $id  
        esc_html__('Display order','chauffeur'), // $title  
        'show_fleet5_meta_box', // $callback  
        'fleet', // $page  
        'normal', // $context  
        'low'); // $priority  
}  
add_action('add_meta_boxes', 'add_fleet5_meta_box');



// Field Array  
$fleet5_meta_fields = array(  	
	array(  
        'label'=> esc_html__('Display order','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'fleet_order',  
        'type'  => 'text'
    )
);

// The Callback  
function show_fleet5_meta_box() 
{

	global $fleet5_meta_fields, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="fleet5_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	foreach ($fleet5_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);

		switch($field['type']) {
			
			// text
			case 'text':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo !empty($meta) ? $meta : ''; ?>"><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
			case 'textarea':
?><div class="chauffeur-field-wrapper field-padding clearfix">
	<div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
	<div class="four-fifths"><textarea rows="4" cols="130" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>"><?php echo !empty($meta) ? $meta : ''; ?></textarea><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
		} //end switch
   } // end foreach
}


// Save the Data  
function save_fleet5_meta($post_id) 
{ 
    global $fleet5_meta_fields;  
  	
	$post_data = '';
	
	if(isset($_POST['fleet1_meta_box_nonce'])) {
		$post_data = $_POST['fleet1_meta_box_nonce'];
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
  
    // loop through fields and save the data  
    foreach ($fleet5_meta_fields as $field) {  
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];  
        if ($new && $new != $old) {  
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } // end foreach  	
}  
add_action('save_post', 'save_fleet5_meta');

?>
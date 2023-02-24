<?php

function create_post_type_payment() {
	
	register_post_type('payment', 
		array(
			'labels' => array(
				'name' => esc_html__( 'Bookings', 'chauffeur' ),
                'singular_name' => esc_html__( 'Booking', 'chauffeur' ),
				'add_new' => esc_html__('Add Booking', 'chauffeur' ),
				'add_new_item' => esc_html__('Add New Booking' , 'chauffeur' ),
				'edit_item' => esc_html__('Edit Booking' , 'chauffeur' ),
			),
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'rewrite'=> false,
        'exclude_from_search' => true,
        'show_in_menu' => false,
        'has_archive' => false,
		/*'menu_position' => 5,
		'menu_icon' => 'dashicons-admin-post',*/
		'rewrite' => array(
			'slug' => esc_html__('payment','chauffeur')
		), 
        'supports' => array( 'title', 'author')
	));
}

add_action( 'init', 'create_post_type_payment' );
function sp_portal_data_meta_box( $post_type, $post ) {
    add_meta_box( 
        'sp-manage-data',
        'Suppliers Portal',
        'render_sp_portal_data_meta',
        'payment',
        'side',
        'low'
    );
}
add_action( 'add_meta_boxes', 'sp_portal_data_meta_box', 10, 2 );

function render_sp_portal_data_meta(){
    ?>
        <div>
            <label for="sp_show">
                <input type="checkbox" name="sp_show" id="sp_show" value="yes" <?php if(get_post_meta(get_the_ID(), 'sp_show', TRUE) == 'yes'){echo 'checked';}?>> Show on Suppliers Portal ?
            </label>
            <label for="guide_amount" style="display: block; margin-top: 15px; margin-bottom: 7px;">Guide Amount - One Way</label>
            <input type="number" name="guide_amount" id="guide_amount"  value="<?php echo !empty(get_post_meta(get_the_ID(), 'guide_amount', TRUE)) ? get_post_meta(get_the_ID(), 'guide_amount', TRUE) : ''; ?>">
            <label for="guide_amount_2" style="display: block; margin-top: 15px; margin-bottom: 7px;">Guide Amount - Return</label>
            <input type="number" name="guide_amount_2" id="guide_amount_2"  value="<?php echo !empty(get_post_meta(get_the_ID(), 'guide_amount_2', TRUE)) ? get_post_meta(get_the_ID(), 'guide_amount_2', TRUE) : ''; ?>">
        </div>
    <?php
}
function adding_custom_meta_boxes( $post_type, $post ) {
    add_meta_box( 
        'booking-action-buttons',
        'Cancel & Refund',
        'render_booking_action_buttons',
        'payment',
        'side',
        'low'
    );
}
add_action( 'add_meta_boxes', 'adding_custom_meta_boxes', 10, 2 );

function render_booking_action_buttons(){
    ?>
    <style>
        .button-red {
            background: #f43a3a!important;
            border-color: #f43a3a!important;
            display: block!important;
            text-align: center!important;
            margin: 15px 0!important;
        }
        .button-red2 {
            background: #ff0040 !important;
            border-color: #ff0040!important;
            display: block!important;
            text-align: center!important;
        }
    </style>
        <div class="button-group-payment-actions-wrap">
            <p><?php echo get_post_meta(get_the_ID(), 'chauffeur_payment_additional_info', TRUE);?></p>
        </div>
        
    <?php
}
function atb_booking_status_fnc() {
    add_meta_box( 'atb-booking-status', 'Booking Status', 'atb_booking_status_render', 'payment', 'side', 'high' );
}
add_action( 'add_meta_boxes', 'atb_booking_status_fnc' );


function atb_booking_status_render( $post ) {
    $atb_booking_status = get_post_meta(get_the_ID(), 'atb-booking-status', TRUE);
    ?>
    <p>
        <?php if( get_post_meta(get_the_ID(), 'chauffeur_payment_status', TRUE) != 'Refunded') { ?>
        <select name='atb-booking-status' id='atb-booking-status' required style="width: 100%;">
            <option value="" selected disabled>Please Select</option>
            <option value="pending" <?php selected( $atb_booking_status, 'pending' ); ?>>Pending</option>
            <option value="processing" <?php selected( $atb_booking_status, 'processing' ); ?>>Processing</option>
            <option value="completed" <?php selected( $atb_booking_status, 'completed' ); ?>>Completed</option>
            <option value="cancelled" <?php selected( $atb_booking_status, 'cancelled' ); ?>>Cancelled</option>
            <option value="deleted" <?php selected( $atb_booking_status, 'deleted' ); ?>>Deleted</option>
            <option value="incomplete" <?php selected( $atb_booking_status, 'incomplete' ); ?>>Incomplete</option>
            <?php if( get_post_meta(get_the_ID(), 'chauffeur_payment_status', TRUE) == 'Paid' && get_post_meta(get_the_ID(), 'chauffeur_payment_payment_reference', TRUE)) { ?>
                <option value="cancel_refund" <?php selected( $atb_booking_status, 'cancel_refund' ); ?>>Cancel & Refund</option>
            <?php }?>
        </select>
        <input type="hidden" name="atb-booking-status-old" value="<?php echo get_post_meta(get_the_ID(), 'atb-booking-status', TRUE);?>">
        <?php }else{
            echo '<p style="background: #ef3f55; font-size: 16px; color: #fff; text-align: center; padding: 5px; border-radius: 5px;">Canceled & Refunded</p>';
            echo '<input type="hidden" name="atb-booking-status" id="atb-booking-status" value="cancel_refund">';
        }?>
        
        <select name="refund_reason" style="width: 100%;margin-top: 15px;display:none;" id="refund_reason">
            <option value="" selected disabled>Select Refund Reason</option>
            <option value="duplicate">duplicate</option>
            <option value="fraudulent">fraudulent</option>
            <option value="requested_by_customer">requested_by_customer</option>
            <option value="expired_uncaptured_charge">expired_uncaptured_charge</option>
        </select>
        <script>
            jQuery( document ).ready(function($) {
                $('#atb-booking-status').on('change', function (e) {
                    var optionSelected = $(this).find("option:selected");
                    var book_status = optionSelected.val();
                    if(book_status == 'cancel_refund'){
                        $("#refund_reason").attr("required", true);
                        $('#refund_reason').show();
                    }else{
                        $("#refund_reason").attr("required", false);
                        $('#refund_reason').hide();
                    }
                });
                
                <?php if(get_post_meta(get_the_ID(), 'chauffeur_payment_status', TRUE) == 'Paid'){?>
                    $(".submitdelete.deletion").attr("href", "#");
                    $('.submitdelete.deletion').on('click', function (e) {
                        alert('Sorry you can not delete this booking. First Cancel the booking then you will be able to delete it.');
                        return false;
                    });
                <?php }?>
            });
        </script>
        <style>
            #submitpost > #minor-publishing {
                display: none;
            }
        </style>
    </p>
    <?php   
}
// Add the Meta Box  
function add_payment_meta_box() {
    add_meta_box( 
        'payment_meta_box',
        esc_html__('Booking Details','chauffeur'), 
        'show_payment_meta_box',
        'payment',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_payment_meta_box');

// Field Array  
$prefix = 'chauffeur_';  
$payment_meta_fields = array(
	array(  
        'label'=> esc_html__('Payment Status','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_status',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Payment Amount','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_amount',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Payment Method Selected','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_method',  
        'type'  => 'text'
    ),
	array( 
        'label'=> esc_html__('Trip Type','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_trip_type',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Vehicle Type Requested','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_item_name',  
        'type'  => 'text'
    ),
	array( 
        'label'=> esc_html__('Pickup Address','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_pickup_address',  
        'type'  => 'text'
    ),
	// array(  
    //     'label'=> esc_html__('Full Pickup Address','chauffeur'),  
    //     'desc'  => '',  
    //     'id'    => $prefix.'payment_full_pickup_address',  
    //     'type'  => 'text'
    // ),
	// array(  
    //     'label'=> esc_html__('Pickup Instructions','chauffeur'),  
    //     'desc'  => '',  
    //     'id'    => $prefix.'payment_pickup_instructions',  
    //     'type'  => 'text'
    // ),
    array(  
        'label'=> esc_html__('Pickup Via Address','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_pickup_via',  
        'type'  => 'textarea'
    ),
	array(  
        'label'=> esc_html__('Dropoff Address','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_dropoff_address',  
        'type'  => 'text'
    ),
	// array(  
    //     'label'=> esc_html__('Full Dropoff Address','chauffeur'),  
    //     'desc'  => '',  
    //     'id'    => $prefix.'payment_full_dropoff_address',  
    //     'type'  => 'text'
    // ),
	// array(  
    //     'label'=> esc_html__('Dropoff Instructions','chauffeur'),  
    //     'desc'  => '',  
    //     'id'    => $prefix.'payment_dropoff_instructions',  
    //     'type'  => 'text'
    // ),
	array(  
        'label'=> esc_html__('Pickup Date','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_pickup_date',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Pickup Time','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_pickup_time',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Estimated Trip Distance','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_trip_distance',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Estimated Trip Time','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_trip_time',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Flight Number','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_flight_number',  
        'type'  => 'text'
    ),
	// array(  
    //     'label'=> esc_html__('Number Hours','chauffeur'),  
    //     'desc'  => '',  
    //     'id'    => $prefix.'payment_num_hours',  
    //     'type'  => 'text'
    // ),
    array(  
        'label'=> esc_html__('First Journey Origin','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_first_journey_origin',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('First Journey Greet','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_first_journey_greet',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Return','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_journey',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Return Address','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_address',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Return Via','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_pickup_via',  
        'type'  => 'textarea'
    ),
    array(  
        'label'=> esc_html__('Return Dropoff','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_dropoff',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Return Date','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_date',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Return Time','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_time',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Estimated Return Trip Distance','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_trip_distance',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Estimated Return Trip Time','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_trip_time',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Return Flight Number','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_flight_number',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Return Journey Origin','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_journey_origin',  
        'type'  => 'text'
    ),
    array(  
        'label'=> esc_html__('Return Journey Greet','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_return_journey_greet',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Number of Passengers','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_num_passengers',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Number of Bags','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_num_bags',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('First Name','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_first_name',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Last Name','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_last_name',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Email','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_email',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Phone Number','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_phone_num',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Additional Info','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_additional_info',  
        'type'  => 'textarea'
    ),
	array(  
        'label'=> esc_html__('Stripe Payment Reference','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'payment_payment_reference',  
        'type'  => 'text'
    ),/*,
		array(
		'label'=> esc_html__('Authorization reference','chauffeur'),  
		'desc'  => '',  
        'id'    => $prefix.'payment_authorization_reference',  
		'type'  => 'text'
    ),
	array(
		'label'=> esc_html__('Booking reference','chauffeur'),  
		'desc'  => '',  
        'id'    => $prefix.'payment_booking_reference',  
		'type'  => 'text'
    )*/
	
);

// The Callback  
function show_payment_meta_box() {
	global $payment_meta_fields, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="payment_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
	foreach ($payment_meta_fields as $field) {
		// get value of this field if it exists for this post
		$meta = get_post_meta($post->ID, $field['id'], true);
        if(is_array($meta) || is_object($meta)){
            $meta = implode("\n",$meta);
        }

		switch($field['type']) {
			
			// text
			case 'text':
    ?>
    <div class="chauffeur-field-wrapper field-padding clearfix">
        <div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
        <div class="four-fifths">
            <input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo !empty($meta) ? $meta : ''; ?>">
            <?php 
                if($field['id'] == 'chauffeur_payment_amount'){
                    echo 'Oneway cost: <b>'.get_post_meta($post->ID, 'chauffeur_payment_amount_pickup', TRUE).'</b>';
                    if(get_post_meta($post->ID, 'chauffeur_payment_amount_return', TRUE)){
                        echo  ' | '.'Return cost: <b>'.get_post_meta($post->ID, 'chauffeur_payment_amount_return', TRUE).'</b>';
                    }
                }
            ?>
            
        </div>
    </div>
    
    <?php
		break;
			
		case 'textarea':
    ?>
    <div class="chauffeur-field-wrapper field-padding clearfix">
        <div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
        <div class="four-fifths"><textarea rows="10" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>"><?php echo !empty($meta) ? $meta : ''; ?></textarea></div>
        <?php
            
            if($field['id'] == 'chauffeur_payment_pickup_via'){
                $rr = explode(PHP_EOL, $meta);
            }
        ?>
    </div>
    
    <?php
		break;
			
		} //end switch
   } // end foreach
   ?>
    <style>
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
function add_sp_order_bids_meta_box() {
    add_meta_box( 
        'orders_sp_bids',
        esc_html__('Supplier Bids','chauffeur'), 
        'show_orders_sp_bids',
        'payment',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_sp_order_bids_meta_box');

function show_orders_sp_bids(){
    $order_id = get_the_ID();
    $args = array(
		'post_type' => 'suppliers_portal',
		'post_status' => 'publish',
		'posts_per_page' => 1,
		'meta_query' => array(
			array(
				'key' => 'atb_reference_number',
				'value' => trim($order_id),
				'compare' => '==',
			)
		)
	);

	$wp_query = new WP_Query( $args );
    
    if ($wp_query->have_posts()){
        echo '
        <table>
        <tr>
            <th>Date</th>
            <th>Invoice No.</th>
            <th>Trip Details</th>
            <th>Supplier Name</th>
            <th>Proposed Price</th>
            <th>Options</th>
        </tr>';
        foreach($wp_query->posts as $post){
			$post_id = $post->ID;
            echo '<tr>
            <td>'.get_the_date( 'F j, Y', $post_id ).'</td>
            <td>'.get_post_meta($post_id, 'atb_invoice_number', true).'</td><td>';

            $booking_id = get_post_meta($post_id, 'atb_reference_number', true);

            if(get_post_meta($post_id, 'atb_return_journey', true) == 1){
                echo '<span style="background: #007cff;color: #fff;padding: 2px 6px;border-radius: 5px;">Return</span><br>';
                echo 'Pickup Date & Time: '.get_post_meta($booking_id,'chauffeur_payment_return_date',TRUE).' '.get_post_meta($booking_id,'chauffeur_payment_return_time',TRUE).'<br>';
                echo 'From: '.get_post_meta($booking_id,'chauffeur_payment_return_address',TRUE).'<br>';
                echo 'To: '.get_post_meta($booking_id,'chauffeur_payment_return_dropoff',TRUE).'<br>';
                if(!empty(get_post_meta($booking_id,'chauffeur_payment_return_pickup_via',TRUE))){
                    echo 'Via: '.implode(', ', get_post_meta($booking_id,'chauffeur_payment_return_pickup_via',TRUE));
                }
            }else{
                echo 'Pickup Date & Time: '.get_post_meta($booking_id,'chauffeur_payment_pickup_date',TRUE).' '.get_post_meta($booking_id,'chauffeur_payment_pickup_time',TRUE).'<br>';
                echo 'From: '.get_post_meta($booking_id,'chauffeur_payment_pickup_address',TRUE).'<br>';
                echo 'To: '.get_post_meta($booking_id,'chauffeur_payment_dropoff_address',TRUE).'<br>';
                if(!empty(get_post_meta($booking_id,'chauffeur_payment_pickup_via',TRUE))){
                    echo 'Via: '.implode(', ', get_post_meta($booking_id,'chauffeur_payment_pickup_via',TRUE));
                }
            }

            echo '</td>
            <td>'.get_post_meta($post_id, 'atb_user', true).'</td>
            <td>£'.get_post_meta($post_id, 'atb_proposed_price', true).'</td>';
        
            if(get_post_meta($post_id, 'atb_status', true) == 'pending'){
                echo '<td><a onclick="return confirm(\'Are you sure?\')" href="'.site_url().'/wp-json/suppliers/v1/admin-actions?sp_id='.$post_id.'&b_id='.get_post_meta($post_id, 'atb_reference_number', true).'&action=approve">Approve</a> | <a onclick="return confirm(\'Are you sure?\')" href="'.site_url().'/wp-json/suppliers/v1/admin-actions?sp_id='.$post_id.'&b_id='.get_post_meta($post_id, 'atb_reference_number', true).'&action=cancel">Cancel</a></td>';
            }else{
                echo '<td>';
                if(get_post_meta($post_id, 'atb_status', TRUE) == 'approved'){
                    echo '<p style="text-transform: uppercase;margin-bottom: 0;display: inline-block;background: #f2ff0a;color: #000;padding: 4px 18px;border-radius: 15px;margin-top: 0;font-weight: bold;">'.get_post_meta($post_id, 'atb_status', TRUE).'</p> <a onclick="return confirm(\'Are you sure?\')" href="'.site_url().'/wp-json/suppliers/v1/admin-actions?sp_id='.$post_id.'&b_id='.get_post_meta($post_id, 'atb_reference_number', true).'&action=complete">Mark as Completed</a> ';
                }else if(get_post_meta($post_id, 'atb_status', TRUE) == 'pending'){
                    echo '<p style="text-transform: uppercase;margin-bottom: 0;display: inline-block;background: #000000;color: #fff;padding: 4px 18px;border-radius: 15px;margin-top: 0;">'.get_post_meta($post_id, 'atb_status', TRUE).'</p>';
                }else if(get_post_meta($post_id, 'atb_status', TRUE) == 'completed'){
                    echo '<p style="text-transform: uppercase;margin-bottom: 0;display: inline-block;background: #0ba84f;color: #fff;padding: 4px 18px;border-radius: 15px;margin-top: 0;">'.get_post_meta($post_id, 'atb_status', TRUE).'</p>';
                }else {
                    echo '<p style="text-transform: uppercase;margin-bottom: 0;display: inline-block;background: #a80b0b;color: #fff;padding: 4px 18px;border-radius: 15px;margin-top: 0;">'.get_post_meta($post_id, 'atb_status', TRUE).'</p>';
                }
                echo '</td>';
            }
            echo '</tr>';
		}
        echo '</table>';
		wp_reset_query();
    }else{
        echo 'No bids yet!';
    }
    ?>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
    <?php
}


// Save the Data  
function save_payment_meta($post_id, $post, $update) {  
    global $payment_meta_fields;  
  	
	$post_data = '';
	
	if(isset($_POST['payment_meta_box_nonce'])) {
		$post_data = $_POST['payment_meta_box_nonce'];
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
    foreach ($payment_meta_fields as $field) {  
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];
        
        if ($new && $new != $old) {
            if($field['id'] == 'chauffeur_payment_pickup_via' || $field['id'] == 'chauffeur_payment_return_pickup_via'){
                $expl = explode(PHP_EOL, $_POST[$field['id']]);
                update_post_meta($post_id, $field['id'], $expl);  
            }
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } // end foreach  
	
    $atb_booking_status = sanitize_text_field( $_POST['atb-booking-status'] );
    update_post_meta( $post_id, 'atb-booking-status', $atb_booking_status );
    
    $sp_show = sanitize_text_field( $_POST['sp_show'] );
    $guide_amount = sanitize_text_field( $_POST['guide_amount'] );
    $guide_amount_2 = sanitize_text_field( $_POST['guide_amount_2'] );
    
    
    update_post_meta( $post_id, 'sp_show', $sp_show );
    update_post_meta( $post_id, 'guide_amount', $guide_amount );
    update_post_meta( $post_id, 'guide_amount_2', $guide_amount_2 );
    
	$get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
    /*
	$fileName = get_home_path().'update.txt';
	$file = fopen($fileName, "wb");
	fprintf($file, "%s\n", $get_payment_email);
    */
	
    
    if($_POST['atb-booking-status'] == 'cancelled' && $_POST['atb-booking-status-old'] != 'cancelled'){
        $get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
        $get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
    
        $get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
        $email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);
    
    
        $options = get_option( 'email-templates-settings-att', array() );
        $subject = str_replace('{{booking_id}}', '#'.$post_id, $options['booking_email_cancelled_subject']);
    
        $email_data1 = str_replace('{{first_name}}', $get_first_name, $options['booking_email_cancelled_user']);
    
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
    }else if($_POST['atb-booking-status-old'] != 'completed' &&  $_POST['atb-booking-status'] == 'completed'){
        $get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
        $get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
    
        $get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
        $email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);
    
    
        $options = get_option( 'email-templates-settings-att', array() );
        $subject = str_replace('{{booking_id}}', '#'.$post_id, $options['booking_email_completed_subject']);
    
        $email_data1 = str_replace('{{first_name}}', $get_first_name, $options['booking_email_completed_user']);
    
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
    }
    if(isset($_POST['refund_reason']) &&  $atb_booking_status == 'cancel_refund' && $_POST['chauffeur_payment_status'] == 'Paid'){
        $get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
        $get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
    
        $get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
        $email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);
    
    
        $options = get_option( 'email-templates-settings-att', array() );
        $subject = str_replace('{{booking_id}}', '#'.$post_id, $options['booking_email_cancelled_refunded_subject']);
    
        $email_data1 = str_replace('{{first_name}}', $get_first_name, $options['booking_email_cancelled_refunded_user']);
    
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

        global $chauffeur_data;

        require chauffeur_BASE_DIR .'/includes/vendor/stripe-new/autoload.php';

        $stripe = new \Stripe\StripeClient($chauffeur_data['stripe_secret_key']);
        $pi_id = get_post_meta($post_id,'chauffeur_payment_payment_reference',TRUE);
        $refund_status = $stripe->refunds->create(['payment_intent' => $pi_id, 'reason' => $_POST['refund_reason']]);
        $chargeJson = $refund_status->jsonSerialize();
        $refund_id = $chargeJson['id'];
        if($_POST['refund_reason']){
            $refund_reason = $_POST['refund_reason'];
            $old_info = get_post_meta($post_id,'chauffeur_payment_additional_info',TRUE);
            update_post_meta($post_id, 'chauffeur_payment_additional_info', $old_info.' Refund Reason: '.$refund_reason.' Refund Ref ID: '.$refund_id );
            update_post_meta($post_id, 'chauffeur_payment_status', 'Refunded' );
        }
    }

	if ($update == true && strlen($get_payment_email)>0) {
        /*
		cancel_taxi_booking($post_id);
		rebook_taxi($post_id);

		$get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
		$get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
		$strContactName=$get_first_name." ".$get_last_name;
		
		$content = '';
		$content .= 'Dear '.$strContactName.',<br><br>';
		$content .= 'Your taxi has been rebooked. Please, check updated details:<br>';
		$get_pickup_date = get_post_meta($post_id,'chauffeur_payment_pickup_date',TRUE);
		$get_pickup_time = get_post_meta($post_id,'chauffeur_payment_pickup_time',TRUE);
		$content .= 'Time: '.$get_pickup_date." ".$get_pickup_time.'<br>';
		$strFromAddress = get_post_meta($post_id,'chauffeur_payment_pickup_address',TRUE);
		$content .= 'From: '.$strFromAddress.'<br>';
		$strToAddress = get_post_meta($post_id,'chauffeur_payment_dropoff_address',TRUE);
		$content .= 'To: '.$strToAddress.'<br>';
		$content .= '<br>';
		$content .= letter_ending();
		
		remove_action('save_post', 'save_payment_meta');
		wp_update_post(array('ID' => $post_id, 'post_title' => $strContactName.'('.$get_pickup_date.' at '.$get_pickup_time.') - #'.$post_id));
		add_action('save_post', 'save_payment_meta', 10, 3);

		$headers = get_mail_headers();
		$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);
		wp_mail($email, "taxi rebooked", $content, $headers);
        */
	}
	
}  
add_action('save_post', 'save_payment_meta', 10, 3);


function cancel_taxi_booking($post_id)
{
	//$authorization_reference = 'mha-autocab-chauffeur';
	$authorization_reference = get_post_meta($post_id, 'chauffeur_payment_authorization_reference', true);
	$booking_reference = get_post_meta($post_id, 'chauffeur_payment_booking_reference', true);
	
	//$fileName = get_home_path().'cancel.txt';
	//$file = fopen($fileName, "wb");
	//fprintf($file, "authorization reference: %s\n", $authorization_reference);
	//fprintf($file, "booking reference: %s\n", $booking_reference);
	//fclose($file);
	
	if (empty($booking_reference)==FALSE && $booking_reference != '')
	{
		$s = get_canceling_request($post_id, $booking_reference, $authorization_reference);
		
		$currTime = new DateTime("now");
		$xmlFileName = get_home_path().'cancel_request_'.$currTime->format("Y-m-d__H_i_s-u");
		//$fileRequest = fopen($xmlFileName, "wb");
		if ($fileRequest != FALSE)
		{
		//	fprintf($fileRequest, "%s", $s);
		}
		$response = call($s);
		$currTime = new DateTime("now");
		$xmlFileName = get_home_path().'cancel_response_'.$currTime->format("Y-m-d__H_i_s-u");
		//$fileResponse = fopen($xmlFileName, "wb");
		if ($fileResponse != FALSE)
		{
		//	fprintf($fileResponse, "%s", $response->asXML());
		}

		$get_return_journey = get_post_meta($post_id,'chauffeur_payment_return_journey',TRUE);
		if($get_return_journey == 'Return')
		{
			$return_authorization_reference = get_post_meta($post_id, 'chauffeur_payment_return_authorization_reference', true);
			$return_booking_reference = get_post_meta($post_id, 'chauffeur_payment_return_booking_reference', true);
	
			$s = get_canceling_request($post_id, $return_booking_reference, $return_authorization_reference);
			//fprintf($fileRequest, "%s", $s);
			$response = call($s);
			//fprintf($fileResponse, "%s", $response->asXML());
		}		

		//fclose($fileRequest);
		//fclose($fileResponse);
	}
}

function get_canceling_request($post_id, $booking_reference, $authorization_reference)
{
	$s = '';
	$s .= '<AgentBookingCancellationRequest>';
		$now = new DateTime("now");
		$now = date("c");
		
		$s .= "<Agent Id='20068'>";
			$s .=  "<Password>bLwg9JJ793gvVCb3UP6HxKpD</Password>";
			$s .= "<Reference>".$booking_reference."</Reference>";
			$s .= "<Time>".$now."</Time>";
		$s .= "</Agent>";
		$s .= "<Vendor Id='72992' />";
		$s .= '<AuthorizationReference>'.$authorization_reference.'</AuthorizationReference>';
	$s .= '</AgentBookingCancellationRequest>';
	return $s;
}


function rebook_taxi($post_id)
{
	$currTime = new DateTime("now");
	$xmlFileName = get_home_path().'rebooking_'.$currTime->format("Y-m-d__H_i_s-u").'.txt';
	//$file = fopen($xmlFileName, "wb");

	$ret = book_there_journey($post_id);
	//fprintf($file, "%s\n", $ret->asXML());
	
	update_post_meta($post_id, 'chauffeur_payment_authorization_reference', strval($ret->AuthorizationReference));
	update_post_meta($post_id, 'chauffeur_payment_booking_reference', strval($ret->BookingReference));
	
	//if ($file != FALSE)
	//{
	//	fprintf($file, "authorization_reference: %s\n", strval($ret->AuthorizationReference));
	//	fprintf($file, "booking_reference: %s\n", strval($ret->BookingReference));
	//}

	$get_return_journey = get_post_meta($post_id,'chauffeur_payment_return_journey',TRUE);
	if($get_return_journey == 'Return')
	{
		$ret = book_return_journey($post_id);
		//fprintf($file, "%s\n", $ret->asXML());
		//$data_array['return_authorization_reference'] = strval($ret->AuthorizationReference);
		//$data_array['return_booking_reference'] = strval($ret->BookingReference);		
		//fprintf($file, "authorization_reference: %s\n", strval($ret->AuthorizationReference));
		//fprintf($file, "booking_reference: %s\n", strval($ret->BookingReference));

		update_post_meta($post_id, 'chauffeur_payment_return_authorization_reference', strval($ret->AuthorizationReference));
		update_post_meta($post_id, 'chauffeur_payment_return_booking_reference', strval($ret->BookingReference));
	}
	

	//fclose($file);
}

function post_updated_event($post_id, $post_after, $post_before)
{
    
	$fileName = get_home_path().'update.txt';
	$file = fopen($fileName, "wb");
	fclose($file);
    
}
add_action('post_updated', 'post_updated_event', 10, 3);


function my_delete_post_function($post_id)
{
	$currTime = new DateTime("now");
	$xmlFileName = get_home_path().'pre_trash_booking_'.$currTime->format("Y-m-d__H_i_s-u").'.txt';
	$file = fopen($xmlFileName, "wb");
	
	$get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
	fprintf($file, "first name: %s\n", $get_first_name);
	fclose($file);
}

function trashed_post_event($post_id){
    update_post_meta( $post_id, 'atb-booking-status', 'deleted');
	//$fileName = get_home_path().'trashed_post.txt';
	//$file = fopen($fileName, "wb");
	/*
	$get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
	//fprintf($file, "%s\n", $get_payment_email);
	if (strlen($get_payment_email)>0)
	{
		cancel_taxi_booking($post_id);
		$get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
		$get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
		$strContactName=$get_first_name." ".$get_last_name;
		
		$content = '';
		$content .= 'Dear '.$strContactName.',<br><br>';
		$content .= 'Your taxi booking from ';
		$strFromAddress = get_post_meta($post_id,'chauffeur_payment_pickup_address',TRUE);
		$content .= $strFromAddress;
		$content .= ' to ';
		$strToAddress = get_post_meta($post_id,'chauffeur_payment_dropoff_address',TRUE);
		$content .= $strToAddress;
		$content .= ' on ';
		$get_pickup_date = get_post_meta($post_id,'chauffeur_payment_pickup_date',TRUE);
		$get_pickup_time = get_post_meta($post_id,'chauffeur_payment_pickup_time',TRUE);
		$content .= $get_pickup_date." ".$get_pickup_time.' has been canceled<br>';
		$content .= letter_ending();
		
		$headers = get_mail_headers();
		
		$email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);
		wp_mail($email , "taxi booking canceled", $content, $headers);
	}
	//fclose($file);

    if(isset($_GET['refund']) && $_GET['refund'] == 1){
        $get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
        $get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
    
        $get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
        $email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);
    
    
        $options = get_option( 'email-templates-settings-att', array() );
        $subject = str_replace('{{booking_id}}', '#'.$post_id, $options['booking_email_cancelled_refunded_subject']);
    
        $email_data1 = str_replace('{{first_name}}', $get_first_name, $options['booking_email_cancelled_refunded_user']);
    
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

        global $chauffeur_data;

        require chauffeur_BASE_DIR .'/includes/vendor/stripe-new/autoload.php';

        $stripe = new \Stripe\StripeClient($chauffeur_data['stripe_secret_key']);
        $pi_id = get_post_meta($post_id,'chauffeur_payment_payment_reference',TRUE);
        $refund_status = $stripe->refunds->create(['payment_intent' => $pi_id, 'reason' => $_GET['refund_reason']]);
        $chargeJson = $refund_status->jsonSerialize();
        $refund_id = $chargeJson['id'];
        if($_GET['refund_reason']){
            $refund_reason = $_GET['refund_reason'];
            $old_info = get_post_meta($post_id,'chauffeur_payment_additional_info',TRUE);
            update_post_meta($post_id, 'chauffeur_payment_additional_info', $old_info.' Refund Reason: '.$refund_reason.' Refund Ref ID: '.$refund_id );
            update_post_meta($post_id, 'chauffeur_payment_status', 'Refunded' );
        }
    }else{
        $get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
        $get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
    
        $get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
        $email = get_customer_email_string($get_first_name, $get_last_name, $get_payment_email);
    
    
        $options = get_option( 'email-templates-settings-att', array() );
        $subject = str_replace('{{booking_id}}', '#'.$post_id, $options['booking_email_cancelled_subject']);
    
        $email_data1 = str_replace('{{first_name}}', $get_first_name, $options['booking_email_cancelled_user']);
    
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
    
        $send = wp_mail($email, $subject, $email_content, $headers);
    
        return $send;
    }
    */
}


function before_delete_post_event($post_id, $post)
{
/*	cancel_taxi_booking($post_id);
	$get_payment_email = get_post_meta($post_id,'chauffeur_payment_email',TRUE);
	$get_first_name = get_post_meta($post_id,'chauffeur_payment_first_name',TRUE);
	$get_last_name = get_post_meta($post_id,'chauffeur_payment_last_name',TRUE);
	$strContactName=$get_first_name." ".$get_last_name;
	
	$content = '';
	$content .= 'Dear '.$strContactName.',<br><br>';
	$content .= 'Your taxi booking has been canceled';
	$content .= letter_ending();
	
//	$headers = '';
//	$headers = "MIME-Version: 1.0\r\n";
//	$headers .= "Content-type: text/html; charset=UTF-8\r\n";
//	$headers .= "From: " . esc_attr($chauffeur_data['email-sender-name']) . " <" . esc_attr($get_payment_email) . ">" . "\r\n" . "Reply-To: " . esc_attr($get_payment_email);

//$to = 'sendto@example.com';
//$subject = 'The subject';
//$body = 'The email body content';
//$headers = array('Content-Type: text/html; charset=UTF-8');

	$headers = array('Content-Type: text/html; charset=UTF-8','From: '.esc_attr($chauffeur_data['email-sender-name']));
	//wp_mail($get_payment_email, "taxi canceled", $content, $headers);
*/	
}

add_action('trashed_post', 'trashed_post_event', 10, 1);
//add_action('before_delete_post', 'before_delete_post_event', 10, 2);

/*
function updated_postmeta_event($meta_id, $object_id, $meta_key, $meta_value)
{
}
add_action('updated_postmeta', 'updated_postmeta_event', 10, 4);
*/


add_filter( 'post_row_actions', 'remove_row_actions', 10, 2 );
function remove_row_actions( $actions, $post ){
  global $current_screen;
    if( $current_screen->post_type != 'payment' ) return $actions;
    unset( $actions['edit'] );
    unset( $actions['view'] );
    unset( $actions['trash'] );
    unset( $actions['inline hide-if-no-js'] );
    return $actions;
}


function atb__payment_status_custom_filter() {
    global $typenow;
    global $wp_query;
      if ( $typenow == 'payment' ) {
        $payment_statuses = array( 'Paid', 'Unpaid', 'Refunded' );
        $current_payment_status = '';
        $border_active = '';
        if( isset( $_GET['payment_status'] ) ) {
          $current_payment_status = $_GET['payment_status'];
        }
        if( isset( $_GET['payment_status']) && $_GET['payment_status'] != 'all'  ) {
          $border_active = 'style="background-color: #24b47e78;"';
        }

        $booking_statuses = array( 'Pending', 'Processing', 'Completed', 'Cancelled', 'Deleted', 'Incomplete' );
        $current_booking_status = '';
        $border_active_2 = '';
        if( isset( $_GET['booking_status'] ) ) {
          $current_booking_status = $_GET['booking_status'];
        }

        if( isset( $_GET['booking_status'] ) && $_GET['booking_status'] != 'all') {
          $border_active_2 = 'style="background-color: #24b47e78;"';
        }
        ?>
        <select name="payment_status" id="payment_status" <?php echo $border_active;?>>
          <option value="all" <?php selected( 'all', $current_payment_status ); ?>><?php _e( 'Payment Status - ALL', 'chauffeur' ); ?></option>
          <?php foreach( $payment_statuses as $key => $value ) { ?>
            <option value="<?php echo strtolower( $value ); ?>" <?php selected( strtolower( $value ), $current_payment_status ); ?>><?php echo esc_attr( $value ); ?></option>
          <?php } ?>
        </select>
        <select name="booking_status" id="booking_status" <?php echo $border_active_2;?>>
          <option value="all" <?php selected( 'all', $current_booking_status ); ?>><?php _e( 'Booking Status - ALL', 'chauffeur' ); ?></option>
          <?php foreach( $booking_statuses as $key => $value ) { ?>
            <option value="<?php echo strtolower( $value ); ?>" <?php selected( strtolower( $value ), $current_booking_status ); ?>><?php echo esc_attr( $value ); ?></option>
          <?php } ?>
        </select>
    <?php }
}
add_action( 'restrict_manage_posts', 'atb__payment_status_custom_filter' );

function atb__payment_status_custom_filter_action( $query ) {
    global $pagenow;
    $post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
    if ( is_admin() && $pagenow=='edit.php' && $post_type == 'payment') {
        if(isset( $_GET['payment_status'] ) && $_GET['payment_status'] !='all' && $_GET['booking_status'] =='all'){
            $query->query_vars['meta_key'] = 'chauffeur_payment_status';
            $query->query_vars['meta_value'] = $_GET['payment_status'];
            $query->query_vars['meta_compare'] = '=';
        }else if(isset( $_GET['booking_status'] ) && $_GET['booking_status'] !='all'  && $_GET['payment_status'] =='all'){
            $query->query_vars['meta_key'] = 'atb-booking-status';
            $query->query_vars['meta_value'] = $_GET['booking_status'];
            $query->query_vars['meta_compare'] = '=';
        }else if(isset( $_GET['payment_status'] ) && $_GET['payment_status'] !='all' && isset( $_GET['booking_status'] ) && $_GET['booking_status'] !='all'){
            $args = array(
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => 'chauffeur_payment_status',
                        'value' => $_GET['payment_status'],
                        'compare' => '='
                    ),
                    array(
                        'key' => 'atb-booking-status',
                        'value' => $_GET['booking_status'],
                        'compare' => '='
                    )
                )
            );
            $query->query_vars['meta_query'][] = $args;
        }
    }
}
add_filter( 'parse_query', 'atb__payment_status_custom_filter_action' );


add_filter('manage_payment_posts_columns', 'atb_payment_table_head');
function atb_payment_table_head( $defaults ) {
    $defaults['journey']    = 'Trip Type';
    $defaults['amount']   = 'Amount';
    $defaults['payment_status'] = 'Payment Status';
    $defaults['booking_status']  = 'Booking Status';
    /*
    $defaults['supplier_bids']  = 'Supplier Bids';
    */
    $defaults['view_action'] = 'Options';
    return $defaults;
}

add_action( 'manage_payment_posts_custom_column', 'atb_payment_table_content', 10, 2 );
function atb_payment_table_content( $column_name, $post_id ) {
    if ($column_name == 'journey') {
        echo  get_post_meta( $post_id, 'chauffeur_payment_return_journey', true );
    }
    if ($column_name == 'amount') {
        echo get_post_meta( $post_id, 'chauffeur_payment_amount', true );
    }
    if ($column_name == 'payment_status') {
        if(get_post_meta( $post_id, 'chauffeur_payment_status', true ) == 'Paid'){
            echo '<div style="background: #24b47e;display: inline;padding: 0px 10px;border-radius: 5px;color: #fff;font-size: 15px;">'.get_post_meta( $post_id, 'chauffeur_payment_status', true ).'</div>';
        }else{
            echo '<div style="color: red;">'.get_post_meta( $post_id, 'chauffeur_payment_status', true ).'</div>';
        }
    }
    if ($column_name == 'booking_status') {
      echo  '<div style="font-weight: bold;text-transform:uppercase;">'.get_post_meta( $post_id, 'atb-booking-status', true ).'</div>';
    }
    /*
    if ($column_name == 'supplier_bids') {
        echo get_total_suppliers_bid($post_id);
    }
    */
    if ($column_name == 'view_action') {
        echo '<a class="button button-primary" href="'.get_edit_post_link( $post_id ).'" target="_blank" rel="noopener noreferrer">Manage</a><style>.bulkactions {display: none;}</style>';
    }
}

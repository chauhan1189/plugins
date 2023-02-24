<?php
if ( !defined( 'ABSPATH' ) ) exit;

function create_post_type_suppliers_portal() {
	
	register_post_type('suppliers_portal', 
		array(
			'labels' => array(
				'name' => esc_html__( 'Suppliers Portal', 'chauffeur' ),
                'singular_name' => esc_html__( 'Suppliers Portal', 'chauffeur' ),
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
			'slug' => esc_html__('suppliers_portal','chauffeur')
		), 
        'supports' => array( 'title', 'author')
	));
}

add_action( 'init', 'create_post_type_suppliers_portal' );

// Field Array  
$prefix = 'atb_';  
$suppliers_meta_fields = array(
	array(  
        'label'=> esc_html__('Invoice Number','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'invoice_number',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Booking ID','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'reference_number',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Return Journey','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'return_journey',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Proposed Price','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'proposed_price',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Supplier email','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'user',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Status','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'status',  
        'type'  => 'select'
    )
);

// Add the Meta Box  
function add_suppliers_meta_box() {
    add_meta_box( 
        'payment_meta_box',
        esc_html__('Details','chauffeur'), 
        'show_suppliers_meta_box',
        'suppliers_portal',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_suppliers_meta_box');

// The Callback  
function show_suppliers_meta_box() {
	global $suppliers_meta_fields, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="suppliers_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	foreach ($suppliers_meta_fields as $field) {
		$meta = get_post_meta($post->ID, $field['id'], true);

		switch($field['type']) {
			case 'text':
        ?>
        <div class="chauffeur-field-wrapper field-padding clearfix">
            <div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
            <div class="four-fifths"><input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo !empty($meta) ? $meta : ''; ?>"></div>
            <?php 
                if($field['id'] == 'atb_user'){
                    $user = get_user_by('email', $meta);
                    if($user){
                        $user_id = $user->ID;
                        echo '<a href="'.admin_url('user-edit.php?user_id='.$user_id).'#spi" target="_blank">View Supplier</a>';
                    }
                }
            ?>
        </div>
    
        <?php break; case 'select': ?>
        <div class="chauffeur-field-wrapper field-padding clearfix">
            <div class="one-fifth"><label><?php echo $field['label']; ?></label></div>
            <div class="four-fifths">
                <select name="atb_status" id="atb_status" required="" style="width: 100%;">
                    <option value="" selected="" disabled="">Please Select</option>
                    <option value="pending" <?php if($meta == 'pending'){echo 'selected';}?>>Pending</option>
                    <option value="approved" <?php if($meta == 'approved'){echo 'selected';}?>>Approved</option>
                    <option value="completed" <?php if($meta == 'completed'){echo 'selected';}?>>Completed</option>
                    <option value="cancelled" <?php if($meta == 'cancelled'){echo 'selected';}?>>Cancelled</option>
                </select>
            </div>
        </div>
        <?php } ?>
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
}

// Booking Details Meta Box
function atb_booking_details_sp_meta_box() {
    add_meta_box( 
        'sp_booking_details',
        esc_html__('Booking Details','chauffeur'), 
        'atb_booking_details_sp_meta_box_render',
        'suppliers_portal',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'atb_booking_details_sp_meta_box');

function atb_booking_details_sp_meta_box_render() {
    $post_id = get_the_ID();
    $post_id = get_post_meta($post_id, 'atb_reference_number', true);
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
    echo '<h3 style="margin-bottom: 0;display: inline-block;background: #ff8f25;color: #fff;padding: 4px 18px;border-radius: 15px;margin-top: 0;">Pickup</h3><p class="sp-booking-time"> Pick up at: <b>'.get_post_meta($post_id, 'chauffeur_payment_pickup_date', TRUE).'</b> — <b>'.get_post_meta($post_id, 'chauffeur_payment_pickup_time', TRUE).'</b> </p><p class="sp-booking-location"> From: <b>'.get_post_meta($post_id, 'chauffeur_payment_pickup_address', TRUE).'</b> to <b>'.get_post_meta($post_id, 'chauffeur_payment_dropoff_address', TRUE).$pickup_via.'</b> by <b>'.get_post_meta($post_id, 'chauffeur_payment_item_name', TRUE).'</b></p>';
    
    if(get_post_meta($post_id, 'chauffeur_payment_return_journey', TRUE) == 'Return'){
        echo '<hr><h3 style="margin-bottom: 0;display: inline-block;background: #7925ff;color: #fff;padding: 4px 18px;border-radius: 15px;margin-top: 0;">Return</h3><p class="sp-booking-location">From: <b>'.get_post_meta($post_id, 'chauffeur_payment_return_address', TRUE).'</b> to <b>'.get_post_meta($post_id, 'chauffeur_payment_return_dropoff', TRUE).$return_via.'</b> by <b>'.get_post_meta($post_id, 'chauffeur_payment_item_name', TRUE).'</b></p>';
    }

}

// Save the Data  
function save_suppliers_portal_meta($post_id, $post, $update) {  
    global $suppliers_meta_fields;  
  	
	$post_data = '';
	
	if(isset($_POST['suppliers_meta_box_nonce'])) {
		$post_data = $_POST['suppliers_meta_box_nonce'];
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
    foreach ($suppliers_meta_fields as $field) {  
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } 
}  
add_action('save_post', 'save_suppliers_portal_meta', 10, 3);



add_filter('manage_suppliers_portal_posts_columns', 'atb_suppliers_portal_table_head');
function atb_suppliers_portal_table_head( $defaults ) {
    $defaults['atb_reference_number']    = 'Booking ID';
    $defaults['atb_proposed_price']   = 'Proposed Price';
    $defaults['atb_user'] = 'User';
    $defaults['atb_status'] = 'Status';
    return $defaults;
}

add_action( 'manage_suppliers_portal_posts_custom_column', 'atb_suppliers_portal_table_content', 10, 2 );
function atb_suppliers_portal_table_content( $column_name, $post_id ) {
    if ($column_name == 'atb_reference_number') {
        echo '<b>'. get_post_meta( $post_id, 'atb_reference_number', true ).'</b>';
    }
    if ($column_name == 'atb_proposed_price') {
        echo get_post_meta( $post_id, 'atb_proposed_price', true );
    }
    if ($column_name == 'atb_user') {
        $meta = get_post_meta( $post_id, 'atb_user', true );        
        $user = get_user_by('email', $meta);
        if($user){
            $user_id = $user->ID;
            echo '<a href="'.admin_url('user-edit.php?user_id='.$user_id).'#spi" target="_blank">'.$meta.'</a>';
        }
    }
    if ($column_name == 'atb_status') {
        $atb_status = get_post_meta( $post_id, 'atb_status', true );
        if($atb_status == 'approved'){
            echo '<div style="background: #24b47e;display: inline;padding: 0px 10px;border-radius: 5px;color: #fff;font-size: 15px;">Approved</div>';
        }else if($atb_status == 'pending'){
            echo '<div style="background: #b2941c;display: inline;padding: 0px 10px;border-radius: 5px;color: #fff;font-size: 15px;">Pending</div>';
        }else {
            echo '<div style="background: #b2941c;display: inline;padding: 0px 10px;border-radius: 5px;color: #fff;font-size: 15px;">Cancelled</div>';
        }
    }
}

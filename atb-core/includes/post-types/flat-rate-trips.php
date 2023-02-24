<?php

function create_post_type_flat_rate_trips() {
	
	register_post_type('flat_rate_trips', 
		array(
			'labels' => array(
				'name' => esc_html__( 'Flat Rate Trips', 'chauffeur' ),
                'singular_name' => esc_html__( 'Trip', 'chauffeur' ),
				'add_new' => esc_html__('Add Trip', 'chauffeur' ),
				'add_new_item' => esc_html__('Add New Trip' , 'chauffeur' )
			),
		'public' => true,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-admin-post',
		'rewrite' => array(
			'slug' => esc_html__('flat_rate_trips','chauffeur')
		), 
		'supports' => array( 'title')
	));
}

add_action( 'init', 'create_post_type_flat_rate_trips' );



// Add the Meta Box  
function add_flat_rate_trips_meta_box() {  
    add_meta_box(  
        'flat_rate_trips_meta_box', // $id  
        esc_html__('Trip Details','chauffeur'), // $title  
        'show_flat_rate_trips_meta_box', // $callback  
        'flat_rate_trips', // $page  
        'normal', // $context  
        'high'); // $priority  
}  
add_action('add_meta_boxes', 'add_flat_rate_trips_meta_box');



// Field Array  
$prefix = 'chauffeur_';  
$flat_rate_trips_meta_fields = array(  	
	array(  
        'label'=> esc_html__('Pick Up Location Name','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'flat_rate_trips_pick_up_name',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Drop Off Location Name','chauffeur'),  
        'desc'  => '',  
        'id'    => $prefix.'flat_rate_trips_drop_off_name',  
        'type'  => 'text'
    ),
);



// The Callback  
function show_flat_rate_trips_meta_box() {
	global $flat_rate_trips_meta_fields, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="flat_rate_trips_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	foreach ($flat_rate_trips_meta_fields as $field) {
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
	<div class="four-fifths"><textarea rows="10" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>"><?php echo !empty($meta) ? $meta : ''; ?></textarea><p class="description"><?php echo $field['desc']; ?></p></div>
</div>
<hr class="space1"><?php
			break;
			
		} //end switch
   } // end foreach
}



// Save the Data  
function save_flat_rate_trips_meta($post_id) {  
    global $flat_rate_trips_meta_fields;  
  	
	$post_data = '';
	
	if(isset($_POST['flat_rate_trips_meta_box_nonce'])) {
		$post_data = $_POST['flat_rate_trips_meta_box_nonce'];
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
    foreach ($flat_rate_trips_meta_fields as $field) {  
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];  
        if ($new && $new != $old) {  
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } // end foreach  
}  
add_action('save_post', 'save_flat_rate_trips_meta');


?>
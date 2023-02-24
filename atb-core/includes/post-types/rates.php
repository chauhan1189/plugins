<?php

function create_post_type_rates() {
	
	register_post_type('rates', 
		array(
			'labels' => array(
				'name' => esc_html__( 'Rates', 'chauffeur' ),
                'singular_name' => esc_html__( 'rates', 'chauffeur' ),
				'add_new' => esc_html__('Add Rate', 'chauffeur' ),
				'add_new_item' => esc_html__('Add New Rate' , 'chauffeur' )
			),
		'public' => true,
		'menu_position' => 5,
		'menu_icon' => 'dashicons-admin-post',
		'rewrite' => array(
			'slug' => esc_html__('rates','chauffeur')
		), 
		'supports' => array( 'title','thumbnail')
	));
}

add_action( 'init', 'create_post_type_rates' );



// Add the Meta Box  
function add_rates_meta_box() {  
    add_meta_box(  
        'rates_meta_box', // $id  
        esc_html__('Rate Details','chauffeur'), // $title  
        'show_rates_meta_box', // $callback  
        'rates', // $page  
        'normal', // $context  
        'high'); // $priority  
}  
add_action('add_meta_boxes', 'add_rates_meta_box');



// Field Array  
$prefix = 'chauffeur_';  
$rates_meta_fields = array(  	
	array(  
        'label'=> esc_html__('Currency Unit','chauffeur'),  
        'desc'  => 'e.g. $',  
        'id'    => $prefix.'rates_currency_unit',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Rate Column 1 Price','chauffeur'),  
        'desc'  => 'e.g. 300',  
        'id'    => $prefix.'rates_col_1_price',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Rate Column 1 Unit','chauffeur'),  
        'desc'  => 'e.g. Hour',  
        'id'    => $prefix.'rates_col_1_unit',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Rate Column 1 Description','chauffeur'),  
        'desc'  => '+ fuel and toll surcharges',  
        'id'    => $prefix.'rates_col_1_description',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Rate Column 2 Price','chauffeur'),  
        'desc'  => 'e.g. 300',  
        'id'    => $prefix.'rates_col_2_price',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Rate Column 2 Unit','chauffeur'),  
        'desc'  => 'e.g. Hour',  
        'id'    => $prefix.'rates_col_2_unit',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Rate Column 2 Description','chauffeur'),  
        'desc'  => '+ fuel and toll surcharges',  
        'id'    => $prefix.'rates_col_2_description',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Rate Column 3 Price','chauffeur'),  
        'desc'  => 'e.g. 300',  
        'id'    => $prefix.'rates_col_3_price',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Rate Column 3 Unit','chauffeur'),  
        'desc'  => 'e.g. Hour',  
        'id'    => $prefix.'rates_col_3_unit',  
        'type'  => 'text'
    ),
	array(  
        'label'=> esc_html__('Rate Column 3 Description','chauffeur'),  
        'desc'  => '+ fuel and toll surcharges',  
        'id'    => $prefix.'rates_col_3_description',  
        'type'  => 'text'
    ),
);



// The Callback  
function show_rates_meta_box() {
	global $rates_meta_fields, $post;
	// Use nonce for verification
	echo '<input type="hidden" name="rates_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';

	foreach ($rates_meta_fields as $field) {
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
function save_rates_meta($post_id) {  
    global $rates_meta_fields;  
  	
	$post_data = '';
	
	if(isset($_POST['rates_meta_box_nonce'])) {
		$post_data = $_POST['rates_meta_box_nonce'];
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
    foreach ($rates_meta_fields as $field) {  
        $old = get_post_meta($post_id, $field['id'], true);  
        $new = $_POST[$field['id']];  
        if ($new && $new != $old) {  
            update_post_meta($post_id, $field['id'], $new);  
        } elseif ('' == $new && $old) {  
            delete_post_meta($post_id, $field['id'], $old);  
        }  
    } // end foreach  
}  
add_action('save_post', 'save_rates_meta');


?>
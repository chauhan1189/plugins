<?php
/**
 * Post Type: atb-coupons
 */
if ( !defined( 'ABSPATH' ) ) exit;

function create_post_type_atb_coupons() {	
	register_post_type('atb-coupons', 
		array(
			'labels' => array(
				'name' => esc_html__( 'Coupons', 'chauffeur' ),
                'singular_name' => esc_html__( 'Coupons', 'chauffeur' ),
				'add_new' => esc_html__('Add Coupon', 'chauffeur' ),
				'add_new_item' => esc_html__('Add New Coupon' , 'chauffeur' )
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
			'slug' => esc_html__('atb-coupons','chauffeur')
		), 
		'supports' => array( 'title','thumbnail')
	));
}
add_action( 'init', 'create_post_type_atb_coupons' );

// Add the Meta Box  
function add_atb_coupon_meta_box() {
    add_meta_box( 
        'atb_coupon_meta_box',
        esc_html__('Coupon Details','chauffeur'), 
        'show_atb_coupon_meta_box',
        'atb-coupons',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'add_atb_coupon_meta_box');

// The Callback  
function show_atb_coupon_meta_box() {
	global $post;
	// Use nonce for verification
	echo '<input type="hidden" name="atb_coupon_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
    ?>
        <div class="chauffeur-field-wrapper field-padding clearfix">
            <div class="one-fifth">
                <label for="atb_coupon_code">Coupon Code</label>
            </div>
            <div class="four-fifths">
                <input type="text" name="atb_coupon_code" id="atb_coupon_code" placeholder="Eg. WELCOME10" value="<?php echo !empty(get_post_meta($post->ID, 'atb_coupon_code', true)) ? get_post_meta($post->ID, 'atb_coupon_code', true) : ''; ?>">
                <button type="button" onclick="genCopuonCode(10)" class="button generate-coupon-code">Generate coupon code</button>
            </div>
        </div>
        <div class="chauffeur-field-wrapper field-padding clearfix">
            <div class="one-fifth">
                <label for="atb_coupon_code">Discount Type</label>
            </div>
            <div class="four-fifths">
                <select name="atb_coupon_discount_type" id="atb_coupon_discount_type">
                    <option value="percentage" <?php echo !empty(get_post_meta($post->ID, 'atb_coupon_discount_type', true)) ? 'selected' : ''; ?>>Percentage Discount</option>
                    <option value="fixed" <?php echo !empty(get_post_meta($post->ID, 'atb_coupon_discount_type', true)) ? 'selected' : ''; ?>>Fixed Discount</option>
                </select>
            </div>
        </div>
        <div class="chauffeur-field-wrapper field-padding clearfix">
            <div class="one-fifth">
                <label for="atb_coupon_code">Coupon Amount</label>
            </div>
            <div class="four-fifths">
                <input type="number" name="atb_coupon_amount" id="atb_coupon_amount" placeholder="Eg. 10" value="<?php echo !empty(get_post_meta($post->ID, 'atb_coupon_amount', true)) ? get_post_meta($post->ID, 'atb_coupon_amount', true) : ''; ?>">
            </div>
        </div>

        <style>
        .button.generate-coupon-code {
            margin-top: 5px;
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
        #post-body-content {
            //display: none;
        }
        </style>
        <script>
            function genCopuonCode(length) {
                var result           = '';
                var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                var charactersLength = characters.length;
                for ( var i = 0; i < length; i++ ) {
                    result += characters.charAt(Math.floor(Math.random() * charactersLength));
                }
                jQuery('#atb_coupon_code').val(result);
            }
        </script>
    <?php
}
// Save the Data  
function save_atb_coupons_meta($post_id, $post, $update) {
  	
	$post_data = '';
	
	if(isset($_POST['atb_coupon_meta_box_nonce'])) {
		$post_data = $_POST['atb_coupon_meta_box_nonce'];
	}

    // verify nonce  
    if (!wp_verify_nonce($post_data, basename(__FILE__)))  
        return $post_id;

    // check autosave  
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)  
        return $post_id;

    // check permissions  
    if ('coupons' == $_POST['post_type']) {  
        if (!current_user_can('edit_page', $post_id))  
            return $post_id;  
        } elseif (!current_user_can('edit_post', $post_id)) {  
            return $post_id;  
    }  
    update_post_meta($post_id, 'atb_coupon_code', sanitize_text_field($_POST['atb_coupon_code']));
    update_post_meta($post_id, 'atb_coupon_discount_type', $_POST['atb_coupon_discount_type']);
    update_post_meta($post_id, 'atb_coupon_amount', $_POST['atb_coupon_amount']);

    $post = $original_post;

    $post_title = sanitize_text_field($_POST['atb_coupon_code']);
         
    $my_args = array(
        'ID'           => $post_id,
        'post_title'   => $post_title,
    );
    remove_action('save_post', 'save_atb_coupons_meta');
    wp_update_post( $my_args );
    add_action('save_post', 'save_atb_coupons_meta');
}  
add_action('save_post', 'save_atb_coupons_meta', 10, 3);


add_filter('manage_atb-coupons_posts_columns', 'atb_atb_coupons_table_head');
function atb_atb_coupons_table_head( $defaults ) {
    $defaults['atb_coupon_discount_type']    = 'Discount Type';
    $defaults['atb_coupon_amount']   = 'Coupon Amount';
    return $defaults;
}

add_action( 'manage_atb-coupons_posts_custom_column', 'atb_atb_coupons_table_content', 10, 2 );
function atb_atb_coupons_table_content( $column_name, $post_id ) {
    if ($column_name == 'atb_coupon_discount_type') {
        echo '<b>'. get_post_meta( $post_id, 'atb_coupon_discount_type', true ).'</b>';
    }
    if ($column_name == 'atb_coupon_amount') {
        echo get_post_meta( $post_id, 'atb_coupon_amount', true );
    }
}


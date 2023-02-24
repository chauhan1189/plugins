<?php
	/*
		Plugin Name: Acts Plugin
		Plugin URI: http://infugin.com
    Text Domain: act-lite
		Description: Managing Acts.
		Version: 1.0
		Author: Mangat
		Author URI: http://infugin.com/
		License: GPL2
	*/	
	
// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class Acts{
		
	public function __construct() {
		//define constant
		$this->define_constants();

		//define hooks
		$this->init_hooks();
	}

	public function define_constants() {
		define( 'ACT_VERSION', '1.0' );
		define( 'ACT_DIR_PATH', plugin_dir_path( __FILE__ ) );
		define( 'ACT_URL', plugins_url( '', __FILE__ )  );
	}
		
		
	public function init_hooks() {
		
		// plugin activation
		register_activation_hook (__FILE__, array($this,'act_plugin_activated_callback'));
		
		// enqueue scripts and styles
		add_action( 'wp_enqueue_scripts', array($this, 'act_scripts_frontend'), 15 );

		// ajax
		add_action( 'wp_enqueue_scripts', array($this, 'act_load_admin_ajax'), 15);
		
		add_filter( 'the_content', array($this,'act_theme_slug_filter_the_content'));
		// add custom class in body 
		add_filter( 'body_class', array($this,'act_add_body_custom_class'));

		// ajax function
		add_action('wp_ajax_act_mark_complete', array($this, 'act_mark_complete'));

		// remove acts
		add_action('wp_ajax_act_remove_acts', array($this, 'act_remove_acts'));

		// assign template for my acts page
		add_filter( 'template_include', array($this,'act_myacts_page_template'), 99 );

		// button filter
		add_action('ocean_before_blog_entry_readmore', array($this,'act_mark_button_listing'));

		// menu for my acts page
		add_filter( 'wp_nav_menu_items', array($this, 'act_new_nav_menu_items'));
		add_filter( 'locale', array( $this, 'my_act_localized') );
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 20 );
	}

		// menu for my-acts page
	public function act_plugin_activated_callback() {
			// Create post object
			$my_post = array(
				'post_title'    => wp_strip_all_tags( 'My Acts' ),
				'post_content'  => 'Here are My Acts',
				'post_status'   => 'publish',
				'post_author'   => 1,
				'post_type'     => 'page',
			);
		// Insert the post into the database
			wp_insert_post( $my_post );
	}

	// Filter wp_nav_menu() to add additional links and other output
	public function act_new_nav_menu_items($items) {
			$myacts_link = '<li class="my-acts"><a href="' . site_url( '/my-acts' ) . '">' . __('My Acts','act-lite') . '</a></li>';
			// add the home link to the end of the menu
			$items = $items . $myacts_link;
			return $items;
	}

		


	// define function to add custom class in body 
	public function act_add_body_custom_class( $classes ) {
		
		if ( is_page( 'my-acts' )  ) {
			$classes[] = 'has-sidebar content-both-sidebars';
		}
		
			return $classes;
	} 
	// end add body class function

	public function mark_button_html($user_id, $post_id, $type=NULL) {

		$button = '';
			$button .='<p class="my-acts-main">';
			if(!is_user_logged_in()) {
				$button .= '<a href="'.site_url().'/wp-login.php?redirect_to='.$_SERVER["REQUEST_URI"].'" class="login-to-complete act-btn-style btn">'.__('Please Login To Complete Act','act-lite').'</a>';
			}

			else {
				
				$complete_acts = get_user_meta($user_id, '_user_complete_acts', true);

				//$button .= var_dump(in_array($post_id, $complete_acts));
				//return $button;

			if(sizeof($complete_acts)>0) {


				if(in_array($post_id, $complete_acts)) {
					
					if(is_page('my-acts')) {
						$button .= '<a href="javascript:void(0);" data-type="'.$type.'" data-userid="'.$user_id.'" data-postid="'.$post_id.'"  class="act-btn-style remove_acts btn disabled post_id_'.$post_id.'">'.__('MARK AS INCOMPLETE','act-lite').'</a>';
					}
					else {

					$button .= '<a href="javascript:void(0);" data-type="'.$type.'" data-userid="'.$user_id.'" data-postid="'.$post_id.'"  class="act-btn-style remove_acts btn disabled post_id_'.$post_id.'">'.__('ACT COMPLETED!','act-lite').'</a>';
					}

				}
				else {

						$button .= '<a href="javascript:void(0);" data-type="'.$type.'" data-userid="'.$user_id.'" data-postid="'.$post_id.'"  class="act-btn-style mark-act-completed-c btn mark_complete post_id_'.$post_id.'">'.__('MARK AS COMPLETED','act-lite').'</a>';
				}
			}
				else {

						$button .= '<a href="javascript:void(0);" data-type="'.$type.'" data-userid="'.$user_id.'" data-postid="'.$post_id.'"  class="act-btn-style mark-act-completed-c btn mark_complete post_id_'.$post_id.'">'.__('MARK AS COMPLETED','act-lite').'</a>';
				}
			

			}
							 $button .= '</p>';

						 return $button;

	} 


	public function act_theme_slug_filter_the_content( $content ) {

		global $post;
		 if ( is_singular('post')) {
			
			$user_id = wp_get_current_user();
			$post_id = $post->ID;

			$button = $this->mark_button_html($user_id->ID, $post_id, 'single');

			$content = $content.$button;
				return $content;
					}else{
							return $content;
					}

				return $content;
		}

		
	
	public function act_scripts_frontend() {
		// Load the stylesheet.
		wp_enqueue_style( 'act_style_frontend', ACT_URL.'/css/style.css' );	

		// inclide font awesome
		wp_enqueue_style( 'act_font_awesome', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css");

		wp_register_script('act_script_frontend', ACT_URL.'/js/script.js', array('jquery'), ACT_VERSION, true);
		wp_enqueue_script('act_script_frontend');
	}


	// LOCALIZE SCRIPT
	 public function act_load_admin_ajax() {
			wp_localize_script( 'act_script_frontend', 'ajax_login_object', array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
		));

	 }
		
	 // mark complete ajax request handel

	public function act_mark_complete(){
		
		$user_id = $_POST['user_id'];
		$post_id = $_POST['post_id'];
		$type = $_POST['type'];

		if($user_id != '' && $post_id != '') {

		$complete_acts = get_user_meta($user_id, '_user_complete_acts', true);

		// check if any acts complete done by user before

		$main_array = array();

			if(empty($complete_acts)) {
					$main_array = array($post_id);
					update_user_meta($user_id, '_user_complete_acts', $main_array);
			}
			else {

				$complete_acts[] = $post_id;
				update_user_meta($user_id, '_user_complete_acts', $complete_acts);

			}

			echo json_encode(array('acts'=>'complete', 'button_type'=> $type, 'post_id'=>$post_id, 'button_text'=>__('ACT COMPLETED!','act-lite')));
			die();

		}

		else {
			echo json_encode(array('acts'=>'not-complete'));
			die();
		}


	}

	 // remove acts ajax request handel

	public function act_remove_acts(){
		
		$user_id = $_POST['user_id']?$_POST['user_id']:'';
		$post_id = $_POST['post_id']?$_POST['post_id']:'';
		$type = $_POST['type']?$_POST['type']:'';

		if($user_id != '' && $post_id != '') {

		$complete_acts = get_user_meta($user_id, '_user_complete_acts', true);

		if (($key = array_search($post_id, $complete_acts)) !== false) {
				unset($complete_acts[$key]);

				update_user_meta($user_id, '_user_complete_acts', $complete_acts);

				$complete_acts = get_user_meta($user_id, '_user_complete_acts', true);

				$completed_acts_count = count($complete_acts);
													$oops_msg="";
													if($completed_acts_count==0){
															$oops_msg = __('Oops ! No acts completed.','act-lite');
													}

			echo json_encode(array('acts'=>'acts_removed', 'post_id'=>$post_id, 'completed_count'=>$completed_acts_count, 'button_type'=> $type, 'button_text'=>__('ACT COMPLETED!','act-lite'), 'updated_text'=>__('MARK AS COMPLETED','act-lite'), 'oops_msg'=>$oops_msg));
			die();

			}

		}

		else {
			echo json_encode(array('acts'=>'not-complete'));
			die();
		}


	}

	// include tempalte for my acts page

	public function act_myacts_page_template( $template ) {
		if ( is_page( 'my-acts' )  ) {
			return ACT_DIR_PATH . '/templates/my-acts.php';
		}
		return $template;
	}

	// mark as complete button for archive and listing page

	public function act_mark_button_listing() {

		if(is_page('my-acts')) {
			$type = "myacts";
		}
		else {
			$type = "listing";
		}

		$user_id = wp_get_current_user();
		$post_id = get_the_ID();
		echo "<div class='mark_button_listing'>";
		echo $button = $this->mark_button_html($user_id->ID, $post_id, $type);
		echo '<p class="read_more_wrapper"> <a class="read_more" href="'.get_the_permalink($post_id).'">'.__('Continue Reading','act-lite').' <i class="fa fa-angle-right"></i></a></p>';
		echo "</div>";

	}
							
						
									public function my_act_localized( $locale )
									{
													if ( isset( $_GET['lang'] ) )
													{
																	return sanitize_key( $_GET['lang'] );
													}

													return $locale;
									}
							
										 /**
	 * Loads the plugin language files.
	 *
	 * @since 1.0.0
	 * @since 1.5.0 Load only the lite translation.
	 */
	public function load_textdomain() {
								 
		load_plugin_textdomain( 'act-lite', false, dirname(plugin_basename(__FILE__)).'/languages/' );
	}
}

$Acts = new Acts();


<?php
add_shortcode('booking_manage', 'shortcode_booking_manage');
/**
 * Generates HTML for [booking_manage] shortcode
 * 
 * @param array $atts Shortcode attributes
 * @since 0.1
 * @return string Fully formatted HTML for ads management panel.
 */
function shortcode_booking_manage() {
    
    if(!get_current_user_id()) {
        ob_start();
        $permalink = get_permalink();
        $message = __('Only logged in users can access this page. <strong><a href="%1$s">Login</a></strong> or <strong><a href="%2$s">Register</a></strong>.', "adverts");
        $parsed = sprintf($message, wp_login_url( $permalink ), wp_registration_url( $permalink ) );
        echo '<div style="text-align:center;">' . $parsed . '</div>';
        $content = ob_get_clean();
        return $content;
    }

    ob_start();
    // Load ONLY current user data
    $loop = new WP_Query( array( 
        'post_type' => 'payment',
        'posts_per_page' => -1,
        'author' => get_current_user_id()
    ) );
    include ( chauffeur_BASE_DIR . "/includes/templates/booking-manage-content.php");
    $content = ob_get_clean();
    return $content;
}
?>
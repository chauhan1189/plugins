<?php

    add_action( 'rest_api_init', function() {
        register_rest_route( 'endpoint/v2', '/stripe', [
        'methods' => 'GET',
        'callback' => 'stripe_custom_endpoint_redirect',
        'permission_callback' => '__return_true',
        ] );
    } );
    
    function stripe_custom_endpoint_redirect( $params ) {
        if(isset($_REQUEST['payment_intent'])){
            $myoutput = chauffeur_3dstripe_payment($_REQUEST);
            //send_booking_success_email_new($myoutput["booking_id"]);
            wp_redirect( get_site_url()."/thank-you/?item_number=".$myoutput['booking_id'], 301 );
            exit();
        }else{
            wp_redirect( get_site_url(), 301 );
            exit();
        }
    }

    add_action( 'rest_api_init', function() {
        register_rest_route( 'endpoint/v1', '/sendmail', [
        'methods' => 'GET',
        'callback' => 'email_sender_endpoint_atb',
        'permission_callback' => '__return_true',
        ] );
    } );
    
    function email_sender_endpoint_atb( $params ) {
        /*
        $email = 'davidswisher85@gmail.com';
        $options = get_option( 'email-templates-settings-att', array() );
        $email_data = str_replace('{{first_name}}', 'Avijit', wpautop($options['booking_successful_email_to_user']));

        $vars = array(
            'msg' => $email_data
        );

        ob_start();
        include(ATT_PATH . '/includes/emails/test.php');
        $email_content = ob_get_contents();
        ob_end_clean();
        foreach($vars as $key => $value) {
            $email_content = str_replace('{{'.$key.'}}', $value, $email_content);
        }
        $headers = array('Content-Type: text/html; charset=UTF-8');
        $send = wp_mail($email, "Taxi is successfully booked", $email_content, $headers);

        var_dump($send);

        */

        
        $email = send_booking_success_email_new(11441);
        var_dump($email);
    }
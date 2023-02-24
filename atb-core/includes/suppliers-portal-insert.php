<?php

    add_action( 'rest_api_init', function() {
        register_rest_route( 'suppliers/v1', '/submit', [
        'methods' => 'POST',
        'callback' => 'suppliers_portal_insert',
        'permission_callback' => '__return_true',
        ] );
    } );
    
    function suppliers_portal_insert( $params ) {
        if(isset($_POST['submit-supplier-data'])){
            if($_POST['booking_id'] && $_POST['proposed_price'] && $_POST['redirect'] && $_POST['user_login']){
                function generateRandomString($length = 5) {
                    $characters = '0123456789';
                    $charactersLength = strlen($characters);
                    $randomString = '';
                    for ($i = 0; $i < $length; $i++) {
                        $randomString .= $characters[rand(0, $charactersLength - 1)];
                    }
                    return $randomString;
                }
                $invoice_number = 'ATB-'.$_POST['booking_id'].'-'.generateRandomString();
                $reference_number  =   sanitize_text_field( $_POST['booking_id'] );
                $proposed_price  =   sanitize_text_field( $_POST['proposed_price'] );
                $return_journey  =   sanitize_text_field( $_POST['return_journey'] );
                
                $add_sp_query = array(
                    'post_title'    => $invoice_number,
                    'post_status'   => 'publish',
                    'post_author'   => $_POST['user_login_id'],
                    'post_type'	    => 'suppliers_portal'
                );

                // Insert booking
                $post_id = wp_insert_post( $add_sp_query );

                $current_user = wp_get_current_user();

                update_post_meta($post_id, 'atb_invoice_number', $invoice_number );
                update_post_meta($post_id, 'atb_reference_number', $reference_number );
                update_post_meta($post_id, 'atb_return_journey', $return_journey );
                update_post_meta($post_id, 'atb_proposed_price', $proposed_price );
                update_post_meta($post_id, 'atb_user',  $_POST['user_login']);
                update_post_meta($post_id, 'atb_status',  'pending');

                /* Send Email to User */
                $user_id = $_POST['user_login_id'];
                suppliers_email_send($user_id, $post_id);
                
                wp_redirect( $_POST['redirect'].'?success=Submission was successful.' );
                exit;
            }else{
                wp_redirect( $_POST['redirect'].'?errors=Please enter all required fields.' );
                exit;
            }
        }
    }
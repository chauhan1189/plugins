<?php

    add_action( 'rest_api_init', function() {
        register_rest_route( 'suppliers/v1', '/auth', [
        'methods' => 'POST',
        'callback' => 'suppliers_portal_auth',
        'permission_callback' => '__return_true',
        ] );
    } );
    
    function suppliers_portal_auth( $params ) {
        if(isset($_POST['register-submit'])){
            if($_POST['firstname'] && $_POST['lastname'] && $_POST['email'] && $_POST['password'] && $_POST['company_name'] && $_POST['company_number'] && $_POST['vat_number']){
                $password   =   esc_attr( $_POST['password'] );
                $email      =   sanitize_email( $_POST['email'] );
                $phone      =   sanitize_text_field( $_POST['phone'] );
                $first_name =   sanitize_text_field( $_POST['firstname'] );
                $last_name  =   sanitize_text_field( $_POST['lastname'] );
                $company_name  =   sanitize_text_field( $_POST['company_name'] );
                $company_number  =   sanitize_text_field( $_POST['company_number'] );
                $vat_number  =   sanitize_text_field( $_POST['vat_number'] );

                $arr_file_ext = array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/msword', 'application/pdf', 'image/png', 'image/jpg', 'image/jpeg');

                if ( email_exists( $email ) ) {
                    wp_redirect( $_POST['redirect'].'?register&errors=Sorry! This email already exists.' );
                    exit;
                }else if (!in_array($_FILES['verification_document']['type'], $arr_file_ext)) {
                    wp_redirect( $_POST['redirect'].'?register&errors=Sorry! Verification Document file type must be PDF/Doc/Docx/JPG/PNG.' );
                    exit;
                }else{
                    $upload = wp_upload_bits($_FILES["verification_document"]["name"], null, file_get_contents($_FILES["verification_document"]["tmp_name"]));
                    if($upload['error'] == FALSE){
                        $user_id = wp_create_user( $email, $password, $email );
                        $user_id_role = new WP_User($user_id);
                        $user_id_role->set_role('supplier');
                        update_user_meta( $user_id, 'first_name', $first_name );
                        update_user_meta( $user_id, 'last_name', $last_name );
                        update_user_meta( $user_id, 'supplier_phone_number', $phone );
                        update_user_meta( $user_id, 'company_name', $company_name );
                        update_user_meta( $user_id, 'company_number', $company_number );
                        update_user_meta( $user_id, 'vat_number', $vat_number );
                        update_user_meta( $user_id, 'verification_document', $upload['url'] );
                        update_user_meta( $user_id, 'verification_status', 'unverified' );
                        
                        wp_set_current_user($user_id);
                        wp_set_auth_cookie($user_id);
                        
                        // Send Email
                        suppliers_registration_email_send($user_id);
                        wp_redirect( $_POST['redirect'] );
                        exit;
                    }else{
                        wp_redirect( $_POST['redirect'].'?register&errors=File Upload Error! Please try again later or contact us.' );
                        exit;
                    }
                }
            }else{
                wp_redirect( $_POST['redirect'].'?register&errors=Please enter all required fields.' );
                exit;
            }
        }else if(isset($_POST['login-submit'])){
            if($_POST['email'] && $_POST['password']){
                $email      =   sanitize_email( $_POST['email'] );
                $password   =   esc_attr( $_POST['password'] );

                $info = array(
                    'user_login'  => $email,
                    'user_password'  => $password,
                    'remember'  => false
                );
                $user = get_user_by( 'login', $info['user_login'] );

                if ( $user && in_array( 'supplier', $user->roles ) ) {
                    $user_signon = wp_signon( $info, false );
                    if ( is_wp_error($user_signon) ){
                      wp_redirect( $_POST['redirect'].'?errors=Sorry! Credentials are not valid.' );
                      exit;
                    } else {
                        wp_redirect( $_POST['redirect'] );
                        exit;
                    }
                }else{
                    wp_redirect( $_POST['redirect'].'?errors=Sorry! Only suppliers can login here.');
                    exit;
                }
                if ( email_exists( $email ) ) {
                    wp_redirect( $_POST['redirect'].'?register&errors=Sorry! This email already exists.' );
                    exit;
                }else{
                    $user_id = wp_create_user( $email, $password, $email );
                    $user_id_role = new WP_User($user_id);
                    $user_id_role->set_role('supplier');
                    update_user_meta( $user_id, 'company_name', $company_name );
                    update_user_meta( $user_id, 'company_number', $company_number );
                    update_user_meta( $user_id, 'vat_number', $vat_number );
                    
                    wp_set_current_user($user_id);
                    wp_set_auth_cookie($user_id);
                    wp_redirect( $_POST['redirect'] );
                    exit;
                }

            }else{
                wp_redirect( $_POST['redirect'].'?login&errors=Please enter all required fields.' );
                exit;
            }
        }else if(isset($_POST['reupload-submit'])){
            if($_POST['redirect'] && $_POST['user_id']){
                $upload = wp_upload_bits($_FILES["verification_document"]["name"], null, file_get_contents($_FILES["verification_document"]["tmp_name"]));
                if($upload['error'] == FALSE){
                    $user_id = $_POST['user_id'];
                    
                    update_user_meta( $user_id, 'verification_document', $upload['url'] );
                    update_user_meta( $user_id, 'verification_status', 'unverified' );
                    
                    suppliers_reupload_email_send($user_id);
                    wp_redirect( $_POST['redirect'].'?success=Document uploaded successfully. We will inform you with the verification status within 24-48 hours.' );
                    exit;
                }else{
                    wp_redirect( $_POST['redirect'].'?errors=File Upload Error! Please try again later or contact us.' );
                    exit;
                }
            }else{
                wp_redirect( $_POST['redirect'].'?errors=Please enter all required fields.' );
                exit;
            }
        }
    }

    add_action( 'rest_api_init', function() {
        register_rest_route( 'suppliers/v1', '/admin-actions', [
        'methods' => 'GET',
        'callback' => 'suppliers_portal_admin_actions',
        'permission_callback' => '__return_true',
        ] );
    } );
    function suppliers_portal_admin_actions( $params ) {
        // Check SP ID
        if(isset($_GET['sp_id']) && isset($_GET['b_id']) && isset($_GET['action'])){
            $sp_id = $_GET['sp_id'];
            $booking_id = $_GET['b_id'];
            $action = $_GET['action'];
            $sp_status = get_post_meta($sp_id, 'atb_status', true);
            $email = get_post_meta($sp_id, 'atb_user', true);
            $user = get_user_by( 'email', $email );
            $user_id = $user->ID;

            if($sp_status == 'pending' && $action == 'approve'){
		        update_post_meta($sp_id, 'atb_status', 'approved' );
                suppliers_bid_accepted_email_send($user_id, $sp_id, $booking_id);
                suppliers_confirmation_email_send_to_customer($user_id, $sp_id, $booking_id);
            }else if($sp_status == 'pending' && $action == 'cancel'){
		        update_post_meta($sp_id, 'atb_status', 'cancelled' );
                suppliers_bid_cancelled_email_send($user_id, $sp_id, $booking_id);
            }else if($sp_status == 'approved' && $action == 'complete'){
		        update_post_meta($sp_id, 'atb_status', 'completed' );
                //suppliers_bid_cancelled_email_send($user_id, $sp_id, $booking_id);
            }
            wp_redirect( admin_url( '/post.php?post=' . $booking_id . '&action=edit' ), 301 );
            exit();
        }else{
            // redirect
            wp_redirect( get_site_url(), 301 );
            exit();
        }
    }
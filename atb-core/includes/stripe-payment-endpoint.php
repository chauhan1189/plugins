<?php

    add_action( 'rest_api_init', function() {
        register_rest_route( 'endpoint/v1', '/stripe', [
        'methods' => 'POST',
        'callback' => 'stripe_custom_endpoint_attp',
        'permission_callback' => '__return_true',
        ] );
    } );
    
    function stripe_custom_endpoint_attp( $params ) {
        require chauffeur_BASE_DIR .'/includes/vendor/stripe-new/autoload.php';
        global $chauffeur_data;
       
        \Stripe\Stripe::setApiKey($chauffeur_data['stripe_secret_key']);
        $pubkey = $chauffeur_data['stripe_publishable_key'];

        $jsonStr = file_get_contents('php://input'); 
        $jsonObj = json_decode($jsonStr); 
        
        if($jsonObj->request_type == 'create_payment_intent'){
            $itemPrice = $jsonObj->selectedvehicleprice;            
            $itemPriceCents = round($itemPrice*100); 

            // Set content type to JSON 
            header('Content-Type: application/json'); 
            global $chauffeur_data;
            try {
                // Create PaymentIntent with amount and currency 
                $paymentIntent = \Stripe\PaymentIntent::create([ 
                    'amount' => $itemPriceCents, 
                    'currency' => $chauffeur_data['stripe-currency'],
                    'description' => "Taxi Booking", 
                    'payment_method_types' => [ 
                        'card' 
                    ] 
                ]); 
                
                $output = [ 
                    'id' => $paymentIntent->id, 
                    'clientSecret' => $paymentIntent->client_secret 
                ]; 
                //	print_r($output);
                echo json_encode($output); 
            } catch(\Stripe\Exception\CardException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getError()->message]); 
            }catch (\Stripe\Exception\RateLimitException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getError()->message]); 
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getError()->message]); 
            } catch (\Stripe\Exception\AuthenticationException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getError()->message]); 
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getError()->message]); 
            } catch (\Stripe\Exception\ApiErrorException $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getError()->message]); 
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => $e->getError()->message]); 
            }
       }elseif($jsonObj->request_type == 'create_customer'){ 
           $payment_intent_id = !empty($jsonObj->payment_intent_id)?$jsonObj->payment_intent_id:''; 
           $name = !empty($jsonObj->name)?$jsonObj->name:''; 
           $email = !empty($jsonObj->email)?$jsonObj->email:''; 
            
           // Add customer to stripe 
            try {   
                $customer = \Stripe\Customer::create(array(  
                    'name' => $name,  
                    'email' => $email 
                ));  
            }catch(\Stripe\Exception\CardException $e) {
                $api_error = $e->getError()->message;
            }catch (\Stripe\Exception\RateLimitException $e) {
                $api_error = $e->getError()->message; 
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                $api_error = $e->getError()->message;
            } catch (\Stripe\Exception\AuthenticationException $e) {
                $api_error = $e->getError()->message;
            } catch (\Stripe\Exception\ApiConnectionException $e) {
                $api_error = $e->getError()->message;
            } catch (\Stripe\Exception\ApiErrorException $e) {
                $api_error = $e->getError()->message;
            } catch (Exception $e) {
                $api_error = $e->getError()->message;
            }
            
           if(empty($api_error) && $customer){ 
               try { 
                   // Update PaymentIntent with the customer ID 
                   $paymentIntent = \Stripe\PaymentIntent::update($payment_intent_id, [ 
                       'customer' => $customer->id 
                   ]); 
               } catch (Exception $e) {  
                   // log or do what you want 
               } 
                
               $output = [ 
                   'id' => $payment_intent_id, 
                   'customer_id' => $customer->id 
               ]; 
               echo json_encode($output); 
           }else{ 
               http_response_code(500);
               echo json_encode(['error' => $api_error]); 
           } 
        }else{ 
            http_response_code(500); 
            echo json_encode(['error' => 'Transaction has been failed!']); 
        }

    }
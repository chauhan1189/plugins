<?php 

// Product Details 
// Minimum amount is $0.50 US 
$itemName = "Demo Product"; 
$itemNumber = "PN12345"; 
$itemPrice = 25; 
$currency = "USD"; 
 
// Stripe API configuration  
define('STRIPE_API_KEY', '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'); 
define('STRIPE_PUBLISHABLE_KEY', 'pk_live_nGU5sOxhqGlnq9Zy8e3S10yT');  
 global $chauffeur_data;
// Include the Stripe PHP library 
require_once('vendor/autoload.php');
 
// Set API key 
\Stripe\Stripe::setApiKey(STRIPE_API_KEY); 
 
// Retrieve JSON from POST body 
$jsonStr = file_get_contents('php://input'); 
$jsonObj = json_decode($jsonStr); 
 
if($jsonObj->request_type == 'create_payment_intent'){ 
     $itemPrice = $jsonObj->selectedvehicleprice;
    // Define item price and convert to cents 
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
    } catch (Error $e) { 
        http_response_code(500); 
        echo json_encode(['error' => $e->getMessage()]); 
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
    }catch(Exception $e) {   
        $api_error = $e->getMessage();   
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
}elseif($jsonObj->request_type == 'payment_insert'){ 
    $payment_intent = !empty($jsonObj->payment_intent)?$jsonObj->payment_intent:''; 
    $customer_id = !empty($jsonObj->customer_id)?$jsonObj->customer_id:''; 
     
    // Retrieve customer info 
    try {   
        $customer = \Stripe\Customer::retrieve($customer_id);  
    }catch(Exception $e) {   
        $api_error = $e->getMessage();   
    } 
     
    // Check whether the charge was successful 
    if(!empty($payment_intent) && $payment_intent->status == 'succeeded'){ 
        // Transaction details  
        $transactionID = $payment_intent->id; 
        $paidAmount = $payment_intent->amount; 
        $paidAmount = ($paidAmount/100); 
        $paidCurrency = $payment_intent->currency; 
        $payment_status = $payment_intent->status; 
         
        $name = $email = ''; 
        if(!empty($customer)){ 
            $name = !empty($customer->name)?$customer->name:''; 
            $email = !empty($customer->email)?$customer->email:''; 
        } 
         
        // Check if any transaction data is exists already with the same TXN ID 
        $sqlQ = "SELECT id FROM transactions WHERE txn_id = ?"; 
        $stmt = $db->prepare($sqlQ);  
        $stmt->bind_param("s", $db_txn_id); 
        $db_txn_id = $transactionID; 
        $stmt->execute(); 
        $result = $stmt->get_result(); 
        $prevRow = $result->fetch_assoc(); 
         
        $payment_id = 0; 
        if(!empty($prevRow)){ 
            $payment_id = $prevRow['id']; 
        }else{ 
            // Insert transaction data into the database 
            $sqlQ = "INSERT INTO transactions (customer_name,customer_email,item_name,item_number,item_price,item_price_currency,paid_amount,paid_amount_currency,txn_id,payment_status,created,modified) VALUES (?,?,?,?,?,?,?,?,?,?,NOW(),NOW())"; 
            $stmt = $db->prepare($sqlQ); 
            $stmt->bind_param("ssssdsdsss", $db_customer_name, $db_customer_email, $db_item_name, $db_item_number, $db_item_price, $db_item_price_currency, $db_paid_amount, $db_paid_amount_currency, $db_txn_id, $db_payment_status); 
            $db_customer_name = $name; 
            $db_customer_email = $email; 
            $db_item_name = $itemName; 
            $db_item_number = $itemNumber; 
            $db_item_price = $itemPrice; 
            $db_item_price_currency = $currency; 
            $db_paid_amount = $paidAmount; 
            $db_paid_amount_currency = $paidCurrency; 
            $db_txn_id = $transactionID; 
            $db_payment_status = $payment_status; 
            $insert = $stmt->execute(); 
             
            if($insert){ 
                $payment_id = $stmt->insert_id; 
            } 
        } 
         
        $output = [ 
            'payment_id' => base64_encode($payment_id) 
        ]; 
        echo json_encode($output); 
    }else{ 
        http_response_code(500); 
        echo json_encode(['error' => 'Transaction has been failed!']); 
    } 
} 
 
?>
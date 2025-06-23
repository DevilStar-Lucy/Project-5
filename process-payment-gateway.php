<?php
include('config/constants.php');
require_once 'classes/PaymentGateway.php';

if(isset($_POST['order_id']) && isset($_POST['payment_method'])) {
    $order_id = $_POST['order_id'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];
    $account_number = isset($_POST['account_number']) ? $_POST['account_number'] : '';
    $pin = isset($_POST['pin']) ? $_POST['pin'] : '';
    
    // Initialize payment gateway
    $paymentGateway = new PaymentGateway($conn);
    
    // Process payment based on method
    switch($payment_method) {
        case 'bkash':
            $result = $paymentGateway->processBkashPayment($order_id, $amount, $account_number);
            break;
            
        case 'nagad':
            $result = $paymentGateway->processNagadPayment($order_id, $amount, $account_number);
            break;
            
        case 'upay':
            $result = $paymentGateway->processUpayPayment($order_id, $amount, $account_number);
            break;
            
        case 'rocket':
            $result = $paymentGateway->processRocketPayment($order_id, $amount, $account_number);
            break;
            
        case 'cod':
            // Handle Cash on Delivery
            $transaction_id = 'COD_' . $order_id . '_' . time();
            
            $sql_payment = "INSERT INTO tbl_payment SET
                order_id = '$order_id',
                payment_method = 'cod',
                transaction_id = '$transaction_id',
                amount = '$amount',
                payment_status = 'pending'
            ";
            
            $res_payment = mysqli_query($conn, $sql_payment);
            
            if($res_payment) {
                $result = [
                    'status' => 'success',
                    'transaction_id' => $transaction_id,
                    'message' => 'Order confirmed for Cash on Delivery'
                ];
            } else {
                $result = ['status' => 'error', 'message' => 'Failed to process COD order'];
            }
            break;
            
        default:
            $result = ['status' => 'error', 'message' => 'Invalid payment method'];
    }
    
    // Handle payment result
    if($result['status'] === 'success') {
        // Update order status
        $order_status = ($payment_method === 'cod') ? 'Confirmed (COD)' : 'Paid';
        $payment_status = ($payment_method === 'cod') ? 'pending' : 'completed';
        
        $sql_update = "UPDATE tbl_order SET 
            status = '$order_status',
            payment_status = '$payment_status',
            transaction_id = '{$result['transaction_id']}'
            WHERE id = '$order_id'
        ";
        
        mysqli_query($conn, $sql_update);
        
        // Update payment record if not COD
        if($payment_method !== 'cod') {
            $sql_update_payment = "UPDATE tbl_payment SET 
                payment_status = 'completed',
                gateway_response = '" . json_encode($result) . "'
                WHERE order_id = '$order_id' AND transaction_id = '{$result['transaction_id']}'
            ";
            mysqli_query($conn, $sql_update_payment);
        }
        
        $_SESSION['payment'] = "<div class='success text-center'>{$result['message']}</div>";
        
        // Redirect to payment gateway if URL provided
        if(isset($result['payment_url'])) {
            header('location:' . $result['payment_url']);
        } else {
            header('location:' . SITEURL . 'order-success.php?order_id=' . $order_id);
        }
    } else {
        $_SESSION['payment'] = "<div class='error text-center'>{$result['message']}</div>";
        header('location:' . SITEURL . 'payment.php?order_id=' . $order_id);
    }
} else {
    header('location:' . SITEURL);
}
?>
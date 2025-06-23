<?php
include('config/constants.php');

if(isset($_POST['order_id']) && isset($_POST['payment_method'])) {
    $order_id = $_POST['order_id'];
    $payment_method = $_POST['payment_method'];
    $amount = $_POST['amount'];
    $account_number = isset($_POST['account_number']) ? $_POST['account_number'] : '';
    $pin = isset($_POST['pin']) ? $_POST['pin'] : '';
    
    // Generate transaction ID
    $transaction_id = 'TXN' . time() . rand(1000, 9999);
    $payment_date = date('Y-m-d H:i:s');
    
    // Simulate payment processing (In real implementation, you would integrate with actual payment APIs)
    $payment_status = 'pending';
    
    // For demonstration, we'll simulate different success rates for different methods
    $success_rate = rand(1, 100);
    
    if($payment_method == 'cod') {
        $payment_status = 'pending'; // COD is always pending until delivery
    } else {
        // Simulate payment gateway response
        if($success_rate > 10) { // 90% success rate for demo
            $payment_status = 'completed';
        } else {
            $payment_status = 'failed';
        }
    }
    
    // Insert payment record
    $sql_payment = "INSERT INTO tbl_payment SET
        order_id = '$order_id',
        payment_method = '$payment_method',
        transaction_id = '$transaction_id',
        amount = '$amount',
        account_number = '$account_number',
        payment_status = '$payment_status',
        payment_date = '$payment_date'
    ";
    
    $res_payment = mysqli_query($conn, $sql_payment);
    
    if($res_payment) {
        if($payment_status == 'completed' || $payment_status == 'pending') {
            // Update order status
            $order_status = ($payment_method == 'cod') ? 'Confirmed (COD)' : 'Paid';
            
            $sql_update = "UPDATE tbl_order SET 
                status = '$order_status',
                payment_status = '$payment_status',
                transaction_id = '$transaction_id'
                WHERE id = '$order_id'
            ";
            
            mysqli_query($conn, $sql_update);
            
            if($payment_status == 'completed') {
                $_SESSION['payment'] = "<div class='success text-center'>Payment Successful! Transaction ID: $transaction_id</div>";
            } else {
                $_SESSION['payment'] = "<div class='success text-center'>Order Confirmed! You will pay on delivery.</div>";
            }
            
            header('location:' . SITEURL . 'order-success.php?order_id=' . $order_id);
        } else {
            $_SESSION['payment'] = "<div class='error text-center'>Payment Failed! Please try again.</div>";
            header('location:' . SITEURL . 'payment.php?order_id=' . $order_id);
        }
    } else {
        $_SESSION['payment'] = "<div class='error text-center'>Payment processing error! Please try again.</div>";
        header('location:' . SITEURL . 'payment.php?order_id=' . $order_id);
    }
} else {
    header('location:' . SITEURL);
}
?>
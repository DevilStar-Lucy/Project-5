<?php
include('../config/constants.php');

if(isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // Get payment details
    $sql = "SELECT p.*, o.customer_name 
            FROM tbl_payment p 
            JOIN tbl_order o ON p.order_id = o.id 
            WHERE p.order_id = '$order_id' AND p.payment_status = 'completed'";
    $res = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        
        // Check if refund already exists
        $sql_check = "SELECT * FROM tbl_refund WHERE order_id = '$order_id'";
        $res_check = mysqli_query($conn, $sql_check);
        
        if(mysqli_num_rows($res_check) == 0) {
            // Create refund record
            $refund_id = 'REF' . time() . rand(1000, 9999);
            $refund_date = date('Y-m-d H:i:s');
            
            $sql_refund = "INSERT INTO tbl_refund SET
                order_id = '$order_id',
                refund_id = '$refund_id',
                amount = '{$row['amount']}',
                payment_method = '{$row['payment_method']}',
                refund_status = 'processing',
                refund_date = '$refund_date'
            ";
            
            $res_refund = mysqli_query($conn, $sql_refund);
            
            if($res_refund) {
                // Update order status
                $sql_update = "UPDATE tbl_order SET status = 'Refunded' WHERE id = '$order_id'";
                mysqli_query($conn, $sql_update);
                
                $_SESSION['refund'] = "<div class='success'>Refund initiated successfully! Refund ID: $refund_id</div>";
            } else {
                $_SESSION['refund'] = "<div class='error'>Failed to initiate refund.</div>";
            }
        } else {
            $_SESSION['refund'] = "<div class='error'>Refund already exists for this order.</div>";
        }
    } else {
        $_SESSION['refund'] = "<div class='error'>Invalid order or payment not found.</div>";
    }
    
    header('location:' . SITEURL . 'admin/manage-refund.php');
} else {
    header('location:' . SITEURL . 'admin/manage-payment.php');
}
?>
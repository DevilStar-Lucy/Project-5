<?php
include('config/constants.php');

if(isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // Get order details
    $sql = "SELECT o.*, p.payment_status, p.payment_method, p.amount as paid_amount 
            FROM tbl_order o 
            LEFT JOIN tbl_payment p ON o.id = p.order_id 
            WHERE o.id = '$order_id'";
    $res = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        
        // Check if order can be cancelled
        if(in_array($row['status'], ['Ordered', 'Confirmed (COD)', 'Paid'])) {
            
            // Update order status to cancelled
            $sql_cancel = "UPDATE tbl_order SET status = 'Cancelled' WHERE id = '$order_id'";
            $res_cancel = mysqli_query($conn, $sql_cancel);
            
            if($res_cancel) {
                // If payment was made, initiate refund
                if($row['payment_status'] == 'completed' && $row['payment_method'] != 'cod') {
                    
                    // Create refund record
                    $refund_id = 'REF' . time() . rand(1000, 9999);
                    $refund_date = date('Y-m-d H:i:s');
                    
                    $sql_refund = "INSERT INTO tbl_refund SET
                        order_id = '$order_id',
                        refund_id = '$refund_id',
                        amount = '{$row['paid_amount']}',
                        payment_method = '{$row['payment_method']}',
                        refund_status = 'processing',
                        refund_date = '$refund_date'
                    ";
                    
                    mysqli_query($conn, $sql_refund);
                    
                    $_SESSION['cancel'] = "<div class='success text-center'>Order cancelled successfully! Refund of ৳{$row['paid_amount']} is being processed. Refund ID: $refund_id</div>";
                } else {
                    $_SESSION['cancel'] = "<div class='success text-center'>Order cancelled successfully!</div>";
                }
                
                header('location:' . SITEURL . 'track-order.php?order_id=' . $order_id);
            } else {
                $_SESSION['cancel'] = "<div class='error text-center'>Failed to cancel order. Please try again.</div>";
                header('location:' . SITEURL . 'track-order.php?order_id=' . $order_id);
            }
        } else {
            $_SESSION['cancel'] = "<div class='error text-center'>This order cannot be cancelled at this stage.</div>";
            header('location:' . SITEURL . 'track-order.php?order_id=' . $order_id);
        }
    } else {
        header('location:' . SITEURL);
    }
} else {
    header('location:' . SITEURL);
}
?>
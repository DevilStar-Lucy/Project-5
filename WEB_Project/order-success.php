<?php include('partials-front/menu.php'); ?>

<?php
if(isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
    // Get order details
    $sql = "SELECT o.*, p.transaction_id, p.payment_method, p.payment_status 
            FROM tbl_order o 
            LEFT JOIN tbl_payment p ON o.id = p.order_id 
            WHERE o.id = '$order_id'";
    $res = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
    } else {
        header('location:' . SITEURL);
    }
} else {
    header('location:' . SITEURL);
}
?>

<section class="order-success">
    <div class="container">
        <?php 
        if(isset($_SESSION['payment'])) {
            echo $_SESSION['payment'];
            unset($_SESSION['payment']);
        }
        ?>
        
        <div class="success-container">
            <div class="success-icon">
                <img src="https://cdn-icons-png.flaticon.com/512/190/190411.png" alt="Success" width="100">
            </div>
            
            <h2 class="text-center">Order Placed Successfully!</h2>
            
            <div class="order-details">
                <h3>Order Information</h3>
                <div class="detail-row">
                    <span>Order ID:</span>
                    <span>#<?php echo $order_id; ?></span>
                </div>
                <div class="detail-row">
                    <span>Food Item:</span>
                    <span><?php echo $row['food']; ?></span>
                </div>
                <div class="detail-row">
                    <span>Quantity:</span>
                    <span><?php echo $row['qty']; ?></span>
                </div>
                <div class="detail-row">
                    <span>Total Amount:</span>
                    <span>৳<?php echo $row['total']; ?></span>
                </div>
                <div class="detail-row">
                    <span>Payment Method:</span>
                    <span><?php echo ucfirst(str_replace('_', ' ', $row['payment_method'])); ?></span>
                </div>
                <div class="detail-row">
                    <span>Payment Status:</span>
                    <span class="status-<?php echo $row['payment_status']; ?>">
                        <?php echo ucfirst($row['payment_status']); ?>
                    </span>
                </div>
                <?php if($row['transaction_id']): ?>
                <div class="detail-row">
                    <span>Transaction ID:</span>
                    <span><?php echo $row['transaction_id']; ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span>Order Status:</span>
                    <span><?php echo $row['status']; ?></span>
                </div>
            </div>
            
            <div class="customer-details">
                <h3>Delivery Information</h3>
                <div class="detail-row">
                    <span>Name:</span>
                    <span><?php echo $row['customer_name']; ?></span>
                </div>
                <div class="detail-row">
                    <span>Contact:</span>
                    <span><?php echo $row['customer_contact']; ?></span>
                </div>
                <div class="detail-row">
                    <span>Email:</span>
                    <span><?php echo $row['customer_email']; ?></span>
                </div>
                <div class="detail-row">
                    <span>Address:</span>
                    <span><?php echo $row['customer_address']; ?></span>
                </div>
            </div>
            
            <div class="action-buttons">
                <a href="<?php echo SITEURL; ?>track-order.php?order_id=<?php echo $order_id; ?>" class="btn btn-primary">Track Order</a>
                <a href="<?php echo SITEURL; ?>" class="btn btn-secondary">Continue Shopping</a>
                
                <?php if($row['status'] == 'Ordered' || $row['status'] == 'Confirmed (COD)' || $row['status'] == 'Paid'): ?>
                <a href="<?php echo SITEURL; ?>cancel-order.php?order_id=<?php echo $order_id; ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>
<?php include('partials-front/menu.php'); ?>

<?php
if(isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    
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

<section class="track-order">
    <div class="container">
        <h2 class="text-center">Track Your Order</h2>
        
        <div class="tracking-container">
            <div class="order-info">
                <h3>Order #<?php echo $order_id; ?></h3>
                <p><strong>Food:</strong> <?php echo $row['food']; ?></p>
                <p><strong>Total:</strong> ৳<?php echo $row['total']; ?></p>
                <p><strong>Order Date:</strong> <?php echo date('M d, Y h:i A', strtotime($row['order_date'])); ?></p>
            </div>
            
            <div class="tracking-progress">
                <div class="progress-step <?php echo ($row['status'] != 'Cancelled') ? 'completed' : ''; ?>">
                    <div class="step-icon">1</div>
                    <div class="step-text">Order Placed</div>
                </div>
                
                <div class="progress-step <?php echo (in_array($row['status'], ['Paid', 'Confirmed (COD)', 'On Delivery', 'Delivered'])) ? 'completed' : ''; ?>">
                    <div class="step-icon">2</div>
                    <div class="step-text">Order Confirmed</div>
                </div>
                
                <div class="progress-step <?php echo (in_array($row['status'], ['On Delivery', 'Delivered'])) ? 'completed' : ''; ?>">
                    <div class="step-icon">3</div>
                    <div class="step-text">On Delivery</div>
                </div>
                
                <div class="progress-step <?php echo ($row['status'] == 'Delivered') ? 'completed' : ''; ?>">
                    <div class="step-icon">4</div>
                    <div class="step-text">Delivered</div>
                </div>
            </div>
            
            <div class="current-status">
                <h3>Current Status</h3>
                <div class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $row['status'])); ?>">
                    <?php echo $row['status']; ?>
                </div>
                
                <?php if($row['status'] == 'Cancelled'): ?>
                <p class="cancellation-note">Your order has been cancelled. If you made a payment, the refund will be processed within 3-5 business days.</p>
                <?php endif; ?>
            </div>
            
            <div class="action-buttons">
                <?php if(in_array($row['status'], ['Ordered', 'Confirmed (COD)', 'Paid'])): ?>
                <a href="<?php echo SITEURL; ?>cancel-order.php?order_id=<?php echo $order_id; ?>" 
                   class="btn btn-danger" 
                   onclick="return confirm('Are you sure you want to cancel this order?')">Cancel Order</a>
                <?php endif; ?>
                
                <a href="<?php echo SITEURL; ?>" class="btn btn-primary">Order Again</a>
            </div>
        </div>
    </div>
</section>

<?php include('partials-front/footer.php'); ?>
<?php include('partials/menu.php'); ?>

<div class="main-content">
    <div class="wrapper">
        <h1>Manage Payments</h1>

        <br /><br />

        <?php 
            if(isset($_SESSION['refund'])) {
                echo $_SESSION['refund'];
                unset($_SESSION['refund']);
            }
        ?>

        <table class="tbl-full">
            <tr>
                <th>S.N.</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Transaction ID</th>
                <th>Payment Status</th>
                <th>Payment Date</th>
                <th>Actions</th>
            </tr>

            <?php 
                $sql = "SELECT p.*, o.customer_name, o.food 
                        FROM tbl_payment p 
                        JOIN tbl_order o ON p.order_id = o.id 
                        ORDER BY p.payment_date DESC";
                $res = mysqli_query($conn, $sql);
                $count = mysqli_num_rows($res);
                $sn = 1;

                if($count > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $id = $row['id'];
                        $order_id = $row['order_id'];
                        $customer_name = $row['customer_name'];
                        $amount = $row['amount'];
                        $payment_method = $row['payment_method'];
                        $transaction_id = $row['transaction_id'];
                        $payment_status = $row['payment_status'];
                        $payment_date = $row['payment_date'];
                        ?>

                        <tr>
                            <td><?php echo $sn++; ?>.</td>
                            <td>#<?php echo $order_id; ?></td>
                            <td><?php echo $customer_name; ?></td>
                            <td>৳<?php echo $amount; ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $payment_method)); ?></td>
                            <td><?php echo $transaction_id; ?></td>
                            <td>
                                <span class="status-<?php echo $payment_status; ?>">
                                    <?php echo ucfirst($payment_status); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y h:i A', strtotime($payment_date)); ?></td>
                            <td>
                                <a href="<?php echo SITEURL; ?>admin/view-payment.php?id=<?php echo $id; ?>" class="btn-secondary">View Details</a>
                                <?php if($payment_status == 'completed'): ?>
                                <a href="<?php echo SITEURL; ?>admin/process-refund.php?order_id=<?php echo $order_id; ?>" 
                                   class="btn-danger" 
                                   onclick="return confirm('Are you sure you want to process a refund for this payment?')">Process Refund</a>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php
                    }
                } else {
                    echo "<tr><td colspan='9' class='error'>No payments found</td></tr>";
                }
            ?>
        </table>
    </div>
</div>

<?php include('partials/footer.php'); ?>
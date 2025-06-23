<?php include('partials/menu.php'); ?>

<div class="main-content">
    <div class="wrapper">
        <h1>Manage Refunds</h1>

        <br /><br />

        <?php 
            if(isset($_SESSION['refund-update'])) {
                echo $_SESSION['refund-update'];
                unset($_SESSION['refund-update']);
            }
        ?>

        <table class="tbl-full">
            <tr>
                <th>S.N.</th>
                <th>Refund ID</th>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Refund Status</th>
                <th>Refund Date</th>
                <th>Actions</th>
            </tr>

            <?php 
                $sql = "SELECT r.*, o.customer_name, o.customer_contact 
                        FROM tbl_refund r 
                        JOIN tbl_order o ON r.order_id = o.id 
                        ORDER BY r.refund_date DESC";
                $res = mysqli_query($conn, $sql);
                $count = mysqli_num_rows($res);
                $sn = 1;

                if($count > 0) {
                    while($row = mysqli_fetch_assoc($res)) {
                        $id = $row['id'];
                        $refund_id = $row['refund_id'];
                        $order_id = $row['order_id'];
                        $customer_name = $row['customer_name'];
                        $amount = $row['amount'];
                        $payment_method = $row['payment_method'];
                        $refund_status = $row['refund_status'];
                        $refund_date = $row['refund_date'];
                        ?>

                        <tr>
                            <td><?php echo $sn++; ?>.</td>
                            <td><?php echo $refund_id; ?></td>
                            <td>#<?php echo $order_id; ?></td>
                            <td><?php echo $customer_name; ?></td>
                            <td>৳<?php echo $amount; ?></td>
                            <td><?php echo ucfirst(str_replace('_', ' ', $payment_method)); ?></td>
                            <td>
                                <span class="status-<?php echo $refund_status; ?>">
                                    <?php echo ucfirst($refund_status); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y h:i A', strtotime($refund_date)); ?></td>
                            <td>
                                <?php if($refund_status == 'processing'): ?>
                                <a href="<?php echo SITEURL; ?>admin/update-refund.php?id=<?php echo $id; ?>&status=completed" 
                                   class="btn-secondary" 
                                   onclick="return confirm('Mark this refund as completed?')">Mark Completed</a>
                                <a href="<?php echo SITEURL; ?>admin/update-refund.php?id=<?php echo $id; ?>&status=failed" 
                                   class="btn-danger" 
                                   onclick="return confirm('Mark this refund as failed?')">Mark Failed</a>
                                <?php else: ?>
                                <span class="btn-disabled">No Action</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                        <?php
                    }
                } else {
                    echo "<tr><td colspan='9' class='error'>No refunds found</td></tr>";
                }
            ?>
        </table>
    </div>
</div>

<?php include('partials/footer.php'); ?>
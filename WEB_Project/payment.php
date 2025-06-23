<?php include('partials-front/menu.php'); ?>

<?php 
    if(isset($_GET['order_id']))
    {
        $order_id = $_GET['order_id'];
        
        $sql = "SELECT * FROM tbl_order WHERE id=$order_id";
        $res = mysqli_query($conn, $sql);
        
        if(mysqli_num_rows($res) == 1)
        {
            $row = mysqli_fetch_assoc($res);
            $total = $row['total'];
            $food = $row['food'];
            $customer_name = $row['customer_name'];
            $customer_email = $row['customer_email'];
            $customer_contact = $row['customer_contact'];
        }
        else
        {
            header('location:'.SITEURL);
        }
    }
    else
    {
        header('location:'.SITEURL);
    }
?>

<section class="payment-section">
    <div class="container">
        <h2 class="text-center">Complete Your Payment</h2>
        
        <div class="payment-container">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-item">
                    <span>Food Item:</span>
                    <span><?php echo $food; ?></span>
                </div>
                <div class="summary-item">
                    <span>Customer:</span>
                    <span><?php echo $customer_name; ?></span>
                </div>
                <div class="summary-item total">
                    <span>Total Amount:</span>
                    <span>৳<?php echo $total; ?></span>
                </div>
            </div>

            <div class="payment-methods">
                <h3>Select Payment Method</h3>
                
                <!-- Mobile Banking -->
                <div class="payment-category">
                    <h4>Mobile Banking</h4>
                    <div class="payment-options">
                        <div class="payment-option" onclick="selectPayment('bkash')">
                            <img src="https://seeklogo.com/images/B/bkash-logo-FBB258B90F-seeklogo.com.png" alt="bKash">
                            <span>bKash</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('nagad')">
                            <img src="https://seeklogo.com/images/N/nagad-logo-7A70CCFEE0-seeklogo.com.png" alt="Nagad">
                            <span>Nagad</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('rocket')">
                            <img src="https://seeklogo.com/images/R/rocket-logo-6B43E0B9E8-seeklogo.com.png" alt="Rocket">
                            <span>Rocket</span>
                        </div>
                    </div>
                </div>

                <!-- Bank Transfer -->
                <div class="payment-category">
                    <h4>Bank Transfer</h4>
                    <div class="payment-options">
                        <div class="payment-option" onclick="selectPayment('dutch_bangla')">
                            <img src="https://seeklogo.com/images/D/dutch-bangla-bank-logo-A1C8C8B8B8-seeklogo.com.png" alt="Dutch Bangla Bank">
                            <span>Dutch Bangla Bank</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('brac_bank')">
                            <img src="https://seeklogo.com/images/B/brac-bank-logo-7E8B8B8B8B-seeklogo.com.png" alt="BRAC Bank">
                            <span>BRAC Bank</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('city_bank')">
                            <img src="https://seeklogo.com/images/C/city-bank-logo-8B8B8B8B8B-seeklogo.com.png" alt="City Bank">
                            <span>City Bank</span>
                        </div>
                    </div>
                </div>

                <!-- Cash on Delivery -->
                <div class="payment-category">
                    <h4>Other Options</h4>
                    <div class="payment-options">
                        <div class="payment-option" onclick="selectPayment('cod')">
                            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Cash on Delivery">
                            <span>Cash on Delivery</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Forms -->
            <div id="payment-forms">
                <!-- bKash Form -->
                <div id="bkash-form" class="payment-form" style="display: none;">
                    <h4>bKash Payment</h4>
                    <form action="process-payment.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="bkash">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label>bKash Account Number</label>
                            <input type="text" name="account_number" placeholder="01XXXXXXXXX" required>
                        </div>
                        <div class="form-group">
                            <label>Transaction PIN</label>
                            <input type="password" name="pin" placeholder="Enter your bKash PIN" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Pay ৳<?php echo $total; ?></button>
                    </form>
                </div>

                <!-- Nagad Form -->
                <div id="nagad-form" class="payment-form" style="display: none;">
                    <h4>Nagad Payment</h4>
                    <form action="process-payment.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="nagad">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label>Nagad Account Number</label>
                            <input type="text" name="account_number" placeholder="01XXXXXXXXX" required>
                        </div>
                        <div class="form-group">
                            <label>PIN</label>
                            <input type="password" name="pin" placeholder="Enter your Nagad PIN" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Pay ৳<?php echo $total; ?></button>
                    </form>
                </div>

                <!-- Rocket Form -->
                <div id="rocket-form" class="payment-form" style="display: none;">
                    <h4>Rocket Payment</h4>
                    <form action="process-payment.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="rocket">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label>Rocket Account Number</label>
                            <input type="text" name="account_number" placeholder="01XXXXXXXXX" required>
                        </div>
                        <div class="form-group">
                            <label>PIN</label>
                            <input type="password" name="pin" placeholder="Enter your Rocket PIN" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Pay ৳<?php echo $total; ?></button>
                    </form>
                </div>

                <!-- Bank Transfer Forms -->
                <div id="dutch_bangla-form" class="payment-form" style="display: none;">
                    <h4>Dutch Bangla Bank Transfer</h4>
                    <form action="process-payment.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="dutch_bangla">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" placeholder="Enter your account number" required>
                        </div>
                        <div class="form-group">
                            <label>PIN/Password</label>
                            <input type="password" name="pin" placeholder="Enter your banking PIN" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Transfer ৳<?php echo $total; ?></button>
                    </form>
                </div>

                <div id="brac_bank-form" class="payment-form" style="display: none;">
                    <h4>BRAC Bank Transfer</h4>
                    <form action="process-payment.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="brac_bank">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" placeholder="Enter your account number" required>
                        </div>
                        <div class="form-group">
                            <label>PIN/Password</label>
                            <input type="password" name="pin" placeholder="Enter your banking PIN" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Transfer ৳<?php echo $total; ?></button>
                    </form>
                </div>

                <div id="city_bank-form" class="payment-form" style="display: none;">
                    <h4>City Bank Transfer</h4>
                    <form action="process-payment.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="city_bank">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label>Account Number</label>
                            <input type="text" name="account_number" placeholder="Enter your account number" required>
                        </div>
                        <div class="form-group">
                            <label>PIN/Password</label>
                            <input type="password" name="pin" placeholder="Enter your banking PIN" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Transfer ৳<?php echo $total; ?></button>
                    </form>
                </div>

                <!-- Cash on Delivery -->
                <div id="cod-form" class="payment-form" style="display: none;">
                    <h4>Cash on Delivery</h4>
                    <div class="cod-info">
                        <p>You have selected Cash on Delivery. Please pay ৳<?php echo $total; ?> when your order arrives.</p>
                        <p><strong>Note:</strong> Our delivery person will collect the payment at your doorstep.</p>
                    </div>
                    <form action="process-payment.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="cod">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        <button type="submit" class="btn btn-primary">Confirm Order</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function selectPayment(method) {
    // Hide all forms
    const forms = document.querySelectorAll('.payment-form');
    forms.forEach(form => form.style.display = 'none');
    
    // Remove active class from all options
    const options = document.querySelectorAll('.payment-option');
    options.forEach(option => option.classList.remove('active'));
    
    // Show selected form
    document.getElementById(method + '-form').style.display = 'block';
    
    // Add active class to selected option
    event.currentTarget.classList.add('active');
}
</script>

<?php include('partials-front/footer.php'); ?>
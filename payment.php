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
        
        <?php 
        if(isset($_SESSION['payment'])) {
            echo $_SESSION['payment'];
            unset($_SESSION['payment']);
        }
        ?>
        
        <div class="payment-container">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <div class="summary-item">
                    <span>Order ID:</span>
                    <span>#<?php echo $order_id; ?></span>
                </div>
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
                    <h4><i class="fas fa-mobile-alt"></i> Mobile Banking</h4>
                    <div class="payment-options">
                        <div class="payment-option" onclick="selectPayment('bkash')">
                            <img src="https://seeklogo.com/images/B/bkash-logo-FBB258B90F-seeklogo.com.png" alt="bKash" style="width: 50px;">
                            <span>bKash</span>
                            <small>Most Popular</small>
                        </div>
                        <div class="payment-option" onclick="selectPayment('nagad')">
                            <img src="https://seeklogo.com/images/N/nagad-logo-7A70CCFEE0-seeklogo.com.png" alt="Nagad" style="width: 50px;">
                            <span>Nagad</span>
                            <small>Instant Payment</small>
                        </div>
                        <div class="payment-option" onclick="selectPayment('upay')">
                            <img src="https://cdn.worldvectorlogo.com/logos/upay-1.svg" alt="Upay" style="width: 50px;">
                            <span>Upay</span>
                            <small>Fast & Secure</small>
                        </div>
                        <div class="payment-option" onclick="selectPayment('rocket')">
                            <img src="https://seeklogo.com/images/R/rocket-logo-6B43E0B9E8-seeklogo.com.png" alt="Rocket" style="width: 50px;">
                            <span>Rocket</span>
                            <small>Reliable</small>
                        </div>
                    </div>
                </div>

                <!-- Bank Transfer -->
                <div class="payment-category">
                    <h4><i class="fas fa-university"></i> Bank Transfer</h4>
                    <div class="payment-options">
                        <div class="payment-option" onclick="selectPayment('dutch_bangla')">
                            <img src="https://seeklogo.com/images/D/dutch-bangla-bank-logo-A1C8C8B8B8-seeklogo.com.png" alt="Dutch Bangla Bank" style="width: 50px;">
                            <span>Dutch Bangla Bank</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('brac_bank')">
                            <img src="https://seeklogo.com/images/B/brac-bank-logo-7E8B8B8B8B-seeklogo.com.png" alt="BRAC Bank" style="width: 50px;">
                            <span>BRAC Bank</span>
                        </div>
                        <div class="payment-option" onclick="selectPayment('city_bank')">
                            <img src="https://seeklogo.com/images/C/city-bank-logo-8B8B8B8B8B-seeklogo.com.png" alt="City Bank" style="width: 50px;">
                            <span>City Bank</span>
                        </div>
                    </div>
                </div>

                <!-- Cash on Delivery -->
                <div class="payment-category">
                    <h4><i class="fas fa-hand-holding-usd"></i> Other Options</h4>
                    <div class="payment-options">
                        <div class="payment-option" onclick="selectPayment('cod')">
                            <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Cash on Delivery" style="width: 50px;">
                            <span>Cash on Delivery</span>
                            <small>Pay when you receive</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Forms -->
            <div id="payment-forms">
                <!-- bKash Form -->
                <div id="bkash-form" class="payment-form" style="display: none;">
                    <h4><img src="https://seeklogo.com/images/B/bkash-logo-FBB258B90F-seeklogo.com.png" alt="bKash" style="width: 30px; margin-right: 10px;"> bKash Payment</h4>
                    <div class="payment-info">
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Enter your bKash account number</li>
                            <li>You will receive a payment request</li>
                            <li>Enter your bKash PIN to complete payment</li>
                        </ol>
                    </div>
                    <form action="process-payment-gateway.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="bkash">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> bKash Account Number</label>
                            <input type="text" name="account_number" placeholder="01XXXXXXXXX" pattern="01[3-9][0-9]{8}" required>
                            <small>Enter your 11-digit bKash number</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-credit-card"></i> Pay ৳<?php echo $total; ?> with bKash
                        </button>
                    </form>
                </div>

                <!-- Nagad Form -->
                <div id="nagad-form" class="payment-form" style="display: none;">
                    <h4><img src="https://seeklogo.com/images/N/nagad-logo-7A70CCFEE0-seeklogo.com.png" alt="Nagad" style="width: 30px; margin-right: 10px;"> Nagad Payment</h4>
                    <div class="payment-info">
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Enter your Nagad account number</li>
                            <li>You will be redirected to Nagad payment page</li>
                            <li>Complete payment using your Nagad PIN</li>
                        </ol>
                    </div>
                    <form action="process-payment-gateway.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="nagad">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Nagad Account Number</label>
                            <input type="text" name="account_number" placeholder="01XXXXXXXXX" pattern="01[3-9][0-9]{8}" required>
                            <small>Enter your 11-digit Nagad number</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-credit-card"></i> Pay ৳<?php echo $total; ?> with Nagad
                        </button>
                    </form>
                </div>

                <!-- Upay Form -->
                <div id="upay-form" class="payment-form" style="display: none;">
                    <h4><img src="https://cdn.worldvectorlogo.com/logos/upay-1.svg" alt="Upay" style="width: 30px; margin-right: 10px;"> Upay Payment</h4>
                    <div class="payment-info">
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Enter your Upay account number</li>
                            <li>Complete payment using Upay app or USSD</li>
                            <li>Payment will be processed instantly</li>
                        </ol>
                    </div>
                    <form action="process-payment-gateway.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="upay">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Upay Account Number</label>
                            <input type="text" name="account_number" placeholder="01XXXXXXXXX" pattern="01[3-9][0-9]{8}" required>
                            <small>Enter your 11-digit Upay number</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-credit-card"></i> Pay ৳<?php echo $total; ?> with Upay
                        </button>
                    </form>
                </div>

                <!-- Rocket Form -->
                <div id="rocket-form" class="payment-form" style="display: none;">
                    <h4><img src="https://seeklogo.com/images/R/rocket-logo-6B43E0B9E8-seeklogo.com.png" alt="Rocket" style="width: 30px; margin-right: 10px;"> Rocket Payment</h4>
                    <div class="payment-info">
                        <p><strong>Instructions:</strong></p>
                        <ol>
                            <li>Enter your Rocket account number</li>
                            <li>You will receive a payment request</li>
                            <li>Enter your Rocket PIN to confirm</li>
                        </ol>
                    </div>
                    <form action="process-payment-gateway.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="rocket">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        
                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Rocket Account Number</label>
                            <input type="text" name="account_number" placeholder="01XXXXXXXXX" pattern="01[3-9][0-9]{8}" required>
                            <small>Enter your 11-digit Rocket number</small>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-credit-card"></i> Pay ৳<?php echo $total; ?> with Rocket
                        </button>
                    </form>
                </div>

                <!-- Cash on Delivery -->
                <div id="cod-form" class="payment-form" style="display: none;">
                    <h4><i class="fas fa-hand-holding-usd"></i> Cash on Delivery</h4>
                    <div class="cod-info">
                        <div class="payment-info">
                            <p><strong>Cash on Delivery Details:</strong></p>
                            <ul>
                                <li>Pay ৳<?php echo $total; ?> when your order arrives</li>
                                <li>Our delivery person will collect the payment</li>
                                <li>Please keep exact change ready</li>
                                <li>Delivery time: 30-45 minutes</li>
                            </ul>
                        </div>
                    </div>
                    <form action="process-payment-gateway.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                        <input type="hidden" name="payment_method" value="cod">
                        <input type="hidden" name="amount" value="<?php echo $total; ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check-circle"></i> Confirm Cash on Delivery Order
                        </button>
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
    
    // Scroll to form
    document.getElementById(method + '-form').scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
    });
}

// Add loading state to payment buttons
document.querySelectorAll('.payment-form form').forEach(form => {
    form.addEventListener('submit', function() {
        const button = this.querySelector('button[type="submit"]');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        button.disabled = true;
        
        // Re-enable after 10 seconds as fallback
        setTimeout(() => {
            button.innerHTML = originalText;
            button.disabled = false;
        }, 10000);
    });
});
</script>

<style>
.payment-section {
    padding: 100px 0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.payment-container {
    max-width: 800px;
    margin: 2rem auto;
    background: var(--white);
    border-radius: var(--border-radius);
    box-shadow: var(--shadow-hover);
    overflow: hidden;
}

.order-summary {
    background: var(--light-bg);
    padding: 2rem;
    border-bottom: 1px solid var(--border-color);
}

.summary-item {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
}

.summary-item.total {
    border-top: 2px solid var(--primary-color);
    padding-top: 1rem;
    font-weight: 600;
    font-size: 1.2rem;
    color: var(--primary-color);
}

.payment-methods {
    padding: 2rem;
}

.payment-category {
    margin-bottom: 2rem;
}

.payment-category h4 {
    color: var(--text-dark);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--border-color);
}

.payment-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.payment-option {
    background: var(--white);
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    padding: 1rem;
    text-align: center;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
}

.payment-option:hover {
    border-color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow);
}

.payment-option.active {
    border-color: var(--primary-color);
    background: rgba(255, 107, 53, 0.1);
}

.payment-option img {
    width: 50px;
    height: 50px;
    object-fit: contain;
    margin-bottom: 0.5rem;
}

.payment-option span {
    display: block;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.payment-option small {
    color: var(--text-light);
    font-size: 0.8rem;
}

.payment-form {
    background: var(--light-bg);
    padding: 2rem;
    margin: 2rem;
    border-radius: var(--border-radius);
    border: 2px solid var(--primary-color);
}

.payment-form h4 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.payment-info {
    background: var(--white);
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    border-left: 4px solid var(--primary-color);
}

.payment-info ol, .payment-info ul {
    margin-left: 1rem;
}

.payment-info li {
    margin-bottom: 0.5rem;
    color: var(--text-dark);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: var(--text-dark);
}

.form-group input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid var(--border-color);
    border-radius: var(--border-radius);
    font-size: 1rem;
    transition: var(--transition);
}

.form-group input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.form-group small {
    color: var(--text-light);
    font-size: 0.9rem;
    margin-top: 0.25rem;
    display: block;
}

.cod-info {
    background: var(--white);
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

@media (max-width: 768px) {
    .payment-options {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .payment-container {
        margin: 1rem;
    }
    
    .order-summary, .payment-methods, .payment-form {
        padding: 1rem;
    }
}
</style>

<?php include('partials-front/footer.php'); ?>
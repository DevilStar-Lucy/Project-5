<?php include('partials-front/menu.php'); ?>

<?php 
    // Check if user is logged in
    if(!isset($_SESSION['customer_id'])) {
        $_SESSION['login_required'] = "<div class='error text-center'>Please login to place an order.</div>";
        header('location:' . SITEURL . 'auth/login.php');
        exit();
    }

    if(isset($_GET['food_id'])) {
        $food_id = $_GET['food_id'];
        $sql = "SELECT * FROM tbl_food WHERE id=$food_id";
        $res = mysqli_query($conn, $sql);
        $count = mysqli_num_rows($res);
        
        if($count == 1) {
            $row = mysqli_fetch_assoc($res);
            $title = $row['title'];
            $price = $row['price'];
            $description = $row['description'];
            $image_name = $row['image_name'];
        } else {
            header('location:'.SITEURL);
        }
    } else {
        header('location:'.SITEURL);
    }
?>

<section class="order-section">
    <div class="container">
        <div class="order-container">
            <div class="order-header">
                <h2><i class="fas fa-shopping-cart"></i> Complete Your Order</h2>
                <p>Fill in the details below to place your order</p>
            </div>

            <form action="" method="POST" class="order-form">
                <div class="form-section">
                    <h3><i class="fas fa-utensils"></i> Selected Item</h3>
                    
                    <div class="selected-food">
                        <div class="food-image">
                            <?php 
                                if($image_name == "") {
                                    echo "<div class='error'>Image not Available.</div>";
                                } else {
                                    ?>
                                    <img src="<?php echo SITEURL; ?>images/food/<?php echo $image_name; ?>" alt="<?php echo $title; ?>">
                                    <?php
                                }
                            ?>
                        </div>
                        
                        <div class="food-info">
                            <h4><?php echo $title; ?></h4>
                            <p class="food-description"><?php echo $description; ?></p>
                            <div class="food-price">৳<?php echo $price; ?></div>
                            <input type="hidden" name="food" value="<?php echo $title; ?>">
                            <input type="hidden" name="price" value="<?php echo $price; ?>">
                            <input type="hidden" name="food_id" value="<?php echo $food_id; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-sort-numeric-up"></i> Quantity</label>
                        <div class="quantity-control">
                            <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
                            <span class="quantity-display" id="quantity-display">1</span>
                            <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                            <input type="hidden" name="qty" id="qty-input" value="1">
                        </div>
                        <div style="margin-top: 1rem;">
                            <strong>Total: ৳<span id="total-price"><?php echo $price; ?></span></strong>
                        </div>
                    </div>
                </div>
                
                <div class="form-section">
                    <h3><i class="fas fa-truck"></i> Delivery Information</h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Full Name</label>
                            <input type="text" name="full-name" placeholder="Enter your full name" class="form-control" 
                                   value="<?php echo $_SESSION['customer_name']; ?>" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" name="contact" placeholder="01XXXXXXXXX" class="form-control" 
                                   value="<?php echo $_SESSION['customer_phone']; ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" placeholder="your.email@example.com" class="form-control" 
                               value="<?php echo $_SESSION['customer_email']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Delivery Address</label>
                        <textarea name="address" rows="4" placeholder="Enter your complete delivery address including area, road, house number..." class="form-control" required></textarea>
                    </div>
                </div>

                <div style="text-align: center; padding: 2rem;">
                    <button type="submit" name="submit" class="btn btn-primary" style="padding: 15px 40px; font-size: 1.1rem;">
                        <i class="fas fa-check-circle"></i> Confirm Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    const basePrice = <?php echo $price; ?>;
    
    function updateTotal() {
        const quantity = parseInt(document.getElementById('qty-input').value);
        const total = basePrice * quantity;
        document.getElementById('total-price').textContent = total;
    }
    
    function increaseQuantity() {
        const qtyInput = document.getElementById('qty-input');
        const qtyDisplay = document.getElementById('quantity-display');
        let currentQty = parseInt(qtyInput.value);
        
        if (currentQty < 10) {
            currentQty++;
            qtyInput.value = currentQty;
            qtyDisplay.textContent = currentQty;
            updateTotal();
        }
    }
    
    function decreaseQuantity() {
        const qtyInput = document.getElementById('qty-input');
        const qtyDisplay = document.getElementById('quantity-display');
        let currentQty = parseInt(qtyInput.value);
        
        if (currentQty > 1) {
            currentQty--;
            qtyInput.value = currentQty;
            qtyDisplay.textContent = currentQty;
            updateTotal();
        }
    }
</script>

<?php 
    if(isset($_POST['submit'])) {
        $food = mysqli_real_escape_string($conn, $_POST['food']);
        $price = $_POST['price'];
        $qty = $_POST['qty'];
        $total = $price * $qty; 
        $order_date = date("Y-m-d H:i:s");
        $status = "Ordered"; 
        $customer_name = mysqli_real_escape_string($conn, $_POST['full-name']);
        $customer_contact = mysqli_real_escape_string($conn, $_POST['contact']);
        $customer_email = mysqli_real_escape_string($conn, $_POST['email']);
        $customer_address = mysqli_real_escape_string($conn, $_POST['address']);
        $customer_id = $_SESSION['customer_id'];

        // Insert order into database
        $sql2 = "INSERT INTO tbl_order SET 
            customer_id = '$customer_id',
            food = '$food',
            price = $price,
            qty = $qty,
            total = $total,
            order_date = '$order_date',
            status = '$status',
            customer_name = '$customer_name',
            customer_contact = '$customer_contact',
            customer_email = '$customer_email',
            customer_address = '$customer_address'
        ";

        $res2 = mysqli_query($conn, $sql2);

        if($res2 == true) {
            $order_id = mysqli_insert_id($conn);
            $_SESSION['order_success'] = "<div class='success text-center'>Order placed successfully!</div>";
            header('location:'.SITEURL.'payment.php?order_id='.$order_id);
        } else {
            $_SESSION['order'] = "<div class='error text-center'>Failed to place order. Please try again.</div>";
            header('location:'.SITEURL.'order.php?food_id='.$food_id);
        }
    }
?>

<?php include('partials-front/footer.php'); ?>
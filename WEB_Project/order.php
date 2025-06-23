<?php include('partials-front/menu.php'); ?>

<?php 
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
                            <input type="text" name="full-name" placeholder="Enter your full name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-phone"></i> Phone Number</label>
                            <input type="tel" name="contact" placeholder="01XXXXXXXXX" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" placeholder="your.email@example.com" class="form-control" required>
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
        $food = $_POST['food'];
        $price = $_POST['price'];
        $qty = $_POST['qty'];
        $total = $price * $qty; 
        $order_date = date("Y-m-d h:i:sa");
        $status = "Ordered"; 
        $customer_name = $_POST['full-name'];
        $customer_contact = $_POST['contact'];
        $customer_email = $_POST['email'];
        $customer_address = $_POST['address'];

        $sql2 = "INSERT INTO tbl_order SET 
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
            header('location:'.SITEURL.'payment.php?order_id='.$order_id);
        } else {
            $_SESSION['order'] = "<div class='error text-center'>Failed to place order. Please try again.</div>";
            header('location:'.SITEURL);
        }
    }
?>

<?php include('partials-front/footer.php'); ?>
```
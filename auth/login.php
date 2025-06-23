<?php include('../partials-front/menu.php'); ?>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h2>Customer Login</h2>
                <p>Welcome back! Please login to your account</p>
            </div>

            <?php 
                if(isset($_SESSION['login_error'])) {
                    echo $_SESSION['login_error'];
                    unset($_SESSION['login_error']);
                }
                
                if(isset($_SESSION['register_success'])) {
                    echo $_SESSION['register_success'];
                    unset($_SESSION['register_success']);
                }
            ?>

            <form action="" method="POST" class="auth-form">
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <input type="password" name="password" placeholder="Enter your password" class="form-control" required>
                </div>

                <div class="form-group">
                    <button type="submit" name="login" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </div>

                <div class="auth-links">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                    <p><a href="forgot-password.php">Forgot Password?</a></p>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    
    // Check if customer exists
    $sql = "SELECT * FROM tbl_customer WHERE email = '$email'";
    $res = mysqli_query($conn, $sql);
    
    if(mysqli_num_rows($res) == 1) {
        $row = mysqli_fetch_assoc($res);
        
        // Verify password
        if(password_verify($password, $row['password'])) {
            // Set session variables
            $_SESSION['customer_id'] = $row['id'];
            $_SESSION['customer_name'] = $row['full_name'];
            $_SESSION['customer_email'] = $row['email'];
            $_SESSION['customer_phone'] = $row['phone'];
            
            $_SESSION['login_success'] = "<div class='success text-center'>Login successful! Welcome back.</div>";
            header('location:' . SITEURL . 'customer-dashboard.php');
        } else {
            $_SESSION['login_error'] = "<div class='error text-center'>Invalid email or password.</div>";
            header('location:' . SITEURL . 'auth/login.php');
        }
    } else {
        $_SESSION['login_error'] = "<div class='error text-center'>Account not found. Please register first.</div>";
        header('location:' . SITEURL . 'auth/login.php');
    }
}
?>

<?php include('../partials-front/footer.php'); ?>
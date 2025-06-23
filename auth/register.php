<?php include('../partials-front/menu.php'); ?>

<section class="auth-section">
    <div class="container">
        <div class="auth-container">
            <div class="auth-header">
                <h2>Customer Registration</h2>
                <p>Create your account to start ordering delicious food</p>
            </div>

            <?php 
                if(isset($_SESSION['register_error'])) {
                    echo $_SESSION['register_error'];
                    unset($_SESSION['register_error']);
                }
            ?>

            <form action="" method="POST" class="auth-form">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" name="full_name" placeholder="Enter your full name" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" name="phone" placeholder="01XXXXXXXXX" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email" class="form-control" required>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" placeholder="Create a password" class="form-control" required minlength="6">
                    </div>

                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Confirm Password</label>
                        <input type="password" name="confirm_password" placeholder="Confirm your password" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Address</label>
                    <textarea name="address" rows="3" placeholder="Enter your complete address" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" required> 
                        I agree to the <a href="#" target="_blank">Terms and Conditions</a>
                    </label>
                </div>

                <div class="form-group">
                    <button type="submit" name="register" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </div>

                <div class="auth-links">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </div>
</section>

<?php
if(isset($_POST['register'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Validate passwords match
    if($password !== $confirm_password) {
        $_SESSION['register_error'] = "<div class='error text-center'>Passwords do not match.</div>";
        header('location:' . SITEURL . 'auth/register.php');
        exit();
    }
    
    // Check if email already exists
    $sql_check = "SELECT * FROM tbl_customer WHERE email = '$email'";
    $res_check = mysqli_query($conn, $sql_check);
    
    if(mysqli_num_rows($res_check) > 0) {
        $_SESSION['register_error'] = "<div class='error text-center'>Email already registered. Please use a different email.</div>";
        header('location:' . SITEURL . 'auth/register.php');
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new customer
    $sql = "INSERT INTO tbl_customer SET
        full_name = '$full_name',
        email = '$email',
        phone = '$phone',
        password = '$hashed_password',
        address = '$address'
    ";
    
    $res = mysqli_query($conn, $sql);
    
    if($res) {
        $_SESSION['register_success'] = "<div class='success text-center'>Registration successful! Please login to continue.</div>";
        header('location:' . SITEURL . 'auth/login.php');
        exit();
    } else {
        $_SESSION['register_error'] = "<div class='error text-center'>Registration failed. Please try again.</div>";
        header('location:' . SITEURL . 'auth/register.php');
        exit();
    }
}
?>

<?php include('../partials-front/footer.php'); ?>
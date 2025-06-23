<?php
include('config/constants.php');

// Check if customer is logged in
if(!isset($_SESSION['customer_id'])) {
    $_SESSION['login_required'] = "<div class='error text-center'>Please login to update your profile.</div>";
    header('location:' . SITEURL . 'auth/login.php');
    exit();
}

if(isset($_POST['update_profile'])) {
    $customer_id = $_SESSION['customer_id'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Check if email is already taken by another user
    $sql_check = "SELECT id FROM tbl_customer WHERE email = '$email' AND id != $customer_id";
    $res_check = mysqli_query($conn, $sql_check);
    
    if(mysqli_num_rows($res_check) > 0) {
        $_SESSION['profile_error'] = "<div class='error text-center'>Email is already taken by another user.</div>";
        header('location:' . SITEURL . 'customer-dashboard.php');
        exit();
    }
    
    // Update customer profile
    $sql = "UPDATE tbl_customer SET 
            full_name = '$full_name',
            email = '$email',
            phone = '$phone',
            address = '$address'
            WHERE id = $customer_id";
    
    $res = mysqli_query($conn, $sql);
    
    if($res) {
        // Update session variables
        $_SESSION['customer_name'] = $full_name;
        $_SESSION['customer_email'] = $email;
        $_SESSION['customer_phone'] = $phone;
        $_SESSION['customer_address'] = $address;
        
        $_SESSION['profile_success'] = "<div class='success text-center'>Profile updated successfully!</div>";
    } else {
        $_SESSION['profile_error'] = "<div class='error text-center'>Failed to update profile. Please try again.</div>";
    }
    
    header('location:' . SITEURL . 'customer-dashboard.php');
    exit();
} else {
    header('location:' . SITEURL . 'customer-dashboard.php');
    exit();
}
?>
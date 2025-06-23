<?php
session_start();

// Store a logout message
$_SESSION['logout_message'] = "<div class='success text-center'>You have been logged out successfully.</div>";

// Destroy all session variables
session_unset();
session_destroy();

// Start a new session to show the logout message
session_start();
$_SESSION['logout_message'] = "<div class='success text-center'>You have been logged out successfully.</div>";

// Redirect to home page
header('location:' . '../index.php');
exit();
?>
<?php
session_start();

// Destroy all session variables
session_unset();
session_destroy();

// Redirect to home page
header('location:' . '../index.php');
exit();
?>
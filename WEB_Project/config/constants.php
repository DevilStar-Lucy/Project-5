<?php 
    session_start();


    define('SITEURL', 'http://localhost/WEB_Project/');
    define('LOCALHOST', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'WEB_Project');
    
    $conn = mysqli_connect(LOCALHOST, DB_USERNAME, DB_PASSWORD);
    $db_select = mysqli_select_db($conn, DB_NAME) or die(mysqli_error($conn)); 


?>
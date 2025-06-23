<?php
include('config/constants.php');

if (isset($_POST['submit'])) {
    $food_id = mysqli_real_escape_string($conn, $_POST['food_id']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $rating = (int)$_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    $review_date = date("Y-m-d H:i:s");

    if ($rating < 1 || $rating > 5) {
        $_SESSION['review'] = "<div class='error text-center'>Rating must be between 1 and 5.</div>";
        header('location:' . SITEURL);
        exit();
    }

    $sql = "INSERT INTO tbl_review (food_id, customer_name, rating, comment, review_date) 
            VALUES ('$food_id', '$customer_name', '$rating', '$comment', '$review_date')";
    $res = mysqli_query($conn, $sql);

    if ($res) {
        $_SESSION['review'] = "<div class='success text-center'>Review Submitted Successfully.</div>";
        header('location:' . SITEURL . 'foods.php');
    } else {
        $_SESSION['review'] = "<div class='error text-center'>Failed to Submit Review.</div>";
        header('location:' . SITEURL);
    }
} else {
    header('location:' . SITEURL);
}
?>
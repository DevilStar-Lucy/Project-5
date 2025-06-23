<?php
include('../config/constants.php');

if(isset($_GET['id']) && isset($_GET['status'])) {
    $refund_id = $_GET['id'];
    $status = $_GET['status'];
    
    // Validate status
    if(in_array($status, ['completed', 'failed'])) {
        $sql = "UPDATE tbl_refund SET refund_status = '$status' WHERE id = '$refund_id'";
        $res = mysqli_query($conn, $sql);
        
        if($res) {
            $_SESSION['refund-update'] = "<div class='success'>Refund status updated successfully.</div>";
        } else {
            $_SESSION['refund-update'] = "<div class='error'>Failed to update refund status.</div>";
        }
    } else {
        $_SESSION['refund-update'] = "<div class='error'>Invalid refund status.</div>";
    }
    
    header('location:' . SITEURL . 'admin/manage-refund.php');
} else {
    header('location:' . SITEURL . 'admin/manage-refund.php');
}
?>
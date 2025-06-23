<?php
// Supabase Helper Functions - Updated to work with MySQL fallback

class SupabaseHelper {
    private $conn;
    
    public function __construct($mysql_connection = null) {
        $this->conn = $mysql_connection;
    }
    
    // Categories
    public function getCategories($active_only = true) {
        $sql = "SELECT * FROM tbl_category";
        if ($active_only) {
            $sql .= " WHERE active = 'Yes'";
        }
        $sql .= " ORDER BY title ASC";
        
        $res = mysqli_query($this->conn, $sql);
        $categories = [];
        
        if($res) {
            while($row = mysqli_fetch_assoc($res)) {
                $categories[] = $row;
            }
        }
        
        return $categories;
    }
    
    public function getCategoryById($id) {
        $sql = "SELECT * FROM tbl_category WHERE id = $id";
        $res = mysqli_query($this->conn, $sql);
        
        if($res && mysqli_num_rows($res) > 0) {
            return mysqli_fetch_assoc($res);
        }
        
        return null;
    }
    
    // Food Items
    public function getFoodItems($category_id = null, $featured_only = false, $active_only = true) {
        $sql = "SELECT * FROM tbl_food WHERE 1=1";
        
        if ($category_id) {
            $sql .= " AND category_id = $category_id";
        }
        if ($featured_only) {
            $sql .= " AND featured = 'Yes'";
        }
        if ($active_only) {
            $sql .= " AND active = 'Yes'";
        }
        
        $sql .= " ORDER BY featured DESC, id DESC";
        
        $res = mysqli_query($this->conn, $sql);
        $foods = [];
        
        if($res) {
            while($row = mysqli_fetch_assoc($res)) {
                $foods[] = $row;
            }
        }
        
        return $foods;
    }
    
    public function getFoodById($id) {
        $sql = "SELECT * FROM tbl_food WHERE id = $id";
        $res = mysqli_query($this->conn, $sql);
        
        if($res && mysqli_num_rows($res) > 0) {
            return mysqli_fetch_assoc($res);
        }
        
        return null;
    }
    
    public function searchFood($search_term) {
        $search_term = mysqli_real_escape_string($this->conn, $search_term);
        $sql = "SELECT * FROM tbl_food WHERE (title LIKE '%$search_term%' OR description LIKE '%$search_term%') AND active = 'Yes'";
        
        $res = mysqli_query($this->conn, $sql);
        $foods = [];
        
        if($res) {
            while($row = mysqli_fetch_assoc($res)) {
                $foods[] = $row;
            }
        }
        
        return $foods;
    }
    
    // Orders
    public function createOrder($order_data) {
        $fields = [];
        $values = [];
        
        foreach($order_data as $key => $value) {
            $fields[] = $key;
            $values[] = "'" . mysqli_real_escape_string($this->conn, $value) . "'";
        }
        
        $sql = "INSERT INTO tbl_order (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $res = mysqli_query($this->conn, $sql);
        
        if($res) {
            return ['id' => mysqli_insert_id($this->conn)];
        }
        
        return false;
    }
    
    public function getOrderById($id) {
        $sql = "SELECT * FROM tbl_order WHERE id = $id";
        $res = mysqli_query($this->conn, $sql);
        
        if($res && mysqli_num_rows($res) > 0) {
            return mysqli_fetch_assoc($res);
        }
        
        return null;
    }
    
    public function getOrdersByCustomer($customer_email) {
        $customer_email = mysqli_real_escape_string($this->conn, $customer_email);
        $sql = "SELECT * FROM tbl_order WHERE customer_email = '$customer_email' ORDER BY order_date DESC";
        
        $res = mysqli_query($this->conn, $sql);
        $orders = [];
        
        if($res) {
            while($row = mysqli_fetch_assoc($res)) {
                $orders[] = $row;
            }
        }
        
        return $orders;
    }
    
    public function updateOrderStatus($order_id, $status) {
        $status = mysqli_real_escape_string($this->conn, $status);
        $sql = "UPDATE tbl_order SET status = '$status' WHERE id = $order_id";
        
        return mysqli_query($this->conn, $sql);
    }
    
    // Payments
    public function createPayment($payment_data) {
        $fields = [];
        $values = [];
        
        foreach($payment_data as $key => $value) {
            $fields[] = $key;
            $values[] = "'" . mysqli_real_escape_string($this->conn, $value) . "'";
        }
        
        $sql = "INSERT INTO tbl_payment (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $res = mysqli_query($this->conn, $sql);
        
        if($res) {
            return ['id' => mysqli_insert_id($this->conn)];
        }
        
        return false;
    }
    
    public function getPaymentByOrderId($order_id) {
        $sql = "SELECT * FROM tbl_payment WHERE order_id = $order_id";
        $res = mysqli_query($this->conn, $sql);
        
        if($res && mysqli_num_rows($res) > 0) {
            return mysqli_fetch_assoc($res);
        }
        
        return null;
    }
    
    public function updatePaymentStatus($payment_id, $status, $gateway_response = null) {
        $status = mysqli_real_escape_string($this->conn, $status);
        $sql = "UPDATE tbl_payment SET payment_status = '$status'";
        
        if($gateway_response) {
            $gateway_response = mysqli_real_escape_string($this->conn, $gateway_response);
            $sql .= ", gateway_response = '$gateway_response'";
        }
        
        $sql .= " WHERE id = $payment_id";
        
        return mysqli_query($this->conn, $sql);
    }
    
    // Reviews
    public function createReview($review_data) {
        $fields = [];
        $values = [];
        
        foreach($review_data as $key => $value) {
            $fields[] = $key;
            $values[] = "'" . mysqli_real_escape_string($this->conn, $value) . "'";
        }
        
        $sql = "INSERT INTO tbl_review (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        
        return mysqli_query($this->conn, $sql);
    }
    
    public function getReviewsByFood($food_id, $limit = 10) {
        $sql = "SELECT * FROM tbl_review WHERE food_id = $food_id ORDER BY review_date DESC LIMIT $limit";
        
        $res = mysqli_query($this->conn, $sql);
        $reviews = [];
        
        if($res) {
            while($row = mysqli_fetch_assoc($res)) {
                $reviews[] = $row;
            }
        }
        
        return $reviews;
    }
    
    public function getFoodRating($food_id) {
        $sql = "SELECT AVG(rating) as avg_rating, COUNT(*) as review_count FROM tbl_review WHERE food_id = $food_id";
        $res = mysqli_query($this->conn, $sql);
        
        if($res && mysqli_num_rows($res) > 0) {
            $row = mysqli_fetch_assoc($res);
            return [
                'avg_rating' => round($row['avg_rating'], 1),
                'review_count' => $row['review_count']
            ];
        }
        
        return ['avg_rating' => 0, 'review_count' => 0];
    }
    
    // Customers
    public function createCustomer($customer_data) {
        $fields = [];
        $values = [];
        
        foreach($customer_data as $key => $value) {
            $fields[] = $key;
            $values[] = "'" . mysqli_real_escape_string($this->conn, $value) . "'";
        }
        
        $sql = "INSERT INTO tbl_customer (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $res = mysqli_query($this->conn, $sql);
        
        if($res) {
            return ['id' => mysqli_insert_id($this->conn)];
        }
        
        return false;
    }
    
    public function getCustomerByEmail($email) {
        $email = mysqli_real_escape_string($this->conn, $email);
        $sql = "SELECT * FROM tbl_customer WHERE email = '$email'";
        $res = mysqli_query($this->conn, $sql);
        
        if($res && mysqli_num_rows($res) > 0) {
            return mysqli_fetch_assoc($res);
        }
        
        return null;
    }
    
    public function updateCustomer($customer_id, $data) {
        $updates = [];
        
        foreach($data as $key => $value) {
            $value = mysqli_real_escape_string($this->conn, $value);
            $updates[] = "$key = '$value'";
        }
        
        $sql = "UPDATE tbl_customer SET " . implode(', ', $updates) . " WHERE id = $customer_id";
        
        return mysqli_query($this->conn, $sql);
    }
    
    // Admin
    public function getAdminByUsername($username) {
        $username = mysqli_real_escape_string($this->conn, $username);
        $sql = "SELECT * FROM tbl_admin WHERE username = '$username'";
        $res = mysqli_query($this->conn, $sql);
        
        if($res && mysqli_num_rows($res) > 0) {
            return mysqli_fetch_assoc($res);
        }
        
        return null;
    }
    
    // Statistics
    public function getOrderStats() {
        $sql = "SELECT status, total FROM tbl_order";
        $res = mysqli_query($this->conn, $sql);
        
        $stats = [
            'total_orders' => 0,
            'delivered_orders' => 0,
            'total_revenue' => 0
        ];
        
        if($res) {
            while($row = mysqli_fetch_assoc($res)) {
                $stats['total_orders']++;
                if($row['status'] === 'Delivered') {
                    $stats['delivered_orders']++;
                    $stats['total_revenue'] += $row['total'];
                }
            }
        }
        
        return $stats;
    }
}

// Global instance
$supabase = new SupabaseHelper($conn);
?>
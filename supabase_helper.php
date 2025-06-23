<?php
// Supabase Helper Functions for easier database operations

class SupabaseHelper {
    private $supabase_url;
    private $service_key;
    
    public function __construct() {
        $this->supabase_url = $_ENV['VITE_SUPABASE_URL'] ?? 'your-supabase-url';
        $this->service_key = $_ENV['SUPABASE_SERVICE_ROLE_KEY'] ?? 'your-service-key';
    }
    
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->supabase_url . '/rest/v1/' . $endpoint;
        
        $headers = [
            'apikey: ' . $this->service_key,
            'Authorization: Bearer ' . $this->service_key,
            'Content-Type: application/json'
        ];
        
        if ($method === 'POST') {
            $headers[] = 'Prefer: return=representation';
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true);
        } else {
            error_log("Supabase API Error: " . $response);
            return false;
        }
    }
    
    // Categories
    public function getCategories($active_only = true) {
        $endpoint = 'tbl_category?select=*';
        if ($active_only) {
            $endpoint .= '&active=eq.Yes';
        }
        $endpoint .= '&order=title.asc';
        return $this->makeRequest($endpoint);
    }
    
    public function getCategoryById($id) {
        $endpoint = "tbl_category?id=eq.$id&select=*";
        $result = $this->makeRequest($endpoint);
        return $result ? $result[0] : null;
    }
    
    // Food Items
    public function getFoodItems($category_id = null, $featured_only = false, $active_only = true) {
        $endpoint = 'tbl_food?select=*';
        
        $conditions = [];
        if ($category_id) {
            $conditions[] = "category_id=eq.$category_id";
        }
        if ($featured_only) {
            $conditions[] = "featured=eq.Yes";
        }
        if ($active_only) {
            $conditions[] = "active=eq.Yes";
        }
        
        if (!empty($conditions)) {
            $endpoint .= '&' . implode('&', $conditions);
        }
        
        $endpoint .= '&order=featured.desc,id.desc';
        return $this->makeRequest($endpoint);
    }
    
    public function getFoodById($id) {
        $endpoint = "tbl_food?id=eq.$id&select=*";
        $result = $this->makeRequest($endpoint);
        return $result ? $result[0] : null;
    }
    
    public function searchFood($search_term) {
        $endpoint = "tbl_food?or=(title.ilike.*$search_term*,description.ilike.*$search_term*)&active=eq.Yes&select=*";
        return $this->makeRequest($endpoint);
    }
    
    // Orders
    public function createOrder($order_data) {
        return $this->makeRequest('tbl_order', 'POST', $order_data);
    }
    
    public function getOrderById($id) {
        $endpoint = "tbl_order?id=eq.$id&select=*";
        $result = $this->makeRequest($endpoint);
        return $result ? $result[0] : null;
    }
    
    public function getOrdersByCustomer($customer_email) {
        $endpoint = "tbl_order?customer_email=eq.$customer_email&select=*&order=order_date.desc";
        return $this->makeRequest($endpoint);
    }
    
    public function updateOrderStatus($order_id, $status) {
        $endpoint = "tbl_order?id=eq.$order_id";
        return $this->makeRequest($endpoint, 'PATCH', ['status' => $status]);
    }
    
    // Payments
    public function createPayment($payment_data) {
        return $this->makeRequest('tbl_payment', 'POST', $payment_data);
    }
    
    public function getPaymentByOrderId($order_id) {
        $endpoint = "tbl_payment?order_id=eq.$order_id&select=*";
        $result = $this->makeRequest($endpoint);
        return $result ? $result[0] : null;
    }
    
    public function updatePaymentStatus($payment_id, $status, $gateway_response = null) {
        $data = ['payment_status' => $status];
        if ($gateway_response) {
            $data['gateway_response'] = $gateway_response;
        }
        $endpoint = "tbl_payment?id=eq.$payment_id";
        return $this->makeRequest($endpoint, 'PATCH', $data);
    }
    
    // Reviews
    public function createReview($review_data) {
        return $this->makeRequest('tbl_review', 'POST', $review_data);
    }
    
    public function getReviewsByFood($food_id, $limit = 10) {
        $endpoint = "tbl_review?food_id=eq.$food_id&select=*&order=review_date.desc&limit=$limit";
        return $this->makeRequest($endpoint);
    }
    
    public function getFoodRating($food_id) {
        $endpoint = "tbl_review?food_id=eq.$food_id&select=rating";
        $reviews = $this->makeRequest($endpoint);
        
        if (!$reviews || empty($reviews)) {
            return ['avg_rating' => 0, 'review_count' => 0];
        }
        
        $total_rating = array_sum(array_column($reviews, 'rating'));
        $review_count = count($reviews);
        $avg_rating = $total_rating / $review_count;
        
        return [
            'avg_rating' => round($avg_rating, 1),
            'review_count' => $review_count
        ];
    }
    
    // Customers
    public function createCustomer($customer_data) {
        return $this->makeRequest('tbl_customer', 'POST', $customer_data);
    }
    
    public function getCustomerByEmail($email) {
        $endpoint = "tbl_customer?email=eq.$email&select=*";
        $result = $this->makeRequest($endpoint);
        return $result ? $result[0] : null;
    }
    
    public function updateCustomer($customer_id, $data) {
        $endpoint = "tbl_customer?id=eq.$customer_id";
        return $this->makeRequest($endpoint, 'PATCH', $data);
    }
    
    // Admin
    public function getAdminByUsername($username) {
        $endpoint = "tbl_admin?username=eq.$username&select=*";
        $result = $this->makeRequest($endpoint);
        return $result ? $result[0] : null;
    }
    
    // Statistics
    public function getOrderStats() {
        $orders = $this->makeRequest('tbl_order?select=status,total');
        
        $stats = [
            'total_orders' => count($orders),
            'delivered_orders' => 0,
            'total_revenue' => 0
        ];
        
        foreach ($orders as $order) {
            if ($order['status'] === 'Delivered') {
                $stats['delivered_orders']++;
                $stats['total_revenue'] += $order['total'];
            }
        }
        
        return $stats;
    }
}

// Global instance
$supabase = new SupabaseHelper();
?>
<?php 
    session_start();

    // Supabase Configuration
    define('SUPABASE_URL', $_ENV['VITE_SUPABASE_URL'] ?? 'your-supabase-url');
    define('SUPABASE_ANON_KEY', $_ENV['VITE_SUPABASE_ANON_KEY'] ?? 'your-supabase-anon-key');
    define('SUPABASE_SERVICE_KEY', $_ENV['SUPABASE_SERVICE_ROLE_KEY'] ?? 'your-service-role-key');
    
    // Site Configuration
    define('SITEURL', 'http://localhost:3000/');
    
    // Database connection using Supabase REST API
    class SupabaseDB {
        private $base_url;
        private $api_key;
        
        public function __construct() {
            $this->base_url = SUPABASE_URL . '/rest/v1/';
            $this->api_key = SUPABASE_SERVICE_KEY;
        }
        
        public function query($table, $select = '*', $conditions = []) {
            $url = $this->base_url . $table . '?select=' . urlencode($select);
            
            foreach ($conditions as $key => $value) {
                $url .= '&' . urlencode($key) . '=eq.' . urlencode($value);
            }
            
            $headers = [
                'apikey: ' . $this->api_key,
                'Authorization: Bearer ' . $this->api_key,
                'Content-Type: application/json'
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            return json_decode($response, true);
        }
        
        public function insert($table, $data) {
            $url = $this->base_url . $table;
            
            $headers = [
                'apikey: ' . $this->api_key,
                'Authorization: Bearer ' . $this->api_key,
                'Content-Type: application/json',
                'Prefer: return=representation'
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            return json_decode($response, true);
        }
        
        public function update($table, $data, $conditions) {
            $url = $this->base_url . $table;
            
            foreach ($conditions as $key => $value) {
                $url .= '?' . urlencode($key) . '=eq.' . urlencode($value);
                break; // Only first condition for simplicity
            }
            
            $headers = [
                'apikey: ' . $this->api_key,
                'Authorization: Bearer ' . $this->api_key,
                'Content-Type: application/json'
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($ch);
            curl_close($ch);
            
            return json_decode($response, true);
        }
    }
    
    // Legacy MySQL compatibility functions for existing code
    $conn = new SupabaseDB();
    
    function mysqli_query($conn, $sql) {
        // This is a simplified adapter - you may need to enhance based on your SQL queries
        // For now, we'll return a mock result for compatibility
        return new MockResult();
    }
    
    function mysqli_fetch_assoc($result) {
        return $result->fetch_assoc();
    }
    
    function mysqli_num_rows($result) {
        return $result->num_rows();
    }
    
    function mysqli_insert_id($conn) {
        return $conn->last_insert_id ?? 1;
    }
    
    function mysqli_real_escape_string($conn, $string) {
        return addslashes($string);
    }
    
    class MockResult {
        private $data = [];
        private $index = 0;
        
        public function fetch_assoc() {
            if ($this->index < count($this->data)) {
                return $this->data[$this->index++];
            }
            return null;
        }
        
        public function num_rows() {
            return count($this->data);
        }
    }
?>
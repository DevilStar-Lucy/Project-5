<?php
require_once 'config/payment_config.php';

class PaymentGateway {
    private $conn;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    // bKash Payment Integration
    public function processBkashPayment($order_id, $amount, $customer_phone) {
        $config = PaymentConfig::getGatewayConfig('bkash');
        
        // Get bKash token
        $token = $this->getBkashToken($config);
        if (!$token) {
            return ['status' => 'error', 'message' => 'Failed to get bKash token'];
        }
        
        // Create payment
        $payment_data = [
            'mode' => '0011',
            'payerReference' => $customer_phone,
            'callbackURL' => PaymentConfig::SUCCESS_URL,
            'amount' => $amount,
            'currency' => PaymentConfig::CURRENCY,
            'intent' => 'sale',
            'merchantInvoiceNumber' => 'ORDER_' . $order_id . '_' . time()
        ];
        
        $response = $this->makeBkashRequest('/tokenized/checkout/create', $payment_data, $token, $config);
        
        if ($response && isset($response['paymentID'])) {
            // Store payment record
            $transaction_id = $response['paymentID'];
            $this->storePaymentRecord($order_id, 'bkash', $transaction_id, $amount, $customer_phone, 'pending');
            
            return [
                'status' => 'success',
                'payment_url' => $response['bkashURL'],
                'transaction_id' => $transaction_id
            ];
        }
        
        return ['status' => 'error', 'message' => 'Failed to create bKash payment'];
    }
    
    // Nagad Payment Integration
    public function processNagadPayment($order_id, $amount, $customer_phone) {
        $config = PaymentConfig::getGatewayConfig('nagad');
        
        $transaction_id = 'TXN_' . $order_id . '_' . time();
        
        // Nagad payment initialization
        $payment_data = [
            'account' => $config['merchant_number'],
            'amount' => $amount,
            'orderId' => $transaction_id,
            'productDetails' => 'Food Order #' . $order_id,
            'clientMobile' => $customer_phone
        ];
        
        // For demo purposes, simulate successful payment
        $this->storePaymentRecord($order_id, 'nagad', $transaction_id, $amount, $customer_phone, 'completed');
        
        return [
            'status' => 'success',
            'transaction_id' => $transaction_id,
            'message' => 'Nagad payment processed successfully'
        ];
    }
    
    // Upay Payment Integration
    public function processUpayPayment($order_id, $amount, $customer_phone) {
        $config = PaymentConfig::getGatewayConfig('upay');
        
        $transaction_id = 'UPAY_' . $order_id . '_' . time();
        
        // Upay payment processing
        $payment_data = [
            'merchant_id' => $config['merchant_id'],
            'amount' => $amount,
            'order_id' => $transaction_id,
            'customer_phone' => $customer_phone,
            'success_url' => PaymentConfig::SUCCESS_URL,
            'cancel_url' => PaymentConfig::CANCEL_URL
        ];
        
        // For demo purposes, simulate successful payment
        $this->storePaymentRecord($order_id, 'upay', $transaction_id, $amount, $customer_phone, 'completed');
        
        return [
            'status' => 'success',
            'transaction_id' => $transaction_id,
            'message' => 'Upay payment processed successfully'
        ];
    }
    
    // Rocket Payment Integration
    public function processRocketPayment($order_id, $amount, $customer_phone) {
        $config = PaymentConfig::getGatewayConfig('rocket');
        
        $transaction_id = 'ROCKET_' . $order_id . '_' . time();
        
        // Rocket payment processing
        $payment_data = [
            'merchant_id' => $config['merchant_id'],
            'amount' => $amount,
            'order_id' => $transaction_id,
            'customer_phone' => $customer_phone
        ];
        
        // For demo purposes, simulate successful payment
        $this->storePaymentRecord($order_id, 'rocket', $transaction_id, $amount, $customer_phone, 'completed');
        
        return [
            'status' => 'success',
            'transaction_id' => $transaction_id,
            'message' => 'Rocket payment processed successfully'
        ];
    }
    
    // Get bKash access token
    private function getBkashToken($config) {
        $url = $config['base_url'] . '/tokenized/checkout/token/grant';
        
        $data = [
            'app_key' => $config['app_key'],
            'app_secret' => $config['app_secret']
        ];
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'username: ' . $config['username'],
            'password: ' . $config['password']
        ];
        
        $response = $this->makeHttpRequest($url, $data, $headers);
        
        if ($response && isset($response['id_token'])) {
            return $response['id_token'];
        }
        
        return false;
    }
    
    // Make bKash API request
    private function makeBkashRequest($endpoint, $data, $token, $config) {
        $url = $config['base_url'] . $endpoint;
        
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'authorization: ' . $token,
            'x-app-key: ' . $config['app_key']
        ];
        
        return $this->makeHttpRequest($url, $data, $headers);
    }
    
    // Generic HTTP request method
    private function makeHttpRequest($url, $data, $headers) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        curl_close($ch);
        
        if ($http_code == 200) {
            return json_decode($response, true);
        }
        
        return false;
    }
    
    // Store payment record in database
    private function storePaymentRecord($order_id, $method, $transaction_id, $amount, $account_number, $status) {
        $sql = "INSERT INTO tbl_payment SET
            order_id = '$order_id',
            payment_method = '$method',
            transaction_id = '$transaction_id',
            amount = '$amount',
            account_number = '$account_number',
            payment_status = '$status',
            payment_date = NOW()
        ";
        
        return mysqli_query($this->conn, $sql);
    }
    
    // Verify payment status
    public function verifyPayment($transaction_id, $gateway) {
        switch($gateway) {
            case 'bkash':
                return $this->verifyBkashPayment($transaction_id);
            case 'nagad':
                return $this->verifyNagadPayment($transaction_id);
            case 'upay':
                return $this->verifyUpayPayment($transaction_id);
            case 'rocket':
                return $this->verifyRocketPayment($transaction_id);
            default:
                return false;
        }
    }
    
    private function verifyBkashPayment($payment_id) {
        $config = PaymentConfig::getGatewayConfig('bkash');
        $token = $this->getBkashToken($config);
        
        if (!$token) return false;
        
        $response = $this->makeBkashRequest('/tokenized/checkout/payment/status', 
            ['paymentID' => $payment_id], $token, $config);
        
        return $response && $response['transactionStatus'] === 'Completed';
    }
    
    private function verifyNagadPayment($transaction_id) {
        // Implement Nagad payment verification
        return true; // For demo
    }
    
    private function verifyUpayPayment($transaction_id) {
        // Implement Upay payment verification
        return true; // For demo
    }
    
    private function verifyRocketPayment($transaction_id) {
        // Implement Rocket payment verification
        return true; // For demo
    }
}
?>
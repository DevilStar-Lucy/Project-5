<?php
// Payment Gateway Configuration for Bangladesh

class PaymentConfig {
    
    // bKash Configuration
    const BKASH_BASE_URL = 'https://tokenized.pay.bka.sh/v1.2.0-beta';
    const BKASH_APP_KEY = 'your_bkash_app_key';
    const BKASH_APP_SECRET = 'your_bkash_app_secret';
    const BKASH_USERNAME = 'your_bkash_username';
    const BKASH_PASSWORD = 'your_bkash_password';
    
    // Nagad Configuration
    const NAGAD_BASE_URL = 'https://api.mynagad.com';
    const NAGAD_MERCHANT_ID = 'your_nagad_merchant_id';
    const NAGAD_MERCHANT_NUMBER = 'your_nagad_merchant_number';
    const NAGAD_PUBLIC_KEY = 'your_nagad_public_key';
    const NAGAD_PRIVATE_KEY = 'your_nagad_private_key';
    
    // Upay Configuration
    const UPAY_BASE_URL = 'https://api.upay.com.bd';
    const UPAY_MERCHANT_ID = 'your_upay_merchant_id';
    const UPAY_API_KEY = 'your_upay_api_key';
    const UPAY_SECRET_KEY = 'your_upay_secret_key';
    
    // Rocket Configuration
    const ROCKET_BASE_URL = 'https://api.rocket.com.bd';
    const ROCKET_MERCHANT_ID = 'your_rocket_merchant_id';
    const ROCKET_API_KEY = 'your_rocket_api_key';
    const ROCKET_SECRET_KEY = 'your_rocket_secret_key';
    
    // Common settings
    const CURRENCY = 'BDT';
    const SUCCESS_URL = SITEURL . 'payment-success.php';
    const CANCEL_URL = SITEURL . 'payment-cancel.php';
    const FAIL_URL = SITEURL . 'payment-fail.php';
    
    public static function getGatewayConfig($gateway) {
        switch($gateway) {
            case 'bkash':
                return [
                    'base_url' => self::BKASH_BASE_URL,
                    'app_key' => self::BKASH_APP_KEY,
                    'app_secret' => self::BKASH_APP_SECRET,
                    'username' => self::BKASH_USERNAME,
                    'password' => self::BKASH_PASSWORD
                ];
            case 'nagad':
                return [
                    'base_url' => self::NAGAD_BASE_URL,
                    'merchant_id' => self::NAGAD_MERCHANT_ID,
                    'merchant_number' => self::NAGAD_MERCHANT_NUMBER,
                    'public_key' => self::NAGAD_PUBLIC_KEY,
                    'private_key' => self::NAGAD_PRIVATE_KEY
                ];
            case 'upay':
                return [
                    'base_url' => self::UPAY_BASE_URL,
                    'merchant_id' => self::UPAY_MERCHANT_ID,
                    'api_key' => self::UPAY_API_KEY,
                    'secret_key' => self::UPAY_SECRET_KEY
                ];
            case 'rocket':
                return [
                    'base_url' => self::ROCKET_BASE_URL,
                    'merchant_id' => self::ROCKET_MERCHANT_ID,
                    'api_key' => self::ROCKET_API_KEY,
                    'secret_key' => self::ROCKET_SECRET_KEY
                ];
            default:
                return null;
        }
    }
}
?>
<?php

class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public static $baseUrl;

    public function __construct()
    {
        $server = $_SERVER['HTTP_HOST'] ?? 'localhost';

        if ($server === 'localhost' || $server === '127.0.0.1') {
            // LOCAL
            $this->host = "localhost";
            $this->db_name = "gstore";
            $this->username = "root";
            $this->password = "";

            self::$baseUrl = "http://localhost/gstore/";
        } else {
            // LIVE
            $this->host = "localhost"; // FIXED
            $this->db_name = "u232955123_gdShop";
            $this->username = "u232955123_gdShop";
            $this->password = "Brandweave@24";

            self::$baseUrl = "https://shop.goldendream.in/";
        }
    }

    public function getConnection()
    {
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            error_log("DB Error: " . $e->getMessage());
            echo "Database connection failed.";
            exit;
        }

        return $this->conn;
    }
}

// JWT secret key for token generation and verification
if (!defined('JWT_SECRET')) {
    define('JWT_SECRET', 'goldendream_super_secret_key_2024!@#');
}
// JWT encryption key (32 bytes for AES-256, derived from passphrase)
if (!defined('JWT_ENCRYPT_KEY')) {
    define('JWT_ENCRYPT_KEY', hash('sha256', 'goldendream_super_passphrase_2024', true)); 
}
// Helper functions for encrypting and decrypting JWTs
if (!function_exists('encrypt_jwt')) {
    function encrypt_jwt($jwt) {
        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($jwt, 'AES-256-CBC', JWT_ENCRYPT_KEY, 0, $iv);
        return base64_encode($iv . $ciphertext);
    }
    function decrypt_jwt($encrypted) {
        $data = base64_decode($encrypted);
        $iv = substr($data, 0, 16);
        $ciphertext = substr($data, 16);
        return openssl_decrypt($ciphertext, 'AES-256-CBC', JWT_ENCRYPT_KEY, 0, $iv);
    }
}

?>
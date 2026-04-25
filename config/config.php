<?php

class Database
{
    // Localhost database configuration
    private $host = "localhost";
    private $db_name = "gstore";   
    private $username = "root";
    private $password = "";
    
    public $conn;

    // Base URL for local development
    public static $baseUrl = "http://localhost/gstore/";

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Enable error reporting for PDO
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log the error locally
            error_log("Database connection failed: " . $e->getMessage());
            
            // Check if error.php exists before redirecting to avoid 404 errors
            echo "Database connection error. Please ensure your MySQL service is running and the database '{$this->db_name}' exists.";
            exit();
        }

        return $this->conn;
    }
}

// --- JWT Configuration ---

if (!defined('JWT_SECRET')) {
    define('JWT_SECRET', 'goldendream_super_secret_key_2024!@#');
}

if (!defined('JWT_ENCRYPT_KEY')) {
    define('JWT_ENCRYPT_KEY', hash('sha256', 'goldendream_super_passphrase_2024', true)); 
}

// Encryption Helpers
if (!function_exists('encrypt_jwt')) {
    function encrypt_jwt($jwt) {
        $iv = openssl_random_pseudo_bytes(16);
        $ciphertext = openssl_encrypt($jwt, 'AES-256-CBC', JWT_ENCRYPT_KEY, 0, $iv);
        return base64_encode($iv . $ciphertext);
    }
}

if (!function_exists('decrypt_jwt')) {
    function decrypt_jwt($encrypted) {
        $data = base64_decode($encrypted);
        if (!$data) return false;
        
        $iv = substr($data, 0, 16);
        $ciphertext = substr($data, 16);
        return openssl_decrypt($ciphertext, 'AES-256-CBC', JWT_ENCRYPT_KEY, 0, $iv);
    }
}

?>
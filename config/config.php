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
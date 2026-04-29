<?php

class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public static $shop_db = "u232955123_gdShop"; 

    public static $baseUrl;

    public function __construct()
    {
        $server = $_SERVER['HTTP_HOST'];

        // 👉 LOCAL ENVIRONMENT
        if ($server === 'localhost' || $server === '127.0.0.1') {
            $this->host = "localhost";
            $this->db_name = "local_db_name"; // change this
            $this->username = "root";
            $this->password = "";

            self::$baseUrl = "http://localhost/your-project-folder/";
        } 
        // 👉 LIVE (HOSTINGER)
        else {
            $this->host = "82.25.121.121";
            $this->db_name = "u232955123_gdShop";
            $this->username = "u232955123_gdShop";
            $this->password = "Brandweave@24";

            self::$baseUrl = "https://shop.goldendream.in/";
        }
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            header("Location: " . self::$baseUrl . "error.php");
            exit();
        }

        return $this->conn;
    }
}
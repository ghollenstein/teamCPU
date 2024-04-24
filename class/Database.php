<?php
class Database {
    private static $instance = null;
    private $mysqli;

    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $db = 'fhwien';

    // Constructor is private to prevent initiating the class directly
    private function __construct() {
        $this->mysqli = new mysqli($this->host, $this->user, $this->pass, $this->db);

        if ($this->mysqli->connect_error) {
            die('Connection failed: ' . $this->mysqli->connect_error);
        }

        // Set the charset to ensure proper encoding
        $this->mysqli->set_charset("utf8mb4");
    }

    // Prevent the instance from being cloned
    private function __clone() { }

    // Method to get the single instance of the class
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Method to get the mysqli connection
    public function getConnection() {
        return $this->mysqli;
    }

    // Close the connection when the object is destroyed
    public function __destruct() {
        $this->mysqli->close();
    }
}

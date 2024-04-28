<?php
class Database
{
    private static $instance = null;
    private $mysqli;

    private $host = 'localhost';
    private $user = 'root';
    private $pass = '';
    private $db = 'fhwien';

    // Constructor is private to prevent initiating the class directly
    private function __construct()
    {
        // Initially connect without specifying the database to check its existence
        $this->mysqli = new mysqli($this->host, $this->user, $this->pass);

        // Check for connection errors (note that mysqli connects even if the database doesn't exist)
        if ($this->mysqli->connect_error) {
            throw new Exception('Connection failed: ' . $this->mysqli->connect_error);
        }

        // Check if the specified database exists
        try {
            $dbExists = $this->mysqli->select_db($this->db);
        } catch (\Throwable $th) {
            //throw $th;
        }

        // Set the charset to ensure proper encoding
        $this->mysqli->set_charset("utf8mb4");
    }

    // Prevent the instance from being cloned
    private function __clone()
    {
    }

    // Method to get the single instance of the class
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    // Method to get the mysqli connection
    public function getConnection()
    {
        return $this->mysqli;
    }

    public function __destruct()
    {
        // Close the connection if it exists and is still open
        if ($this->mysqli && $this->mysqli->connect_errno === 0) {
            $this->mysqli->close();
        }
    }
}

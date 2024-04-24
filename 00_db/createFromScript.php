<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fhwien";
$logFile = "error_log.txt"; // Define the path to your log file

// Function to log errors
function logError($message) {
    global $logFile;
    $timestamp = date("Y-m-d H:i:s");
    $msg = "{$timestamp} - ERROR: {$message}\n";
    file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    logError("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error); // Optionally, remove or modify the die statement for production
}

echo "01 Connected successfully<br />";

// Read SQL file
$sql = file_get_contents('datenmodell.sql');

// Perform queries
if ($conn->multi_query($sql)) {
    echo "02 SQL script executed successfully.";
} else {
    $error = "Error executing SQL script: " . $conn->error;
    logError($error);
    echo $error; // Optionally, modify this for production to not display sensitive information
}

$conn->close();
?>

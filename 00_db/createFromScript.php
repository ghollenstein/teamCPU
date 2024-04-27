<?php
require_once '../class/Database.php';

// Path to the SQL file
$sqlFilePath = 'datenmodell.sql';

// Get database instance
$dbInstance = Database::getInstance();
$conn = $dbInstance->getConnection();

// Function to execute queries from a file
function executeSQLFromFile($filePath, $conn) {
    // Read the entire file into a single string
    $queries = file_get_contents($filePath);

    // Split the file into individual queries
    $queries = explode(';', $queries);

    // Execute each query in the file
    foreach ($queries as $query) {
        $query = trim($query);
        if (!empty($query)) {
            if ($conn->query($query) === false) {
                echo "Error executing query '$query': " . $conn->error . "<br/>";
            } else {
                echo "Successfully executed query: $query<br/>";
            }
        }
    }
}

// Check if the file exists and is readable
if (is_readable($sqlFilePath)) {
    executeSQLFromFile($sqlFilePath, $conn);
} else {
    echo "SQL file does not exist or is not readable.";
}

$conn->close();
?>

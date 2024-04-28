<?php
session_start();

define('TEMPLATE_PATH', 'class/DatabaseExample.php');
define('OUTPUT_PATH', 'class/Database.php');
define('DB_CREATION_SCRIPT', '00_db/createDB.php');
define('INSTALL_PASSWORD', 'FH-Wien-TeamCPU'); // Set a secure password

$errorMessage = '';
$successMessage = '';

// Check for existing setup
if (file_exists(OUTPUT_PATH)) {
    require OUTPUT_PATH;
    $db = Database::getInstance()->getConnection();
    if ($db->connect_error) {
        $errorMessage = 'Existing database configuration is invalid or the database cannot be reached.';
    } else {
        $successMessage = 'Database is already configured and connected successfully.';
    }
}

// Handle login
if (isset($_POST['password']) && $_POST['password'] === INSTALL_PASSWORD) {
    $_SESSION['authenticated'] = true;
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: setup.php');
    exit;
}

// Process the form if authenticated
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hostname'])) {
        // Collecting data from the form
        $hostname = $_POST['hostname'] ?? 'localhost';
        $database = $_POST['database'] ?? 'fhwien';
        $username = $_POST['username'] ?? 'root';
        $password = $_POST['password'] ?? '';

        // Read and update the template
        $template = file_get_contents(TEMPLATE_PATH);
        $template = str_replace(["'localhost'", "'root'", "''", "'fhwien'"], ["'$hostname'", "'$username'", "'$password'", "'$database'"], $template);

        // Write the updated configuration
        if (file_put_contents(OUTPUT_PATH, $template) === false) {
            $errorMessage = 'Failed to write configuration file.';
        } else {
            require DB_CREATION_SCRIPT;
            $successMessage = 'Database setup completed successfully.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Setup</title>
</head>
<body>
    <h1>Database Setup</h1>

    <?php if (!empty($errorMessage)): ?>
        <p style="color: red;"><?= $errorMessage ?></p>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <p style="color: green;"><?= $successMessage ?></p>
    <?php endif; ?>

    <?php if (!isset($_SESSION['authenticated'])): ?>
        <form method="post">
            <label for="password">Enter Password:</label>
            <input type="password" id="password" name="password">
            <button type="submit">Login</button>
        </form>
    <?php elseif ($errorMessage || !$successMessage): ?>
        <form method="post">
            <label for="hostname">Database Host:</label>
            <input type="text" id="hostname" name="hostname" placeholder="localhost" required><br><br>
            <label for="database">Database Name:</label>
            <input type="text" id="database" name="database" placeholder="fhwien" required><br><br>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="root" required><br><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password"><br><br>
            <button type="submit">Install</button>
        </form>
        <p><a href="?logout=1">Logout</a></p>
    <?php endif; ?>
</body>
</html>

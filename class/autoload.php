<?php
spl_autoload_register(function ($className) {
    // Paths to search for class files
    $paths = [
        __DIR__,  // Current directory where the Autoloader.php resides
        __DIR__ . '/../00_db/models'  // The models directory
    ];

    // Replace the namespace separator with the directory separator
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);

    // Check each path and include the class file if found
    foreach ($paths as $path) {
        $file = $path . DIRECTORY_SEPARATOR . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});

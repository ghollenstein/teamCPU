<?php
header('Content-Type: application/json');

// Ensure autoloader path is correct relative to this file
require_once __DIR__ . '/../class/autoload.php';

$controller = new TeaShopController();
$response = ['success' => false];

try {
    // Assume entity and action are determined by some request parameter, adjust accordingly
    $entity = 'products';  // Example entity
    $action = 'readAll';  // Example action
    $data = $_POST;       // Example data, make sure to validate and sanitize

    // Process the request
    $result = $controller->handleRequest($entity, $action, $data);

    if (!$result) {
        throw new Exception("No result returned from handler");
    }

    // Directly use the result for response
    $response = [
        'success' => true,
        'data' => convertResultToJson($result)
    ];
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
} catch (Throwable $t) {
    $response = [
        'success' => false,
        'error' => 'Critical error: ' . $t->getMessage()
    ];
}

function convertResultToJson($result)
{
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return json_encode($data, JSON_UNESCAPED_UNICODE);
}


// Output the final JSON response
echo json_encode($response, JSON_UNESCAPED_UNICODE);

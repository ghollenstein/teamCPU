<?php
header('Content-Type: application/json');

// Ensure autoloader path is correct relative to this file
require_once __DIR__ . '/../class/autoload.php';

$controller = new TeaShopController();

// Initialize response array with hostname and current ISO 8601 timestamp
$response = [
    'success' => false,
    'apiInfo' => [
        'hostname' => gethostname(),
        'timestamp' => gmdate('c'), // ISO 8601 format in UTC
        'version' => 1
    ]
];

function convertDbResultToJson($result)
{
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}

try {
    $json = file_get_contents('php://input');
    $requestData = json_decode($json, true);

    if (!isset($requestData['entity']) || !isset($requestData['action'])) {
        http_response_code(400); // Bad Request
        throw new Exception("Invalid request data");
    }

    $entity = $requestData['entity'];
    $action = $requestData['action'];
    $result = $controller->handleRequest($entity, $action, $requestData);

    if (!$result) {
        http_response_code(404); // Not Found
        throw new Exception("No result returned from handler");
    }

    $type = gettype($result);

    switch ($type) {
        case 'array':
            $data = $result;
            break;

        default:
            http_response_code(500); // Internal Server Error
            throw new Exception("Invalid type " . $type . " - API");
            $data = convertDbResultToJson($result);
            break;
    }

    $response['success'] = true;
    $response['data'] = $data;
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    $response['error'] = $e->getMessage();
} catch (Throwable $t) {
    http_response_code(500); // Internal Server Error
    $response['error'] = 'Critical error: ' . $t->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);

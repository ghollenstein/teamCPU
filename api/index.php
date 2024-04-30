<?php
error_reporting(0);
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../class/autoload.php';
$controller = new TeaShopController();

$response = [
    'success' => false,
    'apiInfo' => [
        'hostname' => gethostname(),
        'timestamp' => gmdate('c'),
        'version' => 1
    ],
    'data' => []
];

try {
    $json = file_get_contents('php://input');
    $requestData = json_decode($json, true);

    if (!is_array($requestData) || !isset($requestData['entity']) || !isset($requestData['action'])) {
        http_response_code(400); // Bad Request
        throw new Exception("Invalid request data");
    }

    $entity = $requestData['entity'];
    $action = $requestData['action'];

    $result = $controller->handleRequest($entity, $action, $requestData);

    // Assuming handleRequest is expected to return an array or false if no data is found
    if ($result === false) {
        http_response_code(404); // Not Found
        throw new Exception("No result returned from handler");
    }

    if (is_array($result)) {
        $response['data'] = $result; // Directly use the result if it's an array
    } elseif ($result instanceof mysqli_result) {
        $response['data'] = convertDbResultToJson($result); // Convert database result to JSON
    } elseif (is_int($result)) {
        $response['data'] = $result;
    } else {
        http_response_code(500); // Internal Server Error
        throw new Exception("Unexpected data type returned");
    }

    $response['success'] = true;
} catch (Exception $e) {
    http_response_code(400); // Bad Request
    $response['error'] = $e->getMessage();
} catch (Throwable $t) {
    http_response_code(500); // Internal Server Error
    $response['error'] = 'Critical error: ' . $t->getMessage();
}

function convertDbResultToJson($result)
{
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    return $data;
}


echo json_encode($response, JSON_UNESCAPED_UNICODE);

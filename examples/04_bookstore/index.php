<?php

namespace Examples\BookStore;

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true', true);
header('Access-Control-Allow-Origin: http://127.0.0.1:8000');
if ($_SERVER['REQUEST_METHOD'] == "OPTIONS") {
    return;
}
if ($_SERVER['REQUEST_URI'] == '/graphiql.css') {
    http_response_code(404);
    die();
}

use Examples\BookStore\Schema\BookStoreSchema;
use Youshido\GraphQL\Execution\Processor;
use Youshido\GraphQL\Schema\Schema;

require_once __DIR__ . '/schema-bootstrap.php';
/** @var Schema $schema */
$schema = new BookStoreSchema();

if ((isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json')
    || isset($_SERVER['HTTP_CONTENT_TYPE']) && $_SERVER['HTTP_CONTENT_TYPE'] === 'application/json'
) {
    $rawBody     = file_get_contents('php://input');
    $requestData = json_decode($rawBody ?: '', true);
} else {
    $requestData = $_POST;
}

$payload   = isset($requestData['query']) ? $requestData['query'] : null;
$variables = isset($requestData['variables']) ? $requestData['variables'] : null;

if (empty($payload)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['errors' => [['message' => 'Provide a GraphQL "query" in the request payload.']]]);
    die();
}
$processor = new Processor($schema);
$response = $processor->processPayload($payload, $variables)->getResponseData();

header('Content-Type: application/json');
echo json_encode($response);

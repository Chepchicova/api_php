<?php
header("Content-Type: application/json");

require_once __DIR__ . "/config/Database.php";
require_once __DIR__ . "/config/Auth.php";
require_once __DIR__ . "/models/ServiceRequest.php";
require_once __DIR__ . "/controllers/ServiceController.php";

$db = (new Database())->getConnection();
$headers = function_exists('getallheaders') ? getallheaders() : [];
$auth = new Auth($db);
$auth->checkApiKey($headers);

$model = new ServiceRequest($db);
$controller = new ServiceController($model);

// Получаем URI и разбиваем на сегменты
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($uri, '/'));

// Ищем сегмент "api"
$pos = array_search('api', $segments);

if ($pos === false || !isset($segments[$pos+1]) || $segments[$pos+1] !== 'service_requests') {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint not found"]);
    exit;
}

$id = isset($segments[$pos+2]) ? (int)$segments[$pos+2] : null;

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents("php://input"), true);

$controller->handleRequest($method, $id, $input);

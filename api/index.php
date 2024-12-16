<?php
require_once 'auth.php';
require_once 'user.php';
require_once 'JWTHandler.php';
require_once 'RateLimiter.php';
require_once 'AuthMiddleware.php';
require_once '../config/variable.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}


$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];
$baseUri = '/siber/rest';
if (strpos($uri, $baseUri) === 0) {
    $uri = substr($uri, strlen($baseUri));
}
$uri = rtrim($uri, '/');


$rateLimiter = new RateLimiter(5, 10);
$userId = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

if (!$rateLimiter->checkLimit($userId)) {
    sendErrorResponse(429, 'Too Many Requests. Please try again later.');
}


if ($uri === '/api/auth/register' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['username'], $data['password'])) {
        sendErrorResponse(400, 'Invalid input. Username and password are required.');
    } else {
        $response = registerUser($data['username'], $data['password']);
        sendResponse(201, 'User registered successfully', $response);
    }
} else 
    if ($uri === '/api/auth/login' && $method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!isset($data['username'], $data['password'])) {
        sendErrorResponse(400, 'Invalid input. Username and password are required.');
    } else {
        $response = loginUser($data['username'], $data['password']);
        sendResponse(200, 'Login successful', $response);
    }
} else {
    $payload = AuthMiddleware::authenticate();
    if ($uri === '/api/user' && $method === 'GET') {
        checkCurrentUser();
    }
    if (preg_match('/^\/api\/user\/(\d+)$/', $uri, $matches) && $method === 'GET') {
        $response = getUserById($payload['user_id']);
        sendResponse(200, 'User retrieved successfully', $response);
    } elseif (preg_match('/^\/api\/user\/(\d+)$/', $uri, $matches) && $method === 'PUT') {
        $id = $matches[1];
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            sendErrorResponse(400, 'Invalid input. No data provided.');
        } else {
            $response = updateUser($id, $data);
            sendResponse(200, 'User updated successfully', $response);
        }
    } else {
        sendErrorResponse(404, 'Endpoint not found.');
    }
}

function sendErrorResponse($statusCode, $message)
{
    http_response_code($statusCode);
    echo json_encode(['status' => (string)$statusCode, 'message' => $message]);
    exit();
}

function sendResponse($statusCode, $message, $data = null)
{
    http_response_code($statusCode);
    $response = ['status' => (string)$statusCode, 'message' => $message];
    if (!is_null($data)) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

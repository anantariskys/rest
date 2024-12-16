<?php
include_once '../config/db.php';
require_once '../config/variable.php';
require_once '../config/config.php';
require_once 'JWTHandler.php';
require_once 'index.php';

function registerUser($username, $password): array
{
    global $pdo;

    if (empty($username) || empty($password)) {
        return sendResponse(400, 'Username and password are required.');
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE username = ?");
        $stmt->execute([$username]);
        $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingUser) {
            return sendResponse(409, 'Username already exists.');
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO members (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hashedPassword]);

        return sendResponse(201, 'User registered successfully.');
    } catch (PDOException $e) {
        return sendResponse(500, 'Database error: ' . $e->getMessage());
    }
}

function loginUser($username, $password): array
{
    global $pdo;

    if (empty($username) || empty($password)) {
        return sendResponse(400, 'Username and password are required.');
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM members WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            $token = JWTHandler::generateToken(['id' => $user['id'], 'role' => $user['role']]);

            return sendResponse(200, 'Login successful', ['user' => $user, 'token' => $token]);
        } else {
            return sendResponse(404, 'User not found or incorrect password.');
        }
    } catch (PDOException $e) {
        return sendResponse(500, 'Database error: ' . $e->getMessage());
    }
}

function checkCurrentUser(){
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(['error' => 'No token provided']);
        exit;
    }
    
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $payload = JWTHandler::verifyToken($token);
    global $pdo;

  

    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$payload['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

  
    
    if ($token) {
        return sendResponse(200, 'Current user is authorized', $user);
    } else {
        return sendResponse(401, 'Unauthorized');
    }
}

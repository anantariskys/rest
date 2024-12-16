<?php
class AuthMiddleware {
    public static function authenticate() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            http_response_code(401);
            echo json_encode(['error' => 'No token provided']);
            exit;
        }
        
        $token = str_replace('Bearer ', '', $headers['Authorization']);
        $payload = JWTHandler::verifyToken($token);
        
        if (!$payload) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid token']);
            exit;
        }
        
        return $payload;
    }
    
    public static function checkRole($requiredRole) {
        $payload = self::authenticate();
        if ($payload['role'] !== $requiredRole) {
            http_response_code(403);
            echo json_encode(['error' => 'Insufficient permissions']);
            exit;
        }
        return $payload;
    }
}
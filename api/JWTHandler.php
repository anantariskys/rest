<?php
include_once '../config/config.php';
class JWTHandler {
    public static function generateToken($userData) {
        $header = base64_encode(json_encode(['typ' => 'JWT', 'alg' => 'HS256']));
        $payload = base64_encode(json_encode([
            'user_id' => $userData['id'],
            'role' => $userData['role'],
            'exp' => time() + (60 *10 ),
            'iat' => time()
        ]));
        
        $signature = hash_hmac('sha256', "$header.$payload", JWT_SECRET_KEY, true);
        $signature = base64_encode($signature);
        
        return "$header.$payload.$signature";
    }
    
    public static function verifyToken($token) {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return false;
        }
        
        $header = $parts[0];
        $payload = $parts[1];
        $signature = $parts[2];
        
        $validSignature = base64_encode(hash_hmac(
            'sha256',
            "$header.$payload",
            JWT_SECRET_KEY,
            true
        ));
        
        if ($signature !== $validSignature) {
            return false;
        }
        
        $payload = json_decode(base64_decode($payload), true);
        if ($payload['exp'] < time()) {
            return false;
        }
        
        return $payload;
    }
}
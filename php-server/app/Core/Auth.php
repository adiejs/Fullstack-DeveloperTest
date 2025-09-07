<?php

namespace App\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Auth {
    private static string $secret_key;
    private static array $encryption = ['HS256'];

    public function __construct() {
        self::$secret_key = $_ENV['JWT_SECRET'] ?? 'Sangat_rahasia_sekali_123';
    }

    public static function setToken(int $user_id, string $email): string {
        $issued_at = time();
        $expiration_time = $issued_at + (3600 * 24);
        $payload = [
            'iat' => $issued_at,
            'exp' => $expiration_time,
            'sub' => $user_id,
            'email' => $email,
        ];
        return JWT::encode($payload, self::$secret_key, self::$encryption[0]);
    }

    public static function validateToken(): ?object {
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (!preg_match('/Bearer\s(\S+)/', $auth_header, $matches)) {
            return null;
        }

        $token = $matches[1];
        if (!$token) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key(self::$secret_key, self::$encryption[0]));
            return $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }
}
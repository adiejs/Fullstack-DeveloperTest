<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Core\Response;
use App\Models\User;

class AuthMiddleware
{
    private string $secret;
    private User $userModel;

    public function __construct(string $secret, User $userModel)
    {
        $this->secret = $secret;
        $this->userModel = $userModel;
    }

    public function handle(array $headers): ?array
    {
        // Abaikan preflight OPTIONS
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }

        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            Response::json(['error' => 'Unauthorized'], 401);
            return null;
        }

        $token = trim(substr($auth, 7));

        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            $userId = (int)$decoded->sub;
            $user = $this->userModel->findById($userId);
            if (!$user) {
                Response::json(['error' => 'User not found'], 401);
                return null;
            }
            return $user;
        } catch (\Exception $e) {
            Response::json(['error' => 'Invalid token', 'message' => $e->getMessage()], 401);
            return null;
        }
    }
}

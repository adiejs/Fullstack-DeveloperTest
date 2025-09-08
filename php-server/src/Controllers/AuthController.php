<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{
    private User $userModel;
    private array $jwtConfig;

    public function __construct(User $userModel, array $jwtConfig)
    {
        $this->userModel = $userModel;
        $this->jwtConfig = $jwtConfig;
    }

    public function register(Request $request)
    {
        $data = $request->body;
        if (empty($data['name'] ?? '') || empty($data['email'] ?? '') || empty($data['password'] ?? '')) {
            return Response::json(['error' => 'name, email, password required'], 422);
        }

        if ($this->userModel->findByEmail($data['email'])) {
            return Response::json(['error' => 'email already registered'], 409);
        }

        $id = $this->userModel->create($data['name'], $data['email'], $data['password']);
        return Response::json(['message' => 'registered', 'user_id' => $id], 201);
    }

    public function login(Request $request)
    {
        $data = $request->body;
        if (empty($data['email'] ?? '') || empty($data['password'] ?? '')) {
            return Response::json(['error' => 'email and password required'], 422);
        }

        $user = $this->userModel->findByEmail($data['email']);
        if (!$user || !password_verify($data['password'], $user['password'])) {
            return Response::json(['error' => 'invalid credentials'], 401);
        }

        $now = time();
        $payload = [
            'iat' => $now,
            'exp' => $now + ($this->jwtConfig['expire'] ?? 3600),
            'iss' => $this->jwtConfig['issuer'] ?? '',
            'aud' => $this->jwtConfig['aud'] ?? '',
            'sub' => (string)$user['id']
        ];

        $jwt = JWT::encode($payload, $this->jwtConfig['secret'], 'HS256');

        return Response::json(['access_token' => $jwt, 'token_type' => 'Bearer', 'expires_in' => $this->jwtConfig['expire'] ?? 3600]);
    }
}
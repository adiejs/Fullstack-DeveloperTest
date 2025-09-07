<?php

namespace App\Controllers;

use App\Models\User;
use App\Core\Auth;

class AuthController {
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
        new Auth(); // Inisialisasi Auth untuk mengambil JWT_SECRET
    }

    public function test(): void {
        http_response_code(200);
        echo json_encode(["message" => "API is working!"]);
    }

    public function register(): void {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data."]);
            return;
        }

        $existing_user = $this->userModel->findByEmail($data['email']);
        if ($existing_user) {
            http_response_code(409); // Conflict
            echo json_encode(["message" => "Email already exists."]);
            return;
        }

        if ($this->userModel->create($data['name'], $data['email'], $data['password'])) {
            http_response_code(201);
            echo json_encode(["message" => "User registered successfully."]);
        } else {
            http_response_code(500);
            echo json_encode(["message" => "Unable to register user."]);
        }
    }

    public function login(): void {
        $data = json_decode(file_get_contents("php://input"), true);

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data."]);
            return;
        }

        $user = $this->userModel->findByEmail($data['email']);
        
        if ($user && password_verify($data['password'], $user['password'])) {
            $token = Auth::setToken($user['id'], $user['email']);
            http_response_code(200);
            echo json_encode(["message" => "Login successful.", "access_token" => $token]);
        } else {
            http_response_code(401);
            echo json_encode(["message" => "Invalid credentials."]);
        }
    }
}
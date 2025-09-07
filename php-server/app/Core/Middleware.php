<?php

namespace App\Core;

class Middleware {
    public static function auth(): void {
        $decoded_token = Auth::validateToken();
        if (!$decoded_token) {
            http_response_code(401);
            echo json_encode(["message" => "Unauthorized access. Token is invalid or missing."]);
            exit();
        }
    }
}
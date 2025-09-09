<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Database;
use App\Models\User;
use App\Models\Product;
use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;

$config = require __DIR__ . '/../config/config.php';

// Panggil CORS middleware paling atas
CorsMiddleware::handle();

$request = new Request();
$pdo = Database::getConnection($config);

$userModel = new User($pdo);
$productModel = new Product($pdo);

$authController = new AuthController($userModel, $config['jwt']);
$productController = new ProductController($productModel);
$authMiddleware = new AuthMiddleware($config['jwt']['secret'], $userModel);

$method = $request->method;
$uri = parse_url($request->uri, PHP_URL_PATH);

// POST /auth/register
if ($method === 'POST' && $uri === '/auth/register') {
    $authController->register($request);
    exit;
}

// POST /auth/login
if ($method === 'POST' && $uri === '/auth/login') {
    $authController->login($request);
    exit;
}

// Protected product routes
if (str_starts_with($uri, '/products')) {

    // Tangani preflight OPTIONS sebelum Auth
    if ($method === 'OPTIONS') {
        http_response_code(200);
        exit;
    }

    $user = $authMiddleware->handle($request->headers);
    if (!$user) exit;

    // GET /products
    if ($method === 'GET' && $uri === '/products') {
        $productController->index();
        exit;
    }

    // GET /products/{id}
    if ($method === 'GET' && preg_match('#^/products/(\d+)$#', $uri, $m)) {
        $productController->show($m[1]);
        exit;
    }

    // POST /products
    if ($method === 'POST' && $uri === '/products') {
        $productController->store($request);
        exit;
    }

    // PUT /products/{id} atau PATCH
    if (($method === 'PUT' || $method === 'PATCH') && preg_match('#^/products/(\d+)$#', $uri, $m)) {
        $productController->update($request, $m[1]);
        exit;
    }

    // DELETE /products/{id}
    if ($method === 'DELETE' && preg_match('#^/products/(\d+)$#', $uri, $m)) {
        $productController->destroy($m[1]);
        exit;
    }

    // POST /products/upload-image
    if ($method === 'POST' && $uri === '/products/upload-image') {
        $productController->uploadImage($request);
        exit;
    }
}

// Default fallback
http_response_code(404);
echo json_encode(['error' => 'Not Found']);

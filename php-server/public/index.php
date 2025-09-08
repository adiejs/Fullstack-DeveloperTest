<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Core\Request;
use App\Core\Database;
use App\Models\User;
use App\Models\Product;
use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Middleware\AuthMiddleware;

$config = require __DIR__ . '/../config/config.php';

$request = new Request();
$pdo = Database::getConnection($config);

$userModel = new User($pdo);
$productModel = new Product($pdo);

$authController = new AuthController($userModel, $config['jwt']);
$productController = new ProductController($productModel);
$authMiddleware = new AuthMiddleware($config['jwt']['secret'], $userModel);

$method = $request->method;
$uri = parse_url($request->uri, PHP_URL_PATH);
$segments = array_values(array_filter(explode('/', $uri)));

// Routing (very simple)
// POST /register
if ($method === 'POST' && $uri === '/auth/register') {
    $authController->register($request);
    exit;
}

// POST /login
if ($method === 'POST' && $uri === '/auth/login') {
    $authController->login($request);
    exit;
}

// Protected product routes
if (str_starts_with($uri, '/products')) {
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
        $productController->store($request, $user);
        exit;
    }

    // PUT /products/{id}
    if (($method === 'PUT' || $method === 'PATCH') && preg_match('#^/products/(\d+)$#', $uri, $m)) {
        $productController->update($request, $m[1], $user);
        exit;
    }

    // DELETE /products/{id}
    if ($method === 'DELETE' && preg_match('#^/products/(\d+)$#', $uri, $m)) {
        $productController->destroy($m[1], $user);
        exit;
    }
}

// Default fallback
http_response_code(404);
echo json_encode(['error' => 'Not Found']);
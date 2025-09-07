<?php
// public/index.php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Core\Router;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$router = new Router();

$router->get('/', 'AuthController@test');

$router->post('/auth/register', 'AuthController@register');
$router->post('/auth/login', 'AuthController@login');

$router->get('/products', 'ProductController@index');
$router->get('/products/{id}', 'ProductController@show');
$router->post('/products', 'ProductController@create');
$router->put('/products/{id}', 'ProductController@update');
$router->delete('/products/{id}', 'ProductController@delete');

$router->dispatch();
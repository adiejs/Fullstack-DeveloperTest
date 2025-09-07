<?php

namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, string $action): void {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action): void {
        $this->routes['POST'][$path] = $action;
    }

    public function put(string $path, string $action): void {
        $this->routes['PUT'][$path] = $action;
    }

    public function delete(string $path, string $action): void {
        $this->routes['DELETE'][$path] = $action;
    }

    public function dispatch(): void {
        $request_uri = $_SERVER['REQUEST_URI'];
        $script_name = $_SERVER['SCRIPT_NAME'];

        $base_path = dirname($script_name);
        if ($base_path === '/') {
            $base_path = '';
        }

        $uri = substr($request_uri, strlen($base_path));
        $uri = strtok($uri, '?');
        $method = $_SERVER['REQUEST_METHOD'];

        if (array_key_exists($method, $this->routes)) {
            if (array_key_exists($uri, $this->routes[$method])) {
                $action = $this->routes[$method][$uri];
                list($controller, $method) = explode('@', $action);
                
                $controllerPath = 'App\\Controllers\\' . $controller;
                if (class_exists($controllerPath) && method_exists($controllerPath, $method)) {
                    $controllerInstance = new $controllerPath();
                    $controllerInstance->$method();
                    return;
                }
            }

      
            $segments = explode('/', trim($uri, '/'));
            foreach ($this->routes[$method] as $route_path => $action) {
                $route_segments = explode('/', trim($route_path, '/'));
                
                if (count($segments) === count($route_segments)) {
                    $params = [];
                    $match = true;

                    for ($i = 0; $i < count($segments); $i++) {
                        if (strpos($route_segments[$i], '{') === 0 && strpos($route_segments[$i], '}') === strlen($route_segments[$i]) - 1) {
                            $param_name = trim($route_segments[$i], '{}');
                            $params[$param_name] = $segments[$i];
                        } elseif ($segments[$i] !== $route_segments[$i]) {
                            $match = false;
                            break;
                        }
                    }

                    if ($match) {
                        list($controller, $method) = explode('@', $action);
                        $controllerPath = 'App\\Controllers\\' . $controller;
                        $controllerInstance = new $controllerPath();
                        call_user_func_array([$controllerInstance, $method], array_values($params));
                        return;
                    }
                }
            }
        }

        http_response_code(404);
        echo json_encode(["message" => "Endpoint not found"]);
    }
}
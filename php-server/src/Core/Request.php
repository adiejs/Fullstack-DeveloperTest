<?php
namespace App\Core;

class Request
{
    public string $method;
    public array $query = [];
    public array $body = [];
    public array $headers = [];
    public string $uri;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->query = $_GET;
        $this->headers = getallheaders() ?: [];

        $input = file_get_contents('php://input');
        $json = json_decode($input, true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $this->body = $json;
        } else {
            parse_str($input, $parsed);
            $this->body = $parsed;
        }
    }
}
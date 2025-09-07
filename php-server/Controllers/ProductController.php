<?php

namespace App\Controllers;

use App\Models\Product;
use App\Core\Middleware;

class ProductController {
    private Product $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index(): void {
        Middleware::auth();
        $products = $this->productModel->readAll();
        http_response_code(200);
        echo json_encode($products);
    }
    
    public function show(int $id): void {
        Middleware::auth();
        $product = $this->productModel->readOne($id);
        if ($product) {
            http_response_code(200);
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Product not found."]);
        }
    }

    public function create(): void {
        Middleware::auth();
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name']) || !isset($data['price'])) {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data."]);
            return;
        }

        if ($this->productModel->create($data)) {
            http_response_code(201);
            echo json_encode(["message" => "Product created successfully."]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to create product."]);
        }
    }
    
    public function update(int $id): void {
        Middleware::auth();
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$this->productModel->readOne($id)) {
            http_response_code(404);
            echo json_encode(["message" => "Product not found."]);
            return;
        }

        if (!isset($data['name']) || !isset($data['price'])) {
            http_response_code(400);
            echo json_encode(["message" => "Incomplete data."]);
            return;
        }
        
        if ($this->productModel->update($id, $data)) {
            http_response_code(200);
            echo json_encode(["message" => "Product updated successfully."]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to update product."]);
        }
    }
    
    public function delete(int $id): void {
        Middleware::auth();
        
        if (!$this->productModel->readOne($id)) {
            http_response_code(404);
            echo json_encode(["message" => "Product not found."]);
            return;
        }

        if ($this->productModel->delete($id)) {
            http_response_code(200);
            echo json_encode(["message" => "Product deleted successfully."]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to delete product."]);
        }
    }
}
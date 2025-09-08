<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\UploadHelper;
use App\Models\Product;

class ProductController
{
    private Product $productModel;

    public function __construct(Product $productModel)
    {
        $this->productModel = $productModel;
    }

    public function index()
    {
        $list = $this->productModel->all();
        return Response::json(['data' => $list]);
    }

    public function show($id)
    {
        $p = $this->productModel->find((int)$id);
        if (!$p) return Response::json(['error' => 'Not found'], 404);
        return Response::json($p);
    }

    public function store(Request $request, array $user)
    {
        $data = $request->body;
        $file = $_FILES['image'] ?? null;

        if (empty($data['name'] ?? '') || !isset($data['price'])) {
            return Response::json(['error' => 'name and price required'], 422);
        }

        $imageUrl = null;
        if ($file) {
            $imageUrl = UploadHelper::uploadFile($file);
        }

        $payload = [
            'user_id' => $user['id'],
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'price' => $data['price'],
            'imageUrl' => $imageUrl
        ];

        $id = $this->productModel->create($payload);
        return Response::json(['message' => 'created', 'id' => $id], 201);
    }

    public function update(Request $request, $id, array $user)
    {
        $product = $this->productModel->find((int)$id);
        if (!$product) return Response::json(['error' => 'Not found'], 404);
        if ($product['user_id'] != $user['id']) return Response::json(['error' => 'Forbidden'], 403);

        $data = $request->body;
        $file = $_FILES['image'] ?? null;

        if (empty($data['name'] ?? '') || !isset($data['price'])) {
            return Response::json(['error' => 'name and price required'], 422);
        }

        $imageUrl = $product['imageUrl'] ?? null;
        if ($file) {
            $imageUrl = UploadHelper::uploadFile($file);
        }

        $data['imageUrl'] = $imageUrl;

        $this->productModel->update((int)$id, $data);
        return Response::json(['message' => 'updated']);
    }

    public function destroy($id, array $user)
    {
        $product = $this->productModel->find((int)$id);
        if (!$product) return Response::json(['error' => 'Not found'], 404);
        if ($product['user_id'] != $user['id']) return Response::json(['error' => 'Forbidden'], 403);

        $this->productModel->delete((int)$id);
        return Response::json(['message' => 'deleted']);
    }
}

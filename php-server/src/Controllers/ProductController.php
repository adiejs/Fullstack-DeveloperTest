<?php
namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Models\Product;
use App\Helpers\UploadHelper;

class ProductController
{
    private Product $productModel;

    public function __construct(Product $productModel)
    {
        $this->productModel = $productModel;
    }

    // GET /products
    public function index()
    {
        $products = $this->productModel->all();
        return Response::json($products);
    }

    // GET /products/{id}
    public function show($id)
    {
        $product = $this->productModel->find((int)$id);
        if (!$product) {
            return Response::json(['error' => 'Not found'], 404);
        }
        return Response::json($product);
    }

    // POST /products
    public function store(Request $request)
    {
        $data = $request->body;
        $file = $_FILES['file'] ?? null;

        if (empty($data['name']) || !isset($data['price'])) {
            return Response::json(['error' => 'Name and price required'], 422);
        }

        $imageUrl = null;
        if ($file) {
            $imageUrl = UploadHelper::uploadFile($file);
        }

        $payload = [
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'price' => $data['price'],
            'imageUrl' => $imageUrl,
            'updatedAt' => date('Y-m-d H:i:s')
        ];

        $id = $this->productModel->create($payload);
        return Response::json(['message' => 'created', 'id' => $id], 201);
    }

    // PUT /products/{id}
    public function update(Request $request, $id)
    {
        $product = $this->productModel->find((int)$id);
        if (!$product) {
            return Response::json(['error' => 'Not found'], 404);
        }

        $data = $request->body;
        $file = $_FILES['file'] ?? null;

        if (empty($data['name']) || !isset($data['price'])) {
            return Response::json(['error' => 'Name and price required'], 422);
        }

        $imageUrl = $product['imageUrl'] ?? null;
        if ($file) {
            $imageUrl = UploadHelper::uploadFile($file);
        }

        $data['imageUrl'] = $imageUrl;
        $data['updatedAt'] = date('Y-m-d H:i:s');

        $this->productModel->update((int)$id, $data);
        return Response::json(['message' => 'updated']);
    }

    // DELETE /products/{id}
    public function destroy($id)
    {
        $product = $this->productModel->find((int)$id);
        if (!$product) {
            return Response::json(['error' => 'Not found'], 404);
        }

        $this->productModel->delete((int)$id);
        return Response::json(['message' => 'deleted']);
    }

    // POST /products/upload-image
    public function uploadImage(Request $request)
    {
        if (!isset($_FILES['file'])) {
            return Response::json(['error' => 'No file uploaded'], 400);
        }

        $file = $_FILES['file'];
        $imageUrl = UploadHelper::uploadFile($file);

        if (!$imageUrl) {
            return Response::json(['error' => 'Upload failed'], 500);
        }

        return Response::json([
            'message' => 'Upload successful',
            'imageUrl' => $imageUrl
        ], 200);
    }
}

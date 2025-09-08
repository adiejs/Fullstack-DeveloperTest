<?php
namespace App\Helpers;

class UploadHelper
{
    public static function uploadFile(array $file, string $uploadDir = __DIR__ . '/../../uploads/'): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) return null;

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            return null; 
        }

        $filename = uniqid('img_') . '.' . $ext;
        $target = rtrim($uploadDir, '/') . '/' . $filename;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        if (move_uploaded_file($file['tmp_name'], $target)) {
            return '/uploads/' . $filename;
        }

        return null;
    }
}
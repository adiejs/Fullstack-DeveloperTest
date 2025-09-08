<?php
namespace App\Models;

use PDO;

class Product
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT * FROM product');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM product WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO product (user_id, name, description, price) VALUES (?, ?, ?, ?)');
        $stmt->execute([$data['user_id'], $data['name'], $data['description'], $data['price']]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('UPDATE product SET name = ?, description = ?, price = ? WHERE id = ?');
        return $stmt->execute([$data['name'], $data['description'], $data['price'], $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM product WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
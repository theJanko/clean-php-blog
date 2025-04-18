<?php

namespace App\Models;

readonly class Article
{
    private const ALLOWED_FIELDS = ['title', 'description'];
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM articles ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM articles WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $data = $this->sanitizeData($data);

        if (empty($data['title'])) {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        $stmt = $this->db->prepare(
            'INSERT INTO articles (title, description, created_at) VALUES (?, ?, NOW())'
        );
        $stmt->execute([$data['title'], $data['description']]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $data = $this->sanitizeData($data);

        if (empty($data['title'])) {
            throw new \InvalidArgumentException('Title cannot be empty');
        }

        $stmt = $this->db->prepare(
            'UPDATE articles SET title = ?, description = ?, updated_at = NOW() WHERE id = ?'
        );
        return $stmt->execute([$data['title'], $data['description'], $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM articles WHERE id = ?');
        return $stmt->execute([$id]);
    }

    private function sanitizeData(array $data): array
    {
        $sanitized = [];

        foreach (self::ALLOWED_FIELDS as $field) {
            $sanitized[$field] = isset($data[$field]) && $data[$field] !== ''
                ? trim($data[$field])
                : '';
        }

        return $sanitized;
    }
}

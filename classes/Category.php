<?php
declare(strict_types=1);
class Category
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }
    public function all(): array { return $this->db->query('SELECT id, name FROM categories ORDER BY id')->fetchAll(); }
    public function exists(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return (bool) $stmt->fetch();
    }
}

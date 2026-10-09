<?php
declare(strict_types=1);
class Comment
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }
    public function all(int $recipeId): array
    {
        $stmt = $this->db->prepare('SELECT c.*, u.name AS author FROM comments c JOIN users u ON u.id = c.user_id WHERE c.recipe_id = ? ORDER BY c.created_at, c.id');
        $stmt->execute([$recipeId]); return $stmt->fetchAll();
    }
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM comments WHERE id = ?');
        $stmt->execute([$id]); return $stmt->fetch() ?: null;
    }
    public function isOwner(int $id, int $userId): bool
    {
        $comment = $this->find($id);
        return $comment !== null && (int) $comment['user_id'] === $userId;
    }
    public function add(int $recipeId, int $userId, string $content): void
    {
        $stmt = $this->db->prepare('INSERT INTO comments (recipe_id, user_id, content) VALUES (?, ?, ?)');
        $stmt->execute([$recipeId, $userId, $content]);
    }
    public function update(int $id, int $userId, string $content): bool
    {
        if (!$this->isOwner($id, $userId)) { return false; }
        // Binary comparison also detects case-only edits.
        $stmt = $this->db->prepare('UPDATE comments SET content = ?, is_edited = 1 WHERE id = ? AND user_id = ? AND BINARY content <> BINARY ?');
        $stmt->execute([$content, $id, $userId, $content]); return true;
    }
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM comments WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]); return $stmt->rowCount() > 0;
    }
}

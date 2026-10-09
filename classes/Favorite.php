<?php
declare(strict_types=1);
class Favorite
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }
    public function exists(int $userId, int $recipeId): bool
    {
        $stmt = $this->db->prepare('SELECT user_id FROM favorites WHERE user_id = ? AND recipe_id = ?');
        $stmt->execute([$userId, $recipeId]); return (bool) $stmt->fetch();
    }
    public function add(int $userId, int $recipeId): void
    {
        $stmt = $this->db->prepare('INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_id = VALUES(user_id)');
        $stmt->execute([$userId, $recipeId]);
    }
    public function remove(int $userId, int $recipeId): void
    {
        $stmt = $this->db->prepare('DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?');
        $stmt->execute([$userId, $recipeId]);
    }
    public function forUser(int $userId): array { return (new Recipe($this->db))->all($userId, '', 0, true); }
}

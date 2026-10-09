<?php
declare(strict_types=1);
class Recipe
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }
    public function all(int $viewerId, string $search = '', int $categoryId = 0, bool $savedOnly = false): array
    {
        // LEFT JOIN keeps recipes even if the viewer has not saved them.
        $sql = 'SELECT r.*, u.name AS author, c.name AS category,
                (f.user_id IS NOT NULL) AS is_favorited
                FROM recipes r JOIN users u ON u.id = r.user_id
                JOIN categories c ON c.id = r.category_id
                LEFT JOIN favorites f ON f.recipe_id = r.id AND f.user_id = ? WHERE 1 = 1';
        $params = [$viewerId];
        if ($search !== '') {
            $sql .= ' AND (r.title LIKE ? OR r.description LIKE ?)';
            $params[] = '%' . $search . '%'; $params[] = '%' . $search . '%';
        }
        if ($categoryId > 0) { $sql .= ' AND r.category_id = ?'; $params[] = $categoryId; }
        if ($savedOnly) { $sql .= ' AND f.user_id IS NOT NULL'; }
        $stmt = $this->db->prepare($sql . ' ORDER BY r.created_at DESC, r.id DESC');
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT r.*, u.name AS author, c.name AS category
            FROM recipes r JOIN users u ON u.id = r.user_id
            JOIN categories c ON c.id = r.category_id WHERE r.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    public function ingredients(int $id): array
    {
        $stmt = $this->db->prepare('SELECT ingredient_name FROM ingredients WHERE recipe_id = ? ORDER BY sort_order');
        $stmt->execute([$id]);
        return array_column($stmt->fetchAll(), 'ingredient_name');
    }
    public function isOwner(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM recipes WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        return (bool) $stmt->fetch();
    }
    private function saveIngredients(int $id, array $ingredients): void
    {
        $stmt = $this->db->prepare('INSERT INTO ingredients (recipe_id, ingredient_name, sort_order) VALUES (?, ?, ?)');
        foreach ($ingredients as $order => $ingredient) { $stmt->execute([$id, $ingredient, $order]); }
    }
    public function create(int $userId, array $data): int
    {
        // All steps must succeed together.
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('INSERT INTO recipes (user_id, category_id, title, description, instructions) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$userId, $data['category_id'], $data['title'], $data['description'], $data['instructions']]);
            $id = (int) $this->db->lastInsertId();
            $this->saveIngredients($id, $data['ingredients']);
            $this->db->commit();
            return $id;
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) { $this->db->rollBack(); }
            throw $error;
        }
    }
    public function update(int $id, int $userId, array $data): bool
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('SELECT * FROM recipes WHERE id = ? AND user_id = ? FOR UPDATE');
            $stmt->execute([$id, $userId]);
            $old = $stmt->fetch();
            if (!$old) { $this->db->rollBack(); return false; }
            $changed = $old['title'] !== $data['title'] || $old['description'] !== $data['description']
                || $old['instructions'] !== $data['instructions'] || (int) $old['category_id'] !== $data['category_id']
                || $this->ingredients($id) !== $data['ingredients'];
            if ($changed) {
                $stmt = $this->db->prepare('UPDATE recipes SET category_id = ?, title = ?, description = ?, instructions = ?, is_edited = 1 WHERE id = ? AND user_id = ?');
                $stmt->execute([$data['category_id'], $data['title'], $data['description'], $data['instructions'], $id, $userId]);
                $stmt = $this->db->prepare('DELETE FROM ingredients WHERE recipe_id = ?');
                $stmt->execute([$id]);
                $this->saveIngredients($id, $data['ingredients']);
            }
            $this->db->commit();
            return true;
        } catch (Throwable $error) {
            if ($this->db->inTransaction()) { $this->db->rollBack(); }
            throw $error;
        }
    }
    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM recipes WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        return $stmt->rowCount() > 0;
    }
}

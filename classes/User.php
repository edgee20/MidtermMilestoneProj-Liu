<?php
declare(strict_types=1);
class User
{
    private PDO $db;
    public function __construct(PDO $db) { $this->db = $db; }
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }
    public function register(string $name, string $email, string $password): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
        return (int) $this->db->lastInsertId();
    }
    public function authenticate(string $email, string $password): ?array
    {
        $user = $this->findByEmail($email);
        return $user && password_verify($password, $user['password']) ? $user : null;
    }
}

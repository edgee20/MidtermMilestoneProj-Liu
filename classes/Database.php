<?php
declare(strict_types=1);
class Database
{
    private PDO $connection;
    public function __construct(array $config)
    {
        $this->connection = new PDO(
            'mysql:host=' . $config['host'] . ';dbname=' . $config['name'] . ';charset=utf8mb4',
            $config['user'], $config['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
             PDO::ATTR_EMULATE_PREPARES => false]
        );
        $this->connection->exec("SET time_zone = '+08:00'");
    }
    public function getConnection(): PDO { return $this->connection; }
}

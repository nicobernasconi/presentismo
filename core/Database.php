<?php
namespace Core;

use PDO;
use PDOException;

/**
 * Database - Singleton para conexión PDO
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $config = require CONFIG_PATH . '/database.php';

        $dsn = sprintf(
            '%s:host=%s;port=%d;dbname=%s;charset=%s',
            $config['driver'],
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            $this->connection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                $config['options']
            );
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    /**
     * Obtiene la instancia única
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Obtiene la conexión PDO
     */
    public function getConnection(): PDO
    {
        return $this->connection;
    }

    /**
     * Ejecuta una consulta preparada
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Obtiene todos los registros
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Obtiene un registro
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Inserta un registro y retorna el ID
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));

        return (int) $this->connection->lastInsertId();
    }

    /**
     * Actualiza registros
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        $sql = "UPDATE {$table} SET {$set} WHERE {$where}";

        $params = array_merge(array_values($data), $whereParams);
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Elimina registros
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        return $this->query($sql, $params)->rowCount();
    }

    /**
     * Inicia una transacción
     */
    public function beginTransaction(): bool
    {
        return $this->connection->beginTransaction();
    }

    /**
     * Confirma una transacción
     */
    public function commit(): bool
    {
        return $this->connection->commit();
    }

    /**
     * Revierte una transacción
     */
    public function rollBack(): bool
    {
        return $this->connection->rollBack();
    }

    // Prevenir clonación
    private function __clone() {}
}

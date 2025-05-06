<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;
use Exception;
use PDOException;

class BaseRepository implements RepositoryInterface {
    protected $conn;

    public function __construct() {
        try {
            $this->conn = Database::getConnection();
        } catch (Exception $e) {
            echo "Database connection failed: " . $e->getMessage();
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (Exception $e) {
            echo "SQL query execution failed: " . $e->getMessage();
            return false;
        }
    }

    public function insert($table, $data) {
        try {
            $columns = implode(',', array_keys($data));
            $placeholders = implode(',', array_fill(0, count($data), '?'));
            $stmt = $this->query("INSERT INTO $table ($columns) VALUES ($placeholders)", array_values($data));
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            echo "Insert into $table failed: " . $e->getMessage();
            return false;
        }
    }

    public function getAll($table) {
        try {
            $stmt = $this->query("SELECT * FROM $table");
            return $stmt ? $stmt->fetchAll(PDO::FETCH_OBJ) : [];
        } catch (Exception $e) {
            echo "Fetch all from $table failed: " . $e->getMessage();
            return [];
        }
    }

    public function findById($table, int $id) {
        try {
            $stmt = $this->query("SELECT * FROM $table WHERE id = ?", [$id]);
            return $stmt ? $stmt->fetch(PDO::FETCH_OBJ) : null;
        } catch (Exception $e) {
            echo "Find by ID $id in $table failed: " . $e->getMessage();
            return null;
        }
    }

    public function findBy($table, $condition) {
        try {
            $whereClause = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($condition)));
            $stmt = $this->query("SELECT * FROM $table WHERE $whereClause", array_values($condition));
            return $stmt ? $stmt->fetch(PDO::FETCH_OBJ) : null;
        } catch (Exception $e) {
            echo "Find by condition in $table failed: " . $e->getMessage();
            return null;
        }
    }

    public function update($table, int $id, array $data) {
        try {
            $setPart = implode('=?, ', array_keys($data)) . '=?';
            $values = array_values($data);
            $values[] = $id;
            $stmt = $this->query("UPDATE $table SET $setPart WHERE id = ?", $values);
            return $stmt ? $stmt->rowCount() : 0;
        } catch (Exception $e) {
            echo "Update record with ID $id in $table failed: " . $e->getMessage();
            return 0;
        }
    }

    public function delete($table, int $id) {
        try {
            $stmt = $this->query("DELETE FROM $table WHERE id = ?", [$id]);
            return $stmt ? $stmt->rowCount() : 0;
        } catch (Exception $e) {
            echo "Delete record with ID $id from $table failed: " . $e->getMessage();
            return 0;
        }
    }
}

<?php

namespace App\Repositories;
use App\Config\Database;
use PDO;
use Exception; // Ajout de la classe Exception

class BaseRepository implements RepositoryInterface {
    protected $conn;

    public function __construct(){
        try {
            $this->conn = Database::getConnection();
        } catch (Exception $e) {
            // Gestion des erreurs de connexion à la base de données
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (Exception $e) {
            // Capture et affichage des erreurs SQL
            die("Query failed: " . $e->getMessage());
        }
    }

    public function insert($table, $data)
    {
        try {
            $columns = implode(',', array_keys($data));
            $placeholders = implode(',', array_fill(0, count($data), '?'));
            $stmt = $this->query("INSERT INTO $table ($columns) VALUES ($placeholders)", array_values($data));
            return $this->conn->lastInsertId();
        } catch (Exception $e) {
            die("Insert failed: " . $e->getMessage());
        }
    }

    public function getAll($table)
    {
        try {
            $stmt = $this->query("SELECT * FROM $table");
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die("Get all records failed: " . $e->getMessage());
        }
    }

    public function findById($table, int $id)
    {
        try {
            $stmt = $this->query("SELECT * FROM $table WHERE id = ?", [$id]);
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die("Find by ID failed: " . $e->getMessage());
        }
    }

    public function findBy($table, $condition)
    {
        try {
            $whereClause = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($condition)));
            $stmt = $this->query("SELECT * FROM $table WHERE $whereClause", array_values($condition));
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            die("Find by condition failed: " . $e->getMessage());
        }
    }

    public function update($table, int $id, array $data)
    {
        try {
            $setPart = implode('=?, ', array_keys($data)) . '=?';
            $values = array_values($data);
            $values[] = $id;
            $stmt = $this->query("UPDATE $table SET $setPart WHERE id = ?", $values);
            return $stmt->rowCount();
        } catch (Exception $e) {
            die("Update failed: " . $e->getMessage());
        }
    }

    public function delete($table, int $id)
    {
        try {
            $stmt = $this->query("DELETE FROM $table WHERE id = ?", [$id]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            die("Delete failed: " . $e->getMessage());
        }
    }
}

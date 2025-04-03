<?php

namespace App\Repositories;
use App\Config\Database;
use PDO;

class BaseRepository implements RepositoryInterface {
    protected $conn;

    public function __construct(){
        $this->conn = Database::getConnection();
    }


    public function query($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }


    public function insert($table, $data)
    {

        $columns = implode(',', array_keys($data));

        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $stmt = $this->query("INSERT INTO $table ($columns) VALUES ($placeholders)", array_values($data));
        return $this->conn->lastInsertId();
    }


    public function getAll($table){
        $stmt = $this->query("SELECT * FROM $table");
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function findById($table ,int $id){
        $stmt = $this->query("SELECT * FROM $table WHERE id = ?", [$id]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function findBy($table, $condition)
    {
        $whereClause = implode(' AND ', array_map(fn($col) => "$col = ?", array_keys($condition)));
    
        $stmt = $this->query("SELECT * FROM $table WHERE $whereClause", array_values($condition));
    
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
 
    public function update($table ,int $id, array $data){
        $setPart = implode('=?, ', array_keys($data)) . '=?';
        $values = array_values($data);
        $values[] = $id;
        $stmt = $this->query("UPDATE $table SET $setPart WHERE id = ?", $values);
        return $stmt->rowCount();
    }
    public function delete($table ,int $id){
        $stmt = $this->query("DELETE FROM $table WHERE id = ?", [$id]);
        return $stmt->rowCount();
    }

    
} 
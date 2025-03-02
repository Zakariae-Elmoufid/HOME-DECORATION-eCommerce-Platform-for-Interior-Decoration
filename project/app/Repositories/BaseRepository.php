<?php

namespace App\Repositories;
use App\Config\Database;

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


    public function getAll(){

    }
    public function findById(int $id){

    }
    public function create(array $data){

    }
    public function update(int $id, array $data){

    }
    public function delete(int $id){
        
    }
} 
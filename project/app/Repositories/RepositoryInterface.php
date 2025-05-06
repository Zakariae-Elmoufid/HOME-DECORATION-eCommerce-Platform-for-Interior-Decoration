<?php
namespace App\Repositories;

interface RepositoryInterface{
    public function getAll($table);
    public function findById($table, int $id);
    public function insert($table ,array $data);
    public function update($table ,int $id, array $data);
    public function delete($table ,int $id);
}
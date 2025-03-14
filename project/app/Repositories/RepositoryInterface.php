<?php
namespace App\Repositories;
interface RepositoryInterface{
    public function getAll($table);
    public function findById($table, int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}
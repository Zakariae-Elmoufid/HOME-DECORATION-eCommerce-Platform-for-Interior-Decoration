<?php 

namespace App\Repositories;

use App\Models\Category;
use PDO;

class CategoryRepository extends BaseRepository {

    private $table = "categories";


    public  function fechAll(){
      $categories =  $this->getAll($this->table);
      $data = [];
      foreach( $categories as $category ){
         $data = new Category($category);
      }
      return $categories;
    }
    
    public function create($data){

        $result = $this->insert($this->table,$data);
        $data['id'] = $result;
          $category = new category($data);
          return $category;
        
    }

    public function fetchById($id){
      $data =  $this->findById($this->table, $id);
      $category = new category($data);
      return $category;
    }

    public function updat($id ,$date){
       return $this->update($this->table, $id ,$date);
       $data['id'] = $id;
       $category = new category($data);
       return $category;
    }
    
    public function remove($id){
      return $this->delete($this->table ,$id);
    }

}
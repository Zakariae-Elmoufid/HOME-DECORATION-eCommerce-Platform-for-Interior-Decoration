<?php 

namespace App\Repositories;

use App\Models\Category;
use PDO;

class CategoryRepository extends BaseRepository {

    private $table = "categorys";


    public  function fechAll(){
      return  $this->getAll($this->table);
    }
    
    public function create($data){

        $result = $this->insert($this->table,$data );

          $category = new category($data["title"],$data["icon"]);
          return $category;
        
    }


}
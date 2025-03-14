<?php 

namespace App\Services;

use App\Core\Validator;
use App\Core\Response;
use App\Repositories\CategoryRepository;

class CategoryService {
    
   private $CategoryRepository;
   private $response;
   public function __construct(){
       $this->CategoryRepository = new CategoryRepository();
       $this->response = new Response();
   } 

   public function create($data){
     
    $errors = [];
    $validator = new Validator($data);

    $validator->setRules([
        'title' => 'required|string|min:6|max:50',
        'icon' => 'string',
    ]);

    $oldData = $data;
    

    if (!$validator->validate()) {
        $errors = $validator->getErrors();


        echo json_encode(["errors" => $errors, "data" => $oldData]);
      
       
        
    }


    

    //   $this->CategoryRepository-
   }

}
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
        'icon' => 'string|min:2',
    ]);

    $oldData = $data;
    

    if (!$validator->validate()) {
        $errors = $validator->getErrors();
        return $this->response->jsonEncode(["errors" => $errors, "data" => $oldData]);
    }
        return $this->response->jsonEncode("susscuful");
    }

}
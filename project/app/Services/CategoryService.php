<?php 

namespace App\Services;

use App\Core\Validator;
use App\Core\Response;
use App\Repositories\CategoryRepository;

class CategoryService {
    
   private $categoryRepository;
   private $response;

   public function __construct(){
       $this->categoryRepository = new CategoryRepository();
       $this->response = new Response();
   } 

   public function fechAll(){
    return  $this->categoryRepository->fechAll();

   }

   public function create($data){
     
    $errors = [];
    $validator = new Validator($data);

    $validator->setRules([
        'title' => 'required|string|min:2|max:50|unique:categorys,title',
        'icon' => 'string|min:2|unique:categorys,icon',
    ]);

    $oldData = $data;
    

    if (!$validator->validate()) {
        $errors = $validator->getErrors();
        return $this->response->jsonEncode(["errors" => $errors, "data" => $oldData]);
    }

    $result = $this->categoryRepository->create($data);

    return $this->response->jsonEncode([ "message" => "susscuful" ,'data' => $data]);
    }

    public function show($id){
        $result = $this->categoryRepository->fetchById($id);
        return $this->response->jsonEncode( $result);
    }


    public function update($data){
        $errors = [];
        $validator = new Validator($data);
    
        $validator->setRules([
            'title' => 'required|string|min:2|max:50',
            'icon' => 'string|min:2',
        ]);
    
        $oldData = $data;
        
    
        if (!$validator->validate()) {
            $errors = $validator->getErrors();
        
            return $this->response->jsonEncode(["errors" => $errors, "data" => $oldData]);
        }
        $id =  $data['id'] ;
        $result = $this->categoryRepository->updat($id,$data);

        return $this->response->jsonEncode([ "message" => "update susscuful" ]);
    
    }


    public function delete($id){
        $result = $this->categoryRepository->remove($id);
        return $this->response->jsonEncode([ "message" => "susscuful delete" ]);
    }

}
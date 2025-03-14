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
    $result = $this->categoryRepository->fechAll();
    return $this->response->jsonEncode([ 'data' => $result]);

    // return $this->response->render('admin/categorys/index',[
    //     'result' => $result
    // ]);

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

}
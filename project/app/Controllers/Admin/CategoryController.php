<?php 

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Services\CategoryService;
use App\Core\Response;

class CategoryController extends Controller{

    private $CategoryService ;
    private $response;

    public function __construct(){
        $this->CategoryService = new CategoryService() ; 
        $this->response = new Response();

    }

    public function index(){
        $this->render('admin/categorys/index');
    }

    public function fech(){
       $result = $this->CategoryService->fechAll();
       return $this->response->jsonEncode($result);
    }
    public function show(Request $request){
        $body = $request->getbody();
        $id = isset($body['id']) ? (int) $body['id'] : null;
        $result = $this->CategoryService->show($id);
         
        $category = $this->CategoryService->show($id);
        
        $result = [
            'id' => $category->getId(),
            'title' => $category->getTitle(),
            'icon' => $category->getIcon()
        ];
    
        return $this->response->jsonEncode($result);
    }

    public function store(Request $request){
        $data = $request->getbody();
        $category = $this->CategoryService->create($data);

       if(!empty($category["errors"])){
           return $this->response->jsonEncode($category);
        }

        return $this->response->jsonEncode([ "message" => "susscuful" ,'data' => $data]);


    }

    
    public function update(Request $request){
        $data = $request->getBody();
        $result = $this->CategoryService->update($data);
        if($result){
            $this->response->jsonEncode([ "message" => "update susscuful" ]);
        }
    }

    public function delete(Request $request){
        $body = $request->getbody();
        $id = $body['id'];
        $this->CategoryService->delete($id);

    }



}
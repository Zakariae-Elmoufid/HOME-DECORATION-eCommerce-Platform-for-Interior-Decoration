<?php 

namespace App\Controllers\Admin;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Services\CategoryService;

class CategoryController extends BaseControllerAdmin{

    private $CategoryService;
    private $response;

    public function __construct(){
        parent::__construct();
        $this->CategoryService = new CategoryService() ; 
        $this->response = new Response();
    }

    public function index(){
       
        $this->response->render('admin/categorys/index');
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
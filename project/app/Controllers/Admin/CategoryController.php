<?php 

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Services\CategoryService;

class CategoryController extends Controller{

    private $CategoryService ;

    public function __construct(){
        $this->CategoryService = new CategoryService() ; 
    }

    public function index(){
        $this->render('admin/categorys/index');
    }

    public function fech(){
        $this->CategoryService->fechAll();
    }
    public function show(Request $request){
        $body = $request->getbody();
        $id = isset($body['id']) ? (int) $body['id'] : null;

        $this->CategoryService->show($id);

    }



    public function store(Request $request){
        $data = $request->getbody();
         $this->CategoryService->create($data);
    }

    
    public function update(Request $request){
        $data = $request->getBody();
        $this->CategoryService->update($data);
    }

    public function delete(Request $request){
        $body = $request->getbody();
        $id = $body['id'];
        $this->CategoryService->delete($id);

    }



}
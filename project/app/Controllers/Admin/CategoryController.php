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

    public function store(Request $request){
         $data = $request->getBody();
         $this->CategoryService->create($data);
    }



}
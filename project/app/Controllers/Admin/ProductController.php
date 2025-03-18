<?php 

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Services\ProductService;

class ProductController extends Controller{

    private $ProductService ;

    public function __construct(){
        $this->ProductService = new ProductService() ; 
    }

    public function index(){
        $this->ProductService->fetchAll();
    }
    
    public function create(){
        $this->ProductService->fetchCategory();
    }


    public function store(Request $request){
        $data = $request->getbody();
       $this->ProductService->store($data);
    }

}
<?php 

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\ProductService;

class ProductController extends Controller{

    private $ProductService ;

    public function __construct(){
        $this->ProductService = new ProductService() ; 
    }

    public function index(){
        $this->ProductService->fetchAll();
    }
}
<?php
namespace App\Controllers;
use App\Core\Controller;
use App\core\Request;
use App\core\Response;
use App\core\Validator;
use App\Services\CategoryService;
use App\Services\ProductService;

class HomeController extends Controller{


    private $CategoryService;
    private $ProductService;
    private $response;

    public function __construct(){
        $this->CategoryService = new CategoryService() ; 
        $this->ProductService = new ProductService() ; 
        $this->response = new Response();


    }

    public function index()
    {
        $categories =  $this->CategoryService->fechAll();
        $products = $this->ProductService->fetchAll();

        return $this->render('home', [
            'categories' => $categories,
            'products' => $products
        ]);
    }

    public function product()
    {
        $products = $this->ProductService->fetchAll();
        return $this->render('products', [
            'products' => $products
        ]);
    }
    
    public function search(Request $request){
        // $products = $this->ProductService->fechByKey();
        return $this->response->jsonEncode( $request);

    }







}
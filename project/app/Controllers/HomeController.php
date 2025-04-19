<?php
namespace App\Controllers;
use App\Core\Controller;
use App\core\Request;
use App\core\Response;
use App\core\Validator;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Repositories\ProductRepository;


class HomeController extends Controller{


    private $CategoryService;
    private $ProductService;
    private $productRepository;
    private $response;

    public function __construct(){
        $this->CategoryService = new CategoryService() ; 
        $this->ProductService = new ProductService() ; 
        $this->productRepository = new ProductRepository();
        $this->response = new Response();
    }

    public function index()
    {
        $categories =  $this->CategoryService->fechAll();
        $products = $this->ProductService->fetchAll();
        $newProducts =   $this->productRepository->getNewProducts();
        

        return $this->render('home', [
            'categories' => $categories,
            'products' => $products,
            'newProducts' =>$newProducts
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
        $body = $request->getbody();
        $products = $this->productRepository->fechByKeyWord($body['keyword']);
         $this->response->jsonEncode(["products" => $products]);
    }

    public function getProductsByCategory(Request $request){
        $data = $request->getbody();
        $id = $data['category'];
        $products = $this->productRepository->getProductsByCategory($id);
        return $this->render('customer/productsByCategory', ['products' => $products ]);
    }



    







}
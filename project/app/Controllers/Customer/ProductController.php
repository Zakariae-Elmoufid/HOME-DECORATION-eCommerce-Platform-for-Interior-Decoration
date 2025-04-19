<?php
namespace App\Controllers\Customer;

use App\Core\Controller;
use App\core\Request;
use App\core\Response;
use App\Repositories\ProductRepository;


class ProductController extends Controller{
    private $CategoryService;
    private $ProductService;
    private $productRepository;
    private $response;

    public function __construct(){
        $this->productRepository = new ProductRepository();
        $this->response = new Response();
    }

    public function getProductsCategory(Request $request){
        $data = $request->getbody();
 
        $id = $data['category'];
        $products = $this->productRepository->getProductsByCategory($id);
        $this->response->jsonEncode(['products' => $products]);
        // return $this->render('customer/productsByCategory', ['products' => $products ]);
    }

}
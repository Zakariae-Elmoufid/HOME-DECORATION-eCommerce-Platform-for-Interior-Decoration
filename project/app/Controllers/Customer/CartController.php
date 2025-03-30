<?php 

namespace App\Controllers\Customer;
use App\Core\Controller;
use App\Core\Response;


class CartController extends Controller  {
    
    private $response;


    public function __cunstruct(){
      $this->response  =  new Response();
    }
   
    public function index(){
        $this->render("customer/cart");

    }
}
<?php


namespace App\Controllers\Customer;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\WishlistRepository;


Session::start();
class WishlistController  {
    
    private $response;
    private $wishlistRepository;

    public function __construct(){
        $this->response = new Response();
        $this->wishlistRepository = new wishlistRepository();
    }


    public function index(){
        $user_id = Session::get('id');
        $wishlists = $this->wishlistRepository->fetchByUser($user_id);
        $this->response->render('customer/account/wishlist',['wishlists'=> $wishlists]);
    }


    public function show($product_id){
        $user_id = Session::get('id');
        $data['product_id'] = $product_id;
        $data['user_id'] = $user_id;
        $wishlists = $this->wishlistRepository->fetchByUserAndProduct($data);
       
        return  $wishlists;
    }


    public function store(Request $request){
        $user_id = Session::get('id');
        $data =  $request->getbody();
        $id = $data['product_id'];
        $data['user_id'] = $user_id;
        $wishlist =  $this->show($id);
        
        if($wishlist->getUserId() == $data['user_id'] and $wishlist->getProductId() == $data['product_id']){
            return $this->response->jsonEncode(['errore' => "this product has a wishlist"]);
        }
        $this->wishlistRepository->create($data);
        return $this->response->jsonEncode(['success' => "wishlist createed seccussful"]);
    }

    public function delete(Request $request){
       $data  = $request->getbody();
       $id  = $data['id'];
       $wishlist =$this->wishlistRepository->deleteWishlist($id);

       if(!$wishlist){
        return $this->response->render('customer/account/wishlist' ,['errore' => "don't delete this wishlist"]);
       }
       return $this->response->redirect('/customer/wishlist');

    }
}
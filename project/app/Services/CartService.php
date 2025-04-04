<?php

namespace App\Services;
use App\Core\Session;
use App\Repositories\CartRepository;
require_once __DIR__.'/../../vendor/autoload.php';
Session::start();

class CartService {
  
public function isConnected(){

    if(Session::get('id') && !empty(Session::get('id'))){
      return Session::get('id');
    }
     return false;
}


public  function getOrCreateGuestId() {
  
  if (!Session::get('guest_identifier')) {
    Session::set('guest_identifier' ,  $this->generateUniqueIdentifier());
  }
  
  return Session::get('guest_identifier');
}

private  function generateUniqueIdentifier() {
  return bin2hex(random_bytes(16)); // 32 caractères hexadécimaux
}

public function countItem($id){
   $cartRepository = new CartRepository();
  return $cartRepository->countItem($id);
}



    public  function associateCartAfterLogin($userId) {
      
      if (Session::get('guest_identifier')) {
          $guestIdentifier = Session::get('guest_identifier');
           
          $cartRepository = new CartRepository();

          
          $guestCartId = $cartRepository->getCartIdBySessionId($guestIdentifier);
          $userCartId = $cartRepository->getCartIdByUserId($userId);
        
          if (isset($guestCartId['id']) && $userCartId['id']) {
              $cartRepository->mergeCarts($userCartId['id'], $guestCartId['id']);
          } elseif ($guestCartId['id']) {
              $cartRepository->assignCartToUser($guestCartId['id'], $userId);
          }
          
          unset($_SESSION['guest_identifier']);
          return true;
      }
  }

}
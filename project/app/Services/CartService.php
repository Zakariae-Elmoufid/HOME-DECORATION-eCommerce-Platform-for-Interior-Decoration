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
      
      // Si un identifiant de visiteur existe
      if (Session::get('guest_identifier')) {
          $guestIdentifier = Session::get('guest_identifier');
           
          $cartRepository = new CartRepository();

          
          // Récupérer les IDs des paniers
          $guestCartId = $cartRepository->getCartIdBySessionId($guestIdentifier);
          $userCartId = $cartRepository->getCartIdByUserId($userId);
          
          if ($guestCartId && $userCartId) {
              // Fusionner les paniers
              $cartRepository->mergeCarts($userCartId, $guestCartId);
          } elseif ($guestCartId) {
              // Associer le panier du visiteur à l'utilisateur
              $cartRepository->assignCartToUser($guestCartId, $userId);
          }
          
          // Supprimer l'identifiant de visiteur
          unset($_SESSION['guest_identifier']);
      }
  }

}
<?php

namespace App\Services;
use App\Core\Session;
require_once __DIR__.'/../../vendor/autoload.php';
Session::start();

class CartService {
  
public function isConnected(){
    if(!Session::get('id') && !empty(Session::get('id'))){
      return Session::get('id');
    }
     return false;
}


public  function getOrCreateGuestIdentifier() {
  
  if (!Session::get('guest_identifier')) {
    Session::set('guest_identifier' ,  $this->generateUniqueIdentifier());
  }
  
  return Session::get('guest_identifier');
}

private  function generateUniqueIdentifier() {
  return bin2hex(random_bytes(16)); // 32 caractères hexadécimaux
}

}
<?php 

namespace App\Services;

use App\Core\Validator;
use App\Core\Session;
use App\Core\Response;

class OrderServise {

  public function validetOrder($data){
       $validator  = new Validator($data);
       $validator->setRules([
        'first_name' => 'required|min:4|max:60|string',
        'last_name' => 'required|min:4|max:60|string',
        'email' => 'required|email',
        'phone' => 'required|string|min:9|max:20',
        'address' => 'required|min:5|max:100',
        'city' => 'required|min:8|max:50',
        'country' => 'required|min:2|max:50',
        'shipping_method' => 'required',
        'comments' => 'min:8|max:100',
    ]);

    $oldData = $data;

    if (!$validator->validate()) {
        $errors = $validator->getErrors();
        return  [
            'errors' => $errors,
            'old' => $oldData
        ]; 
    }    
  }

    

}
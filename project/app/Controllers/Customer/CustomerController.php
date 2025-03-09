<?php

namespace App\Controllers\Customer;
use App\Core\controller;

class CustomerController extends Controller {

  public function index(){
    $this->render('customer/index');
  }

}
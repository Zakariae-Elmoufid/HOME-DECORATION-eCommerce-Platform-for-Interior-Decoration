<?php

namespace App\Controllers\Auth;
use App\Core\Controller;
class RegisterController extends Controller  {

public function index(){
  return $this->render('auth/register');
}      

}
<?php
require_once __DIR__.'/../../vendor/autoload.php';

use App\Core\Application;
use App\Controllers\HomeController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\LoginController; 
use App\Middleware\AuthMiddleware;
use App\Controllers\Customer\CustomerController;
// namespace App\Controllers\Auth\LogoutController;




$app = new Application(dirname(__DIR__));
$app->router->get('/','HomeController@index');
$app->router->get('/register','Auth\RegisterController@index');
$app->router->get('/login','Auth\LoginController@index');
$app->router->post('/store_user',[RegisterController::class, 'store']);
$app->router->post('/find_user',[LoginController::class, 'login']);
$app->router->post('/login_google',[LoginController::class, 'loginGoogle']);
$app->router->post('/register_google', [RegisterController::class, 'registerGoogle']);
$app->router->get('/customer',[CustomerController::class,'index'])
->middleware('customer', AuthMiddleware::class);

$app->router->get('/logout', 'Auth\LogoutController@logout' );

// $app->get('/dashboard', [DashboardController::class, 'index']
// $app->get('/login', [AuthController::class, 'login']);
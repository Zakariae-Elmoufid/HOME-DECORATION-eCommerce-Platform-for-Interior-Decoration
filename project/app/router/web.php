<?php
require_once __DIR__.'/../../vendor/autoload.php';

use App\Core\Application;
use App\Controllers\HomeController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\LoginController; 
use App\Middlewares\AuthMiddleware;
use App\Controllers\Customer\CustomerController;
use App\Controllers\Auth\LogoutController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\CategoryController;




$app = new Application(dirname(__DIR__));
$app->router->get('/','HomeController@index');
$app->router->get('/register','Auth\RegisterController@index');
$app->router->get('/login','Auth\LoginController@index');
$app->router->post('/store_user',[RegisterController::class, 'store']);
$app->router->post('/find_user',[LoginController::class, 'login']);
$app->router->post('/login_google',[LoginController::class, 'loginGoogle']);
$app->router->post('/register_google', [RegisterController::class, 'registerGoogle']);
$app->router->get('/customer',[CustomerController::class,'index'])
->middleware('/customer', AuthMiddleware::class);
$app->router->get('/logout', 'Auth\LogoutController@logout' );

$app->router->get('/admin' ,'Admin\DashboardController@index');
$app->router->get('/categorys' ,'Admin\CategoryController@index');
$app->router->get('/allCategorys' ,'Admin\CategoryController@fech');

$app->router->post('/categorys/store', [CategoryController::class ,'store']);
$app->router->get('/categorys/show', [CategoryController::class ,'show']);
$app->router->patch('/categorys/update',[CategoryController::class ,'update']);

// $app->router->post('/categorys/store', [CategoryController::class ,'store']);



// $app->get('/dashboard', [DashboardController::class, 'index']
// $app->get('/login', [AuthController::class, 'login']);
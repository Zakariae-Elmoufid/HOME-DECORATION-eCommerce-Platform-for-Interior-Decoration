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
use App\Controllers\Admin\ProductController;




$app = new Application(dirname(__DIR__));
$app->router->get('/','HomeController@index');
$app->router->get('/register','Auth\RegisterController@index');
$app->router->get('/login','Auth\LoginController@index');
$app->router->post('/store_user',[RegisterController::class, 'store']);
$app->router->post('/find_user',[LoginController::class, 'login']);
$app->router->post('/login_google',[LoginController::class, 'loginGoogle']);
$app->router->post('/register_google', [RegisterController::class, 'registerGoogle']);
$app->router->get('/customer',[CustomerController::class,'index']);
// ->middleware('/customer', AuthMiddleware::class);
$app->router->get('/logout', 'Auth\LogoutController@logout' );

$app->router->get('/admin' ,'Admin\DashboardController@index');
$app->router->get('/categorys' ,'Admin\CategoryController@index');
$app->router->get('/allCategorys' ,'Admin\CategoryController@fech');

$app->router->post('/categorys/store', [CategoryController::class ,'store']);
$app->router->get('/categorys/show', [CategoryController::class ,'show']);
$app->router->patch('/categorys/update',[CategoryController::class ,'update']);
$app->router->delete('/categorys/delete',[CategoryController::class ,'delete']);

$app->router->get('/admin/products' ,'Admin\ProductController@index');
$app->router->get('/products/create' , 'Admin\ProductController@create');
$app->router->post('/products/store' ,[ProductController::class , 'store']);
$app->router->get('/products/edit', [ProductController::class ,'getProduct']);
$app->router->get('/product', [ProductController::class ,'show']);
$app->router->post('/products/update', [ProductController::class ,'update']);
$app->router->delete('/products/delete' ,[ProductController::class , 'delete']);


$app->router->get('/products' , 'HomeController@product');
$app->router->post('/products/search' , [HomeController::class , 'search']);
$app->router->get('/cart','Customer\CartController@index');


// $app->get('/dashboard', [DashboardController::class, 'index']
// $app->get('/login', [AuthController::class, 'login']);
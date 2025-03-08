<?php
require_once __DIR__.'/../../vendor/autoload.php';

use App\Core\Application;
use App\Controllers\HomeController;
use App\Controllers\Auth\RegisterController;
use App\Controllers\Auth\LoginController; 

$app = new Application(dirname(__DIR__));

$app->router->get('/','HomeController@index');
$app->router->get('/register','Auth\RegisterController@index');
$app->router->get('/login','Auth\LoginController@index');
$app->router->post('/store_user',[Auth\RegisterController::class, 'store']);
$app->router->post('/find_user',[Auth\LoginController::class, 'login']);
$app->router->post('/login_google',[Auth\LoginController::class, 'loginGoogle']);
// $app->get('/dashboard', [DashboardController::class, 'index'])->middleware('/dashboard', AuthMiddleware::class);
// $app->get('/login', [AuthController::class, 'login']);
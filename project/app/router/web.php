<?php
require_once __DIR__.'/../../vendor/autoload.php';

use App\Core\Application;
use App\Controllers\HomeController;
use App\Controllers\Auth\RegisterController;

$app = new Application(dirname(__DIR__));

$app->router->get('/','HomeController@index');
$app->router->get('/register','Auth\RegisterController@index');
$app->router->get('/login','Auth\LoginController@index');
$app->router->post('/store_user',[Auth\RegisterController::class, 'store']);
// $app->get('/dashboard', [DashboardController::class, 'index'])->middleware('/dashboard', AuthMiddleware::class);
// $app->get('/login', [AuthController::class, 'login']);
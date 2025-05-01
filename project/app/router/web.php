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
use App\Controllers\Admin\OrderController as  AdminOrderController;
use App\Controllers\Admin\AccessController ;
use App\Controllers\Customer\CartController;
use App\Controllers\Customer\AccountController;
use App\Controllers\Customer\OrderController;
use App\Controllers\Customer\PaymentController;
use App\Controllers\Customer\StripePaymentController;
use App\Controllers\Customer\ReviewController;
use App\Controllers\Customer\WishlistController;
use App\Controllers\Customer\ProductController  as CustomerProductController;

use App\Middlewares\PermissionMiddleware;



$app = new Application(dirname(__DIR__));
$app->router->get('/','HomeController@index');
$app->router->get('/about_us','HomeController@about');
$app->router->get('/contact_us','HomeController@contact');

$app->router->get('/register','Auth\RegisterController@index');
$app->router->get('/login','Auth\LoginController@index');
$app->router->post('/store_user',[RegisterController::class, 'store']);
$app->router->post('/find_user',[LoginController::class, 'login']);
$app->router->post('/login_google',[LoginController::class, 'loginGoogle']);
$app->router->post('/register_google', [RegisterController::class, 'registerGoogle']);
$app->router->get('/customer',[CustomerController::class,'index']);
// ->middleware('/customer', AuthMiddleware::class);
$app->router->get('/logout', 'Auth\LogoutController@logout' );

$app->router->get('/error/permission', 'errorController@errorPermission' );



$app->router->get('/admin' ,'Admin\DashboardController@index');

$app->router->get('/admin/dashboard/sales-data',[DashboardController::class ,'getSalesData']);
$app->router->get('/admin/dashboard/popular-products',[DashboardController::class ,'getPopularProducts']);


$app->router->get('/admin/categorys' ,'Admin\CategoryController@index');
$app->router->middleware('/admin/categorys', new PermissionMiddleware('Manage Categories'));

$app->router->get('/admin/allCategorys' ,'Admin\CategoryController@fech');
$app->router->middleware('/admin/allCategorys', new PermissionMiddleware('Manage Categories'));

$app->router->post('/admin/categorys/store', [CategoryController::class ,'store']);
$app->router->middleware('/admin/categorys/store', new PermissionMiddleware('Manage Categories'));

$app->router->get('/admin/categorys/show', [CategoryController::class ,'show']);
$app->router->middleware('/admin/categorys/show', new PermissionMiddleware('Manage Categories'));


$app->router->patch('/admin/categorys/update',[CategoryController::class ,'update']);
$app->router->middleware('/admin/categorys/update', new PermissionMiddleware('Manage Categories'));

$app->router->delete('/admin/categorys/delete',[CategoryController::class ,'delete']);
$app->router->middleware('/admin/categorys/delete', new PermissionMiddleware('Manage Categories'));


$app->router->get('/admin/products' ,'Admin\ProductController@index');
$app->router->middleware('/admin/products', new PermissionMiddleware('Manage Products'));

$app->router->get('/admin/products/create' , 'Admin\ProductController@create');
$app->router->middleware('/admin/products/create', new PermissionMiddleware('Manage Products'));

$app->router->post('/admin/products/store' ,[ProductController::class , 'store']);
$app->router->middleware('/admin/products/store', new PermissionMiddleware('Manage Products'));

$app->router->get('/admin/products/edit', [ProductController::class ,'getProduct']);
$app->router->middleware('/admin/products/edit', new PermissionMiddleware('Manage Products'));

$app->router->get('/admin/products/images', [ProductController::class ,'editImages']);
$app->router->middleware('/admin/products/images', new PermissionMiddleware('Manage Products'));

$app->router->post('/admin/products/upload-images' ,[ProductController::class ,'uploadImages']);
$app->router->middleware('/admin/products/upload-images', new PermissionMiddleware('Manage Products'));


$app->router->post('/admin/products/update', [ProductController::class ,'update']);
$app->router->middleware('/admin/products/update', new PermissionMiddleware('Manage Products'));

$app->router->get('/admin/products/set-primary-image' ,[ProductController::class , 'setPrimaryImage']);
$app->router->get('/admin/products/delete-image' ,[ProductController::class , 'deleteImage']);

$app->router->delete('/admin/products/delete' ,[ProductController::class , 'delete']);
$app->router->middleware('/admin/products/delete', new PermissionMiddleware('Manage Products'));



$app->router->get('/product', [ProductController::class ,'show']);

$app->router->get('/admin/orders','Admin\OrderController@index');
$app->router->get('/admin/orders/details' , [AdminOrderController::class ,'orderDetails']);

$app->router->get('/products/bycategory' ,[HomeController::class , 'getProductsByCategory']);

$app->router->get('/admin/customer','Admin\CustomerController@index');


$app->router->get('/admin/access', 'Admin\AccessController@index')->middleware('/admin/access', new  PermissionMiddleware('Manage Admins'));


$app->router->get('/admin/access/create', 'Admin\AccessController@create');
$app->router->middleware('/admin/access/create', new PermissionMiddleware('Manage Admins'));

$app->router->post('/admin/access/add', [AccessController::class, 'addAdmin']);
$app->router->middleware('/admin/access/add', new PermissionMiddleware('Manage Admins'));

$app->router->get('/admin/access/edit', [AccessController::class, 'edit']);
$app->router->middleware('/admin/access/edit', new PermissionMiddleware('Manage Admins'));

$app->router->post('/admin/access/update', [AccessController::class, 'update']);
$app->router->middleware('/admin/access/update', new PermissionMiddleware('Manage Admins'));

$app->router->get('/admin/access/delete', [AccessController::class, 'delete']);
$app->router->middleware('/admin/access/delete', new PermissionMiddleware('Manage Admins'));


$app->router->get('/admin/status', [AccessController::class, 'updateStatusAdmin']);
$app->router->middleware('/admin/status', new PermissionMiddleware('Manage Admins'));

$app->router->get('/products' , 'HomeController@product');
$app->router->post('/products/search' , [HomeController::class ,'search']);
$app->router->get('/products/fetch' ,[HomeController::class ,'productsPaginator']);
$app->router->get('/products/category', [CustomerProductController::class ,'getProductsCategory' ]);

$app->router->get('/cart','Customer\CartController@index');
$app->router->post('/cart/add', [CartController::class , 'addToCart']);
$app->router->patch('/cart/update', [CartController::class , 'update']);
$app->router->delete('/cart/delete', [CartController::class , 'delete']);

$app->router->get('/cart/count', [CartController::class , 'countItem']);


$app->router->get('/order',"Customer\OrderController@index");
$app->router->get('/order/show',"Customer\OrderController@show");
$app->router->post('/order/add',[OrderController::class , 'store']);

$app->router->post('/payment/checkout',[StripePaymentController::class,'checkout']);
$app->router->get('/payment/success',[StripePaymentController::class,'success']);
$app->router->get('/payment/cancel',[StripePaymentController::class,'cancel']);
$app->router->post('/stripe/webhook', [StripePaymentController::class, 'webhook']);

$app->router->post('/payment/create-intent',[PaymentController::class , 'createIntent']);
$app->router->post('/payment/update-status',[PaymentController::class , 'updateStatus']);
$app->router->get('/payment/confirmation',[StripePaymentController::class , 'confirmation']);


$app->router->get('/customer/account', 'Customer\AccountController@index');
$app->router->get('/customer/account/details', 'Customer\AccountController@account');
$app->router->get('/customer/account/order', 'Customer\AccountController@order');
$app->router->patch('/account/update', [AccountController::class , 'update']);
$app->router->patch('/account/update-address', [AccountController::class , 'updateAddress']);
$app->router->post('/account/add-address', [AccountController::class , 'addAddress']);

$app->router->get('/customer/review',[ReviewController::class , 'create' ]);
$app->router->post('/customer/review/store',[ReviewController::class ,'store' ]);
$app->router->patch('/customer/review/update',[ReviewController::class ,'update']);
$app->router->get('/customer/review/delete',[ReviewController::class ,'delete']);
$app->router->get('/customer/myReview','Customer\ReviewController@reviewByUserId');

$app->router->get('/customer/wishlist', 'Customer\WishlistController@index');
$app->router->post('/wishlist/add',[WishlistController::class , 'store' ]);
$app->router->get('/wishlist/delete',[WishlistController::class , 'delete' ]);

// $app->get('/dashboard', [DashboardController::class, 'index']
// $app->get('/login', [AuthController::class, 'login']);
<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PublicController;

Route::controller(PublicController::class)->group(function () {
    Route::get('list-products', 'ProductList');
    Route::get('product-info/{id}', 'ProductInfo');
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::get('me', 'me');

});

Route::controller(ProductController::class)->group(function(){
    Route::post('post-product', 'ProductPost');
    Route::get('delet-product/{id}', 'DeletProduct');
    Route::post('update-product', 'UpdateProduct');
    Route::post('upload-product-photo', 'UploadProductPhoto');
});

Route::controller(ClientController::class)->group(function(){
    Route::post('add-product-to-cart/{id}', 'BagAddProduct');
    Route::get('show-cart', 'ShowBag');
    Route::post('calc-logistic', 'LogisticCalc');
    Route::post('buy-product', 'BuyProduct');
    Route::post('change-quantity', 'ChangeQuantity');
    Route::post('clear-cart', 'ClearBag');
    Route::get('remove-product-from-cart/{id}', 'RemoveProduct');
    Route::post('send-adress', 'SendAdress');
    
    // Route::get('list-bag-produts/{id}', 'ListBagProduts');
});

Route::controller(AdminController::class)->group(function(){
    Route::post('dash-board-list', 'DashBoardList');
    Route::post('orders-status', 'ProductsStatus');
    Route::post('orders-to-transport', 'TransportProducts');
    Route::post('orders-delivered', 'DeliveredProducts');
    Route::post('export-order', 'exportOrders');
});


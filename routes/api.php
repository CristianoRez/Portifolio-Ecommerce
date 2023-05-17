<?php

use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PublicController;

Route::controller(PublicController::class)->group(function () {
    Route::get('list-products', 'ProductList');
    Route::get('product-info/{id}', 'ProductInfo');
    Route::post('search-product-by-description', 'SearchProduct');
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
    Route::post('delete-product-photo', 'DeleteProductPhoto');
    Route::post('update-product-photo', 'UpdateProductPhoto');
});

Route::controller(ClientController::class)->group(function(){
    Route::get('show-products', 'ShowProducts');
    Route::post('add-product-to-cart/{id}', 'CartAddProduct');
    Route::get('show-cart', 'ShowCart');
    Route::post('calc-logistic', 'LogisticCalc');
    Route::post('buy-product', 'BuyProduct');
    Route::post('change-quantity', 'ChangeQuantity');
    Route::post('clear-cart', 'ClearCart');
    Route::get('remove-product-from-cart/{id}', 'RemoveProduct');
    Route::post('send-adress', 'SendAdress');
    Route::post('user-open-chat', 'UserOpenChat');
    Route::post('user-send-message', 'UserSendMessage');
    Route::get('show-user-chats', 'ShowUserChats');
    
    // Route::get('list-Cart-produts/{id}', 'ListCartProduts');
});

Route::controller(AdminController::class)->group(function(){
    Route::post('dash-board-list', 'DashBoardList');
    Route::post('orders-status', 'ProductsStatus');
    Route::post('orders-to-transport', 'TransportProducts');
    Route::post('orders-delivered', 'DeliveredProducts');
    Route::post('export-order', 'exportOrders');
    Route::get('see-questions', 'SeeQuestions');
    Route::post('answer-question', 'AnswerQuestion');
    Route::post('see-user-question', 'SeeUserQuestion');
    Route::post('admin-open-chat', 'AdminOpenChat');
    Route::post('admin-send-message', 'AdminSendMessage');
    Route::get('show-admin-chats', 'ShowAdminChats');
});

Route::controller(PDFController::class)->group(function(){
    Route::post('generate-pdf', 'GeneratePDF');
});


<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);



Route::middleware('auth:api')->group( function () {

    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('users', UserController::class);
    Route::patch('users/{id}/restore', [UserController::class, 'restore']);
    Route::put('users/{id}/restore', [UserController::class, 'restore']);
    Route::delete('users/{id}/permanent-delete', [UserController::class, 'permanentDelete']);

    Route::patch('categories/{id}/restore', [CategoryController::class, 'restore']);
    Route::delete('categories/{id}/permanent-delete', [CategoryController::class, 'permanentDelete']);
    Route::apiResource('categories', CategoryController::class);

    Route::patch('products/{id}/restore', [ProductController::class, 'restore']);
    Route::delete('products/{id}/permanent-delete', [ProductController::class, 'permanentDelete']);
    Route::apiResource('products', ProductController::class);

    Route::patch('customers/{id}/restore', [CustomerController::class, 'restore']);
    Route::delete('customers/{id}/permanent-delete', [CustomerController::class, 'permanentDelete']);
    Route::apiResource('customers', CustomerController::class);

    // Orders
    Route::post('orders/{id}/add-item', [OrderController::class, 'addItem']);
    Route::post('orders/{id}/add-multiple-items', [OrderController::class, 'addMultipleItems']);
    Route::delete('orders/{id}/remove-items', [OrderController::class, 'removeItems']);
    Route::patch('orders/{id}/restore', [OrderController::class, 'restore']);
    Route::delete('orders/{id}/permanent-delete', [OrderController::class, 'permanentDelete']);
    Route::apiResource('orders', OrderController::class);

    // OrderItems
    Route::post('order-items', [OrderItemController::class, 'store']);
    Route::put('order-items/{id}', [OrderItemController::class, 'update']);
    Route::patch('order-items/{id}', [OrderItemController::class, 'update']);
    Route::delete('order-items/{id}', [OrderItemController::class, 'destroy']);
    Route::patch('order-items/{id}/restore', [OrderItemController::class, 'restore']);
    Route::delete('order-items/{id}/permanent-delete', [OrderItemController::class, 'permanentDelete']);
});

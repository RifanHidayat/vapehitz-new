<?php

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductSubcategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CentralPurchaseController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('dashboard.index');
});

//RouteProduct
Route::prefix('/product')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::get('/create', [ProductController::class, 'create']);
    Route::post('/', [ProductController::class, 'store']);
    Route::get('/edit/{id}', [ProductController::class, 'edit']);
    Route::patch('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::get('/show/{id}', [ProductController::class, 'show']);
});

//RouteProductCategory
Route::prefix('/product-category')->group(function () {
    Route::get('/', [ProductCategoryController::class, 'index']);
    Route::get('/create', [ProductCategoryController::class, 'create']);
    Route::get('/edit/{id}', [ProductCategoryController::class, 'edit']);
    Route::post('/', [ProductCategoryController::class, 'store']);
    Route::patch('/{id}', [ProductCategoryController::class, 'update']);
    Route::delete('/{id}', [ProductCategoryController::class, 'destroy']);
});

//RouteProductSubcategory
Route::prefix('/product-subcategory')->group(function () {
    Route::get('/', [ProductSubcategoryController::class, 'index']);
    Route::get('/create', [ProductSubcategoryController::class, 'create']);
    Route::get('/edit/{id}', [ProductSubcategoryController::class, 'edit']);
    Route::post('/', [ProductSubcategoryController::class, 'store']);
    Route::patch('/{id}', [ProductSubcategoryController::class, 'update']);
    Route::delete('/{id}', [ProductSubcategoryController::class, 'destroy']);
});

//RouteCustomers
Route::prefix('/customer')->group(function () {
    Route::get('/', [CustomerController::class, 'index']);
    Route::get('/create', [CustomerController::class, 'create']);
    Route::post('/', [CustomerController::class, 'store']);
    Route::get('/edit/{id}', [CustomerController::class, 'edit']);
    Route::patch('/{id}', [CustomerController::class, 'update']);
    Route::delete('/{id}', [CustomerController::class, 'destroy']);
});

//RouteSupplier
Route::prefix('/supplier')->group(function () {
    Route::get('/', [SupplierController::class, 'index']);
    Route::get('/create', [SupplierController::class, 'create']);
    Route::post('/', [SupplierController::class, 'store']);
    Route::get('/edit/{id}', [SupplierController::class, 'edit']);
    Route::patch('/{id}', [SupplierController::class, 'update']);
    Route::delete('/{id}', [SupplierController::class, 'destroy']);
});

//RouteCentralPurchase
Route::prefix('/central-purchase')->group(function () {
    Route::get('/', [CentralPurchaseController::class, 'index']);
    Route::get('/create', [CentralPurchaseController::class, 'create']);
    Route::post('/', [CentralPurchaseController::class, 'store']);
    Route::get('/edit/{id}', [CentralPurchaseController::class, 'edit']);
    Route::patch('/{id}', [CentralPurchaseController::class, 'update']);
    Route::delete('/{id}', [CentralPurchaseController::class, 'destroy']);
});

// Datatables
Route::prefix('/datatables')->group(function () {
    Route::prefix('/central-purchases')->group(function () {
        Route::get('/products', [CentralPurchaseController::class, 'datatableProducts']);
    });
});

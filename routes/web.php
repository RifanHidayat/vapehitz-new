<?php

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductSubcategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CentralPurchaseController;
use App\Http\Controllers\CentralSaleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountTransactionController;
use App\Http\Controllers\PurchaseTransactionController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\BadstockReleaseController;
use App\Http\Controllers\ReqToRetailController;
use App\Http\Controllers\ReturSupplierController;
use App\Http\Controllers\SaleRetailController;
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

Route::prefix('/dashboard')->group(function () {
    Route::get('/', [HomeController::class, 'index']);
});

Route::get('/', function () {
    return view('dashboard.index');
});

Route::get('/', [AuthController::class, 'showFormLogin'])->name('login');
Route::get('login', [AuthController::class, 'showFormLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showFormRegister'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth'], function () {

    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
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
    Route::get('/edit/{id}', [CentralPurchaseController::class, 'edit']);
    Route::get('/pay/{id}', [CentralPurchaseController::class, 'pay']);
    Route::get('/return/{id}', [CentralPurchaseController::class, 'return']);
    Route::post('/', [CentralPurchaseController::class, 'store']);
    Route::patch('/{id}', [CentralPurchaseController::class, 'update']);
    Route::delete('/{id}', [CentralPurchaseController::class, 'destroy']);
    Route::get('/show/{id}', [CentralPurchaseController::class, 'show']);
});

//RouteCentralSale
Route::prefix('/central-sale')->group(function () {
    Route::get('/', [CentralSaleController::class, 'index']);
    Route::get('/create', [CentralSaleController::class, 'create']);
    Route::post('/', [CentralSaleController::class, 'store']);
    Route::get('/edit/{id}', [CentralSaleController::class, 'edit']);
    Route::get('/approve/{id}', [CentralSaleController::class, 'approve']);
    Route::patch('/approve/{id}', [CentralSaleController::class, 'approved']);
    Route::patch('/{id}', [CentralSaleController::class, 'update']);
    Route::delete('/{id}', [CentralSaleController::class, 'destroy']);
    Route::get('/show/{id}', [CentralSaleController::class, 'show']);
});

//RoutePurchaseTransaction
Route::prefix('/purchase-transaction')->group(function () {
    Route::get('/', [PurchaseTransactionController::class, 'index']);
    Route::get('/create', [PurchaseTransactionController::class, 'create']);
    Route::post('/', [PurchaseTransactionController::class, 'store']);
    Route::get('/edit/{id}', [PurchaseTransactionController::class, 'edit']);
    Route::patch('/{id}', [PurchaseTransactionController::class, 'update']);
    Route::delete('/{id}', [PurchaseTransactionController::class, 'destroy']);
});

//RouteAccount
Route::prefix('/account')->group(function () {
    Route::get('/', [AccountController::class, 'index']);
    Route::get('/create', [AccountController::class, 'create']);
    Route::post('/', [AccountController::class, 'store']);
    Route::get('/edit/{id}', [AccountController::class, 'edit']);
    Route::patch('/{id}', [AccountController::class, 'update']);
    Route::delete('/{id}', [AccountController::class, 'destroy']);
});

//RouteAccountTransaction
Route::prefix('/account-transaction')->group(function () {
    Route::get('/', [AccountTransactionController::class, 'index']);
    Route::get('/create', [AccountTransactionController::class, 'create']);
    Route::post('/', [AccountTransactionController::class, 'store']);
    Route::get('/edit/{id}', [AccountTransactionController::class, 'edit']);
    Route::patch('/{id}', [AccountTransactionController::class, 'update']);
    Route::delete('/{id}', [AccountTransactionController::class, 'destroy']);
});

//RouteStockOpname
Route::prefix('/stock-opname')->group(function () {
    Route::get('/', [StockOpnameController::class, 'index']);
    Route::get('/create', [StockOpnameController::class, 'create']);
    Route::post('/', [StockOpnameController::class, 'store']);
    Route::get('/edit/{id}', [StockOpnameController::class, 'edit']);
    Route::patch('/{id}', [StockOpnameController::class, 'update']);
    Route::delete('/{id}', [StockOpnameController::class, 'destroy']);
});

//RouteBadstockRelease
Route::prefix('/badstock-release')->group(function () {
    Route::get('/', [BadstockReleaseController::class, 'index']);
    Route::get('/create', [BadstockReleaseController::class, 'create']);
    Route::post('/', [BadstockReleaseController::class, 'store']);
    Route::get('/edit/{id}', [BadstockReleaseController::class, 'edit']);
    Route::patch('/{id}', [BadstockReleaseController::class, 'update']);
    Route::delete('/{id}', [BadstockReleaseController::class, 'destroy']);
});

//RouteShipment
Route::prefix('/shipment')->group(function () {
    Route::get('/', [ShipmentController::class, 'index']);
    Route::get('/create', [ShipmentController::class, 'create']);
    Route::post('/', [ShipmentController::class, 'store']);
    Route::get('/edit/{id}', [ShipmentController::class, 'edit']);
    Route::patch('/{id}', [ShipmentController::class, 'update']);
    Route::delete('/{id}', [ShipmentController::class, 'destroy']);
});

//RouteReturSupplier
Route::prefix('/retur-supplier')->group(function () {
    Route::get('/', [ReturSupplierController::class, 'index']);
    Route::get('/create', [ReturSupplierController::class, 'create']);
    Route::post('/', [ReturSupplierController::class, 'store']);
    Route::get('/edit/{id}', [ReturSupplierController::class, 'edit']);
    Route::patch('/{id}', [ReturSupplierController::class, 'update']);
    Route::delete('/{id}', [ReturSupplierController::class, 'destroy']);
});

//RouteReqToRetail
Route::prefix('/reqtoretail')->group(function () {
    Route::get('/', [ReqToRetailController::class, 'index']);
    Route::get('/create', [ReqToRetailController::class, 'create']);
    Route::post('/', [ReqToRetailController::class, 'store']);
    Route::get('/edit/{id}', [ReqToRetailController::class, 'edit']);
    Route::patch('/{id}', [ReqToRetailController::class, 'update']);
    Route::delete('/{id}', [ReqToRetailController::class, 'destroy']);
});

//RouteSaleRetail
Route::prefix('/saleretail')->group(function () {
    Route::get('/', [SaleRetailController::class, 'index']);
    Route::get('/create', [SaleRetailController::class, 'create']);
    Route::post('/', [SaleRetailController::class, 'store']);
    Route::get('/edit/{id}', [SaleRetailController::class, 'edit']);
    Route::patch('/{id}', [SaleRetailController::class, 'update']);
    Route::delete('/{id}', [SaleRetailController::class, 'destroy']);
});

// Datatables
Route::prefix('/datatables')->group(function () {
    Route::prefix('/central-purchases')->group(function () {
        Route::get('/', [CentralPurchaseController::class, 'datatableCentralPurchase']);
        Route::get('/products', [CentralPurchaseController::class, 'datatableProducts']);
    });
    Route::prefix('/suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'datatableSuppliers']);
    });
    Route::prefix('/customers')->group(function () {
        Route::get('/', [CustomerController::class, 'datatableCustomers']);
    });
    Route::prefix('/products')->group(function () {
        Route::get('/', [ProductController::class, 'datatableProducts']);
    });
    Route::prefix('/stock-opname')->group(function () {
        Route::get('/', [StockOpnameController::class, 'datatableStockOpname']);
        Route::get('/products', [StockOpnameController::class, 'datatableProducts']);
    });
    Route::prefix('/central-sale')->group(function () {
        Route::get('/', [CentralSaleController::class, 'datatableCentralSale']);
    });
    Route::prefix('/badstock-release')->group(function () {
        Route::get('/', [BadstockReleaseController::class, 'datatableBadstockRelease']);
    });
    Route::prefix('/reqtoretail')->group(function () {
        Route::get('/', [ReqToRetailController::class, 'datatableReqtoretail']);
    });
});

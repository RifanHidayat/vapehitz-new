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
use App\Http\Controllers\ApproveCentralController;
use App\Http\Controllers\ApproveCentralStudioController;
use App\Http\Controllers\ApproveRetailController;
use App\Http\Controllers\ApproveStudioController;
use App\Http\Controllers\PurchaseTransactionController;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\BadstockReleaseController;
use App\Http\Controllers\PurchaseReturController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\PurchaseReturnTransactionController;
use App\Http\Controllers\CentralSaleReturnController;
use App\Http\Controllers\CentralSaleReturnTransactionController;
use App\Http\Controllers\CentralSaleTransactionController;
use App\Http\Controllers\ChangelogController;
use App\Http\Controllers\ReqToRetailController;
use App\Http\Controllers\RetailSaleController;
use App\Http\Controllers\ReturSupplierController;
use App\Http\Controllers\SaleRetailController;
use App\Http\Controllers\StockOpnameRetailController;
use App\Http\Controllers\StockOpnameStudioController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\PurchaseReceiptController;
use App\Http\Controllers\RetailRequestToCentralController;
use App\Http\Controllers\RequestToStudioController;
use App\Http\Controllers\RequestToRetailController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RetailSaleReturnController;
use App\Http\Controllers\RetailSaleTransactionController;
use App\Http\Controllers\StudioRequestToCentralController;
use App\Http\Controllers\StudioSaleController;
use App\Http\Controllers\StudioSaleReturnController;
use App\Http\Controllers\StudioSaleTransactionController;
use App\Http\Controllers\UserController;
use App\Models\Account;
use App\Models\PurchaseTransaction;
use App\Models\RequestToStudio;
use App\Models\RetailSaleTransaction;
use GuzzleHttp\Psr7\Request;
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

//RouteAuth
Route::get('/', [AuthController::class, 'showFormLogin'])->name('login');
Route::get('login', [AuthController::class, 'showFormLogin'])->name('login');
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => 'auth'], function () {
    Route::get('register', [AuthController::class, 'showFormRegister'])->name('register');
    Route::post('register', [AuthController::class, 'register']);

    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
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
    // Route::get('/', [AuthController::class, 'showFormLogin'])->name('login');
    // Route::get('login', [AuthController::class, 'showFormLogin'])->name('login');
    // Route::post('login', [AuthController::class, 'login']);
    // Route::get('register', [AuthController::class, 'showFormRegister'])->name('register');
    // Route::post('register', [AuthController::class, 'register']);

    // Route::group(['middleware' => 'auth'], function () {

    //     Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('logout', [AuthController::class, 'logout'])->name('logout');
    // });

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

    //RouteCustomers
    Route::prefix('/customer')->group(function () {
        Route::get('/', [CustomerController::class, 'index']);
        Route::get('/create', [CustomerController::class, 'create']);
        Route::post('/', [CustomerController::class, 'store']);
        Route::get('/edit/{id}', [CustomerController::class, 'edit']);
        Route::get('/pay/{id}', [CustomerController::class, 'pay']);
        Route::patch('/{id}', [CustomerController::class, 'update']);
        Route::delete('/{id}', [CustomerController::class, 'destroy']);
    });

    //RouteCentralPurchase
    Route::prefix('/central-purchase')->group(function () {
        Route::get('/', [CentralPurchaseController::class, 'index']);
        Route::get('/create', [CentralPurchaseController::class, 'create']);
        Route::get('/edit/{id}', [CentralPurchaseController::class, 'edit']);
        Route::get('/pay/{id}', [CentralPurchaseController::class, 'pay']);
        Route::get('/return/{id}', [CentralPurchaseController::class, 'return']);
        Route::get('/report-by-supplier/sheet', [CentralPurchaseController::class, 'reportBySupplier']);
        Route::get('/report-by-product/sheet', [CentralPurchaseController::class, 'reportByProduct']);
        // Route::get('/report/', [CentralPurchaseController::class, 'return']);
        Route::post('/', [CentralPurchaseController::class, 'store']);
        Route::patch('/{id}', [CentralPurchaseController::class, 'update']);
        Route::delete('/{id}', [CentralPurchaseController::class, 'destroy']);
        Route::get('/show/{id}', [CentralPurchaseController::class, 'show']);
        Route::get('/receipt/{id}', [CentralPurchaseController::class, 'receipt']);
    });



    //RouteShipment
    Route::prefix('/shipment')->group(function () {
        Route::get('/', [ShipmentController::class, 'index']);
        Route::get('/create', [ShipmentController::class, 'create']);
        Route::get('/edit/{id}', [ShipmentController::class, 'edit']);
        Route::post('/', [ShipmentController::class, 'store']);
        Route::patch('/{id}', [ShipmentController::class, 'update']);
        Route::delete('/{id}', [ShipmentController::class, 'destroy']);
    });

    //RouteSupplier
    Route::prefix('/supplier')->group(function () {
        Route::get('/', [SupplierController::class, 'index']);
        Route::get('/create', [SupplierController::class, 'create']);
        Route::post('/', [SupplierController::class, 'store']);
        Route::get('/edit/{id}', [SupplierController::class, 'edit']);
        Route::patch('/{id}', [SupplierController::class, 'update']);
        Route::delete('/{id}', [SupplierController::class, 'destroy']);
        Route::get('/pay/{id}', [SupplierController::class, 'pay']);
        Route::post('/purchase-transactions', [SupplierController::class, 'payment']);
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

    //Route Central Sale Transaction
    Route::prefix('/central-sale-transaction')->group(function () {
        Route::get('/', [CentralSaleTransactionController::class, 'index']);
        Route::get('/create', [CentralSaleTransactionController::class, 'create']);
        Route::get('/edit/{id}', [CentralSaleTransactionController::class, 'edit']);
        Route::get('/show/{id}', [CentralSaleTransactionController::class, 'show']);
        Route::post('/', [CentralSaleTransactionController::class, 'store']);
        Route::post('/action/bulk-store', [CentralSaleTransactionController::class, 'bulkStore']);
        Route::patch('/{id}', [CentralSaleTransactionController::class, 'update']);
        Route::delete('/{id}', [CentralSaleTransactionController::class, 'destroy']);
    });

    //Route Central Sale Return
    Route::prefix('/central-sale-return')->group(function () {
        Route::get('/', [CentralSaleReturnController::class, 'index']);
        Route::get('/create', [CentralSaleReturnController::class, 'create']);
        Route::post('/', [CentralSaleReturnController::class, 'store']);
        Route::get('/edit/{id}', [CentralSaleReturnController::class, 'edit']);
        Route::get('/pay/{id}', [CentralSaleReturnController::class, 'pay']);
        Route::get('/show/{id}', [CentralSaleReturnController::class, 'show']);
        Route::patch('/{id}', [CentralSaleReturnController::class, 'update']);
        Route::delete('/{id}', [CentralSaleReturnController::class, 'destroy']);
    });

    Route::prefix('/central-sale-return-transaction')->group(function () {
        Route::get('/', [CentralSaleReturnTransactionController::class, 'index']);
        Route::get('/create', [CentralSaleReturnTransactionController::class, 'create']);
        Route::post('/', [CentralSaleReturnTransactionController::class, 'store']);
        Route::get('/edit/{id}', [CentralSaleReturnTransactionController::class, 'edit']);
        Route::get('/show/{id}', [CentralSaleReturnTransactionController::class, 'show']);
        Route::patch('/{id}', [CentralSaleReturnTransactionController::class, 'update']);
        Route::delete('/{id}', [CentralSaleReturnTransactionController::class, 'destroy']);
    });

    //Route Retail Sale
    Route::prefix('/retail-sale')->group(function () {
        Route::get('/', [RetailSaleController::class, 'index']);
        Route::get('/create', [RetailSaleController::class, 'create']);
        Route::get('/show/{id}', [RetailSaleController::class, 'show']);
        Route::get('/edit/{id}', [RetailSaleController::class, 'edit']);
        Route::get('/return/{id}', [RetailSaleController::class, 'return']);
        Route::get('/print/{id}', [RetailSaleController::class, 'print']);
        Route::get('/report/sheet', [RetailSaleController::class, 'report']);
        Route::post('/', [RetailSaleController::class, 'store']);
        Route::patch('/{id}', [RetailSaleController::class, 'update']);
        Route::delete('/{id}', [RetailSaleController::class, 'destroy']);
    });

    //Route Retail Sale
    Route::prefix('/retail-sale-transaction')->group(function () {
        Route::get('/', [RetailSaleTransactionController::class, 'index']);
        Route::get('/create', [RetailSaleTransactionController::class, 'create']);
        Route::get('/show/{id}', [RetailSaleTransactionController::class, 'show']);
        Route::get('/edit/{id}', [RetailSaleTransactionController::class, 'edit']);
        Route::get('/return/{id}', [RetailSaleTransactionController::class, 'return']);
        Route::get('/print/{id}', [RetailSaleTransactionController::class, 'print']);
        Route::get('/report/sheet', [RetailSaleTransactionController::class, 'report']);
        Route::post('/action/bulk-store', [RetailSaleTransactionController::class, 'bulkStore']);
        Route::post('/', [RetailSaleTransactionController::class, 'store']);
        Route::patch('/{id}', [RetailSaleTransactionController::class, 'update']);
        Route::delete('/{id}', [RetailSaleTransactionController::class, 'destroy']);
    });

    //Route Retail Sale
    Route::prefix('/retail-sale-return')->group(function () {
        Route::get('/', [RetailSaleReturnController::class, 'index']);
        Route::get('/create', [RetailSaleReturnController::class, 'create']);
        Route::get('/edit/{id}', [RetailSaleReturnController::class, 'edit']);
        Route::get('/show/{id}', [RetailSaleReturnController::class, 'show']);
        Route::post('/', [RetailSaleReturnController::class, 'store']);
        Route::patch('/{id}', [RetailSaleReturnController::class, 'update']);
        Route::delete('/{id}', [RetailSaleReturnController::class, 'destroy']);
    });

    //Route Studio Sale
    Route::prefix('/studio-sale')->group(function () {
        Route::get('/', [StudioSaleController::class, 'index']);
        Route::get('/create', [StudioSaleController::class, 'create']);
        Route::get('/edit/{id}', [StudioSaleController::class, 'edit']);
        Route::get('/return/{id}', [StudioSaleController::class, 'return']);
        Route::get('/print/{id}', [StudioSaleController::class, 'print']);
        Route::get('/show/{id}', [StudioSaleController::class, 'show']);
        Route::get('/report/sheet', [StudioSaleController::class, 'report']);
        Route::post('/', [StudioSaleController::class, 'store']);
        Route::patch('/{id}', [StudioSaleController::class, 'update']);
        Route::delete('/{id}', [StudioSaleController::class, 'destroy']);
    });

    //Route Retail Sale
    Route::prefix('/studio-sale-transaction')->group(function () {
        Route::get('/', [StudioSaleTransactionController::class, 'index']);
        Route::get('/create', [StudioSaleTransactionController::class, 'create']);
        Route::get('/show/{id}', [StudioSaleTransactionController::class, 'show']);
        Route::get('/edit/{id}', [StudioSaleTransactionController::class, 'edit']);
        Route::get('/return/{id}', [StudioSaleTransactionController::class, 'return']);
        Route::get('/print/{id}', [StudioSaleTransactionController::class, 'print']);
        Route::get('/report/sheet', [StudioSaleTransactionController::class, 'report']);
        Route::post('/action/bulk-store', [StudioSaleTransactionController::class, 'bulkStore']);
        Route::post('/', [StudioSaleTransactionController::class, 'store']);
        Route::patch('/{id}', [StudioSaleTransactionController::class, 'update']);
        Route::delete('/{id}', [StudioSaleTransactionController::class, 'destroy']);
    });

    //Route Studio Sale Return
    Route::prefix('/studio-sale-return')->group(function () {
        Route::get('/', [StudioSaleReturnController::class, 'index']);
        Route::get('/create', [StudioSaleReturnController::class, 'create']);
        Route::get('/edit/{id}', [StudioSaleReturnController::class, 'edit']);
        Route::get('/show/{id}', [StudioSaleReturnController::class, 'show']);
        Route::post('/', [StudioSaleReturnController::class, 'store']);
        Route::patch('/{id}', [StudioSaleReturnController::class, 'update']);
        Route::delete('/{id}', [StudioSaleReturnController::class, 'destroy']);
    });

    //RouteCentralSale
    // Route::prefix('/central-sale')->group(function () {
    //     Route::get('/', [CentralSaleController::class, 'index']);
    //     Route::get('/create', [CentralSaleController::class, 'create']);
    //     Route::get('/show/{id}', [CentralSaleController::class, 'show']);
    //     Route::get('/edit/{id}', [CentralSaleController::class, 'edit']);
    //     Route::get('/approve/{id}', [CentralSaleController::class, 'approve']);
    //     Route::get('/report-by-customer/sheet', [CentralSaleController::class, 'reportByCustomer']);
    //     Route::get('/report-by-product/sheet', [CentralSaleController::class, 'reportByProduct']);
    //     Route::post('/', [CentralSaleController::class, 'store']);
    //     Route::post('/action/update-print-status/{id}', [CentralSaleController::class, 'updatePrintStatus']);
    //     Route::patch('/approve/{id}', [CentralSaleController::class, 'approved']);
    //     Route::patch('/{id}', [CentralSaleController::class, 'update']);
    //     Route::delete('/{id}', [CentralSaleController::class, 'destroy']);
    // });

    //RouteCentralSale
    Route::prefix('/central-sale')->group(function () {
        Route::get('/', [CentralSaleController::class, 'index']);
        Route::get('/create', [CentralSaleController::class, 'create']);
        Route::get('/show/{id}', [CentralSaleController::class, 'show']);
        Route::get('/edit/{id}', [CentralSaleController::class, 'edit']);
        Route::get('/approval/{id}', [CentralSaleController::class, 'approval']);
        Route::get('/pay/{id}', [CentralSaleController::class, 'pay']);
        Route::get('/return/{id}', [CentralSaleController::class, 'return']);
        Route::get('/print/{id}', [CentralSaleController::class, 'print']);
        Route::get('/report-by-customer/sheet', [CentralSaleController::class, 'reportByCustomer']);
        Route::get('/report-by-product/sheet', [CentralSaleController::class, 'reportByProduct']);
        Route::post('/', [CentralSaleController::class, 'store']);
        Route::post('/approval/{id}/approve', [CentralSaleController::class, 'approve']);
        Route::post('/approval/{id}/reject', [CentralSaleController::class, 'reject']);
        Route::post('/action/update-print-status/{id}', [CentralSaleController::class, 'updatePrintStatus']);
        Route::post('/action/auth-product-price', [CentralSaleController::class, 'authProductPrice']);
        Route::patch('/{id}', [CentralSaleController::class, 'update']);
        Route::delete('/{id}', [CentralSaleController::class, 'destroy']);
    });


    //RouteCentralSale
    // Route::prefix('/central-sale')->group(function () {
    //     Route::get('/', [CentralSaleController::class, 'index']);
    //     Route::get('/create', [CentralSaleController::class, 'create']);
    //     Route::post('/', [CentralSaleController::class, 'store']);
    //     Route::get('/edit/{id}', [CentralSaleController::class, 'edit']);
    //     Route::get('/approve/{id}', [CentralSaleController::class, 'approve']);
    //     Route::patch('/approve/{id}', [CentralSaleController::class, 'approved']);
    //     Route::patch('/{id}', [CentralSaleController::class, 'update']);
    //     Route::delete('/{id}', [CentralSaleController::class, 'destroy']);
    //     Route::get('/show/{id}', [CentralSaleController::class, 'show']);
    // });
    // =======
    //RoutePurchasereturn
    Route::prefix('/purchase-return')->group(function () {
        Route::get('/', [PurchaseReturnController::class, 'index']);
        Route::get('/create', [PurchaseReturnController::class, 'create']);
        Route::get('/edit/{id}', [PurchaseReturnController::class, 'edit']);
        Route::post('/', [PurchaseReturnController::class, 'store']);
        Route::patch('/{id}', [PurchaseReturnController::class, 'update']);
        Route::delete('/{id}', [PurchaseReturnController::class, 'destroy']);
        Route::get('/show/{id}', [PurchaseReturnController::class, 'show']);
        Route::get('/pay/{id}', [PurchaseReturnController::class, 'pay']);
    });

    //RoutePurchaseReceipt
    Route::prefix('/purchase-receipt')->group(function () {
        Route::get('/', [PurchaseReceiptController::class, 'index']);
        Route::get('/create', [PurchaseReceiptController::class, 'create']);
        Route::get('/edit/{id}', [PurchaseReceiptController::class, 'edit']);
        Route::post('/', [PurchaseReceiptController::class, 'store']);
        Route::patch('/{id}', [PurchaseReceiptController::class, 'update']);
        Route::delete('/{id}', [PurchaseReceiptController::class, 'destroy']);
        Route::get('/show/{id}', [PurchaseReceiptController::class, 'show']);
        Route::get('/pay/{id}', [PurchaseReceiptController::class, 'pay']);
    });

    //RoutePurchasereturn
    Route::prefix('/purchase-return-transaction')->group(function () {
        Route::get('/', [PurchaseReturnTransactionController::class, 'index']);
        Route::get('/create', [PurchaseReturnTransactionController::class, 'create']);
        Route::get('/edit/{id}', [PurchaseReturnTransactionController::class, 'edit']);
        Route::post('/', [PurchaseReturnTransactionController::class, 'store']);
        Route::patch('/{id}', [PurchaseReturnTransactionController::class, 'update']);
        Route::delete('/{id}', [PurchaseReturnTransactionController::class, 'destroy']);
        Route::get('/show/{id}', [PurchaseReturnTransactionController::class, 'show']);
        Route::get('/pay/{id}', [PurchaseReturnTransactionController::class, 'pay']);
    });

    //RoutePurchaseTransaction
    Route::prefix('/purchase-transaction')->group(function () {
        Route::get('/', [PurchaseTransactionController::class, 'index']);
        Route::get('/create', [PurchaseTransactionController::class, 'create']);
        Route::post('/', [PurchaseTransactionController::class, 'store']);
        Route::get('/edit/{id}', [PurchaseTransactionController::class, 'edit']);
        Route::patch('/{id}', [PurchaseTransactionController::class, 'update']);
        Route::delete('/{id}', [PurchaseTransactionController::class, 'destroy']);
        Route::get('/show/{id}', [PurchaseTransactionController::class, 'show']);
    });

    //RouteAccount
    Route::prefix('/account')->group(function () {
        Route::get('/', [AccountController::class, 'index']);
        Route::get('/create', [AccountController::class, 'create']);
        Route::post('/', [AccountController::class, 'store']);
        Route::get('/edit/{id}', [AccountController::class, 'edit']);
        Route::patch('/{id}', [AccountController::class, 'update']);
        Route::delete('/{id}', [AccountController::class, 'destroy']);
        // Route::del('/{id}', [AccountController::class, 'show']);
        Route::get('show/{id}', [AccountController::class, 'show']);
        Route::get('reports/{id}', [AccountController::class, 'reports']);
        Route::get('/export/{id}', [AccountController::class, 'export']);
    });

    //RouteAccountTransaction
    Route::prefix('/account-transaction')->group(function () {
        Route::get('/', [AccountTransactionController::class, 'index']);
        Route::get('/create', [AccountTransactionController::class, 'create']);
        Route::post('/', [AccountTransactionController::class, 'store']);
        Route::get('/edit/{id}', [AccountTransactionController::class, 'edit']);
        Route::patch('/{id}', [AccountTransactionController::class, 'update']);
        Route::get('show/{id}', [AccountTransactionController::class, 'show']);
        Route::delete('/{in_id}/{out_id}', [AccountTransactionController::class, 'destroy']);
    });


    //RouteStockOpname
    Route::prefix('/stock-opname')->group(function () {
        Route::get('/', [StockOpnameController::class, 'index']);
        Route::get('/create', [StockOpnameController::class, 'create']);
        Route::post('/', [StockOpnameController::class, 'store']);
        Route::get('/edit/{id}', [StockOpnameController::class, 'edit']);
        Route::patch('/{id}', [StockOpnameController::class, 'update']);
        Route::delete('/{id}', [StockOpnameController::class, 'destroy']);
        Route::get('/show/{id}', [StockOpnameController::class, 'show']);
    });

    //RouteStockOpnameRetail
    Route::prefix('/retail-stock-opname')->group(function () {
        Route::get('/', [StockOpnameRetailController::class, 'index']);
        Route::get('/create', [StockOpnameRetailController::class, 'create']);
        Route::post('/', [StockOpnameRetailController::class, 'store']);
        // Route::get('/edit/{id}', [StockOpnameRetailController::class, 'edit']);
        // Route::patch('/{id}', [StockOpnameRetailController::class, 'update']);
        // Route::delete('/{id}', [StockOpnameRetailController::class, 'destroy']);
        Route::get('/show/{id}', [StockOpnameRetailController::class, 'show']);
    });

    //RouteStockOpnameStudio
    Route::prefix('/studio-stock-opname')->group(function () {
        Route::get('/', [StockOpnameStudioController::class, 'index']);
        Route::get('/create', [StockOpnameStudioController::class, 'create']);
        Route::post('/', [StockOpnameStudioController::class, 'store']);
        // Route::get('/edit/{id}', [StockOpnameStudioController::class, 'edit']);
        // Route::patch('/{id}', [StockOpnameStudioController::class, 'update']);
        // Route::delete('/{id}', [StockOpnameStudioController::class, 'destroy']);
        Route::get('/show/{id}', [StockOpnameStudioController::class, 'show']);
    });

    //RouteBadstockRelease
    Route::prefix('/badstock-release')->group(function () {
        Route::get('/', [BadstockReleaseController::class, 'index']);
        Route::get('/create', [BadstockReleaseController::class, 'create']);
        Route::post('/', [BadstockReleaseController::class, 'store']);
        Route::get('/edit/{id}', [BadstockReleaseController::class, 'edit']);
        Route::get('/approve/{id}', [BadstockReleaseController::class, 'approve']);
        Route::patch('/approve/{id}', [BadstockReleaseController::class, 'approved']);
        Route::patch('/reject/{id}', [BadstockReleaseController::class, 'rejected']);
        Route::patch('/{id}', [BadstockReleaseController::class, 'update']);
        Route::delete('/{id}', [BadstockReleaseController::class, 'destroy']);
        Route::get('/show/{id}', [BadstockReleaseController::class, 'show']);
    });

    // //RouteReturSupplier
    Route::prefix('/retur-supplier')->group(function () {
        Route::get('/', [ReturSupplierController::class, 'index']);
        Route::get('/create', [ReturSupplierController::class, 'create']);
        Route::post('/', [ReturSupplierController::class, 'store']);
        Route::get('/edit/{id}', [ReturSupplierController::class, 'edit']);
        Route::patch('/{id}', [ReturSupplierController::class, 'update']);
        Route::delete('/{id}', [ReturSupplierController::class, 'destroy']);
    });

    //RouteRequestToRetail
    Route::prefix('/request-to-retail')->group(function () {
        Route::get('/', [RequestToRetailController::class, 'index']);
        Route::get('/create', [RequestToRetailController::class, 'create']);
        Route::post('/', [RequestToRetailController::class, 'store']);
        Route::get('/edit/{id}', [RequestToRetailController::class, 'edit']);
        Route::patch('/{id}', [RequestToRetailController::class, 'update']);
        Route::delete('/{id}', [RequestToRetailController::class, 'destroy']);
    });

    //RouteRetailRequestToCentral
    Route::prefix('/retail-request-to-central')->group(function () {
        Route::get('/', [RetailRequestToCentralController::class, 'index']);
        Route::get('/create', [RetailRequestToCentralController::class, 'create']);
        Route::post('/', [RetailRequestToCentralController::class, 'store']);
        Route::get('/edit/{id}', [RetailRequestToCentralController::class, 'edit']);
        Route::get('/print/{id}', [RetailRequestToCentralController::class, 'print']);
        Route::get('/excel/{id}', [RetailRequestToCentralController::class, 'excel']);
        Route::patch('/{id}', [RetailRequestToCentralController::class, 'update']);
        Route::delete('/{id}', [RetailRequestToCentralController::class, 'destroy']);
    });

    //RouteStudioRequestToCentral
    Route::prefix('/studio-request-to-central')->group(function () {
        Route::get('/', [StudioRequestToCentralController::class, 'index']);
        Route::get('/create', [StudioRequestToCentralController::class, 'create']);
        Route::post('/', [StudioRequestToCentralController::class, 'store']);
        Route::get('/edit/{id}', [StudioRequestToCentralController::class, 'edit']);
        Route::get('/print/{id}', [StudioRequestToCentralController::class, 'print']);
        Route::get('/excel/{id}', [StudioRequestToCentralController::class, 'excel']);
        Route::patch('/{id}', [StudioRequestToCentralController::class, 'update']);
        Route::delete('/{id}', [StudioRequestToCentralController::class, 'destroy']);
    });

    //RouteApproveCentralFromRetail
    Route::prefix('/approve-central-retail')->group(function () {
        Route::get('/', [ApproveCentralController::class, 'index']);
        Route::get('/create', [ApproveCentralController::class, 'create']);
        Route::post('/', [ApproveCentralController::class, 'store']);
        Route::get('/edit/{id}', [ApproveCentralController::class, 'edit']);
        Route::get('/show/{id}', [ApproveCentralController::class, 'show']);
        Route::get('/approve/{id}', [ApproveCentralController::class, 'approve']);
        Route::patch('/approve/{id}', [ApproveCentralController::class, 'approved']);
        Route::patch('/reject/{id}', [ApproveCentralController::class, 'rejected']);
        Route::patch('/{id}', [ApproveCentralController::class, 'update']);
        Route::delete('/{id}', [ApproveCentralController::class, 'destroy']);
    });

    //RouteApproveCentralFromStudio
    Route::prefix('/approve-central-studio')->group(function () {
        Route::get('/', [ApproveCentralStudioController::class, 'index']);
        Route::get('/create', [ApproveCentralStudioController::class, 'create']);
        Route::post('/', [ApproveCentralStudioController::class, 'store']);
        Route::get('/edit/{id}', [ApproveCentralStudioController::class, 'edit']);
        Route::get('/show/{id}', [ApproveCentralStudioController::class, 'show']);
        Route::get('/approve/{id}', [ApproveCentralStudioController::class, 'approve']);
        Route::patch('/approve/{id}', [ApproveCentralStudioController::class, 'approved']);
        Route::patch('/reject/{id}', [ApproveCentralStudioController::class, 'rejected']);
        Route::patch('/{id}', [ApproveCentralStudioController::class, 'update']);
        Route::delete('/{id}', [ApproveCentralStudioController::class, 'destroy']);
    });

    //RouteApproveRetailFromCentral
    Route::prefix('/approve-retail')->group(function () {
        Route::get('/', [ApproveRetailController::class, 'index']);
        Route::get('/create', [ApproveRetailController::class, 'create']);
        Route::post('/', [ApproveRetailController::class, 'store']);
        Route::get('/edit/{id}', [ApproveRetailController::class, 'edit']);
        Route::get('/show/{id}', [ApproveRetailController::class, 'show']);
        Route::get('/approve/{id}', [ApproveRetailController::class, 'approve']);
        Route::patch('/approve/{id}', [ApproveRetailController::class, 'approved']);
        Route::patch('/reject/{id}', [ApproveRetailController::class, 'rejected']);
        Route::patch('/{id}', [ApproveRetailController::class, 'update']);
        Route::delete('/{id}', [ApproveRetailController::class, 'destroy']);
    });

    //RouteApproveStudioFromCentral
    Route::prefix('/approve-studio')->group(function () {
        Route::get('/', [ApproveStudioController::class, 'index']);
        Route::get('/create', [ApproveStudioController::class, 'create']);
        Route::post('/', [ApproveStudioController::class, 'store']);
        Route::get('/edit/{id}', [ApproveStudioController::class, 'edit']);
        Route::get('/show/{id}', [ApproveStudioController::class, 'show']);
        Route::get('/approve/{id}', [ApproveStudioController::class, 'approve']);
        Route::patch('/approve/{id}', [ApproveStudioController::class, 'approved']);
        Route::patch('/reject/{id}', [ApproveStudioController::class, 'rejected']);
        Route::patch('/{id}', [ApproveStudioController::class, 'update']);
        Route::delete('/{id}', [ApproveStudioController::class, 'destroy']);
    });

    //RouteRequestToStudio
    Route::prefix('/request-to-studio')->group(function () {
        Route::get('/', [RequestToStudioController::class, 'index']);
        Route::get('/create', [RequestToStudioController::class, 'create']);
        Route::post('/', [RequestToStudioController::class, 'store']);
        Route::get('/edit/{id}', [RequestToStudioController::class, 'edit']);
        Route::patch('/{id}', [RequestToStudioController::class, 'update']);
        Route::delete('/{id}', [RequestToStudioController::class, 'destroy']);
    });


    //RouteSaleRetail
    Route::prefix('/saleretail')->group(function () {
        Route::get('/', [SaleRetailController::class, 'index']);
        Route::get('/create', [SaleRetailController::class, 'create']);
        Route::get('/edit/{id}', [SaleRetailController::class, 'edit']);
        Route::get('/print/{id}', [SaleRetailController::class, 'print']);
        Route::post('/', [SaleRetailController::class, 'store']);
        Route::patch('/{id}', [SaleRetailController::class, 'update']);
        Route::delete('/{id}', [SaleRetailController::class, 'destroy']);
    });

    //RouteGroup
    Route::prefix('/group')->group(function () {
        Route::get('/', [GroupController::class, 'index']);
        Route::get('/create', [GroupController::class, 'create']);
        Route::post('/', [GroupController::class, 'store']);
        Route::get('/edit/{id}', [GroupController::class, 'edit']);
        Route::patch('/{id}', [GroupController::class, 'update']);
        Route::delete('/{id}', [GroupController::class, 'destroy']);
    });

    //RouteUser
    Route::prefix('/user')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/create', [UserController::class, 'create']);
        Route::post('/', [UserController::class, 'store']);
        Route::get('/edit/{id}', [UserController::class, 'edit']);
        // Route::get('/show/{id}', [UserController::class, 'show']);
        Route::patch('/{id}', [UserController::class, 'update']);
        Route::patch('/change/{id}', [UserController::class, 'change']);
        Route::delete('/{id}', [UserController::class, 'destroy']);
    });

    //RouteUser
    Route::prefix('/report')->group(function () {
        Route::get('/', [ReportController::class, 'index']);
        Route::get('/central-sale/customer/detail', [ReportController::class, 'centralSaleByCustomerDetail']);
        Route::get('/central-sale/customer/summary', [ReportController::class, 'centralSaleByCustomerSummary']);
        Route::get('/central-sale/product/detail', [ReportController::class, 'centralSaleByProductDetail']);
        Route::get('/central-sale/product/summary', [ReportController::class, 'centralSaleByProductSummary']);
        // Route::get('/central-sale/summary', [ReportController::class, 'centralSaleSummary']);
        Route::get('/retail-sale/detail', [ReportController::class, 'retailSaleDetail']);
        Route::get('/studio-sale/detail', [ReportController::class, 'studioSaleDetail']);
        Route::get('/central-purchase/supplier/detail', [ReportController::class, 'centralPurchaseBySupplierDetail']);
        Route::get('/central-purchase/supplier/summary', [ReportController::class, 'centralPurchaseBySupplierSummary']);
        Route::get('/central-purchase/product/detail', [ReportController::class, 'centralPurchaseByProductDetail']);
        Route::get('/central-purchase/product/summary', [ReportController::class, 'centralPurchaseByProductSummary']);
    });

    Route::prefix('/changelog')->group(function () {
        Route::get('/', [ChangelogController::class, 'index']);
    });
});



// Datatables
Route::prefix('/datatables')->group(function () {
    Route::prefix('/central-purchases')->group(function () {
        Route::get('/', [CentralPurchaseController::class, 'datatableCentralPurchase']);
        Route::get('/products', [CentralPurchaseController::class, 'datatableProducts']);
    });
    Route::prefix('/suppliers')->group(function () {
        Route::get('/', [SupplierController::class, 'datatableSuppliers']);
        Route::get('/pay/{id}', [SupplierController::class, 'datatableSupplierPayment']);
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
    Route::prefix('/central-sale-transactions')->group(function () {
        Route::get('/', [CentralSaleTransactionController::class, 'datatableCentralSaleTransactions']);
    });
    Route::prefix('/central-sale-returns')->group(function () {
        Route::get('/', [CentralSaleReturnController::class, 'datatableCentralSaleReturns']);
    });
    Route::prefix('/central-sale-return-transactions')->group(function () {
        Route::get('/', [CentralSaleReturnTransactionController::class, 'datatableCentralSaleReturnTransactions']);
    });
    Route::prefix('/retail-sales')->group(function () {
        Route::get('/', [RetailSaleController::class, 'datatableRetailSale']);
    });
    Route::prefix('/retail-sale-transactions')->group(function () {
        Route::get('/', [RetailSaleTransactionController::class, 'datatableRetailSaleTransactions']);
    });
    Route::prefix('/retail-sale-returns')->group(function () {
        Route::get('/', [RetailSaleReturnController::class, 'datatableRetailSaleReturns']);
    });
    Route::prefix('/studio-sales')->group(function () {
        Route::get('/', [StudioSaleController::class, 'datatableStudioSales']);
    });
    Route::prefix('/studio-sale-transactions')->group(function () {
        Route::get('/', [StudioSaleTransactionController::class, 'datatableStudioSaleTransactions']);
    });
    Route::prefix('/studio-sale-returns')->group(function () {
        Route::get('/', [StudioSaleReturnController::class, 'datatableStudioSaleReturns']);
    });
    Route::prefix('/badstock-release')->group(function () {
        Route::get('/', [BadstockReleaseController::class, 'datatableBadstockRelease']);
    });
    Route::prefix('/request-to-retail')->group(function () {
        Route::get('/', [RequestToRetailController::class, 'datatableRequestToRetail']);
        Route::get('/products', [RequestToRetailController::class, 'datatableProduct']);
    });
    Route::prefix('/request-to-studio')->group(function () {
        Route::get('/', [RequestToStudioController::class, 'datatableRequestToStudio']);
        Route::get('/products', [RequestToStudioController::class, 'datatableProduct']);
    });
    Route::prefix('/retail-request-to-central')->group(function () {
        Route::get('/', [RetailRequestToCentralController::class, 'datatableRetailRequestToCentral']);
        Route::get('/products', [RetailRequestToCentralController::class, 'datatableProduct']);
    });
    Route::prefix('/studio-request-to-central')->group(function () {
        Route::get('/', [StudioRequestToCentralController::class, 'datatableStudioRequestToCentral']);
        Route::get('/products', [StudioRequestToCentralController::class, 'datatableProduct']);
    });
    Route::prefix('/approve-central-retail')->group(function () {
        Route::get('/', [ApproveCentralController::class, 'datatableApproveCentral']);
        Route::get('/products', [ApproveCentralController::class, 'datatableProduct']);
    });
    Route::prefix('/approve-central-studio')->group(function () {
        Route::get('/', [ApproveCentralStudioController::class, 'datatableApproveCentral']);
        Route::get('/products', [ApproveCentralStudioController::class, 'datatableProduct']);
    });
    Route::prefix('/approve-retail')->group(function () {
        Route::get('/', [ApproveRetailController::class, 'datatableApproveRetail']);
        Route::get('/products', [ApproveRetailController::class, 'datatableProducts']);
    });
    Route::prefix('/approve-studio')->group(function () {
        Route::get('/', [ApproveStudioController::class, 'datatableApproveStudio']);
        Route::get('/products', [ApproveStudioController::class, 'datatableProducts']);
    });
    Route::prefix('/stock-opname-retail')->group(function () {
        Route::get('/', [StockOpnameRetailController::class, 'datatableStockOpnameRetail']);
        Route::get('/products', [StockOpnameRetailController::class, 'datatableProducts']);
    });
    Route::prefix('/stock-opname-studio')->group(function () {
        Route::get('/', [StockOpnameStudioController::class, 'datatableStockOpnameStudio']);
        Route::get('/products', [StockOpnameStudioController::class, 'datatableProducts']);
    });
    Route::prefix('/group')->group(function () {
        Route::get('/', [GroupController::class, 'datatableGroup']);
    });
    Route::prefix('/user')->group(function () {
        Route::get('/', [UserController::class, 'datatableUser']);
    });
    Route::prefix('/purchases-transactions')->group(function () {
        Route::get('/', [PurchaseTransactionController::class, 'datatablePurchaseTransaction']);
    });
    Route::prefix('/purchase-returns')->group(function () {
        Route::get('/', [PurchaseReturnController::class, 'datatablePurchaseReturn']);
    });
    Route::prefix('/purchase-return-transactions')->group(function () {
        Route::get('/', [PurchaseReturnTransactionController::class, 'datatablePurchaseReturnTransactions']);
    });
    Route::prefix('/account-transactions')->group(function () {
        Route::get('/{id}', [AccountController::class, 'datatableAccountTransactions']);
    });
    Route::prefix('/reports')->group(function () {
        Route::get('/central-sale-detail', [ReportController::class, 'centralSaleByCustomerDetailData']);
        Route::get('/central-sale-summary', [ReportController::class, 'centralSaleSummaryData']);
    });
    Route::prefix('/accounts')->group(function () {
        Route::get('/', [AccountController::class, 'datatableAccounts']);
    });
});

<?php
namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Exception;
use Illuminate\Http\Request;

class AccountApiController extends Controller {
    public function index() {
        try {
            $accounts = Account::all();
            return response()->json([
                'message' => 'OK',
                'code' => 200,
                'error' => false,
                'data' => $accounts
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
    }
    
    public function show($id) {
        try {
            $accounts = Account::with('accountTransactions')->find($id);
            return response()->json([
                'message' => 'OK',
                'code' => 200,
                'error' => false,
                'data' => $accounts
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CentralPurchase;
use App\Models\PurchaseTransaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class PurchaseTransactionController extends Controller
{
    public function index()
    {
        return view('purchase-transaction.index');
    }

    public function create(Request $request)
    {
    }

    private function formatDate($date = "", $format = "Y-m-d")
    {
        return date_format(date_create($date), $format);
    }

    public function store(Request $request)
    {
        $date = $request->date;
        $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
        $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

        $purchaseId = $request->purchase_id;
        $amount = $this->clearThousandFormat($request->amount);

        $transaction = new PurchaseTransaction;
        $transaction->code = $transactionNumber;
        $transaction->date = $request->date;
        $transaction->account_id = $request->account_id;
        $transaction->supplier_id = $request->supplier_id;
        $transaction->amount = $amount;
        $transaction->payment_method = $request->payment_method;
        $transaction->note = $request->note;

        try {
            $transaction->save();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $transaction,
            // ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // $keyedQuotations = collect($quotations)->mapWithKeys(function ($item) {
        //     return [
        //         $item['id'] => [
        //             'estimation_id' => $item['selected_estimation'],
        //             'created_at' => Carbon::now()->toDateTimeString(),
        //             'updated_at' => Carbon::now()->toDateTimeString(),
        //         ]
        //     ];
        // })->all();

        try {
            $transaction->centralPurchases()->attach([
                $purchaseId => [
                    'amount' => $amount,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ]);
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $transaction,
            ]);
        } catch (Exception $e) {
            $transaction->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
    }

    private function clearThousandFormat($number = 0)
    {
        return str_replace(".", "", $number);
    }
}

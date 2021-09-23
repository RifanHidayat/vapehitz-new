<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\CentralPurchase;
use App\Models\PurchaseTransaction;
use App\Models\Supplier;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    private function formatDate($date = "", $format = "Y-m-d")
    {
        return date_format(date_create($date), $format);
    }

    private function clearThousandFormat($number)
    {
        return str_replace(".", "", $number);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_supplier", $permission)) {
            return redirect("/dashboard");
        }
        $supplier = Supplier::all();

        return view('supplier.index', [
            'suppliers' => $supplier,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("add_supplier", $permission)) {
            return redirect("/dashboard");
        }
        $maxid = DB::table('suppliers')->max('id');
        $code = "S" . sprintf('%04d', $maxid + 1);
        return view('supplier.create', [
            'code' => $code,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(([
            'name' => 'required',
            'handphone' => 'nullable',
        ]));

        $code = $request->code;
        if ($code == null) {
            $maxid = DB::table('suppliers')->max('id');
            $code = "S" . sprintf('%04d', $maxid + 1);
        }

        $supplier = new Supplier;
        $supplier->name = $request->name;
        $supplier->code = $code;
        $supplier->address = $request->address;
        // $supplier->telephone = $request->telephone;
        $supplier->handphone = $request->handphone;
        $supplier->email = $request->email;
        $supplier->status = $request->status;
        try {
            $supplier->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $supplier,
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("edit_supplier", $permission)) {
            return redirect("/dashboard");
        }
        $supplier = Supplier::findOrFail($id);

        return view('supplier.edit', [
            'supplier' => $supplier,
        ]);
    }

    public function pay($id)
    {

        $accounts = Account::all();
        $supplier = Supplier::with(['centralPurchases', 'purchaseTransactions'])->find($id);
        $payAmountentralPurchase = collect($supplier->purchaseTransactions)->sum('amount');
        $grandTotalCentralPurchase = collect($supplier->centralPurchases)->sum('netto');

        return view('supplier.pay', [

            'payRemaining' => $grandTotalCentralPurchase - $payAmountentralPurchase,
            'accounts' => $accounts,
            'supplier_id' => $id

        ]);
    }

    public function payment(Request $request)
    {
        $date = $request->date;
        $transactionsByCurrentDateCount = PurchaseTransaction::query()->where('date', $date)->get()->count();
        $transactionNumber = 'PT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

        $amount = $this->clearThousandFormat($request->amount);
        $purchaseTransaction = new PurchaseTransaction;
        $purchaseTransaction->code = $transactionNumber;
        $purchaseTransaction->date = $request->date;
        $purchaseTransaction->account_id = $request->account_id;
        $purchaseTransaction->supplier_id = $request->supplier_id;
        $purchaseTransaction->amount = $amount;
        $purchaseTransaction->payment_method = $request->payment_method;
        $purchaseTransaction->note = $request->note;
        $centralPurchaseSelected = $request->central_purchase_selected;
        $accountTransaction = new AccountTransaction;
        // return $centralPurchaseSelected;
        //purchase transaction
        try {
            $purchaseTransaction->save();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }


        //account transaction
        try {
            $accountTransaction = new AccountTransaction;
            $accountTransaction->account_id = $request->account_id;
            $accountTransaction->amount = $amount;
            $accountTransaction->type = "out";
            $accountTransaction->note = $transactionNumber . ' | ' . $request->note;
            $accountTransaction->date = $request->date;
            $accountTransaction->save();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        //central purchase purchase transaction
        $purchaseTransactionId = $purchaseTransaction->id;
        // $keyCentralPurchase = collect($centralPurchaseSelected)->mapWithKeys(function ($item) use ($purchaseTransactionId) {
        //     return [
        //         $purchaseTransactionId => [
        //             'central_purchase_id'=>$item['id'],
        //             'amount'=>$item['amount'],
        //             'created_at' => Carbon::now()->toDateTimeString(),
        //             'updated_at' => Carbon::now()->toDateTimeString(),
        //         ]
        //     ];
        // })->all();
        //return $centralPurchaseSelected;
        //return $keyCentralPurchase;

        $keyCentralPurchase = collect($centralPurchaseSelected)->mapWithKeys(function ($item) {

            return [
                $item['id'] => [
                    // 'central_purchase_id'=>$item['id'],
                    'amount' => $item['amount'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        });;




        try {
            $purchaseTransaction->centralPurchases()->attach($keyCentralPurchase);
        } catch (Exception $e) {
            $purchaseTransaction->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e . "e",
            ], 500);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $request->validate(([
            'name' => 'required',
            'handphone' => 'nullable',
        ]));

        $supplier->name = $request->name;
        $supplier->code = $request->code;
        $supplier->address = $request->address;
        // $supplier->telephone = $request->telephone;
        $supplier->handphone = $request->handphone;
        $supplier->email = $request->email;
        $supplier->status = $request->status;

        try {
            $supplier->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $supplier,
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("delete_supplier", $permission)) {
            return view("dashboard.index");
        }
        $supplier = Supplier::findOrFail($id);
        try {
            $supplier->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $supplier,
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

    public function datatableSuppliers()
    {

        $suppliers = Supplier::all();
        return DataTables::of($suppliers)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $permission = json_decode(Auth::user()->group->permission);
                if (in_array("edit_supplier", $permission)) {
                    $edit = '<a href="/supplier/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                <span>Edit</span>
                </a>';
                } else {
                    $edit = "";
                }
                if (in_array("delete_supplier", $permission)) {
                    $delete = '<a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                   <span>Delete</span>
                   </a>';
                } else {
                    $delete = "";
                }
                $button = '
                <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">

                    ' . $edit . '
                    ' . $delete . '
                    
                        <a href="/supplier/pay/' . $row->id . '"><em class="icon fas fa-check"></em>

                            <span>Pay</span>
                        </a>
                    </ul>
                </div>
                </div>';
                return $button;
            })
            ->make();
    }

    public function datatableSupplierPayment($id)
    {
        $centralPurchase = CentralPurchase::with(['supplier'])->select('central_purchases.*')->where('supplier_id', '=', $id);
        return DataTables::eloquent($centralPurchase)
            ->addIndexColumn()
            ->addColumn('date', function ($row) {
                return ($row->date);
            })

            ->addColumn('action', function ($row) {
                $button = '  <input class="checked-central-purchase" type="checkbox" value="true">';
                return $button;
            })

            ->addColumn('netto', function ($row) {
                return (number_format($row->netto));
            })

            ->addColumn('payAmount', function ($row) {
                $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($row->id);
                $transactions = collect($purchase->purchaseTransactions)->sum('pivot.amount');
                return (number_format($transactions));
            })

            ->addColumn('remainingAmount', function ($row) {
                $paidOff = '<div><span class="badge badge-sm badge-dim badge-outline-success d-none d-md-inline-flex">Lunas</span></div>';
                $purchase = CentralPurchase::with(['supplier', 'products'])->findOrFail($row->id);
                $transactions = collect($purchase->purchaseTransactions)->sum('pivot.amount');
                return number_format(($row->netto) - ($transactions));
            })

            ->make(true);
    }
}

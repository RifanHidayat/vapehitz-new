<?php

namespace App\Http\Controllers;

use App\Models\AccountTransaction;
use App\Models\Product;
use App\Models\StudioSaleReturn;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StudioSaleReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('studio-sale-return.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $date = $request->date;
        $returnsByCurrentDateCount = StudioSaleReturn::query()->where('date', $date)->get()->count();
        $returnNumber = 'RSR/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $returnsByCurrentDateCount + 1);

        $saleId = $request->sale_id;
        $amount = $this->clearThousandFormat($request->amount);

        $return = new StudioSaleReturn;
        $return->code = $returnNumber;
        $return->date = $request->date;
        $return->studio_sale_id = $request->sale_id;
        $return->account_id = $request->account_id;
        // $return->customer_id = $request->customer_id;
        $return->payment_method = $request->payment_method;
        $return->quantity = $request->quantity;
        $return->amount = $amount;
        $return->note = $request->note;

        $products = $request->selected_products;

        // return response()->json([
        //     'message' => 'Data has been saved',
        //     'code' => 200,
        //     'error' => false,
        //     'data' => $products,
        // ]);

        try {
            $return->save();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $return,
            // ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        $keyedProducts = collect($products)->mapWithKeys(function ($item) {
            return [
                $item['id'] => [
                    'quantity' => $item['return_quantity'],
                    'cause' => $item['cause'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $return->products()->attach($keyedProducts);
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $return,
            // ]);
        } catch (Exception $e) {
            $return->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        try {
            foreach ($products as $product) {
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }
                // $productRow->booked = $productRow->booked + $product['quantity'] + $product['free'];
                if ($product['cause'] == 'defective') {
                    $productRow->bad_stock += $product['return_quantity'];
                } else if ($product['cause'] == 'wrong') {
                    $productRow->studio_stock += $product['return_quantity'];
                }
                $productRow->save();
            }
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $return,
            // ]);
        } catch (Exception $e) {
            $return->products()->detach();
            $return->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        // Account Transaction
        $saleReturn = StudioSaleReturn::find($saleId);
        $accountTransaction = new AccountTransaction;
        $accountTransaction->account_in = $request->account_id;
        $accountTransaction->amount = $amount;
        $accountTransaction->type = "in";
        $accountTransaction->note = "Retur penjualan studio No. " . $saleReturn->code;
        $accountTransaction->date = $request->date;

        try {
            $accountTransaction->save();
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

        // $saleId = $request->sale_id;

        // $sale = RetailSaleReturn::find($saleId);
        // $transactionAmount = 0;
        // if ($sale !== null) {
        //     $saleTotalPaid = collect($sale->centralSaleTransactions)->sum('amount');
        //     $saleNetTotal = $sale->net_total;
        //     $saleUnpaid = $saleNetTotal - $saleTotalPaid;
        //     if ($amount > $saleUnpaid) {
        //         $transactionAmount = $saleUnpaid;
        //     } else {
        //         $transactionAmount = $amount;
        //     }
        // }

        // Central Sale Return Transaction

        // $returnTransactionsByCurrentDateCount = CentralSaleReturnTransaction::query()->where('date', $date)->get()->count();
        // $returnTransactionNumber = 'SRT/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $returnTransactionsByCurrentDateCount + 1);

        // $returnId = $return->id;

        // $returnTransaction = new CentralSaleReturnTransaction;
        // $returnTransaction->code = $returnTransactionNumber;
        // $returnTransaction->date = $request->date;
        // $returnTransaction->account_id = $request->account_id;
        // $returnTransaction->customer_id = $request->customer_id;
        // $returnTransaction->amount = $request->payment_method == 'hutang' ? $transactionAmount : $amount;
        // $returnTransaction->payment_method = $request->payment_method;
        // // $returnTransaction->note = $request->note;

        // try {
        //     $returnTransaction->save();
        //     // return response()->json([
        //     //     'message' => 'Data has been saved',
        //     //     'code' => 200,
        //     //     'error' => false,
        //     //     'data' => $returnTransaction,
        //     // ]);
        // } catch (Exception $e) {
        //     return response()->json([
        //         'message' => 'Internal error',
        //         'code' => 500,
        //         'error' => true,
        //         'errors' => $e,
        //     ], 500);
        // }

        // try {
        //     $returnTransaction->centralSaleReturns()->attach([
        //         $returnId => [
        //             'amount' => $request->payment_method == 'hutang' ? $transactionAmount : $amount,
        //             'created_at' => Carbon::now()->toDateTimeString(),
        //             'updated_at' => Carbon::now()->toDateTimeString(),
        //         ]
        //     ]);
        // } catch (Exception $e) {
        //     $returnTransaction->delete();
        //     return response()->json([
        //         'message' => 'Internal error',
        //         'code' => 500,
        //         'error' => true,
        //         'errors' => $e,
        //     ], 500);
        // }

        // Central Sale Transaction

        // if ($request->payment_method == 'hutang') {
        //     $date = $request->date;
        //     $transactionsByCurrentDateCount = CentralSaleTransaction::query()->where('date', $date)->get()->count();
        //     $transactionNumber = 'ST/VH/' . $this->formatDate($date, "d") . $this->formatDate($date, "m") . $this->formatDate($date, "y") . '/' . sprintf('%04d', $transactionsByCurrentDateCount + 1);

        //     $transaction = new CentralSaleTransaction;
        //     $transaction->code = $transactionNumber;
        //     $transaction->date = $request->date;
        //     $transaction->account_id = $request->account_id;
        //     $transaction->customer_id = $request->customer_id;
        //     $transaction->amount = $transactionAmount;
        //     $transaction->payment_method = 'hutang';
        //     // $transaction->note = $request->note;

        //     try {
        //         $transaction->save();
        //         // return response()->json([
        //         //     'message' => 'Data has been saved',
        //         //     'code' => 200,
        //         //     'error' => false,
        //         //     'data' => $transaction,
        //         // ]);
        //     } catch (Exception $e) {
        //         return response()->json([
        //             'message' => 'Internal error',
        //             'code' => 500,
        //             'error' => true,
        //             'errors' => $e,
        //         ], 500);
        //     }

        //     try {
        //         $transaction->centralSales()->attach([
        //             $saleId => [
        //                 'amount' => $transactionAmount,
        //                 'created_at' => Carbon::now()->toDateTimeString(),
        //                 'updated_at' => Carbon::now()->toDateTimeString(),
        //             ]
        //         ]);
        //     } catch (Exception $e) {
        //         $transaction->delete();
        //         return response()->json([
        //             'message' => 'Internal error',
        //             'code' => 500,
        //             'error' => true,
        //             'errors' => $e,
        //         ], 500);
        //     }
        // }

        return response()->json([
            'message' => 'Data has been saved',
            'code' => 200,
            'error' => false,
            'data' => $return,
            // 'data' => $returnTransaction,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $return = StudioSaleReturn::findOrFail($id);
        try {
            $return->products()->detach();
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error detaching',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        try {
            $return->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $return,
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

    public function datatableStudioSaleReturns()
    {
        $studioSaleReturns = StudioSaleReturn::with(['products', 'studioSale'])->orderBy('date', 'desc')->select('studio_sale_returns.*');
        return DataTables::of($studioSaleReturns)
            ->addIndexColumn()
            // ->addColumn('shipment_name', function ($row) {
            //     return ($row->shipment ? $row->shipment->name : "");
            // })
            ->addColumn('action', function ($row) {
                $button = '
            <div class="dropright">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                        <a href="/studio-sale-return/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                            <span>Edit</span>
                        </a>
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                        </a>
                        <a href="/studio-sale-return/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                            <span>Detail</span>
                        </a>';

                $button .= '           
                    </ul>
                </div>
            </div>';
                return $button;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    private function formatDate($date = "", $format = "Y-m-d")
    {
        return date_format(date_create($date), $format);
    }

    private function clearThousandFormat($number = 0)
    {
        return str_replace(".", "", $number);
    }
}

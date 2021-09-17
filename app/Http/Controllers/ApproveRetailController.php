<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RequestToRetail;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class ApproveRetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('approve-retail.index');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $approveRetail = RequestToRetail::with('products')->findOrFail($id);
        $selectedProducts = collect($approveRetail->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('approve-retail.show', [
            'approve_retail' => $approveRetail,
        ]);
    }

    public function approve($id)
    {
        $approveRetail = RequestToRetail::findOrFail($id);
        $selectedProducts = collect($approveRetail->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('approve-retail.approve', [
            'approve_retail' => $approveRetail,
        ]);
    }

    public function approved(Request $request, $id)
    {
        $approveRetail = RequestToRetail::findOrFail($id);
        $approveRetail->code = $request->code;
        $approveRetail->date = $request->date;
        $approveRetail->status = "approved";
        $products = $request->selected_products;

        try {
            $approveRetail->save();
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
                    'retail_stock' => $item['retail_stock'],
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $approveRetail->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $approveRetail,
            // ]);
        } catch (Exception $e) {
            $approveRetail->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $approveRetail->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $approveRetail->delete();
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

                $productRow->retail_stock = $productRow->retail_stock - $product['quantity'];
                $productRow->central_stock = $productRow->central_stock + $product['quantity'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $approveRetail,
            ]);
        } catch (Exception $e) {
            $approveRetail->products()->detach();
            $approveRetail->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
    }

    public function rejected(Request $request, $id)
    {
        $approveRetail = RequestToRetail::findOrFail($id);
        $approveRetail->code = $request->code;
        $approveRetail->date = $request->date;
        $approveRetail->status = "rejected";
        $products = $request->selected_products;

        try {
            $approveRetail->save();
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
                    'retail_stock' => $item['retail_stock'],
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $approveRetail->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $approveRetail,
            // ]);
        } catch (Exception $e) {
            $approveRetail->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $approveRetail->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $approveRetail->delete();
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

                // $productRow->retail_stock = $productRow->retail_stock - $product['quantity'];
                // $productRow->central_stock = $productRow->central_stock + $product['quantity'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $approveRetail,
            ]);
        } catch (Exception $e) {
            $approveRetail->products()->detach();
            $approveRetail->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
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
        //
    }

    public function datatableApproveRetail()
    {
        $approveRetail = RequestToRetail::all();
        return DataTables::of($approveRetail)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $pending = "<a href='/approve-retail/approve/{$row->id}' class='btn btn-outline-warning btn-sm'>
                <span>Pending</span>
                </a>";
                $approved = "<span class='badge badge-outline-success text-success'>Approved</span>";
                $rejected = "<span class='badge badge-outline-danger text-danger'>Rejected</span>";
                $button = $row->status;
                if ($button == 'approved') {
                    return $approved;
                }
                if ($button == 'pending') {
                    return $pending;
                } else {
                    return $rejected;
                }
            })
            ->addColumn('action', function ($row) {
                $show = '<a href="/approve-retail/show/' . $row->id . '" class="btn btn-outline-warning btn-sm"><em class="icon fas fa-eye"></em>
                <span>Detail</span>
            </a>';
                $button = ".$show.";
                return $button;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function datatableProduct()
    {
        $product = Product::all();
        return DataTables::of($product)
            ->addIndexColumn()
            ->addColumn('action', function () {
                $button = '<button class="btn btn-outline-primary btn-sm btn-choose"><em class="fas fa-plus"></em>&nbsp;Pilih</button>';
                return $button;
            })
            ->make(true);
    }
}

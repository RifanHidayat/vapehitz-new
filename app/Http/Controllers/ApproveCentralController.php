<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RetailRequestToCentral;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ApproveCentralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('approve-central-retail.index');
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
        $approveCentral = RetailRequestToCentral::with('products')->findOrFail($id);
        $selectedProducts = collect($approveCentral->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('approve-central-retail.show', [
            'approve_central' => $approveCentral,
        ]);
    }

    public function approve($id)
    {
        $approveCentral = RetailRequestToCentral::findOrFail($id);
        $selectedProducts = collect($approveCentral->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('approve-central-retail.approve', [
            'approve_central' => $approveCentral,
        ]);
    }

    public function approved(Request $request, $id)
    {
        $approveCentral = RetailRequestToCentral::findOrFail($id);
        $approveCentral->code = $request->code;
        $approveCentral->date = $request->date;
        $approveCentral->status = "approved";
        $products = $request->selected_products;

        try {
            $approveCentral->save();
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
                    'central_stock' => $item['central_stock'],
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $approveCentral->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $approveCentral,
            // ]);
        } catch (Exception $e) {
            $approveCentral->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $approveCentral->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $approveCentral->delete();
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

                $productRow->central_stock = $productRow->central_stock - $product['quantity'];
                $productRow->retail_stock = $productRow->retail_stock + $product['quantity'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $approveCentral,
            ]);
        } catch (Exception $e) {
            $approveCentral->products()->detach();
            $approveCentral->delete();
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
        $approveCentral = RetailRequestToCentral::findOrFail($id);
        $approveCentral->code = $request->code;
        $approveCentral->date = $request->date;
        $approveCentral->status = "rejected";
        $products = $request->selected_products;

        try {
            $approveCentral->save();
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
                    'central_stock' => $item['central_stock'],
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $approveCentral->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $approveCentral,
            // ]);
        } catch (Exception $e) {
            $approveCentral->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $approveCentral->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $approveCentral->delete();
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
                'data' => $approveCentral,
            ]);
        } catch (Exception $e) {
            $approveCentral->products()->detach();
            $approveCentral->delete();
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

    public function datatableApproveCentral()
    {
        $approveCentral = RetailRequestToCentral::all();
        return DataTables::of($approveCentral)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $pending = "<a href='/approve-central-retail/approve/{$row->id}' class='btn btn-outline-warning btn-sm'>
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
                $show = '<a href="/approve-central-retail/show/' . $row->id . '" class="btn btn-outline-light btn-sm"><em class="icon fas fa-eye"></em>
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

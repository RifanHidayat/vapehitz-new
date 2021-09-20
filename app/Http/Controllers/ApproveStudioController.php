<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RequestToStudio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ApproveStudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('approve-studio.index');
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
        $approveStudio = RequestToStudio::with('products')->findOrFail($id);
        $selectedProducts = collect($approveStudio->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('approve-studio.show', [
            'approve_studio' => $approveStudio,
        ]);
    }

    public function approve($id)
    {
        $approveStudio = RequestToStudio::findOrFail($id);
        $selectedProducts = collect($approveStudio->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('approve-studio.approve', [
            'approve_studio' => $approveStudio,
        ]);
    }

    public function approved(Request $request, $id)
    {
        $approveStudio = RequestToStudio::findOrFail($id);
        $approveStudio->code = $request->code;
        $approveStudio->date = $request->date;
        $approveStudio->status = "approved";
        $products = $request->selected_products;

        try {
            $approveStudio->save();
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
                    'studio_stock' => $item['studio_stock'],
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $approveStudio->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $approveStudio,
            // ]);
        } catch (Exception $e) {
            $approveStudio->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $approveStudio->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $approveStudio->delete();
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

                $productRow->studio_stock = $productRow->studio_stock + $product['quantity'];
                $productRow->central_stock = $productRow->central_stock - $product['quantity'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $approveStudio,
            ]);
        } catch (Exception $e) {
            $approveStudio->products()->detach();
            $approveStudio->delete();
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
        $approveStudio = RequestToStudio::findOrFail($id);
        $approveStudio->code = $request->code;
        $approveStudio->date = $request->date;
        $approveStudio->status = "rejected";
        $products = $request->selected_products;

        try {
            $approveStudio->save();
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
                    'studio_stock' => $item['studio_stock'],
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $approveStudio->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $approveStudio,
            // ]);
        } catch (Exception $e) {
            $approveStudio->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $approveStudio->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $approveStudio->delete();
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

                // $productRow->studio_stock = $productRow->studio_stock - $product['quantity'];
                // $productRow->central_stock = $productRow->central_stock + $product['quantity'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $approveStudio,
            ]);
        } catch (Exception $e) {
            $approveStudio->products()->detach();
            $approveStudio->delete();
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

    public function datatableApproveStudio()
    {
        $approveStudio = RequestToStudio::all();
        return DataTables::of($approveStudio)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $pending = "<a href='/approve-studio/approve/{$row->id}' class='btn btn-outline-warning btn-sm'>
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
                $show = '<a href="/approve-studio/show/' . $row->id . '" class="btn btn-outline-warning btn-sm"><em class="icon fas fa-eye"></em>
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

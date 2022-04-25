<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Exports\ApproveCentralStudioExport;
use App\Models\StudioRequestToCentral;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ApproveCentralStudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('approve-central-studio.index');
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
        $approveCentral = StudioRequestToCentral::with('products')->findOrFail($id);
        $selectedProducts = collect($approveCentral->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
             $product['studio_stock'] = $product->pivot->studio_stock;
        });
        return view('approve-central-studio.show', [
            'approve_central' => $approveCentral,
        ]);
    }

    public function print($id)
    {
        // return view('central-sale.print');
        $req = StudioRequestToCentral::with(['products'])->findOrFail($id);

        $data = [
            'req' => $req,
        ];

        $pdf = PDF::loadView('studio-request-to-central.print', $data);
        return $pdf->stream($req->code . '.pdf');
    }


    public function approve($id)
    {
        
        $approveCentral = StudioRequestToCentral::findOrFail($id);
        $selectedProducts = collect($approveCentral->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('approve-central-studio.approve', [
            'approve_central' => $approveCentral,
        ]);
    }
    
    
    public function export(){
      
           
        return Excel::download(new ApproveCentralStudioExport(), "Permintaan dari studio" . '.xlsx');
       
    }

    public function approved(Request $request, $id)
    {
         DB::beginTransaction();
        $approveCentral = StudioRequestToCentral::findOrFail($id);
        $approveCentral->code = $request->code;
        $approveCentral->date = $request->date;
        $approveCentral->status = "approved";
        $products = $request->selected_products;

        //return $products;

        try {
            $approveCentral->save();
        } catch (Exception $e) {
              DB::rollBack();
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
              DB::rollBack();
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
              DB::rollBack();
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
                $productRow->studio_stock = $productRow->studio_stock + $product['quantity'];
                $productRow->save();
            }
            DB::commit();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $approveCentral,
            ]);
        } catch (Exception $e) {
            $approveCentral->products()->detach();
              DB::rollBack();
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
        $approveCentral = StudioRequestToCentral::findOrFail($id);
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
                    'studio_stock' => $item['studio_stock'],
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

                // $productRow->studio_stock = $productRow->studio_stock - $product['quantity'];
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
        $approveCentral = StudioRequestToCentral::all();
        return DataTables::of($approveCentral)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $pending = "<a href='/approve-central-studio/approve/{$row->id}' class='btn btn-outline-warning btn-sm'>
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
                $show = '<a href="/approve-central-studio/show/' . $row->id . '" class="btn btn-outline-light btn-sm"><em class="icon fas fa-eye"></em>
                <span>Detail</span>
            </a>';
            $print = '<a href="/approve-central-studio/print/' . $row->id . '" class="btn btn-outline-light btn-sm"><em class="icon fas fa-print"></em>
            <span>Print</span>
        </a>';
                $button = ".$show $print.";
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

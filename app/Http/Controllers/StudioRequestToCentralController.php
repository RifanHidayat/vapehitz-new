<?php

namespace App\Http\Controllers;

use App\Exports\StudioRequestToCentralExport;
use App\Models\Product;
use App\Models\StudioRequestToCentral;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use PDF;

class StudioRequestToCentralController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('studio-request-to-central.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $maxid = DB::table('studio_request_to_centrals')->max('id');
        $code = "SOGR/VH/" . date("m-y") . "/" . sprintf('%04d', $maxid + 1);
        return view('studio-request-to-central.create', [
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
        $request->validate([
            'date' => 'required',
            'selected_products' => 'required',
        ]);

        $reqtocentral = new StudioRequestToCentral();
        $reqtocentral->code = $request->code;
        $reqtocentral->date = $request->date;
        $products = $request->selected_products;

        try {
            $reqtocentral->save();
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
                    'central_stock' => $item['central_stock'],
                    'studio_stock' => $item['studio_stock'],
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $reqtocentral->products()->attach($keyedProducts);
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $reqtocentral,
            // ]);
        } catch (Exception $e) {
            $reqtocentral->delete();
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

                // Calculate average purchase price
                $productRow->central_stock = $product['central_stock'];
                $productRow->studio_stock = $product['studio_stock'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $reqtocentral,
            ]);
        } catch (Exception $e) {
            $reqtocentral->products()->detach();
            $reqtocentral->delete();
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
        $studioRequestToCentral = StudioRequestToCentral::with('products')->findOrFail($id);
        $selectedProducts = collect($studioRequestToCentral->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('studio-request-to-central.edit', [
            'studio_request_to_central' => $studioRequestToCentral,
        ]);
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
        $studioRequestToCentral = StudioRequestToCentral::findOrFail($id);
        try {
            $studioRequestToCentral->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $studioRequestToCentral,
            // ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $studioRequestToCentral->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $studioRequestToCentral,
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


    public function print($id)
    {
        // return view('central-sale.print');
        $req = StudioRequestToCentral::with(['products'])->findOrFail($id);

        $data = [
            'req' => $req,
        ];

        $pdf = PDF::loadView('retail-request-to-central.print', $data);
        return $pdf->stream($req->code . '.pdf');
    }

    public function excel($id)
    {
        return Excel::download(new StudioRequestToCentralExport($id), 'Permintaan Ke Pusat.xlsx');
    }

    public function datatableStudioRequestToCentral()
    {
        $reqtocentral = StudioRequestToCentral::all();
        return DataTables::of($reqtocentral)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $pending = "<span class='badge badge-outline-warning text-warning'>Pending</span>";
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
                $edit = '
                <a href="/studio-request-to-central/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                    <span>Edit</span>
                </a>';
                $delete = '<a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                   <span>Delete</span>
                   </a>';
                $print = '<a href="/retail-request-to-central/print/' . $row->id . '" target="_blank"><em class="icon fas fa-print"></em>
                   <span>Cetak</span>
                   </a>';
                $excel = '<a href="/studio-request-to-central/excel/' . $row->id . '" target="_blank"><em class="icon fas fa-th"></em>
                   <span>Excel</span>
                   </a>';
                $button = '
                   <div class="dropdown">
                   <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                   <div class="dropdown-menu dropdown-menu-right">
                       <ul class="link-list-opt no-bdr">
                           ' . $edit . '
                           ' . $delete . '
                           ' . $print . '
                           ' . $excel . '
                       </ul>
                   </div>
                   </div>';
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

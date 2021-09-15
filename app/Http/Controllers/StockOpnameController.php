<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOpname;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_stock_opname", $permission)) {
            return redirect("/dashboard");
        }
        $stock_opname = StockOpname::with('products')->get();
        return view('stock-opname.index', [
            'stock_opname' => $stock_opname,
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
        if (!in_array("add_stock_opname", $permission)) {
            return redirect("/dashboard");
        }
        $maxid = DB::table('stock_opnames')->max('id');
        $code = "SOGP" . "-" . sprintf('%04d', $maxid + 1) . "/" . "VH" . "/" . date('d-y');
        // return $code;
        return view('stock-opname.create', [
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
            'code' => 'required',
            'date' => 'required',
            'selected_products' => 'required',
        ]);
        $stockOpname = new StockOpname;
        $stockOpname->code = $request->code;
        $stockOpname->date = $request->date;
        $stockOpname->note = $request->note;
        $products = $request->selected_products;

        try {
            $stockOpname->save();
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
                    'good_stock' => $item['good_stock'],
                    'bad_stock' => $item['bad_stock'],
                    'description' => $item['description'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $stockOpname->products()->attach($keyedProducts);
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $stockOpname,
            // ]);
        } catch (Exception $e) {
            $stockOpname->delete();
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
                $productRow->central_stock = $product['good_stock'];
                $productRow->bad_stock = $product['bad_stock'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $stockOpname,
            ]);
        } catch (Exception $e) {
            $stockOpname->products()->detach();
            $stockOpname->delete();
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_stock_opname", $permission)) {
            return redirect("/dashboard");
        }
        $stockOpname = StockOpname::with('products')->findOrFail($id);
        $selectedProducts = collect($stockOpname->products)->each(function ($product) {
            $product['good_stock'] = $product->pivot->good_stock;
            $product['bad_stock'] = $product->pivot->bad_stock;
            $product['description'] = $product->pivot->description;
        });
        return view('stock-opname.show', [
            'stockOpname' => $stockOpname,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $stockOpname = StockOpname::with('products')->findOrFail($id);
        $selectedProducts = collect($stockOpname->products)->each(function ($product) {
            $product['good_stock'] = $product->pivot->good_stock;
            $product['bad_stock'] = $product->pivot->bad_stock;
            $product['description'] = $product->pivot->description;
        });
        return view('stock-opname.edit', [
            'stockOpname' => $stockOpname,
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
        $stockOpname = StockOpname::findOrFail($id);
        $stockOpname->code = $request->code;
        $stockOpname->date = $request->date;
        $stockOpname->note = $request->note;
        $products = $request->selected_products;
        try {
            $stockOpname->save();
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
                    'good_stock' => $item['good_stock'],
                    'bad_stock' => $item['bad_stock'],
                    'description' => $item['description'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $stockOpname->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $stockOpname,
            // ]);
        } catch (Exception $e) {
            $stockOpname->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $stockOpname->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $stockOpname->delete();
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
                $productRow->central_stock = $product['good_stock'];
                $productRow->bad_stock = $product['bad_stock'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $stockOpname,
            ]);
        } catch (Exception $e) {
            $stockOpname->products()->detach();
            $stockOpname->delete();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $stockOpname = StockOpname::findOrFail($id);
        try {
            $stockOpname->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $stockOpname,
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

    public function datatableProducts()
    {
        $products = Product::with('productCategory')->with('productSubcategory')->get();
        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('action', function () {
                $button = '<button class="btn btn-outline-primary btn-sm btn-choose"><em class="fas fa-plus"></em>&nbsp;Pilih</button>';
                return $button;
            })
            ->make(true);
    }
    // <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
    //  <span>Delete</span>
    // </a>
    public function datatableStockOpname()
    {
        $stockOpname = StockOpname::with('products')->select('stock_opnames.*');
        return DataTables::eloquent($stockOpname)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '
                <a href="/stock-opname/show/' . $row->id . '" class="btn btn-outline-success btn-sm"><em class="icon fas fa-eye"></em>
                    <span>Detail</span>
                </a>';
                return $button;
            })
            ->make();
    }
}

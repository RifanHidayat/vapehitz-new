<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOpnameRetail;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameRetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_sop_retail", $permission)) {
            return redirect("/dashboard");
        }
        return view('stock-opname-retail.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("add_sop_retail", $permission)) {
            return redirect("/dashboard");
        }
        $maxid = DB::table('stock_opname_retails')->max('id');
        $code = "SOGR/VH/" . date('d-y') . "/" . sprintf('%04d', $maxid + 1);
        return view('stock-opname-retail.create', [
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
        $stockOpnameRetail = new StockOpnameRetail();
        $stockOpnameRetail->code = $request->code;
        $stockOpnameRetail->date = $request->date;
        $stockOpnameRetail->note = $request->note;
        $products = $request->selected_products;

        try {
            $stockOpnameRetail->save();
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
                    'description' => $item['description'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $stockOpnameRetail->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $stockOpnameRetail,
            // ]);
        } catch (Exception $e) {
            $stockOpnameRetail->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $stockOpnameRetail->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $stockOpnameRetail->delete();
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
                $productRow->retail_stock = $product['good_stock'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $stockOpnameRetail,
            ]);
        } catch (Exception $e) {
            $stockOpnameRetail->products()->detach();
            $stockOpnameRetail->delete();
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
        if (!in_array("view_sop_retail", $permission)) {
            return redirect("/dashboard");
        }
        $stockOpnameRetail = StockOpnameRetail::with('products')->findOrFail($id);
        $selectedProducts = collect($stockOpnameRetail->products)->each(function ($product) {
            $product['good_stock'] = $product->pivot->good_stock;
            $product['description'] = $product->pivot->description;
        });
        return view('stock-opname-retail.show', [
            'stockOpnameRetail' => $stockOpnameRetail,
            'selected_products' => $selectedProducts,
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("edit_sop_retail", $permission)) {
            return redirect("/dashboard");
        }
        $stockOpnameRetail = StockOpnameRetail::with('products')->findOrFail($id);
        $selectedProducts = collect($stockOpnameRetail->products)->each(function ($product) {
            $product['good_stock'] = $product->pivot->good_stock;
            $product['description'] = $product->pivot->description;
        });
        return view('stock-opname-retail.edit', [
            'stockOpnameRetail' => $stockOpnameRetail,
            'selected_products' => $selectedProducts,
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
        $stockOpnameRetail = StockOpnameRetail::findOrFail($id);
        $stockOpnameRetail->code = $request->code;
        $stockOpnameRetail->date = $request->date;
        $stockOpnameRetail->note = $request->note;
        $products = $request->selected_products;
        try {
            $stockOpnameRetail->save();
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
                    'description' => $item['description'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $stockOpnameRetail->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $stockOpnameRetail,
            // ]);
        } catch (Exception $e) {
            $stockOpnameRetail->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $stockOpnameRetail->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $stockOpnameRetail->delete();
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

                $productRow->retail_stock = $product['good_stock'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $stockOpnameRetail,
            ]);
        } catch (Exception $e) {
            $stockOpnameRetail->products()->detach();
            $stockOpnameRetail->delete();
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("delete_sop_retail", $permission)) {
            return redirect("/dashboard");
        }
        $stockOpnameRetail = StockOpnameRetail::findOrFail($id);
        try {
            $stockOpnameRetail->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $stockOpnameRetail,
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

    public function datatableStockOpnameRetail()
    {
        $stockOpnameRetail = StockOpnameRetail::with('products')->select('stock_opname_retails.*');
        return DataTables::eloquent($stockOpnameRetail)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '
                    <a href="/retail-stock-opname/show/' . $row->id . '" class="btn btn-outline-success btn-sm"><em class="icon fas fa-eye"></em>
                        <span>Detail</span>
                    </a>';
                return $button;
            })
            ->make();
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
}

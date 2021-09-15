<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockOpnameStudio;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class StockOpnameStudioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_sop_studio", $permission)) {
            return redirect("/dashboard");
        }
        return view('stock-opname-studio.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("add_sop_studio", $permission)) {
            return redirect("/dashboard");
        }
        $maxid = DB::table('stock_opname_studios')->max('id');
        $code = "SOGS/VH/" . date('d-y') . "/" . sprintf('%04d', $maxid + 1);
        return view('stock-opname-studio.create', [
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
        $stockOpnameStudio = new StockOpnameStudio();
        $stockOpnameStudio->code = $request->code;
        $stockOpnameStudio->date = $request->date;
        $stockOpnameStudio->note = $request->note;
        $products = $request->selected_products;

        try {
            $stockOpnameStudio->save();
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
            $stockOpnameStudio->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $stockOpnameStudio,
            // ]);
        } catch (Exception $e) {
            $stockOpnameStudio->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $stockOpnameStudio->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $stockOpnameStudio->delete();
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
                $productRow->studio_stock = $product['good_stock'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $stockOpnameStudio,
            ]);
        } catch (Exception $e) {
            $stockOpnameStudio->products()->detach();
            $stockOpnameStudio->delete();
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
        if (!in_array("view_sop_studio", $permission)) {
            return redirect("/dashboard");
        }
        $stockOpnameStudio = StockOpnameStudio::with('products')->findOrFail($id);
        $selectedProducts = collect($stockOpnameStudio->products)->each(function ($product) {
            $product['good_stock'] = $product->pivot->good_stock;
            $product['description'] = $product->pivot->description;
        });
        return view('stock-opname-studio.show', [
            'stockOpnameStudio' => $stockOpnameStudio,
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
        if (!in_array("edit_sop_studio", $permission)) {
            return redirect("/dashboard");
        }
        $stockOpnameStudio = StockOpnameStudio::with('products')->findOrFail($id);
        $selectedProducts = collect($stockOpnameStudio->products)->each(function ($product) {
            $product['good_stock'] = $product->pivot->good_stock;
            $product['description'] = $product->pivot->description;
        });
        return view('stock-opname-studio.edit', [
            'stockOpnameStudio' => $stockOpnameStudio,
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
        $stockOpnameStudio = StockOpnameStudio::findOrFail($id);
        $stockOpnameStudio->code = $request->code;
        $stockOpnameStudio->date = $request->date;
        $stockOpnameStudio->note = $request->note;
        $products = $request->selected_products;
        try {
            $stockOpnameStudio->save();
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
            $stockOpnameStudio->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $stockOpnameStudio,
            // ]);
        } catch (Exception $e) {
            $stockOpnameStudio->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $stockOpnameStudio->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $stockOpnameStudio->delete();
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

                $productRow->studio_stock = $product['good_stock'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $stockOpnameStudio,
            ]);
        } catch (Exception $e) {
            $stockOpnameStudio->products()->detach();
            $stockOpnameStudio->delete();
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("delete_sop_studio", $permission)) {
            return redirect("/dashboard");
        }
        $stockOpnameStudio = StockOpnameStudio::findOrFail($id);
        try {
            $stockOpnameStudio->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $stockOpnameStudio,
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

    public function datatableStockOpnameStudio()
    {
        $stockOpnameStudio = StockOpnameStudio::with('products')->select('stock_opname_studios.*');
        return DataTables::eloquent($stockOpnameStudio)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '
                    <a href="/studio-stock-opname/show/' . $row->id . '" class="btn btn-outline-success btn-sm"><em class="icon fas fa-eye"></em>
                        <span>Detail</span>
                    </a>
                    ';
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

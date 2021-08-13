<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $product = Product::all();
        // $product = DB::table('products')
        //     ->join('product_categories', 'products.product_category_id', '=', 'product_categories.id')
        //     ->select('products.*', 'product_categories.name')
        //     ->get();
        $products = Product::with('productCategory')->get();
        return view('product.index', [
            'products' => $products,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $productCategories = ProductCategory::all();
        $productSubcategories = ProductSubcategory::all();
        $maxid = DB::table('products')->max('id');
        $code = sprintf('%05d', $maxid + 1);
        return view('product.create', [
            'product_categories' => $productCategories,
            'product_subcategories' => $productSubcategories,
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
            'code' => 'required|unique:products',
        ]));

        $product = new Product;
        $product->name = $request->name;
        $product->code = $request->code;
        $product->product_category_id = $request->product_category_id;
        $product->product_subcategory_id = $request->product_subcategory_id;
        $product->weight = $request->weight;
        $product->central_stock = $request->central_stock;
        $product->retail_stock = $request->retail_stock;
        $product->studio_stock = $request->studio_stock;
        $product->bad_stock = $request->bad_stock;
        $product->purchase_price = str_replace(".", "", $request->purchase_price);
        $product->agent_price = str_replace(".", "", $request->agent_price);
        $product->ws_price = str_replace(".", "", $request->ws_price);
        $product->retail_price = str_replace(".", "", $request->retail_price);
        $product->status = $request->status;
        $product->is_changeable = $request->is_changeable;
        try {
            $product->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $product,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $products = Product::findOrFail($id);
        return view('product.show', [
            'products' => $products,
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
        $product = Product::findOrFail($id);
        $productCategories = ProductCategory::all();
        $productSubcategories = ProductSubcategory::all();
        return view('product.edit', [
            'product_categories' => $productCategories,
            'product_subcategories' => $productSubcategories,
            'product' => $product,
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
        $request->validate(([
            'name' => 'required',
        ]));

        $product = Product::find($id);
        $product->name = $request->name;
        $product->code = $request->code;
        $product->product_category_id = $request->product_category_id;
        $product->product_subcategory_id = $request->product_subcategory_id;
        $product->weight = $request->weight;
        $product->central_stock = $request->central_stock;
        $product->retail_stock = $request->retail_stock;
        $product->studio_stock = $request->studio_stock;
        $product->bad_stock = $request->bad_stock;
        $product->purchase_price = $request->purchase_price;
        $product->agent_price = $request->agent_price;
        $product->ws_price = $request->ws_price;
        $product->retail_price = $request->retail_price;
        $product->status = $request->status;
        $product->is_changeable = $request->is_changeable;

        try {
            $product->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $product,
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        try {
            $product->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $product,
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
}

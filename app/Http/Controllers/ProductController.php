<?php

namespace App\Http\Controllers;

use Exception;
use App\Exports\ProductExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductSubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_product", $permission)) {
            return redirect("/dashboard");
        }
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("add_product", $permission)) {
            return redirect("/dashboard");
        }

        $productCategories = ProductCategory::all();
        $productSubcategories = ProductSubcategory::with('productCategory')->get();

        // return $productSubcategories;
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_product", $permission)) {
            return redirect("/dashboard");
        }
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("edit_product", $permission)) {
            return redirect("/dashboard");
        }
        $product = Product::findOrFail($id);
        $productCategories = ProductCategory::all();
        $productSubcategories = ProductSubcategory::all();
        return view('product.edit', [
            'product_categories' => $productCategories,
            'product_subcategories' => $productSubcategories,
            'product' => $product,
        ]);
    }
      public function export(Request $request){
       
        return Excel::download(new ProductExport(), 'products.xlsx');
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    public function upload()
    {
        return view('product.upload');  
    }
    public function doUpload(Request $request)
    {        
        if ($request->hasFile('file')) {
            try {   
                $rejectedData = [];
                $rowIndex = 0;
                $importData = Excel::toCollection(collect([]), $request->file('file'));
                $finalImportData = $importData[0]->filter(function ($data, $key) {
                    return $key !== 0;
                })
                    ->map(function ($data, $key) use (&$rejectedData, &$rowIndex) {
                        $rowIndex = $key;
                            $productCategoryId=$data[0];
                        $productSubCategoryId=$data[1];
                        $productCategory=ProductCategory::findOrFail($productCategoryId);
                        $productSubCategory=ProductSubcategory::findOrFail($productSubCategoryId);
                    
                        $name=$data[3];
                        $weight=$data[4];
                        $centralStock=$data[5];
                        $retailStock=$data[6];
                        $studioStock=$data[7];
                        $badStock=$data[8];
                        $booked=$data[9];
                        $purchasePrice=$data[10];
                        $puechaseAgen=$data[11];
                        $priceWS=$data[12];
                        $retailPrice=$data[13];
                      
                        $prodcutCategoryCollect=collect($productCategory)->where('id',$productCategoryId);
                        $productSubCategoryCollect=collect($productSubCategory)->where('id',$productSubCategoryId);
                       
                        // $code = sprintf('%05d', $data[2]);
                        // $productCode=$prodcutCategoryCollect[0]['code'].'-'.$productSubCategoryCollect[0]['code'].'-'.$code;
                        
                         $code = sprintf('%05d', $data[2]);
                        $productCode=$productCategory['code'].'-'.$productSubCategory['code'].'-'.$code;
                       
                       
                        $record = [];
                        array_push($record, [
                        'product_category_id' => $productCategoryId,
                            'product_subcategory_id' => $productSubCategoryId,
                            'code' => $productCode,
                            'name' => $name,
                            'weight' => $weight,
                            'central_stock' => $centralStock,
                            'retail_stock' => $retailStock,
                            'studio_stock' => $studioStock,
                            'bad_stock'=>$badStock,
                            'booked'=>$booked,
                            'purchase_price'=>$purchasePrice,
                            'agent_price'=>$puechaseAgen,
                            'ws_price'=>$priceWS,
                            'retail_price'=>$retailPrice,
                            'status'=>"1",
                            'is_changeable'=>"1"
                        ]);

                  
                        return $record;
                    })
                    ->flatMap(function ($data) {
                        return $data;
                    })
                    ->filter(function ($data) {
                        return $data['error'] = true;
                    })
                    ->all();

                DB::table("products")->insert($finalImportData);
                // $flatImportData->all();s

                // ->function;
                // $importData->each(function ($data, $key) {
                //     $UNIX_DATE = ((int) $data[1] - 25569) * 86400;
                //     $date_column = gmdate("Y-m-d", $UNIX_DATE);
                //     $data[1] = $date_column;
                // });
                // $importData = $importData->flatten();

                return response()->json([
                    'message' => 'file received',
                    'error' => false,
                    'code' => 200,
                    'data' => [
                        'accepted' => $finalImportData,
                        'rejected' => $rejectedData,
                        'row_index' => $rowIndex,
                    ],
                ], 200);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Error while uploading',
                    'error' => true,
                    'code' => 500,
                    'errors' => "".$e,
                ], 500);
            }
        }

        return response()->json([
            'message' => 'no file was sent',
            'error' => true,
            'code' => 500,
            // 'errors' => $e,
        ], 500);
    }
      public function uploadUpdate()
    {
        return view('product.update.upload');  
    }

   public function doUploadUpdate(Request $request)
    {       // return "tes";
          DB::beginTransaction();
        if ($request->hasFile('file')) {
            try {   
                $rejectedData = [];
                $rowIndex = 0;
                $importData = Excel::toCollection(collect([]), $request->file('file'));
                $finalImportData = $importData[0]->filter(function ($data, $key) {
                    return $key !== 0;
                })
                    ->map(function ($data, $key) use (&$rejectedData, &$rowIndex) {
                        $rowIndex = $key;
                        $id=$data[0];
                        $weight=$data[3]; 
                        $centralStock=$data[4];
                        $retailStock=$data[5];
                        $studioStock=$data[6];
                        $badStock=$data[7];
                        $booked=$data[8];
                        $purchasePrice=$data[9];
                        $puechaseAgen=$data[10];
                        $priceWS=$data[11];
                        $retailPrice=$data[12];
                        

                        $product=Product::find($id);
                        $product->weight=$weight;
                        $product->central_stock=$centralStock;
                        $product->retail_stock=$retailStock;
                        $product->studio_stock=$studioStock;
                        $product->bad_stock=$badStock;
                        $product->purchase_price=$purchasePrice;
                        $product->agent_price=$puechaseAgen;
                        $product->ws_price=$priceWS;
                        $product->retail_price=$retailPrice;
                        $product->booked=$booked;
                        $product->save();    
                       
                        $record = [];
                        array_push($record, [
                            
                            'weight' => $weight,
                            'central_stock' => $centralStock,
                            'retail_stock' => $retailStock,
                            'studio_stock' => $studioStock,
                            'bad_stock'=>$badStock,
                            'booked'=>$booked,
                            'purchase_price'=>$purchasePrice,
                            'agent_price'=>$puechaseAgen,
                            'ws_price'=>$priceWS,
                            'retail_price'=>$retailPrice,
                          
                        ]);

                  
                        return $record;
                    })
                    ->flatMap(function ($data) {
                        return $data;
                    })
                    ->filter(function ($data) {
                        return $data['error'] = true;
                    })
                    ->all();

       
                 DB::commit();

                return response()->json([
                    'message' => 'file received',
                    'error' => false,
                    'code' => 200,
                    'data' => [
                        'accepted' => $finalImportData,
                        'rejected' => $rejectedData,
                        'row_index' => $rowIndex,
                    ],
                ], 200);
            } catch (Exception $e) {
                 DB::rollBack();
                return response()->json([
                    'message' => 'Error while uploading',
                    'error' => true,
                    'code' => 500,
                    'errors' => "".$e,
                ], 500);
            }
        }
           DB::rollBack();

        return response()->json([
            'message' => 'no file was sent',
            'error' => true,
            'code' => 500,
            // 'errors' => $e,
        ], 500);
    }


    public function destroy($id)
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("delete_product", $permission)) {
            return redirect("/dashboard");
        }
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

    public function datatableProducts()
    {
        $products = Product::with(['productCategory', 'productSubcategory'])->get();
        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $permission = json_decode(Auth::user()->group->permission);
                if (in_array("edit_product", $permission)) {
                    $edit = '<a href="/product/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                    <span>Edit</span>
                </a>';
                } else {
                    $edit = "";
                }
                if (in_array("delete_product", $permission)) {
                    $delete = '<a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                    <span>Delete</span>
                    </a>';
                } else {
                    $delete = "";
                }
                if (in_array("view_product", $permission)) {
                    $show = '<a href="/product/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                    <span>Detail</span>
                </a>';
                } else {
                    $show = "";
                }
                $button = '
            <div class="dropright">
            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
            <div class="dropdown-menu dropdown-menu-right">
                <ul class="link-list-opt no-bdr">
                ' . $edit . '
                ' . $delete . '
                ' . $show . '
                </ul>
            </div>
            </div>';
                return $button;
            })
            ->make();
    }
}

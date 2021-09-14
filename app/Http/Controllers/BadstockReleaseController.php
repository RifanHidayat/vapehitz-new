<?php

namespace App\Http\Controllers;

use App\Models\BadstockRelease;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class BadstockReleaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_badstock_release", $permission)) {
            return redirect("/dashboard");
        }
        $badstock = BadstockRelease::all();
        return view('badstock-release.index', [
            'badstock' => $badstock,
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
        if (!in_array("add_badstock_release", $permission)) {
            return redirect("/dashboard");
        }
        $maxid = DB::table('badstock_releases')->max('id');
        $code = "BS/VH/" . date('Y-m') . "/" . sprintf('%04d', $maxid + 1);
        return view('badstock-release.create', [
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

        // if ($request->hasFile('image')) {
        //     return response()->json([
        //         'status' => 'hasFile',
        //     ]);
        // } else {
        //     return response()->json([
        //         'status' => 'noFile',
        //     ]);
        // }

        $request->validate([
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();
        $newFileName = time() . '-' . $originalName;
        $path = $file->move(public_path('images'), $newFileName);

        $badstockRelease = new BadstockRelease;
        $badstockRelease->code = $request->code;
        $badstockRelease->date = $request->date;
        $badstockRelease->image = 'images/' . $newFileName;
        // dd($badstockRelease->image);
        $products = json_decode($request->selected_products);

        // return response()->json([
        //     'status' => 'ok',
        //     'product' => $products
        // ]);

        try {
            $badstockRelease->save();
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
                $item->id => [
                    'bad_stock' => $item->bad_stock,
                    'quantity' => $item->quantity,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $badstockRelease->products()->attach($keyedProducts);
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $badstockRelease,
            // ]);
        } catch (Exception $e) {
            $badstockRelease->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        try {
            foreach ($products as $product) {
                $productRow = Product::find($product->id);
                if ($productRow == null) {
                    continue;
                }

                // Calculate average purchase price
                $productRow->bad_stock = $product->bad_stock;
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $badstockRelease,
            ]);
        } catch (Exception $e) {
            $badstockRelease->products()->detach();
            $badstockRelease->delete();
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
        if (!in_array("view_badstock_release", $permission)) {
            return redirect("/dashboard");
        }
        $badstock = BadstockRelease::with('products')->findOrFail($id);
        $selectedProducts = collect($badstock->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('badstock-release.show', [
            'badstock' => $badstock,
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
        if (!in_array("edit_badstock_release", $permission)) {
            return redirect("/dashboard");
        }
        $badstock = BadstockRelease::with('products')->findOrFail($id);
        $selectedProducts = collect($badstock->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('badstock-release.edit', [
            'badstock' => $badstock,
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
        $request->validate([
            'image' => 'nullable|image|mimes:jpg,png,jpeg,gif,svg|max:2048',
        ]);
        $file = $request->file('image');
        $originalName = $file->getClientOriginalName();
        $newFileName = time() . '-' . $originalName;
        $path = $file->move(public_path('images'), $newFileName);

        $badstockRelease = BadstockRelease::findOrFail($id);
        $badstockRelease->code = $request->code;
        $badstockRelease->date = $request->date;
        $badstockRelease->image = 'images/' . $newFileName;
        // dd($badstockRelease->image);
        $products = json_decode($request->selected_products);

        try {
            $badstockRelease->save();
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
                    'bad_stock' => $item->bad_stock,
                    'quantity' => $item->quantity,
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();
        try {
            $badstockRelease->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $badstockRelease,
            // ]);
        } catch (Exception $e) {
            $badstockRelease->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $badstockRelease->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $badstockRelease->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            foreach ($products as $product) {
                $productRow = Product::find($product->id);
                if ($productRow == null) {
                    continue;
                }

                $productRow->bad_stock = $product->bad_stock;
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $badstockRelease,
            ]);
        } catch (Exception $e) {
            $badstockRelease->products()->detach();
            $badstockRelease->delete();
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
        if (!in_array("delete_badstock_release", $permission)) {
            return redirect('/dashboard');
        }
        $badstockRelease = BadstockRelease::findOrFail($id);
        try {
            $badstockRelease->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $badstockRelease,
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

    public function approve($id)
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("approval_badstock_release", $permission)) {
            return redirect("/dashboard");
        }
        $badstockRelease = BadstockRelease::with('products')->findOrFail($id);
        $selectedProducts = collect($badstockRelease->products)->each(function ($product) {
            $product['quantity'] = $product->pivot->quantity;
        });
        return view('badstock-release.approve', [
            'badstockRelease' => $badstockRelease,
        ]);
    }

    public function approved(Request $request, $id)
    {
        $badstockRelease = BadstockRelease::findOrFail($id);
        $badstockRelease->code = $request->code;
        $badstockRelease->date = $request->date;
        $badstockRelease->image = $request->image;
        $badstockRelease->status = "approved";
        $products = $request->selected_products;

        try {
            $badstockRelease->save();
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
                    'bad_stock' => $item['bad_stock'],
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();

        try {
            $badstockRelease->products()->detach();
            // return response()->json([
            //     'message' => 'Data has been saved',
            //     'code' => 200,
            //     'error' => false,
            //     'data' => $badstockRelease,
            // ]);
        } catch (Exception $e) {
            $badstockRelease->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
        try {
            $badstockRelease->products()->attach($keyedProducts);
        } catch (Exception $e) {
            $badstockRelease->delete();
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

                $productRow->bad_stock = $productRow->bad_stock - $product['quantity'];
                $productRow->save();
            }
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $badstockRelease,
            ]);
        } catch (Exception $e) {
            $badstockRelease->products()->detach();
            $badstockRelease->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }
    }

    public function datatableBadstockRelease()
    {
        $badstockRelease = BadstockRelease::all();
        return DataTables::of($badstockRelease)
            ->addIndexColumn()
            ->addColumn('status', function ($row) {
                $button = $row->status;
                if ($button == 'pending') {
                    return "<a href='/badstock-release/approve/{$row->id}' class='btn btn-outline-warning btn-sm'>
                    <span>Pending</span>
                    </a>";
                }
                if ($button == 'approved') {
                    return "<span class='badge badge-outline-success text-success'>Approved</span>";
                } else {
                    return "<span class='badge badge-outline-danger text-danger'>Rejected</span>";
                }
            })
            ->addColumn('action', function ($row) {
                $button = '
                <div class="drodown">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                        <a href="/badstock-release/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                            <span>Edit</span>
                        </a>
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                        </a>
                        <a href="/badstock-release/show/' . $row->id . '"><em class="icon fas fa-eye"></em>
                            <span>Detail</span>
                        </a>
                    </ul>
                </div>
                </div>';
                return $button;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
}

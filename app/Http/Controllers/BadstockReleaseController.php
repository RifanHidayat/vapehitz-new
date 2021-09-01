<?php

namespace App\Http\Controllers;

use App\Models\BadstockRelease;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
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
        $badstockRelease = new BadstockRelease;
        $badstockRelease->code = $request->code;
        $badstockRelease->date = $request->date;
        $badstockRelease->image = $request->image;
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
                $productRow = Product::find($product['id']);
                if ($productRow == null) {
                    continue;
                }

                // Calculate average purchase price
                $productRow->bad_stock = $product['bad_stock'];
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
        $badstock = BadstockRelease::findOrFail($id);
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

    public function datatableBadstockRelease()
    {
        $badstockRelease = BadstockRelease::all();
        return DataTables::of($badstockRelease)
            ->addIndexColumn()
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
                        <a href="#"><em class="icon fas fa-check"></em>
                            <span>Pay</span>
                        </a>
                    </ul>
                </div>
                </div>';
                return $button;
            })
            ->make();
    }
}

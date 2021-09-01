<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ReqToRetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class ReqToRetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $maxid = DB::table('req_to_retails')->max('id');
        // $code = "ROGP/VH/" . date("m-y") . sprintf('%04d', $maxid + 1);
        return view('reqtoretail.index', [
            // 'code' => $code,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $maxid = DB::table('req_to_retails')->max('id');
        $code = "ROGP/VH/" . date("m-y") . "/" . sprintf('%04d', $maxid + 1);
        return view('reqtoretail.create', [
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

    public function datatableReqtoretail()
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

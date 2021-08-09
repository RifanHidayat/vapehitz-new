<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $supplier = Supplier::all();

        return view('supplier.index', [
            'suppliers' => $supplier,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $maxid = DB::table('customers')->max('id');
        $code = "S" . sprintf('%04d', $maxid + 1);
        return view('supplier.create', [
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
            'handphone' => 'nullable|numeric',
        ]));
        $supplier = new Supplier;
        $supplier->name = $request->name;
        $supplier->code = $request->code;
        $supplier->address = $request->address;
        $supplier->telephone = $request->telephone;
        $supplier->handphone = $request->handphone;
        $supplier->email = $request->email;
        $supplier->status = $request->status;
        try {
            $supplier->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $supplier,
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
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        return view('supplier.edit', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $supplier = Supplier::find($id);
        $request->validate(([
            'name' => 'required',
            'handphone' => 'nullable|numeric',
        ]));

        $supplier->name = $request->name;
        $supplier->code = $request->code;
        $supplier->address = $request->address;
        $supplier->telephone = $request->telephone;
        $supplier->handphone = $request->handphone;
        $supplier->email = $request->email;
        $supplier->status = $request->status;

        try {
            $supplier->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $supplier,
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
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        try {
            $supplier->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $supplier,
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

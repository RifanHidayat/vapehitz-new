<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Exception;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer = Customer::all();

        return view('customer.index', [
            'customers' => $customer,
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
        $code = "C" . sprintf('%04d', $maxid + 1);
        return view('customer.create', [
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
        $customer = new Customer;
        $customer->name = $request->name;
        $customer->code = $request->code;
        $customer->address = $request->address;
        $customer->telephone = $request->telephone;
        $customer->handphone = $request->handphone;
        $customer->email = $request->email;
        $customer->status = $request->status;
        try {
            $customer->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $customer,
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
        $customer = Customer::findOrFail($id);

        return view('customer.edit', [
            'customer' => $customer,
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
            'handphone' => 'nullable|numeric',
        ]));
        $customer = Customer::find($id);
        $customer->name = $request->name;
        $customer->code = $request->code;
        $customer->address = $request->address;
        $customer->telephone = $request->telephone;
        $customer->handphone = $request->handphone;
        $customer->email = $request->email;
        $customer->status = $request->status;
        try {
            $customer->save();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $customer,
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
        $customer = Customer::findOrFail($id);
        try {
            $customer->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $customer,
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

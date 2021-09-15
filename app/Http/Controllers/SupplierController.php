<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_supplier", $permission)) {
            return redirect("/dashboard");
        }
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("add_supplier", $permission)) {
            return redirect("/dashboard");
        }
        $maxid = DB::table('suppliers')->max('id');
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

        $code = $request->code;
        if ($code == null) {
            $maxid = DB::table('suppliers')->max('id');
            $code = "S" . sprintf('%04d', $maxid + 1);
        }

        $supplier = new Supplier;
        $supplier->name = $request->name;
        $supplier->code = $code;
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("edit_supplier", $permission)) {
            return redirect("/dashboard");
        }
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("delete_supplier", $permission)) {
            return view("dashboard.index");
        }
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

    public function datatableSuppliers()
    {
        $suppliers = Supplier::all();
        return DataTables::of($suppliers)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $permission = json_decode(Auth::user()->group->permission);
                if (in_array("edit_supplier", $permission)) {
                    $edit = '<a href="/supplier/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                <span>Edit</span>
                </a>';
                } else {
                    $edit = "";
                }
                if (in_array("delete_supplier", $permission)) {
                    $delete = '<a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                   <span>Delete</span>
                   </a>';
                } else {
                    $delete = "";
                }
                $button = '
                <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                    ' . $edit . '
                    ' . $delete . '
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

<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_data_user", $permission)) {
            return redirect("/dashboard");
        }
        $groups = Group::all();
        $user = User::all();
        return view('user.index', [
            'user' => $user,
            'groups' => $groups,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // 
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
        $group = Group::all();
        $user = User::with('group')->findOrFail($id);
        return view('user.show', [
            'user' => $user,
            'group' => $group,
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
        // $permission = json_decode(Auth::user()->group->permission);
        // if (!in_array("edit_data_user", $permission)) {
        //     return redirect("/dashboard");
        // }
        $group = Group::all();
        $user = User::with('group')->findOrFail($id);
        return view('user.edit', [
            'user' => $user,
            'group' => $group,
        ]);
    }

    public function change(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->password = Hash::make($request->password);

        try {
            $user->save();
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->username = $request->username;
        $user->group_id = $request->group;

        try {
            $user->save();
            return redirect("/dashboard")->with('status', 'Data Berhasil di Perbarui');
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
        $user = User::findOrFail($id);
        try {
            $user->delete();
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $user,
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

    public function datatableUser()
    {
        $user = User::with('group')->get();
        return DataTables::of($user)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '
            <div class="dropdown">
            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
            <div class="dropdown-menu dropdown-menu-right">
                <ul class="link-list-opt no-bdr">
                    <a href="/user/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                        <span>Edit</span>
                    </a>
                    <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                    <span>Delete</span>
                    </a>
                </ul>
            </div>
            </div>';
                return $button;
            })
            ->make(true);
    }
}




// $saleReturnProducts = PurchaseReturn::with(['products'])
// ->where('central_purchase_id', $id)
// ->get()
// ->flatMap(function ($saleReturn) {
//     return $saleReturn->products;
// })->groupBy('id')
// ->map(function ($group, $id) {
//     $returnedQuantity = collect($group)->map(function ($product) {
//         return $product->pivot->quantity;
//     })->sum();
//     return [
//         'id' => $id,
//         'returned_quantity' => $returnedQuantity,
//     ];
// })
// ->all();
// // return $purchase->products;
// // return $saleReturnProducts;

// $selectedProducts = collect($purchase->products)->each(function ($product) use ($saleReturnProducts,$purchaseReceiptProducts) {
// $saleReturn = collect($saleReturnProducts)->where('id', $product['id'])->first();
// $receipt=collect($purchaseReceiptProducts)->where('id', $product['id'])->first();
// $product['returned_quantity'] = 0;
// if ($saleReturn !== null) {
//     $product['returned_quantity'] = $saleReturn['returned_quantity'];
// }
// if ($receipt!==null){
//     $product['received_quantity']=$receipt['received_quantity'];
    
// }
// $product['received_quantity']=$receipt['received_quantity'];
//  $availableQuantity = $product->pivot->quantity - $product['returned_quantity'];
// //$availableQuantity = $product['received_quantity'] - $product['returned_quantity'];

// //$product['received_product'] = $purchaseReceiptProducts->po;
// $product['return_quantity'] = $availableQuantity;
// $product['initial_quantity'] = 0;
// $product['cause'] = 'defective';
//  $product['finish'] = $product['returned_quantity'] >= $product->pivot->quantity ? 1 : 0;
// //$product['finish'] = $product['returned_quantity'] >= $product['received_quantity'] ? 1 : 0;
// })->sortBy('finish')->values()->all();

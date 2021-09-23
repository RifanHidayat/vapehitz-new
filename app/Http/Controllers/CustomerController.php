<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\CentralSale;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("view_customer", $permission)) {
            return redirect("/dashboard");
        }
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("add_customer", $permission)) {
            return redirect("/dashboard");
        }
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
            'handphone' => 'nullable',
        ]));
        $customer = new Customer;
        $customer->name = $request->name;
        $customer->code = $request->code;
        $customer->address = $request->address;
        // $customer->telephone = $request->telephone;
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("edit_customer", $permission)) {
            return redirect("/dashboard");
        }
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
            'handphone' => 'nullable',
        ]));
        $customer = Customer::find($id);
        $customer->name = $request->name;
        $customer->code = $request->code;
        $customer->address = $request->address;
        // $customer->telephone = $request->telephone;
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
        $permission = json_decode(Auth::user()->group->permission);
        if (!in_array("delete_customer", $permission)) {
            return redirect("/dashboard");
        }
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

    public function pay($id)
    {
        $customer = Customer::findOrFail($id);

        // $sales = $customer->centralSales;
        $sales = CentralSale::with(['centralSaleTransactions'])
            ->where('customer_id', $customer->id)
            ->orderBy('date', 'DESC')
            // ->where('paid', 0)
            ->get()
            ->each(function ($sale) {
                $sale['total_payment'] = collect($sale->centralSaleTransactions)
                    ->map(function ($transaction) {
                        return $transaction->pivot->amount;
                    })->sum();
            })
            ->filter(function ($sale) {
                return $sale->total_payment < $sale->net_total;
            })
            // ->map(function ($sale) {
            //     return $sale->total_payment;
            // })
            ->values()->all();

        // $sale = CentralSale::with(['customer', 'products'])->findOrFail($id);
        $accounts = Account::all();

        // return $purchase;

        // foreach($purchase->products)
        // $selectedProducts = collect($purchase->products)->each(function ($product) {
        //     $product['quantity'] = $product->pivot->quantity;
        //     $product['purchase_price'] = $product->pivot->price;
        // });

        // return $selectedProducts;
        // $transactions = collect($sale->centralSaleTransactions)->sortBy('date')->values()->all();
        // $totalPaid = collect($sale->centralSaleTransactions)->sum('amount');

        $sidebarClass = 'compact';

        return view('customer.pay', [
            // 'sale' => $sale,
            'accounts' => $accounts,
            'sales' => $sales,
            // 'total_paid' => $totalPaid,
            // 'transactions' => $transactions,
            'sidebar_class' => $sidebarClass,
        ]);
    }

    public function datatableCustomers()
    {
        $customers = Customer::all();
        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $permission = json_decode(Auth::user()->group->permission);
                if (in_array("edit_customer", $permission)) {
                    $edit = '<a href="/customer/edit/' . $row->id . '"><em class="icon fas fa-pencil-alt"></em>
                <span>Edit</span>
                </a>';
                } else {
                    $edit = "";
                }
                if (in_array("delete_customer", $permission)) {
                    $delete = '<a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                   <span>Delete</span>
                   </a>';
                } else {
                    $delete = "";
                }
                $button = '
                <div class="drodown">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                    ' . $edit . '
                    ' . $delete . '
                        <a href="/customer/pay/' . $row->id . '"><em class="icon fas fa-credit-card"></em>
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

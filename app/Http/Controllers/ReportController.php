<?php

namespace App\Http\Controllers;

use App\Models\CentralSale;
use App\Models\Customer;
use App\Models\Shipment;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('report.index');
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

    public function centralSaleByCustomerDetail()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.detail.central-sale-by-customer', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }

    public function centralSaleByCustomerSummary()
    {
        return view('report.sales.summary.central-sale');
    }

    public function centralSaleByCustomerDetailData(Request $request)
    {
        // $columnSelections = explode(',', $request->query('columns'));
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $status = $request->query('status');
        $customer = $request->query('customer');
        $sortBy = $request->query('sort_by');
        $sortIn = $request->query('sort_in');
        // $estimations = Estimation::with(['customer'])->select('estimations.*');
        $query = CentralSale::with(['customer', 'shipment'])->select('central_sales.*')->whereBetween('date', [$startDate, $endDate]);

        if ($status !== '' && $status !== null) {
            $query->where('status', $status);
        }

        if ($customer !== '' && $customer !== null) {
            $query->where('customer_id', $customer);
        }

        if ($sortBy !== '' && $sortBy !== null) {
            $query->orderBy($sortBy, $sortIn);
        }

        $estimations = $query->get();

        return DataTables::of($estimations)
            ->addIndexColumn()
            ->addColumn('shipment_name', function ($row) {
                return ($row->shipment ? $row->shipment->name : "");
            })
            ->addColumn('status', function ($row) {
                // $button = $row->status;
                // if ($button == 'pending') {
                //     return "<a href='/central-sale/approval/{$row->id}' class='btn btn-warning'>
                //     <span>Pending</span>
                //     </a>";
                // }
                // if ($button == 'approved') {
                //     return "Approved";
                // } else {
                //     return "Rejected";
                // }
                $color = 'primary';
                switch ($row->status) {
                    case 'pending':
                        $color = 'warning';
                        break;
                    case 'approved':
                        $color = 'success';
                        break;
                    case 'rejected':
                        $color = 'danger';
                        break;
                    default:
                        $color = 'primary';
                };
                return '<span class="badge badge-' . $color . ' text-capitalize">' . $row->status . '</span>';
            })
            ->addColumn('print_status', function ($row) {
                if ($row->is_printed == 0) {
                    return '<em class="icon ni ni-cross-circle-fill text-danger" style="font-size: 1.5em"></em>';
                } else {
                    return '<em class="icon ni ni-check-circle-fill text-success" style="font-size: 1.5em"></em>';
                }

                return '<em class="icon ni ni-cross-circle-fill text-danger" style="font-size: 1.5em"></em>';
            })
            ->rawColumns(['status', 'print_status'])
            ->make(true);
    }

    public function centralSaleByProductDetail()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.detail.central-sale-by-product', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }

    public function retailSaleDetail()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.detail.retail-sale', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }

    public function studioSaleDetail()
    {
        $customers = Customer::all();
        $shipments = Shipment::all();
        return view('report.sales.detail.studio-sale', [
            'customers' => $customers,
            'shipments' => $shipments,
        ]);
    }
}

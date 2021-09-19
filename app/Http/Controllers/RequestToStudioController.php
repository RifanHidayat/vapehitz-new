<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RequestToStudio;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RequestToStudioController extends Controller
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
        return view('request-to-studio.index', [
            // 'code' => $code,
        ]);
        $product = RequestToStudio::all();
        return $product;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $maxid = DB::table('request_to_studios')->max('id');
        $code = "ROGP/VH/" . date("m-y") . "/" . sprintf('%04d', $maxid + 1);
        return view('request-to-studio.create', [
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
        $requestToStudio= new RequestToStudio;
        $requestToStudio->code=$request->code;
        $requestToStudio->date=$request->date;
        $selectedProducts=$request->selected_products;
        $requestToStudio->status="pending";

        try {
            $requestToStudio->save();    
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);
        }

        $keyedProducts = collect($selectedProducts)->mapWithKeys(function ($item) {
            return [
                $item['id'] => [
                    'quantity' => $item['quantity'],
                    'studio_stock' => $item['studio_stock'],
                    // 'cause'=>$item['cause'],
                    'created_at' => Carbon::now()->toDateTimeString(),
                    'updated_at' => Carbon::now()->toDateTimeString(),
                ]
            ];
        })->all();
    

        try {
            $requestToStudio->products()->attach($keyedProducts);
            return response()->json([
                'message' => 'Data has been saved',
                'code' => 200,
                'error' => false,
                'data' => $requestToStudio,
            ]);
        } catch (Exception $e) {
            $requestToStudio->delete();
            return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => ''.$e,
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
        $requestToStudio=RequestToStudio::find($id);
        try{
            $requestToStudio->delete();

        }catch(Exception $e){
        return response()->json([
                'message' => 'Internal error',
                'code' => 500,
                'error' => true,
                'errors' => $e,
            ], 500);

        }
    }

    public function datatableRequestToStudio()
    {
        $requestToStudio = RequestToStudio::all();
     
        return DataTables::of($requestToStudio)
            ->addIndexColumn()
            ->addColumn('action', function ($row) {
                $button = '
                <div class="drodown">
                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-toggle="dropdown" aria-expanded="true"><em class="icon ni ni-more-h"></em></a>
                <div class="dropdown-menu dropdown-menu-right">
                    <ul class="link-list-opt no-bdr">
                       
                        <a href="#" class="btn-delete" data-id="' . $row->id . '"><em class="icon fas fa-trash-alt"></em>
                        <span>Delete</span>
                      
                       
                    </ul>
                </div>
                </div>';
                    return $button;
            })
            ->make(true);
    }
}

<?php

namespace App\Exports;

use App\Models\RequestToRetail;
use App\Models\RetailRequestToCentral;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RequestToRetailExport implements FromView, ShouldAutoSize
{
    protected $id;
    public function __construct()
    {
       
    }

    public function view(): View
    {
        
       $req = RequestToRetail::with(['products'])->get();
        return view('request-to-retail.export', [
            'req' => $req,
        ]);
    }
}

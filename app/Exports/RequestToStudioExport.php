<?php

namespace App\Exports;

use App\Models\RequestToRetail;
use App\Models\RequestToStudio;
use App\Models\RetailRequestToCentral;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RequestToStudioExport implements FromView, ShouldAutoSize
{
    protected $id;
    public function __construct()
    {
       
    }

    public function view(): View
    {
        
       $req = RequestToStudio::with(['products'])->get();
        return view('request-to-studio.export', [
            'req' => $req,
        ]);
    }
}

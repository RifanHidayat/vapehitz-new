<?php

namespace App\Exports;

use App\Models\RequestToRetail;
use App\Models\RetailRequestToCentral;
use App\Models\StudioRequestToCentral;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ApproveCentralStudioExport implements FromView, ShouldAutoSize
{
    protected $id;
    public function __construct()
    {
       
    }

    public function view(): View
    {
        
       $req = StudioRequestToCentral::with(['products'])->get();
        return view('approve-central-studio.export', [
            'req' => $req,
        ]);
    }
}

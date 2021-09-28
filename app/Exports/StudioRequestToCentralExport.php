<?php

namespace App\Exports;

use App\Models\RetailRequestToCentral;
use App\Models\StudioRequestToCentral;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class StudioRequestToCentralExport implements FromView, ShouldAutoSize
{
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $id = $this->id;
        $req = StudioRequestToCentral::with(['products'])->find($id);
        return view('studio-request-to-central.excel', [
            'req' => $req,
        ]);
    }
}

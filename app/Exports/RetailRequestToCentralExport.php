<?php

namespace App\Exports;

use App\Models\RetailRequestToCentral;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RetailRequestToCentralExport implements FromView, ShouldAutoSize
{
    protected $id;
    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $id = $this->id;
        $req = RetailRequestToCentral::with(['products'])->find($id);
        return view('retail-request-to-central.excel', [
            'req' => $req,
        ]);
    }
}

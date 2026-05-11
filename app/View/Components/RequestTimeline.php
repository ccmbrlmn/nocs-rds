<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\AssetTransaction;

class RequestTimeline extends Component
{
    public $request;
    public $transactions;

    public function __construct($request)
    {
        $this->request = $request;

        $this->transactions = AssetTransaction::where('request_id', $request->id)->get();
    }

    public function render()
    {
        return view('components.request-timeline');
    }
}

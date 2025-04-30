<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\YooKassaService;

class PayController extends Controller
{
    public function pay_hook(YooKassaService $pay) {
        $pay->pay_fixation();
    }

    public function show_payinfo() {
        return view('payinfo');
    }

}

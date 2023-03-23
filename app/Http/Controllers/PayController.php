<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PayController extends Controller
{
    public function show_payinfo() {
        return view('payinfo');
    }

}

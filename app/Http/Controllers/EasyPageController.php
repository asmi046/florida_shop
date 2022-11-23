<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EasyPageController extends Controller
{
    public function zone() {
        return view('test');
    }
}

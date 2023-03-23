<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EasyPageController extends Controller
{
    public function zone() {
        return view('test');
    }

    public function show_policy() {
        return view('policy');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class HitController extends Controller
{
    public function index(Request $request) {
        $allproduct = Product::where('hit', 1)->get();

        return view('hits', ['allproduct' => $allproduct]);
    }
}

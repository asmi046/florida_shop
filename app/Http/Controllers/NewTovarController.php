<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class NewTovarController extends Controller
{
    public function index(Request $request) {
        $allproduct = Product::where('new', 1)->get();

        return view('new-tovar', ['allproduct' => $allproduct]);
    }
}

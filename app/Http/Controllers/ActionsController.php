<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class ActionsController extends Controller
{
    public function index(Request $request) {

        $allproduct = Product::where('old_price', '!=', 0)->get();

        return view('actions', ['allproduct' => $allproduct]);
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;

class EasyPageController extends Controller
{
    public function zone() {
        return view('test');
    }

    public function show_policy() {
        return view('policy');
    }

    public function policy_accept() {
        return view('policy_accept');
    }

    public function uhod_instruction() {
        return view('uhod-instructionlicy');
    }

    public function show_bonus_system() {
        $sales_liders = Product::select()->orderBy('sales_count')->take(8)->get();
        return view('bonus-system', ['sales_liders' => $sales_liders]);
    }

    public function show_about() {
        $sales_liders = Product::select()->orderBy('sales_count')->take(8)->get();
        return view('about', ['sales_liders' => $sales_liders]);
    }
}

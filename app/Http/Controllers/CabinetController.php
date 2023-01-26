<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;

class CabinetController extends Controller
{
    public function show_cabinet_main() {
        return view('cabinet.cabinet');
    }

    public function show_cabinet_orders() {
        $orders = [];

        if (auth()->check()) {
            $orders = Order::with('orderProducts')->where('user_id', auth()->user()->id)->orderBy('created_at',"DESC")->get();
        }

        return view('cabinet.orders', ['orders' => $orders]);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Order;

use App\Services\PersifloraApiSevice;
use Illuminate\Support\Facades\Auth;

class CabinetController extends Controller
{
    public function show_cabinet_main() {
        return view('cabinet.cabinet');
    }

    public function show_cabinet_bonuses(PersifloraApiSevice $persi) {
        $bonuses_count = 0;
        $phone = Auth::user()["phone"];

        if (!empty($phone))
        {
            $token = $persi->create_session();
            $customer_info = $persi->get_customers(phone_format($phone));
            $bonuses_count = $customer_info->data[0]->attributes->currentPoints;
        }

        return view('cabinet.bonuses', ['bonuses_count' => $bonuses_count]);
    }

    public function show_cabinet_orders() {
        $orders = [];

        if (auth()->check()) {
            $orders = Order::with('orderProducts')->where('user_id', auth()->user()->id)->orderBy('created_at',"DESC")->get();
        }

        return view('cabinet.orders', ['orders' => $orders]);
    }
}

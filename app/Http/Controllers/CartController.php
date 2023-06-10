<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cart;
use App\Models\Order;

use Illuminate\Support\Facades\Mail;
use App\Mail\BascetSend;
use App\Http\Requests\BascetForm;

use App\Actions\BascetToTextAction;
use App\Actions\TelegramSendAction;
use App\Services\SberApiServices;
use App\Services\PersifloraApiSevice;

use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index() {
        return view('cart');
    }

    public function add(Request $request) {
        $product_id = $request->input('product_id');
        $_token = $request->input('_token');

        Cart::add($product_id);

        return array($product_id, $_token);
    }

    public function get_all() {
        $cart_product = Cart::with('tovar_data')->where("carts.session_id", session()->getId())->get();
        return ["count" => Cart::cart_coun(), "user_info" => Auth::user(),  "position" => $cart_product] ;
    }

    public function clear() {
        return Cart::cart_clear();
    }

    public function update(Request $request) {
        $product_id = $request->input('product_id');
        $new_count = $request->input('count');
        return Cart::update_tovar($product_id, $new_count);
    }

    public function delete(Request $request) {
        $product_id = $request->input('product_id');
        return Cart::delete_tovar($product_id);
    }



    public function send(BascetForm $request, BascetToTextAction $to_text, TelegramSendAction $tgsender, SberApiServices $sber, PersifloraApiSevice $persi) {
        $order = Order::create([
            'name' => $request->input('fio'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'adress' => $request->input('adress'),
            'comment' => $request->input('comment'),
            'amount' => $request->input('amount'),
            'count' => $request->input('count'),
            'delivery' => $request->input('delivery'),
            'session_id' => session()->getId(),
            'user_id' => ($request->user())?$request->user()->id:0,
        ]);

        $order->orderProducts()->sync(array_column($request->input('tovars'), "id"));

        $to_text = $to_text->handle($request);
        $tgsender->handle($to_text);


        $token = $persi->create_session();
        $tmp = $persi->create_order($request, $order->id);

        Mail::to(["asmi046@gmail.com","lisa-fon@mail.ru", "danilarepev@yandex.ru"])->send(new BascetSend($request));

        $resSber = $sber->registerOrder($request->input('amount'), $order->id, route("bascet_thencs"));

        Cart::cart_clear();

        return ['pay_info' => $resSber, "persi" => $tmp];
    }

    public function thencs() {
        return view("thencscart");
    }
}

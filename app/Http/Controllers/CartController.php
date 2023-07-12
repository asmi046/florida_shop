<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Cart;
use App\Models\Order;

use Illuminate\Support\Facades\Mail;
use App\Mail\BascetSend;
use App\Http\Requests\BascetForm;

use App\Actions\BascetToTextAction;
use App\Actions\OneClickToTextAction;
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


    public function send_oc(BascetForm $request, OneClickToTextAction $to_text, TelegramSendAction $tgsender, SberApiServices $sber, PersifloraApiSevice $persi) {
        $order = Order::create([
            'name' => "Аноним",
            'phone' => $request->input('phone'),
            'comment' => $request->input('comment'),
            'amount' => $request->input('tovar_position.price'),
            'count' => 1,
            'session_id' => session()->getId(),
            'user_id' => ($request->user())?$request->user()->id:0,
        ]);

        $order->orderProducts()->sync(array($request->input('id')));

        // Генерация номера заказа
        $sber_order_number = "№".$order->id."_S".rand(100, 999);

        // отправка заказа в Telegram
        $to_text = $to_text->handle($request, $sber_order_number);
        $tgsender->handle($to_text);


        // отправка заказа в CRM
        $token = $persi->create_session();
        $customer_id = $persi->get_customer_id(
            "Аноним",
            $request->input('phone'),
            "anonim@pf.ru",
            "Клиент создан при оформлении заказа на сайте в 1 клик");

        $tmp = $persi->create_order($request, $customer_id, $sber_order_number);

        // отправка заказа на почту

        Mail::to(explode(",",config('mailadresat.adresats')))->send(new BascetSend($request));


        return ["persi" => $tmp, "rq" =>$request->all() ];

        // return ["rq" =>$request->all() ];
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

        $order->orderProducts()->sync(array_column($request->input('tovars'), "product_id"));


        // Генерация номера заказа
        $sber_order_number = "№".$order->id."_S".rand(100, 999);

        // отправка заказа в Telegram
        $to_text = $to_text->handle($request, $sber_order_number);
        $tgsender->handle($to_text);


        // отправка заказа в CRM
        $token = $persi->create_session();
        $customer_id = $persi->get_customer_id(
            $request->input('fio'),
            $request->input('phone'),
            $request->input('email'),
            "Клиент создан при оформлении заказа на сайте");

        $tmp = $persi->create_order($request, $customer_id, $sber_order_number);

        // отправка заказа на почту

        Mail::to(explode(",",config('mailadresat.adresats')))->send(new BascetSend($request));

        // Генерация заказа в сбере

        $resSber = $sber->registerOrder($request->input('amount'), $sber_order_number, route("bascet_thencs"));

        if (!empty($resSber) && isset($resSber["orderId"]))
            Order::update_order_pay_id($order->id, $resSber["orderId"]);

        Cart::cart_clear();

        return ['pay_info' => $resSber, "persi" => $tmp];
    }

    public function thencs(Request $request, SberApiServices $sber, TelegramSendAction $tgsender) {
        $orderId = $request->input("orderId");
        if (!empty($orderId)) {
            $orderInfo = $sber->getOrderStatus($orderId);

            if (isset($orderInfo["orderStatus"]))
            {
                $orderStatusText = ($orderInfo["orderStatus"] == 2)?"Оплачен":"Не оплачен";
                Order::update_order_status($orderId, $orderInfo["orderStatus"], $orderStatusText);

                $pay_text = "<b>Заказ #".$orderInfo["orderNumber"]." ".$orderStatusText." </b>\n\r";
                $pay_text .= "<b>ID Сбера: </b>".$orderId."\n\r";
                $pay_text .= "<b>Сумма: </b>".(floatval($orderInfo["amount"])/100)." ₽\n\r";
                $tgsender->handle($pay_text);
            }
        }

        return view("thencscart");
    }
}

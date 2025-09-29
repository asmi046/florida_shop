<?php

namespace App\Http\Controllers;

use App\Models\Cart;

use App\Models\Order;
use App\Models\Product;

use App\Mail\BascetSend;
use Illuminate\Http\Request;
use App\Http\Requests\BascetForm;

use App\Services\YooKassaService;
use App\Actions\BascetToTextAction;
use App\Actions\TelegramSendAction;
use Illuminate\Support\Facades\Log;
use App\Actions\BascetToMediaAction;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Actions\OneClickToTextAction;
use App\Services\AmoApiSevice;
use App\Actions\TelegramSendMediaAction;

class CartController extends Controller
{
    public function index() {
        return view('cart');
    }

    public function add(Request $request) {
        $product_id = $request->input('product_id');
        $_token = $request->input('_token');

        Cart::add($product_id);
        $product = Product::with('tovar_categories')->where('sku', $product_id)->first();

        return array($product_id, $_token, $product);
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


    public function send_oc(BascetForm $request, BascetToMediaAction $to_media, OneClickToTextAction $to_text, TelegramSendAction $tgsender, TelegramSendMediaAction $tg_media, SberApiServices $sber, AmoApiSevice $amo) {
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
        $media = $to_media->handle($request, $sber_order_number);
        $tgsender->handle($to_text);
        $tg_media->handle($media);


        // отправка заказа в CRM
        // $token = $persi->create_session();
        // $customer_id = $persi->get_customer_id(
        //     "Аноним",
        //     $request->input('phone'),
        //     "anonim@pf.ru",
        //     "Клиент создан при оформлении заказа на сайте в 1 клик");

        $tmp = $amo->create_order($request, $customer_id, $sber_order_number);

        // отправка заказа на почту

        Mail::to(explode(",",config('mailadresat.adresats')))->send(new BascetSend($request));


        return [ "rq" =>$request->all(), "order_id"=> $order->id ];

        // return ["rq" =>$request->all() ];
    }


    public function send(BascetForm $request,
                        BascetToMediaAction $to_media,
                        BascetToTextAction $to_text,
                        TelegramSendAction $tgsender,
                        TelegramSendMediaAction $tg_media,
                        YooKassaService $pay,
                        AmoApiSevice $amo) {


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
        $media = $to_media->handle($request, $sber_order_number);
        $text = $to_text->handle($request, $sber_order_number);
        $tgsender->handle($text, $media);
        $tg_media->handle($media);


        // отправка заказа в CRM
        // $token = $persi->create_session();
        // $customer_id = $persi->get_customer_id(
        //     $request->input('fio'),
        //     $request->input('phone'),
        //     $request->input('email'),
        //     "Клиент создан при оформлении заказа на сайте");

        $tmp = $amo->create_order($request, $sber_order_number);

        // отправка заказа на почту

        Mail::to(explode(",", config('mailadresat.adresats')))->send(new BascetSend($request));

        // Генерация заказа в сбере

        $resPay = $pay->registerOrder($order,$request->input('tovars'));

        if (!empty($resPay) && isset($resPay["id"]))
            Order::update_order_pay_id($order->id, $resPay["id"]);

        Cart::cart_clear();

        return ['pay_info' => $resPay, "order_id" => $order->id];
    }

    public function thencs(Request $request, YooKassaService $pay, TelegramSendAction $tgsender) {
        $orderId = $request->input("order_id");
        if (!empty($orderId)) {
            $order = Order::where("id", $orderId)->first();
            $orderInfo = $pay->getOrderStatus($order->pay_order);

            if ($orderInfo)
            {
                // dd($orderInfo);
                $orderStatusText = ($orderInfo["status"] === "succeeded")?"Оплачен":"Не оплачен";

                $pay_text = "<b>Заказ #".$orderId." ".$orderStatusText." </b>\n\r";
                $pay_text .= "<b>ID Сбера: </b>".$orderInfo["id"]."\n\r";
                $pay_text .= "<b>Сумма: </b>".floatval($orderInfo["amount"]["value"])." ₽\n\r";
                $tgsender->handle($pay_text);
            }
        }

        return view("thencscart");
    }
}

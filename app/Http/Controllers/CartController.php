<?php

namespace App\Http\Controllers;

use App\Models\Cart;

use App\Models\Order;
use App\Models\Product;

use App\Mail\BascetSend;
use Illuminate\Http\Request;
use App\Services\AmoApiSevice;

use App\Events\PayOrderConfirmed;
use App\Http\Requests\BascetForm;
use App\Services\YooKassaService;
use App\Actions\BascetToTextAction;
use App\Actions\TelegramSendAction;

use Illuminate\Support\Facades\Log;
use App\Actions\BascetToMediaAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Actions\OneClickToTextAction;
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


    public function send_oc(BascetForm $request,
                            BascetToMediaAction $to_media,
                            OneClickToTextAction $to_text,
                            TelegramSendAction $tgsender,
                            TelegramSendMediaAction $tg_media,
                            AmoApiSevice $amo) {

        $order = Order::create([
            'name' => "Аноним",
            'phone' => $request->input('phone'),
            'comment' => $request->input('comment'),
            'amount' => $request->input('tovar_position.price'),
            'count' => 1,
            'session_id' => session()->getId(),
            'user_id' => ($request->user())?$request->user()->id:0,
        ]);


        foreach ($request->tovars as $item) {
            $order->items()->create([
                'title' => $item['tovar_data']['title'],
                'slug' => $item['tovar_data']['slug'],
                'img' => $item['tovar_data']['img'],
                'sku' => $item['tovar_data']['sku'],
                'price' => $item['price'],
                'quantity' => $item['quentity'],
            ]);
        }

        // Генерация номера заказа
        $order_number = "№".$order->id."_S".rand(100, 999);
        event(new PayOrderConfirmed($order, $order_number));


        return [ "rq" =>$request->all(), "order_id"=> $order->id ];
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
            'raion' => $request->input('rayon'),
            'delivery_price' => $request->input('deliveryprice'),
            'amount' => $request->input('amount'),
            'count' => $request->input('count'),
            'polname' => $request->input('polname'),
            'polphone' => $request->input('polphone'),
            'delivery' => $request->input('delivery'),
            'podezd' => $request->input('podezd'),
            'etazg' => $request->input('etazg'),
            'kvartira' => $request->input('kvartira'),
            'data' => $request->input('data'),
            'time' => $request->input('time'),
            'session_id' => session()->getId(),
            'user_id' => ($request->user())?$request->user()->id:0,
        ]);

        // $order->orderProducts()->sync(array_column($request->input('tovars'), "product_id"));

        foreach ($request->tovars as $item) {
            $order->items()->create([
                'title' => $item['tovar_data']['title'],
                'slug' => $item['tovar_data']['slug'],
                'img' => $item['tovar_data']['img'],
                'sku' => $item['tovar_data']['sku'],
                'price' => $item['price'],
                'quantity' => $item['quentity'],
            ]);
        }

        // Генерация номера заказа
        $order_number = "№".$order->id."_S".rand(100, 999);

        event(new PayOrderConfirmed($order, $order_number));


        $resPay = null;
        try {
            $resPay = $pay->registerOrder($order, $request->input('tovars'));




            if (!empty($resPay) && isset($resPay["id"]))
                Order::update_order_pay_id($order->id, $resPay["id"]);
        } catch (\Exception $e) {
            \Log::channel('pay')->error('Ошибка получения платежа: '. $e->getMessage());
        } finally {
            Cart::cart_clear();
            return ['pay_info' => $resPay, "order_id" => $order->id];
        }



    }

    public function thencs(Request $request, YooKassaService $pay, TelegramSendAction $tgsender) {
        $orderId = $request->input("order_id");
        if (!empty($orderId)) {
            $order = Order::where("id", $orderId)->first();
            $orderInfo = $pay->getOrderStatus($order->pay_order);

            // if ($orderInfo)
            // {
            //     // dd($orderInfo);
            //     $orderStatusText = ($orderInfo["status"] === "succeeded")?"Оплачен":"Не оплачен";

            //     $pay_text = "<b>Заказ #".$orderId." ".$orderStatusText." </b>\n\r";
            //     $pay_text .= "<b>ID Сбера: </b>".$orderInfo["id"]."\n\r";
            //     $pay_text .= "<b>Сумма: </b>".floatval($orderInfo["amount"]["value"])." ₽\n\r";
            //     $tgsender->handle($pay_text);
            // }
        }

        return view("thencscart");
    }
}

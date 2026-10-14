<?php

namespace App\Http\Controllers;

use App\Models\Product;

use App\Mail\ReviewMail;
use Illuminate\Http\Request;
use App\Actions\TelegramSendAction;
use Illuminate\Support\Facades\Mail;
use App\Events\ConsultationRequested;

class SenderController extends Controller
{
    public function send_consultation(Request $request) {
        $data = $request->validate([
            "name" => [],
            "phone" => ['required','string'],
            "review" => [],
            "sku" => []
        ]);

        $product = null;
        if (!empty($data['sku'])) {
            $product = Product::where('sku', $data['sku'])->first();
        }

        event(new ConsultationRequested(
            $data['name'] ?? '',
            $data['phone'],
            $data['review'] ?? null,
            $product
        ));

        return ["Сообщение отправлено"];
    }

    public function send_review(Request $request, TelegramSendAction $tgsender) {
        $data = $request->validate([
            "name" => [],
            "phone" => ['required','string'],
            "review" => []
        ]);


        $tmp = $tgsender->handle("<b>Оставлен отзыв</b>\n\rИмя: ".$data['name']."\n\rТелефон: ".$data['phone']."\n\rОтзыв: ".$data['review']);


        Mail::to(explode(",",config('mailadresat.adresats')))->send(new ReviewMail($data));

        return ["Отзыв принят"];
    }

    public function show_thencs() {
        return view('thencs');
    }
}

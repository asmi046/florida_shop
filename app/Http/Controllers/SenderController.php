<?php

namespace App\Http\Controllers;

use App\Models\Product;

use App\Mail\ReviewMail;
use App\Mail\ConsultMail;
use Illuminate\Http\Request;
use App\Actions\NalToMediaAction;
use App\Actions\TelegramSendAction;
use Illuminate\Support\Facades\Mail;
use App\Actions\TelegramSendMediaAction;

class SenderController extends Controller
{
    public function send_consultation(Request $request, NalToMediaAction $to_media, TelegramSendMediaAction $tg_media, TelegramSendAction $tgsender) {
        $data = $request->validate([
            "name" => [],
            "phone" => ['required','string'],
            "review" => [],
            "sku" => []
        ]);


        if (isset($data['sku']) && !empty($data['sku'])) {
            $product = Product::where('sku', $data['sku'])->first();
            // dd($data['sku']);
            $lnk = "<a href='".route('tovar', $product->slug)."'>".$product->title."</a>";

            $tmp = $tgsender->handle("<b>Консультация флориста</b>\n\rИмя: ".$data['name']."\n\rТелефон: ".$data['phone']."\n\rБукет:\n\r".$lnk);
            $media = $to_media->handle($product);
            $tg_media->handle($media);


        } else {
            $tmp = $tgsender->handle("<b>Консультация флориста</b>\n\rИмя: ".$data['name']."\n\rТелефон: ".$data['phone']."\n\rКомментарий: ".$data['review']);
        }





        Mail::to(explode(",",config('mailadresat.adresats')))->send(new ConsultMail($data));

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

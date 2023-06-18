<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\ReviewMail;
use App\Mail\ConsultMail;
use App\Actions\TelegramSendAction;

class SenderController extends Controller
{
    public function send_consultation(Request $request, TelegramSendAction $tgsender) {
        $data = $request->validate([
            "name" => [],
            "phone" => ['required','string'],
            "review" => []
        ]);


        $tmp = $tgsender->handle("<b>Консультация флориста</b>\n\rИмя: ".$data['name']."\n\rТелефон: ".$data['phone']);


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

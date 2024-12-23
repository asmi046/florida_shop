<?php

namespace App\Actions;

class TelegramSendMediaAction {
    public function handle($media = []) {
        $t_token = config('telegram.tg_token');
        $arr_chat = config('telegram.tg_coresp');

        $output = "";
        if($arr_chat) {

            $output = "";
            $arr_chat = explode(",",$arr_chat);
            $ch = curl_init();

            for ($i = 0; $i<count($arr_chat); $i++) {
                curl_setopt_array(
                    $ch,
                    array(
                        CURLOPT_URL => 'https://api.telegram.org/bot' . $t_token . '/sendMediaGroup',
                        CURLOPT_POST => TRUE,
                        CURLOPT_RETURNTRANSFER => TRUE,
                        CURLOPT_TIMEOUT => 10,
                        CURLOPT_HTTPHEADER => ['Content-Type: multipart/form-data'],
                        CURLOPT_POSTFIELDS => array(
                            'chat_id' => trim($arr_chat[$i]),
                            'media' => json_encode($media),

                        ),
                    )
                );

                $output = curl_exec($ch);
            }
        }

        return $output;
    }
}

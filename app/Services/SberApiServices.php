<?php
namespace App\Services;

class SberApiServices {

    public function registerOrder($total, $order, $returnUrl){
        $url = "https://securepayments.sberbank.ru/payment/rest/register.do?amount=".floatval($total)."00&currency=643&language=ru&orderNumber=".$order."&password=".config('sber.sber_password')."&userName=".config('sber.sber_login')."&returnUrl=".$returnUrl."&sessionTimeoutSecs=1200";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        if( $response )
            return json_decode($response, true);

        return false;
	}

    public function getOrderStatus($orderId){
        $url = "https://securepayments.sberbank.ru/payment/rest/getOrderStatusExtended.do?orderId=".$orderId."&password=".config('sber.sber_password')."&userName=".config('sber.sber_login');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        if( $response )
            return json_decode($response, true);

        return false;
	}


}

<?php
namespace App\Services;

class PersifloraApiSevice {

    public $accessToken = "";

    public function create_session() {

        $payload = [
            "data"=> [
              "type"=> "sessions",
              "attributes" => [
                "username" => config('persiflora.persiflora_login'),
                "password" => config('persiflora.persiflora_password')
              ]
            ]
        ];

        $payload = json_encode ($payload, JSON_UNESCAPED_UNICODE);

        $ch = curl_init(config('persiflora.persiflora_url').'/api/v1/sessions');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode ($result);
        $this->accessToken = $result->data->attributes->accessToken;
        return $result->data->attributes->accessToken;
    }

    public function get_customers($phone = "") {

        $authorization = "Authorization: Bearer ".$this->accessToken;

        $search = (!empty($phone))?"?search=".$phone:"";


        $ch = curl_init(config('persiflora.persiflora_url').'/api/v1/customers'.$search);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' , $authorization ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode ($result);

        return $result;
    }

}

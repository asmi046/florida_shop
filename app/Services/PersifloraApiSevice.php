<?php
namespace App\Services;

use App\Http\Requests\BascetForm;

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

    public function create_order(BascetForm $request) {
        $payload = [
                "data"=> [
                "attributes" => [
                    "budget" => 0,
                    "byBonuses" => false,
                    "date" => date("Y-m-d"),
                    // "delivery" => false,
                    // "deliveryApartment" => "",
                    // "deliveryBuilding" => "",
                    // "deliveryCity" => "",
                    // "deliveryComments" => "",
                    // "deliveryContact" => "",
                    // "deliveryHouse" => "",
                    // "deliveryPhoneCode" => "",
                    // "deliveryPhoneNumber" => "",
                    // "deliveryStreet" => "",
                    // "deliveryTimeFrom" => null,
                    // "deliveryTimeTo" => null,
                    "description" => "",
                    "docNo" => "aabn22000008",
                    "dueTime" => null,
                    "fiscal" => false,
                    "status" => "new",
                    "updatedAt" => "2022-07-19T09:01:55Z"
                ],
                "id" => "21360847-c158-4b68-9b93-6d68169b8d6c",
                "type" => "orders"
                ]
            ];

            $payload = json_encode ($payload, JSON_UNESCAPED_UNICODE);


            $authorization = "Authorization: Bearer ".$this->accessToken;

            $ch = curl_init(config('persiflora.persiflora_url').'/api/v1/orders');
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [ 'Content-Type: application/json' , $authorization ]);

            $result = curl_exec($ch);
            curl_close($ch);

            $result = json_decode ($result);
    }


}

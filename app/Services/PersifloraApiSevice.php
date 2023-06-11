<?php
namespace App\Services;

use App\Http\Requests\BascetForm;

class PersifloraApiSevice {

    public $accessToken = "";

    protected function do_query($url, $method, $payload = [], $autorized = false) {

        $payload_json = json_encode ($payload, JSON_UNESCAPED_UNICODE);

        $header_line = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload_json)
        ];

        if ($autorized) $header_line[] = "Authorization: Bearer ".$this->accessToken;

        $ch = curl_init(config('persiflora.persiflora_url').$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header_line );

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode ($result);

    }


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

        $result = $this->do_query('/api/v1/sessions', 'POST', $payload, false);
        $this->accessToken = $result->data->attributes->accessToken;
        return $result->data->attributes->accessToken;
    }

    public function get_customers($phone = "") {
        $search = (!empty($phone))?"?search=".$phone:"";
        $result = $this->do_query('/api/v1/customers'.$search, 'GET', null, true);
        return $result;
    }

    public function create_order(BascetForm $request, $customer_id) {
        $payload = [
            "data"=> [
                "attributes" => [
                    "budget" => 0,
                    "byBonuses" => false,
                    "date" => date("Y-m-d"),
                    "delivery" => true,
                    "deliveryApartment" => "",
                    "deliveryBuilding" => "",
                    "deliveryCity" => "",
                    "deliveryComments" => "",
                    "deliveryContact" => "",
                    "deliveryHouse" => "",
                    "deliveryPhoneCode" => "",
                    "deliveryPhoneNumber" => "",
                    "deliveryStreet" => "",
                    "deliveryTimeFrom" => null,
                    "deliveryTimeTo" => null,
                    "description" => $request->input('comment'),
                    "dueTime" => null,
                    "fiscal" => false,
                    "status" => "new",
                    "docNo" => "site".rand(100, 9999999),
                ],
                "type" => "orders",
                "relationships" => [
                    "source" => [
                        "data" => [
                            "id" => "9bed7b59-94dc-4ce4-aac2-883aab1a148c",
                            "type" => "order-sources"
                        ]
                    ],

                    "customer" => [
                        "data" => [
                            "id" => $customer_id,
                            "type" => "customers"
                        ]
                    ],

                    "store" => [
                        "data" => [
                            "id" => "0fe9d41a-7019-463c-97a0-c541ba65d913",
                            "type" => "stores"
                        ]
                    ],
                ]
           ]
        ];



        $result = $this->do_query('/api/v1/orders', 'POST', $payload, true);
        return $result;
    }

    public function create_customer($name, $phone, $email, $notes) {
        $payload =   [
            "data" => [
              "type" => "customers",
              "attributes" => [
                "countryCode" => 7,
                "email" => $email,
                "isPerson" => true,
                "notes" => $notes,
                "phone" => phone_format($phone),
                "status" => "on",
                "title" => $name
              ],
            ]
        ];

        $result = $this->do_query('/api/v1/customers', 'POST', $payload, true);
        return $result;
    }


    public function get_customer_id($name, $phone, $email, $notes) {
        $customer_info = $this->get_customers(phone_format($phone));

        if (empty($customer_info))
        {
            $customers_new = $this->create_customer($name, $phone, $email, $notes);
            return (!empty($customers_new))?$customers_new->data[0]->id:null;

        } else {
            return $customer_info->data[0]->id;
        }

    }

}

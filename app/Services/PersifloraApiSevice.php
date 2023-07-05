<?php
namespace App\Services;

use App\Http\Requests\BascetForm;

use App\Actions\BascetBodyToTextAction;

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

    public function get_bonus_group($id = "") {
        $result = $this->do_query('/api/v1/bonus-groups/'.$id, 'GET', null, true);
        return $result;
    }


    public function get_customers($phone = "") {
        $search = (!empty($phone))?"?search=".$phone:"";
        $result = $this->do_query('/api/v1/customers'.$search, 'GET', null, true);
        return $result;
    }

    public function create_order(BascetForm $request, $customer_id) {

        $to_text = new BascetBodyToTextAction();

        $zak_text = $to_text->handle($request);

        $zak_text .= "\n\rКомментарий к заказу:\n\r";

        $zak_text .= $request->input('comment');

        $zak_text = str_replace("\n\r", "<br>", $zak_text);

        $payload = [
            "data"=> [
                "attributes" => [
                    "budget" => $request->input('amount'),
                    "byBonuses" => false,
                    "date" => date("Y-m-d"),
                    "delivery" => true,
                    "deliveryApartment" => $request->input('kvartira'),
                    "deliveryBuilding" => $request->input('podezd'),
                    "deliveryCity" => "Курск",
                    "deliveryComments" => "",
                    "deliveryContact" => $request->input('polname'),
                    "deliveryHouse" => $request->input('home'),
                    "deliveryPhoneCode" => "7",
                    "deliveryPhoneNumber" => phone_format( $request->input("polphone") ),
                    "deliveryStreet" => $request->input('street'),
                    "deliveryTimeFrom" => date("Y-m-d\\TH:i:sP", strtotime($request->input('time')." ".$request->input('data'))),
                    "deliveryTimeTo" => date("Y-m-d\\TH:i:sP", strtotime($request->input('time')." ".$request->input('data'))),
                    "description" => $zak_text,
                    "dueTime" => date("Y-m-d\\TH:i:sP", strtotime($request->input('time')." ".$request->input('data'))),
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

        if (empty($customer_info->data))
        {
            $customers_new = $this->create_customer($name, $phone, $email, $notes);
            return (isset($customers_new->data))?$customers_new->data->id:null;

        } else {
            return $customer_info->data[0]->id;
        }

    }

}

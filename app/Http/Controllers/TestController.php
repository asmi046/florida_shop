<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PersifloraApiSevice;

class TestController extends Controller
{
    public function index(Request $request, PersifloraApiSevice $persiflora) {

        dd(date("Y-m-d\\TH:i:sP", strtotime("2023-07-06 12:25")));
        $token = $persiflora->create_session();

        $customers = $persiflora->get_customers("9534409900");

        // $customers_new = $persiflora->create_customer("Андрей Смирнов", "+7 (903) 633-08-01", "asmi046@gmail.com", "Создан при оформлении заказа");

        // $customers_id = $persiflora->get_customer_id("Андрей Смирнов", "+7 (903) 633-08-01", "asmi046@gmail.com", "Создан при оформлении заказа");
        $customers_id = $persiflora->get_customer_id("Тестовый", "+7 (953) 400-00-00", "asmi046@gmail.com", "Создан при оформлении заказа");

        dd( phone_format( "+7 (960) 687-65-01" ), $token, $customers, $customers_id);
    }
}

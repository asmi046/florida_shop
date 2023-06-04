<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PersifloraApiSevice;

class TestController extends Controller
{
    public function index(Request $request, PersifloraApiSevice $persiflora) {

        $token = $persiflora->create_session();

        $customers = $persiflora->get_customers();

        dd($token, $customers);
    }
}

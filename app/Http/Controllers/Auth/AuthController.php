<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Mail;
use App\Mail\PassRecMail;

class AuthController extends Controller
{
    public function show_login_form() {
        return view('auth.login');
    }

    public function show_register_form() {
        return view('auth.register');
    }

    public function show_passrec_form(Request $request) {
        return view('auth.pass-rec', ['confirm' => $request->get('confirm')]);
    }

    public function logout() {
        auth('web')->logout();
        return redirect(route('home'));
    }

    public function pass_req(Request $request) {
        $data = $request->validate([
            "email" => ['required','string', 'email', 'exists:users']
        ]);

        $user = User::where('email', $data['email'])->first();

        $new_password = uniqid();
        $user->password = bcrypt($new_password);
        $user->save();

        Mail::to($user)->send(new PassRecMail($new_password));
        return redirect(route('passrec',['confirm' => 1]));
    }

    public function login(Request $request) {
        $user_data = $request->validate([
            'email' => ['required', 'email', 'string'],
            'password' => ['required'],
        ]);


        if(auth('web')->attempt($user_data)) {
            return redirect(route('home'));
        }

        return redirect(route('login'))->withErrors(['email'=>'Неверный логин или пароль']);
    }



    public function save_user_data(Request $request) {
        $user_data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'string', 'exists:users,email'],
        ]);

        $user = User::where('id', auth()->user()->id)->first()->update([
            'name' => $user_data['name'],
            'email' => $user_data['email'],
        ]);


        return redirect(route('cabinet.home'));
    }

    public function register(Request $request) {
        $user_data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'string', 'unique:users,email'],
            'password' => ['required','confirmed'],
        ]);

        $user = User::create(
            [
                'name' => $user_data['name'],
                'email' => $user_data['email'],
                'password' => bcrypt($user_data['password']),
            ]
        );

        if ($user) {
            auth('web')->login($user);
        }

        return redirect(route('home'));
    }


}

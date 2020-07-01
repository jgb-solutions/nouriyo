<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function getLoginForm(Request $request) {
      return view('auth.login');
    }

    public function login(Request $request) {
      $credentials = $this->validate($request, [
        'email' => 'required|email',
        'password' => 'required|min:6'
      ]);

      if (Auth::attempt($credentials)) {
        return redirect(route('dashboard'));
      }

      return redirect(route('auth.login'));
    }

    public function logout()
    {
      auth()->logout();

      return redirect(route('auth.getLoginForm'));
    }
}

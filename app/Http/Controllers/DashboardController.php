<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request) {
      $connect_user = auth()->user();

      if ($connect_user->admin) {
        // prepare data for an admin
      } else {
        // prepare data for an agent
      }

      return view('dashboard.index');
    }
}

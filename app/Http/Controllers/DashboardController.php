<?php

  namespace App\Http\Controllers;

  use App\Models\Product;
  use Illuminate\Http\Request;

  class DashboardController extends Controller
  {
    public function index(Request $request)
    {
      $connect_user = auth()->user();

      if ($connect_user->admin) {
        // prepare data for an admin
      } else {
        // prepare data for an agent
      }

      return view('dashboard.index');
    }

    public function orders()
    {
      return view('dashboard.orders');
    }

    public function products()
    {
      return view('dashboard.products', [
        'products' => Product::latest()->paginate(20)
      ]);
    }

    public function packages()
    {
      return view('dashboard.packages');
    }

    public function clients()
    {
      return view('dashboard.clients');
    }

    public function beneficiaries()
    {
      return view('dashboard.beneficiaries');
    }

    public function reports()
    {
      return view('dashboard.reports');
    }
  }

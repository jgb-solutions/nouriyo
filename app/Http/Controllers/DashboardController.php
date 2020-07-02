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

    // Products
    public function products()
    {
      return view('dashboard.products', [
        'products' => Product::latest()->paginate(20),
      ]);
    }

    public function add_product(Request $request)
    {
      $data = $this->validate_product($request);

      Product::create($data);

      flash('New product added!')->overlay()->success();

      return redirect(route('dashboard.products'));
    }

    public function update_product(Request $request, Product $product)
    {
      $data = $this->validate_product($request);

      $product->update($data);

      flash('Product updated!')->overlay()->success();

      return redirect(route('dashboard.products'));
    }

    public  function delete_product(Request $request, Product $product)
    {
      if (auth()->user()->admin) {
        $product->delete();
        flash('Product updated!')->success();
      } else {
        flash('Error deleting the product!')->error();
      }

      return redirect(route('dashboard.products'));
    }

    private function validate_product(Request $request)
    {
      $this->validate($request, [
        'name' => 'required|unique:products',
        'buying_price' => 'required|min:0',
        'selling_price' => 'required',
        'quantity' => 'required|numeric',
        'image' => 'image',
      ]);

      $data = $request->only([
        'name',
        'buying_price',
        'selling_price',
        'quantity',
        'description',
      ]);

      if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('images');
      }

      return $data;
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

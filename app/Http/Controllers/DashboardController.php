<?php

  namespace App\Http\Controllers;

  use App\Models\Beneficiary;
  use App\Models\Order;
  use App\Models\Product;
  use App\Models\Package;
  use App\Models\User;
  use App\Models\Client;
  use Illuminate\Http\Request;

  class DashboardController extends Controller
  {
    public $order_states = ['processing', 'ready', 'delivering', 'delivered'];

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
      if (auth()->user()->admin) {
        $data = $this->validate_product($request);

        $product->update($data);

        flash('Product updated!')->overlay()->success();
      } else {
        flash('Error deleting the product!')->error();
      }


      return redirect(route('dashboard.products'));
    }

    public function delete_product(Product $product)
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

    // Packages
    public function packages()
    {
      return view('dashboard.packages', [
        'packages' => Package::with('products')
          ->withCount('products')
          ->latest()
          ->paginate(20),
        'products' => Product::orderBy('name', 'asc')->get(),
      ]);
    }

    public function add_package(Request $request)
    {
      $data = $this->validate_package($request);

      $package = Package::create($data);

      $this->syncProductsToPackage($package, $request);

      flash('New package added!')->overlay()->success();

      return redirect(route('dashboard.packages'));
    }

    public function update_package(Request $request, Package $package)
    {
      if (auth()->user()->admin) {
        $data = $this->validate_package($request);

        $package->update($data);

        $this->syncProductsToPackage($package, $request);

        flash('Package updated!')->overlay()->success();
      } else {
        flash('Error deleting the package!')->error();
      }


      return redirect(route('dashboard.packages'));
    }

    private function syncProductsToPackage(Package $package, Request $request)
    {
      if ($request->has('products')) {
        $products = [];

        foreach ($request->products as $key => $value) {
          foreach ($value as $id => $quantity) {
            $products[$id] = ['quantity' => $quantity];
          }
        }

        // $package->products()->detach();
        $package->products()->sync($products);
      } else {
        $package->products()->detach();
      }
    }

    public function delete_package(Package $package)
    {
      if (auth()->user()->admin) {
        $package->products()->detach();
        $package->delete();
        flash('Package updated!')->success();
      } else {
        flash('Error deleting the package!')->error();
      }

      return redirect(route('dashboard.packages'));
    }

    private function validate_package(Request $request)
    {
      $this->validate($request, [
        'name' => 'required',
        'price' => 'required',
      ]);

      return $request->only(['name', 'price', 'description']);
    }


    // Orders
    public function orders()
    {
      return view('dashboard.orders', [
        'orders' => Order::with(['products', 'packages', 'client', 'beneficiary'])
          ->withCount(['products', 'packages'])
          ->latest()
          ->paginate(20),
        'products' => Product::orderBy('name', 'asc')->get(),
        'packages' => Package::orderBy('name', 'asc')->get(),
        'order_states' => $this->order_states,
        'clients' => Client::latest()->take(20)->get(),
        'beneficiaries' => Beneficiary::latest()->take(20)->get(),
      ]);
    }

    public function add_order(Request $request)
    {
      $data = $this->validate_order($request);

      $order                 = new Order();
      $order->number         = Order::getNumber();
      $order->state          = 'processing';
      $order->client_id      = $data['client_id'];
      $order->beneficiary_id = $data['beneficiary_id'];

      if ($request->hasFile('receipt')) {
        $order->receipt = $request->file('receipt')->store('receipts');
      }

      $order->save();

      $this->syncProductsAndPackagesToOrder($order, $request);

      flash('New order added!')->overlay()->success();

      return redirect(route('dashboard.orders'));
    }

    public function update_order(Request $request, Order $order)
    {
      if (auth()->user()->admin) {
        $data = $this->validate_order($request);

        $order->update($data);

        if ($request->hasFile('receipt')) {
          $order->receipt = $request->file('receipt')->store('receipts');
        }

        if (
          $request->state != 'delivered' ||
          ($request->state == 'delivered' && $request->hasFile('receipt'))
        ) {
          $order->state = $request->state;
        }

        $order->save();

        $this->syncProductsAndPackagesToOrder($order, $request);

        flash('Order updated!')->overlay()->success();
      } else {
        flash('Error deleting the order!')->error();
      }


      return redirect(route('dashboard.orders'));
    }

    private function syncProductsAndPackagesToOrder(Order $order, Request $request)
    {
      if ($request->has('products')) {
        $products = [];

        foreach ($request->products as $key => $value) {
          foreach ($value as $id => $quantity) {
            $products[$id] = ['quantity' => $quantity, 'type' => 'product'];
          }
        }

        $order->products()->sync($products);
      } else {
        $order->products()->detach();
      }

      if ($request->has('packages')) {
        $packages = [];

        foreach ($request->packages as $key => $value) {
          foreach ($value as $id => $quantity) {
            $packages[$id] = ['quantity' => $quantity, 'type' => 'package'];
          }
        }

        $order->packages()->sync($packages);
      } else {
        $order->packages()->detach();
      }
    }

    public function delete_order(Order $order)
    {
      if (auth()->user()->admin) {
        $order->products()->detach();
        $order->packages()->detach();

        $order->delete();
        flash('Package updated!')->success();
      } else {
        flash('Error deleting the order!')->error();
      }

      return redirect(route('dashboard.orders'));
    }

    private function validate_order(Request $request)
    {
      return $this->validate($request, [
        'client_id' => 'required',
        'beneficiary_id' => 'required',
      ]);
    }


    // Agents
    public function agents()
    {
      return view('dashboard.agents', [
        'agents' => User::latest()->agents()->paginate(20),
        'countries' => ["Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe"],
      ]);
    }

    public function add_agent(Request $request)
    {
      $data = $this->validate_agent($request);

      User::create($data);

      flash('New agent added!')->overlay()->success();

      return redirect(route('dashboard.agents'));
    }

    public function update_agent(Request $request, User $agent)
    {
      if (auth()->user()->admin) {
        $data = $this->validate_agent($request);

        $agent->update($data);

        flash('User updated!')->overlay()->success();
      } else {
        flash('Error deleting the product!')->error();
      }

      return redirect(route('dashboard.agents'));
    }

    public function delete_agent(User $agent)
    {
      if (auth()->user()->admin) {
        $agent->delete();
        flash('User updated!')->success();
      } else {
        flash('Error deleting the agent!')->error();
      }

      return redirect(route('dashboard.agents'));
    }

    private function validate_agent(Request $request)
    {
      $this->validate($request, [
        'business' => 'required',
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email',
        'limit' => 'required|numeric',
        'address' => 'min:10',
      ]);

      $data = $request->only([
        'business',
        'first_name',
        'last_name',
        'email',
        'limit',
        'country',
        'state',
        'city',
        'zip',
        'address',
      ]);

      if ($request->filled('password')) {
        $this->validate($request, ['password' => 'min:6']);

        $data['password'] = bcrypt($request->password);
      }

      $data['active'] = $request->has('active');

      $data['agent'] = true;
      $data['admin'] = false;

      return $data;
    }

    // Clients
    public function clients()
    {
      return view('dashboard.clients', [
        'clients' => Client::withCount('orders')->latest()->paginate(20),
        'countries' => ["Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe"],
      ]);
    }

    public function add_client(Request $request)
    {
      $data = $this->validate_client($request);

      Client::create($data);

      flash('New client added!')->overlay()->success();

      return redirect(route('dashboard.clients'));
    }

    public function update_client(Request $request, Client $client)
    {
      if (auth()->user()->admin) {
        $data = $this->validate_client($request);

        $client->update($data);

        flash('User updated!')->overlay()->success();
      } else {
        flash('Error deleting the product!')->error();
      }

      return redirect(route('dashboard.clients'));
    }

    public function delete_client(Client $client)
    {
      if (auth()->user()->admin) {
        $client->delete();
        flash('User updated!')->success();
      } else {
        flash('Error deleting the client!')->error();
      }

      return redirect(route('dashboard.clients'));
    }

    private function validate_client(Request $request)
    {
      $this->validate($request, [
        'first_name' => 'required',
        'last_name' => 'required',
        'phone' => 'required',
      ]);

      $data = $request->only([
        'first_name',
        'last_name',
        'phone',
      ]);

      return $data;
    }

    // Beneficiaries
    public function beneficiaries()
    {
      return view('dashboard.beneficiaries', [
        'beneficiaries' => Beneficiary::withCount('orders')->latest()->paginate(20),
        'countries' => ["Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe"],
      ]);
    }

    public function add_beneficiary(Request $request)
    {
      $data = $this->validate_beneficiary($request);

      Beneficiary::create($data);

      flash('New beneficiary added!')->overlay()->success();

      return redirect(route('dashboard.beneficiaries'));
    }

    public function update_beneficiary(Request $request, Beneficiary $beneficiary)
    {
      if (auth()->user()->admin) {
        $data = $this->validate_beneficiary($request);

        $beneficiary->update($data);

        flash('User updated!')->overlay()->success();
      } else {
        flash('Error deleting the product!')->error();
      }

      return redirect(route('dashboard.beneficiaries'));
    }

    public function delete_beneficiary(Beneficiary $beneficiary)
    {
      if (auth()->user()->admin) {
        $beneficiary->delete();
        flash('User updated!')->success();
      } else {
        flash('Error deleting the beneficiary!')->error();
      }

      return redirect(route('dashboard.beneficiaries'));
    }

    private function validate_beneficiary(Request $request)
    {
      $this->validate($request, [
        'first_name' => 'required',
        'last_name' => 'required',
        'phone' => 'required',
        'address' => 'required',
      ]);

      $data = $request->only([
        'first_name',
        'last_name',
        'phone',
        'address',
      ]);

      return $data;
    }

    // Admins
    public function admins()
    {
      return view('dashboard.admins', [
        'admins' => User::latest()->admins()->paginate(20),
      ]);
    }

    public function add_admin(Request $request)
    {
      $data = $this->validate_admin($request);

      User::create($data);

      flash('New admin added!')->overlay()->success();

      return redirect(route('dashboard.admins'));
    }

    public function update_admin(Request $request, User $admin)
    {
      if (auth()->user()->admin) {
        $data = $this->validate_admin($request);

        $admin->update($data);

        flash('User updated!')->overlay()->success();
      } else {
        flash('Error deleting the product!')->error();
      }

      return redirect(route('dashboard.admins'));
    }

    public function delete_admin(User $admin)
    {
      if (auth()->user()->admin) {
        $admin->delete();
        flash('User updated!')->success();
      } else {
        flash('Error deleting the admin!')->error();
      }

      return redirect(route('dashboard.admins'));
    }

    private function validate_admin(Request $request)
    {
      $this->validate($request, [
        'first_name' => 'required',
        'last_name' => 'required',
        'email' => 'required|email',
      ]);

      $data = $request->only([
        'first_name',
        'last_name',
        'email',
      ]);

      if ($request->filled('password')) {
        $this->validate($request, ['password' => 'min:6']);

        $data['password'] = bcrypt($request->password);
      }

      $data['agent'] = false;
      $data['admin'] = true;

      return $data;
    }

    public function reports()
    {
      return view('dashboard.reports');
    }
  }

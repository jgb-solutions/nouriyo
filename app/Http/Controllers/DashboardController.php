<?php

    namespace App\Http\Controllers;

    use App\Models\Beneficiary;
    use App\Models\Order;
    use App\Models\Product;
    use App\Models\Package;
    use App\Models\User;
    use App\Models\Client;
    use App\Models\Setting;
    use Carbon\Carbon;
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
                $user = auth()->user();

                if ($user->active && $user->canTakeOrders) {
                    return redirect(route('dashboard.orders'));
                } else {
                    auth()->logout();

                    return view('suspended');
                }
            }

            return view('dashboard.index', [
                'orders_of_today' => Order::latest()
                    ->whereDate('created_at', Carbon::today())
                    ->where('state', 'delivered')
                    ->take(10)
                    ->get(),
                'latest_orders' => Order::latest()
                    ->take(10)
                    ->get(),
            ]);
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

            alert()->success('New product added!');

            return redirect(route('dashboard.products'));
        }

        public function update_product(Request $request, Product $product)
        {
            if (auth()->user()->admin) {
                $data = $this->validate_product($request);

                $product->update($data);

                alert()->success('Product updated!');
            } else {
                alert()->error('Error deleting the product!');
            }


            return redirect(route('dashboard.products'));
        }

        public function delete_product(Product $product)
        {
            if (auth()->user()->admin) {
                $product->delete();
                alert()->success('Product deleted!');
            } else {
                alert()->error('Error deleting the product!');
            }

            return redirect(route('dashboard.products'));
        }

        private function validate_product(Request $request)
        {
            $this->validate($request, [
//        'name' => 'required|unique:products',
                'name' => 'required',
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

            alert()->success('New package added!');

            return redirect(route('dashboard.packages'));
        }

        public function update_package(Request $request, Package $package)
        {
            if (auth()->user()->admin) {
                $data = $this->validate_package($request);

                $package->update($data);

                $this->syncProductsToPackage($package, $request);

                alert()->success('Package updated!');
            } else {
                alert()->error('Error deleting the package!');
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
                alert()->success('Package deleted!');
            } else {
                alert()->error('Error deleting the package!');
            }

            return redirect(route('dashboard.packages'));
        }

        private function validate_package(Request $request)
        {
            $this->validate($request, [
                'name' => 'required',
                'package_quantity' => 'required|numeric',
                'price' => 'required',
            ]);

            $data = $request->only(['name', 'price', 'description']);

            $data['quantity'] = $request->package_quantity;

            return $data;
        }


        // Orders
        public function orders(Request $request)
        {
            $query = Order::latest();

            if ($request->filled('number')) {
                $query->where('number', 'like', '%' . $request->query('number') . '%');
            }

            return view('dashboard.orders', [
                'orders' => $query->with(['products', 'packages', 'client', 'beneficiary'])
                    ->withCount(['products', 'packages'])
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

            $user = auth()->user();

            if ($user->admin || $user->active && $user->canTakeOrders) {
                $data = $this->validate_order($request);

                $settings = Setting::first();

                $order                 = new Order();
                $order->number         = Order::getNumber();
                $order->state          = 'processing';
                $order->client_id      = $data['client_id'];
                $order->beneficiary_id = $data['beneficiary_id'];
                $order->taken_by       = auth()->user()->id;
                $order->transport_fee  = $settings->transport_fee;
                $order->service_fee    = $settings->service_fee;

                if ($request->hasFile('receipt')) {
                    $order->receipt = $request->file('receipt')->store('receipts');
                }

                $order->save();

                $hasError = $this->syncProductsAndPackagesToOrder($order, $request);

                if (!$hasError) {
                    alert()->success('New order added!');
                }
            } else {
                alert()->error(
                    'Error creating the order!',
                    "You can't create orders right now, please contact an administrator."
                );
            }

            return redirect(route('dashboard.orders'));
        }

        public function update_order(Request $request, Order $order)
        {
            $user = auth()->user();

            if ($order->agent_cant_edit) {
                alert()->error('Time to edit expired!', "You can no longer edit this order. Please contact an administrator.");

                return back();
            }

            if ($user->admin || $user->canTakeOrders && $user->active) {
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

                    if ($order->state === 'delivered') {
                        $order->delivered_by = auth()->user()->id;
                    }
                }

                $order->save();

                $hasError = $this->syncProductsAndPackagesToOrder($order, $request);

                if (!$hasError) {
                    alert()->success('Order updated!');
                }
            } else {
                alert()->error('Error updating the order!');
            }


            return redirect(route('dashboard.orders'));
        }

        private function syncProductsAndPackagesToOrder(Order $order, Request $request)
        {
//      dd($request->all());
            if ($request->has('products')) {
                $products = [];

                foreach ($request->products as $key => $value) {
                    foreach ($value as $id => $quantity) {
                        $products[$id] = ['quantity' => $quantity, 'type' => 'product'];

                        $product_to = Product::find($id);
                        if ($product_to) {
                            $quantity_remaining = $product_to->quantity - (int)$quantity;

                            if ($quantity_remaining >= 0) {
                                $product_to->quantity = $product_to->quantity - (int)$quantity;
                                $product_to->save();
                            } else {
                                return alert()->error('Product quantity not enough!', 'We have ' . $product_to->quantity . ' of ' . $product_to->name . ' available. You can\'t buy ' . $quantity . '.');
                            }
                        }
                    }
                }

                $order->products()->sync($products);
            } else {
//        dd('flushing products');
                foreach ($order->products as $product) {
                    $product->quantity += $product->pivot->quantity;
                    $product->save();
                }

                $order->products()->detach($order->products->pluck('id'));
            }

            if ($request->has('packages')) {
                $packages = [];

                foreach ($request->packages as $key => $value) {
                    foreach ($value as $id => $quantity) {
                        $packages[$id] = ['quantity' => $quantity, 'type' => 'package'];

                        $package_to = Package::find($id);

                        $quantity_remaining = $package_to->quantity - (int)$quantity;

                        if ($quantity_remaining >= 0) {
                            $package_to->quantity = $quantity_remaining;
                            $package_to->save();
                        } else {
                            return alert()->error('Package quantity not enough!', 'We have ' . $package_to->quantity . ' of ' . $package_to->name . ' available. You can\'t buy ' . $quantity . '.');
                        }
                    }
                }

                $order->packages()->sync($packages);
            } else {
//        dd('flushing packages');
                foreach ($order->packages as $package) {
                    $package->quantity += $package->pivot->quantity;
                    $package->save();
                }

                $order->packages()->detach($order->packages->pluck('id'));
            }
        }

        public function delete_order(Order $order)
        {
            if (auth()->user()->admin) {
                foreach ($order->products as $product) {
                    $product->quantity = $product->quantity + $product->pivot->quantity;
                    $product->save();
                }

                $order->products()->detach();

                foreach ($order->packages as $package) {
                    $package->quantity = $package->quantity + $package->pivot->quantity;
                    $package->save();
                }

                $order->packages()->detach();

                $order->delete();
                alert()->success('Order <b> ' . $order->number . ' </b> deleted!');
            } else {
                alert()->error('Error deleting the order!');
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
                'agents' => User::latest()
                    ->agents()
                    ->with(['ordersTaken', 'ordersDelivered'])
                    ->withCount(['ordersTaken', 'ordersDelivered'])
                    ->paginate(20),
                'countries' => ["Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe"],
            ]);
        }

        public function add_agent(Request $request)
        {
            $data = $this->validate_agent($request);

            User::create($data);

            alert()->success('New agent added!');


            return redirect(route('dashboard.agents'));
        }

        public function update_agent(Request $request, User $agent)
        {
            if (auth()->user()->admin) {
                $data = $this->validate_agent($request);

                $agent->update($data);

                alert()->success('User updated!');

            } else {
                alert()->error('Error deleting the product!');
            }

            return redirect(route('dashboard.agents'));
        }

        public function delete_agent(User $agent)
        {
            if (auth()->user()->admin) {
                $agent->delete();
                alert()->success('User updated!');
            } else {
                alert()->error('Error deleting the agent!');
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
                'due',
                'paid'
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

            alert()->success('New client added!');

            return redirect(route('dashboard.clients'));
        }

        public function update_client(Request $request, Client $client)
        {
            if (auth()->user()->admin) {
                $data = $this->validate_client($request);

                $client->update($data);

                alert()->success('User updated!');
            } else {
                alert()->error('Error deleting the product!');
            }

            return redirect(route('dashboard.clients'));
        }

        public function delete_client(Client $client)
        {
            if (auth()->user()->admin) {
                $client->delete();
                alert()->success('User updated!');
            } else {
                alert()->error('Error deleting the client!');
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

            alert()->success('New beneficiary added!');

            return redirect(route('dashboard.beneficiaries'));
        }

        public function update_beneficiary(Request $request, Beneficiary $beneficiary)
        {
            if (auth()->user()->admin) {
                $data = $this->validate_beneficiary($request);

                $beneficiary->update($data);

                alert()->success('User updated!');
            } else {
                alert()->error('Error deleting the product!');
            }

            return redirect(route('dashboard.beneficiaries'));
        }

        public function delete_beneficiary(Beneficiary $beneficiary)
        {
            if (auth()->user()->admin) {
                $beneficiary->delete();
                alert()->success('Beneficiary deleted!');
            } else {
                alert()->error('Error deleting the beneficiary!');
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
            alert()->success('New admin added!');

            return redirect(route('dashboard.admins'));
        }

        public function update_admin(Request $request, User $admin)
        {
            if (auth()->user()->admin) {
                $data = $this->validate_admin($request);

                $admin->update($data);
                alert()->success('User updated!');
            } else {
                alert()->error('Error deleting the product!');
            }

            return redirect(route('dashboard.admins'));
        }

        public function delete_admin(User $admin)
        {
            if (auth()->user()->admin) {
                $admin->delete();
                alert()->success('This admin has been deleted!');
            } else {
                alert()->error('Error deleting the admin!');
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

        public function settings()
        {
            return view('dashboard.settings', [
                'settings' => Setting::first(),
            ]);
        }

        public function update_settings(Request $request, Setting $settings)
        {
            if (auth()->user()->admin) {
                $settings->update($request->all());
                alert()->success('Settings updated!');
            } else {
                alert()->error(
                    'Error updating the settings!',
                    "You must be an admin to update the settings."
                );
            }

            return redirect(route('dashboard.settings'));
        }

        public function cancel_order(Request $request, Order $order)
        {
            if ($request->user()->admin) {
                $order->cancelled()->create();

                alert()->success('Order successfully cancelled!');

            } else {
                alert()->error('You must be an admin to cancel an order.');
            }

            return back();
        }

        public function restore_order(Request $request, Order $order)
        {
            if ($request->user()->admin) {
                $order->cancelled->delete();

                alert()->success('Order successfully restored!');
            } else {
                alert()->error('You must be an admin to cancel an order.');
            }

            return back();
        }

        public function business_report(Request $request)
        {
            $user = $request->user();

            $orders = $user->ordersTaken()
                ->with(['products', 'packages', 'client', 'beneficiary'])
                ->withCount(['products', 'packages'])
                ->paginate(20);

            return view('dashboard.business-report', [
                'orders' => $orders,
                'user' => $user,
                'clients' => Client::latest()->take(20)->get(),
                'beneficiaries' => Beneficiary::latest()->take(20)->get(),
            ]);
        }
    }

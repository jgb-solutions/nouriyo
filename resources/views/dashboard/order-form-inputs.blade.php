<script>
    window.products = {!! $products->map(function($product) {
        return ['id' => $product->id, 'name' => $product->name];
    })->toJson() !!}

            {!! !empty($order) ? 'window.chosenProducts_' . $order->id . ' = ' . $order->products->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $product->pivot->quantity
                ];
            })->toJson() : '' !!}

            {{--Packages--}}

        window.packages = {!! $packages->map(function($package) {
        return ['id' => $package->id, 'name' => $package->name];
    })->toJson() !!}

    {!! !empty($order) ? 'window.chosenPackages_' . $order->id . ' = ' . $order->packages->map(function($package) {
        return [
            'id' => $package->id,
            'name' => $package->name,
            'quantity' => $package->pivot->quantity
        ];
    })->toJson() : '' !!}
</script>

<div class="row">
    <div class="form-group col-12">
        <label for="client_id">Choose The Client <b>*</b></label>
        <select class="form-control" id="client" name="client_id" required>
            <option disabled>Choose the client for this order</option>
            @foreach($clients as $client)
                <option
                        value="{{$client->id}}"
                        {{ !empty($order) &&
                                ($order->client_id == $client->id || old('client_id') == $client->id) ? 'selected' : ''}}
                >
                    {{$client->fullName}}
                    (
                    <small>added on {{$client->created_at->format('d/m/Y')}}
                        at {{$client->created_at->format('h:m')}}</small>
                    )
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-12">
        <label for="beneficiary_id">Choose The Beneficiary <b>*</b></label>
        <select class="form-control" id="client" name="beneficiary_id" required>
            <option disabled>Choose the beneficiary for this order</option>
            @foreach($beneficiaries as $beneficiary)
                <option
                        value="{{$beneficiary->id}}"
                        {{ !empty($order) &&
                                ($order->beneficiary_id == $beneficiary->id || old('beneficiary_id') == $beneficiary->id) ? 'selected' : ''}}
                >
                    {{$beneficiary->fullName}}
                    (
                    <small>added on {{$beneficiary->created_at->format('d/m/Y')}}
                        at {{$beneficiary->created_at->format('h:m')}}</small>
                    )
                </option>
            @endforeach
        </select>
    </div>

    @if(!empty($order))
        <div class="form-group col-12">
            <label for="order_state">State Of The Order </label>
            <select class="form-control" id="order_state" name="state">
                <option disabled selected>Choose the state of the order</option>
                @foreach($order_states as $order_state)
                    <option
                            value="{{$order_state}}"
                            {{ !empty($order) &&
                                    ($order->state == $order_state || old('state') == $order_state) ? 'selected' : ''}}
                    >{{ucfirst($order_state)}}</option>
                @endforeach
            </select>
        </div>
    @endif

    @if(!empty($order))
        <div class="form-group col-6">
            <label for="receipt">Add The Receipt For This Order</label>
            <input type="file"
                   accept="image/*"
                   class="form-control-file"
                   id="receipt"
                   name="receipt"
                   placeholder="Update the receipt for this order">
        </div>
    @endif

    <div class="col-12"><hr /></div>

    <div class="col-12" x-data="{
        products: window.products,
        packages: window.packages,
        chosenProducts: {!! !empty($order) ? 'window.chosenProducts_' . $order->id : '[]' !!},
        chosenPackages: {!! !empty($order) ? 'window.chosenPackages_' . $order->id : '[]' !!},
        newProduct: 0,
        newPackage: 0,
        productQuantity: 1,
        packageQuantity: 1,
    }"
         x-init="
            chosenProductIds = chosenProducts.map(p => p.id)

            products = window.products.filter(p => !chosenProductIds.includes(p.id))

            chosenPackageIds = chosenPackages.map(p => p.id)

            packages = window.packages.filter(p => !chosenPackageIds.includes(p.id))
        "
    >
        <h4 class="h4">Add New Products Or Packages</h4>
        <div class="list-group w-100">
            <div class="list-group-item disabled active" x-show="chosenProducts.length">Products</div>
            <template x-for="(product, index) in chosenProducts" :key="index">
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Name: <b x-text="product.name"></b></h5>
                        <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                @click="
                                    chosenProducts = chosenProducts.filter(p => p.id != product.id)
                                    chosenProductIds = chosenProducts.map(p => p.id)

                                    products = window.products.filter(p => !chosenProductIds.includes(p.id))
                                "
                        >X
                        </button>
                    </div>
                    <h6 class="mb-1">Quantity: <b x-text="product.quantity"></b></h6>
                    <input type="hidden" :name="`products[${index}][${product.id}]`" :value="product.quantity"/>
                </div>
            </template>
        </div>

        {{--Packages--}}
        <div class="list-group w-100">
            <div class="list-group-item disabled active" x-show="chosenPackages.length">Packages</div>
            <template x-for="(package, index) in chosenPackages" :key="index">
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Name: <b x-text="package.name"></b></h5>
                        <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                @click="
                                    chosenPackages = chosenPackages.filter(p => p.id != package.id)
                                    chosenPackageIds = chosenPackages.map(p => p.id)

                                    packages = window.packages.filter(p => !chosenPackageIds.includes(p.id))
                                "
                        >X
                        </button>
                    </div>
                    <h6 class="mb-1">Quantity: <b x-text="package.quantity"></b></h6>
                    <input type="hidden" :name="`packages[${index}][${package.id}]`" :value="package.quantity"/>
                </div>
            </template>
        </div>

        <div class="form-group col-12" x-show="chosenPackages.length || chosenProducts.length">
            <br/>
        </div>

        <div class="row">
            <div class="form-group col-6">
                <label for="price">Choose a product to add</label>
                <select class="form-control" id="product" x-model="newProduct">
                    <template x-for="(product, index) in [{id: 0, name: 'Choose a product'},...products]" :key="index">
                        <option
                                :disabled="index == 0"
                                :value="product.id"
                                x-text="product.name"
                                :selected="product.id == newProduct || index == 0"
                        ></option>
                    </template>
                </select>
            </div>
            <div class="form-group col-4">
                <label for="product-quantity">Choose the quantity</label>
                <input type="number"
                       min="1"
                       required
                       class="form-control"
                       id="product-quantity"
                       placeholder="Product Quantity"
                       x-model="productQuantity"
                >
            </div>
            <div class="form-group col-2">
                <label for="price">&nbsp;</label>
                <button type="button" class="btn btn-success"
                        @click="
                            product = products.find(p => newProduct == p.id)

                            if (product) {
                                chosenProducts.push({...product, quantity: productQuantity})
                                products = [...products.filter(p => p.id != newProduct)]
                                newProduct = 0
                                productQuantity = 1
                            }

                            return
                        "
                >
                    Add
                </button>
            </div>
        </div>

        {{--Packages--}}
        <div class="row">
            <div class="form-group col-6">
                <label for="price">Choose a package to add</label>
                <select class="form-control" id="package" x-model="newPackage">
                    <template x-for="(package, index) in [{id: 0, name: 'Choose a package'},...packages]" :key="index">
                        <option
                                :disabled="index == 0"
                                :value="package.id"
                                x-text="package.name"
                                :selected="package.id == newPackage || index == 0"
                        ></option>
                    </template>
                </select>
            </div>
            <div class="form-group col-4">
                <label for="package-quantity">Choose the quantity</label>
                <input type="number"
                       min="1"
                       required
                       class="form-control"
                       id="package-quantity"
                       placeholder="Package Quantity"
                       x-model="packageQuantity"
                >
            </div>
            <div class="form-group col-2">
                <label for="price">&nbsp;</label>
                <button type="button" class="btn btn-success"
                        @click="
                            package = packages.find(p => newPackage == p.id)

                            if (package) {
                                chosenPackages.push({...package, quantity: packageQuantity})
                                packages = [...packages.filter(p => p.id != newPackage)]
                                newPackage = 0
                                packageQuantity = 1
                            }

                            return
                        "
                >
                    Add
                </button>
            </div>
        </div>
    </div>

    <div class="form-group col-12">
        <hr/>
    </div>
</div>
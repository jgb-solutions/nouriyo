<script>
    window.products = {!! $products->map(function($product){
        return ['id' => $product->id, 'name' => $product->name];
        })->toJson() !!}
</script>

<div class="row">
    <div class="form-group col-12">
        <label for="name">Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="name"
               name="name"
               placeholder="Enter the name"
               value="{{!empty($package) ? $package->name : old('name')}}">
    </div>

    <div class="form-group col-12">
        <label for="price">Price <b>*</b></label>
        <input type="number"
               required
               class="form-control"
               id="price"
               name="price"
               placeholder="Enter pakcage price"
               step="0.01"
               min="0"
               value="{{!empty($package) ? $package->price : old('price')}}">
    </div>

    <div class="form-group col-12">
        <label for="description">Description</label>
        <textarea class="form-control"
                  id="description" rows="3"
                  name="description">{{!empty($package) ? $package->description : old('description')}}</textarea>
    </div>

    <div class="form-group col-12">
        <hr/>
    </div>

    <div class="col-12" x-data="{ products: window.products, chosenProducts: [], newProduct: '', quantity: 1 }">
        <h4 class="h4">Add New Products </h4>
        <div class="list-group w-100">
            <template x-for="(product, index) in chosenProducts" :key="index">
                <div class="list-group-item list-group-item-action">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">Name: <b x-text="product.name"></b></h5>
                        <button
                                type="button"
                                class="btn btn-danger btn-sm"
                                @click="
                                    if (chosenProducts.length == 1) {
                                        chosenProducts = []
                                        products = window.products
                                    } else {
                                        chosenProducts = chosenProducts.filter(p => p.id != product.id)
                                        chosenProductIds = chosenProducts.map(p => p.id)

                                        products = window.products.filter(p => !chosenProductIds.includes(p.id))
                                    }
                                "
                        >X
                        </button>
                    </div>
                    <h6 class="mb-1">Quantity: <b x-text="product.quantity"></b></h6>
                    <input type="hidden" :name="`products[${index}][${product.id}]`" :value="product.quantity"/>
                </div>
            </template>
        </div>
        <div class="row">
            <div class="form-group col-6">
                <label for="price">Choose a product to add</label>
                <select class="form-control" id="product" name="product" x-model="newProduct">
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
                <label for="price">Choose the quantity</label>
                <input type="number"
                       required
                       class="form-control"
                       id="quantity"
                       name="quantity"
                       placeholder="Quantity"
                       x-model="quantity"
                >
            </div>
            <div class="form-group col-2">
                <label for="price">&nbsp;</label>
                <button type="button" class="btn btn-success"
                        @click="
                            product = products.find(p => newProduct == p.id)

                            if (product) {
                                chosenProducts.push({...product, quantity})
                                products = [...products.filter(p => p.id != newProduct)]
                                console.log(products)
                                newProduct = 0
                                quantity = 1
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
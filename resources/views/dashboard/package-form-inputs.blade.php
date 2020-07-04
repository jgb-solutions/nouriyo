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
        <hr />
    </div>

    <div class="col-12">
        <h4 class="h4">Add New Products </h4>
        <div class="row">
            <div class="form-group col-6">
                <select class="form-control" id="product" name="product">
                    <option disabled selected>Choose a product</option>
                    @foreach($products as $product)
                        <option
                                value="{{$product->id}}"
                                {{ !empty($package) &&
                                        ($package->product == $product || old('product') == $product) ? 'selected' : ''}}
                        >{{$product->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-4">
                <input type="number"
                       required
                       class="form-control"
                       id="quantity"
                       name="quantity"
                       placeholder="Quantity"
                       min="1"
                       value="{{!empty($package) ? $package->quantity : old('quantity')}}">
            </div>
            <div class="form-group col-2">
                <button type="button" class="btn btn-secondary">
                    Add
                </button>
            </div>
        </div>
    </div>

    <div class="form-group col-12">
        <hr />
    </div>
</div>
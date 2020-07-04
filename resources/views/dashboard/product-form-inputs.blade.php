<div class="row">
    <div class="form-group col-12">
        <label for="name">Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="name"
               name="name"
               placeholder="Enter the name"
               value="{{!empty($product) ? $product->name : old('name')}}">
    </div>

    <div class="form-group col-12">
        <label for="buying_price">Buying Price <b>*</b></label>
        <input type="number"
               required
               class="form-control"
               id="buying_price"
               name="buying_price"
               placeholder="Enter buying price"
               step="0.01"
               min="0"
               value="{{!empty($product) ? $product->buying_price : old('buying_price')}}">
    </div>

    <div class="form-group col-12">
        <label for="selling_price">Selling Price <b>*</b></label>
        <input type="number"
               required
               class="form-control"
               id="selling_price"
               name="selling_price"
               placeholder="Enter selling price"
               step="0.01"
               min="0"
               value="{{!empty($product) ? $product->selling_price : old('selling_price')}}">
    </div>

    <div class="form-group col-12">
        <label for="quantity">Quantity <b>*</b></label>
        <input type="number"
               required
               class="form-control"
               id="quantity"
               name="quantity"
               placeholder="Enter the quantity"
               value="{{!empty($product) ? $product->quantity : old('quantity')}}">
    </div>


    <div class="form-group col-6">
        <label for="image">Choose an image</label>
        <input type="file"
               accept="image/*"
               class="form-control-file"
               id="image"
               name="image"
               placeholder="Choose an image for the product">
    </div>

    <div class="form-group col-12">
        <label for="description">Description</label>
        <textarea class="form-control"
                  id="description" rows="3"
                  name="description">{{!empty($product) ? $product->description : old('description')}}</textarea>
    </div>
</div>
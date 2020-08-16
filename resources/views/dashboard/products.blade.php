@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Products</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">Add
                        a product
                    </button>
                    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog"
                         aria-labelledby="addProductModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addProductModalLabel">Add a new Product</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{route('dashboard.products')}}"
                                          enctype="multipart/form-data">
                                        @csrf

                                        @include('dashboard.product-form-inputs')
                                        <button type="submit" class="btn btn-primary">Add Product</button>
                                    </form>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @include('inc.errors')

        @if($products->count())
            <div class="table-responsive">
                <table class="table table-striped table-sm table-bordered">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        @if(auth()->user()->admin)
                            <th>Buying Price</th>
                        @endif
                        <th>Selling Price</th>
                        @if(auth()->user()->admin)
                            <th>Edit</th>
                            <th>Delete</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>
                                <img width="50"
                                     src="{{ $product->image ? $product->image_url : 'https://via.placeholder.com/50C/O'}}"/>
                            </td>
                            <td>
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#showProductModal-{{$product->id}}">
                                    {{$product->name}}
                                </button>
                                <div class="modal fade" id="showProductModal-{{$product->id}}" tabindex="-1"
                                     role="dialog"
                                     aria-labelledby="showProductModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="showProductModalLabel">Product's details</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">Info</div>
                                                    <div class="list-group-item list-group-item-action">
                                                        <h6 class="mb-1">Name: <b>{{$product->name}}</b></h6>
                                                        <h6 class="mb-1">Quantity: <b>{{$product->quantity}}</b></h6>
                                                            <h6 class="mb-1">Buying Price: <b>{{$product->buying_price}}
                                                                    dollars</b>
                                                            </h6>
                                                            <h6 class="mb-1">Selling Price:
                                                                <b>{{$product->selling_price}} dollars</b>
                                                            </h6>
                                                            <h6 class="mb-1">Description:
                                                                <b>{{$product->description}}</b>
                                                            </h6>
                                                    </div>
                                                </div>

                                                @if($product->image)
                                                    <div class="list-group w-100">
                                                        <div class="list-group-item disabled active">Photo</div>
                                                        <div class="list-group-item list-group-item-action">
                                                            <img
                                                                    height="200"
                                                                    src="{{$product->image_url }}"
                                                            />
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>{{$product->quantity}}</td>
                            @if(auth()->user()->admin)
                                <td>{{$product->buying_price}}</td>
                            @endif
                            <td>{{$product->selling_price}}</td>
                            @if(auth()->user()->admin)
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#editProductModal-{{$product->id}}">Edit
                                    </button>
                                    <div class="modal fade" id="editProductModal-{{$product->id}}" tabindex="-1"
                                         role="dialog"
                                         aria-labelledby="editProductModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editProductModalLabel">
                                                        Edit {{$product->name}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post"
                                                          action="{{route('dashboard.update-product', $product->id)}}"
                                                          enctype="multipart/form-data">
                                                        @method("put")
                                                        @csrf

                                                        @include('dashboard.product-form-inputs', ['product' => $product])
                                                        <button type="submit" class="btn btn-primary">Update</button>
                                                    </form>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <form method="post" action="{{route('dashboard.delete-product', $product->id)}}">
                                        @method("delete")
                                        @csrf
                                        <button onclick='return confirm("Are you sure?")' type="submit"
                                                class="btn btn-danger">Delete
                                        </button>
                                    </form>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $products->links() }}
        @endif
    </main>
@endsection
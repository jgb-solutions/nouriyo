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
                                    <form method="post" action="{{route('dashboard.products')}}" enctype="multipart/form-data">
                                        @csrf

                                        @include('dashboard.product-form-inputs')
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

        @if($products->count())
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        @if(auth()->user()->admin)
                            <th>Buying Price</th>
                        @endif
                        <th>Selling Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td>
                                <img width="50"
                                     src="{{ $product->image ? $product->image : 'https://via.placeholder.com/50C/O'}}"/>
                            </td>
                            <td>{{$product->name}}</td>
                            <td>{{$product->quantity}}</td>
                            @if(auth()->user()->admin)
                                <td>{{$product->buying_price}}</td>
                            @endif
                            <td>{{$product->selling_price}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $products->links() }}
        @endif
    </main>
@endsection
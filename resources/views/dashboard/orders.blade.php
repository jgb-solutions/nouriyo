@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Orders</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
                <div class="btn-group mr-2">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addOrderModal">Add
                        an order
                    </button>
                    <div class="modal fade" id="addOrderModal" tabindex="-1" role="dialog"
                         aria-labelledby="addOrderModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addOrderModalLabel">Add a new Order</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form method="post" action="{{route('dashboard.orders')}}"
                                          enctype="multipart/form-data">
                                        @csrf

                                        @include('dashboard.order-form-inputs')
                                        <button type="submit" class="btn btn-primary">Add Order</button>
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

        @if($orders->count())
            <div class="table-responsive">
                <table class="table table-striped table-sm table-bordered">
                    <thead>
                    <tr>
                        <th>Number</th>
                        <th>Client</th>
                        <th>Benefiary</th>
                        <th>Total</th>
                        @if(auth()->user()->admin)
                            <th>Edit</th>
                            <th>Delete</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#showOrderModal-{{$order->id}}">
                                    {{$order->number}}
                                </button>
                                <div class="modal fade" id="showOrderModal-{{$order->id}}" tabindex="-1"
                                     role="dialog"
                                     aria-labelledby="showOrderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title"
                                                    id="showOrderModalLabel">{{$order->name}}</h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div><img height="200" src="{{$order->image_url}}"/></div>
                                                <p>
                                                    {{$order->description}}
                                                </p>
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
                            <td>{{$order->client->fullName}}</td>
                            <td>{{$order->beneficiary->fullName}}</td>
                            <td>{{$order->total}}</td>
                            @if(auth()->user()->admin)
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal"
                                            data-target="#editOrderModal-{{$order->id}}">Edit
                                    </button>
                                    <div class="modal fade" id="editOrderModal-{{$order->id}}" tabindex="-1"
                                         role="dialog"
                                         aria-labelledby="editOrderModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editOrderModalLabel">
                                                        Edit {{$order->name}}</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post"
                                                          action="{{route('dashboard.update-order', $order->id)}}"
                                                          enctype="multipart/form-data">
                                                        @method("put")
                                                        @csrf

                                                        @include('dashboard.order-form-inputs', ['order' => $order])
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
                                    <form method="post" action="{{route('dashboard.delete-order', $order->id)}}">
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
            {{ $orders->links() }}
        @endif
    </main>
@endsection
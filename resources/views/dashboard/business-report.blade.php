@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 py-md-2">
        <h1 class="h2">
            Your Orders
        </h1>

        <h3 class="h3">
            <small>Total Sold: {{ $user->total_sold }} dollars</small> &bullet;
            <small>Total Due: {{ $user->total_due }} dollars</small> &bullet;
            <small>Total Paid: {{ $user->paid }} dollars</small> &bullet;
        </h3>

        @if($orders->count())
            <div class="table-responsive">
                <table class="table table-striped table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Number</th>
                            <th>Client</th>
                            <th>Benefiary</th>
                            <th>Transport Fee</th>
                            <th>Service Fee</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#showOrderModal-{{$order->id}}"
                                        style="{{$order->cancelled ? 'text-decoration:line-through;' : ''}}"
                                        title="{{$order->cancelled ? 'This order was cancelled. Please contact an administrator.' : ''}}"
                                        {{$order->cancelled ? 'disabled' : ''}}>
                                    {{$order->number}} {{$order->cancelled ? '(cancelled)' : ''}}
                                </button>
                                <div class="modal fade" id="showOrderModal-{{$order->id}}" tabindex="-1"
                                     role="dialog"
                                     aria-labelledby="showOrderModalLabel" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="showOrderModalLabel">
                                                    Details for order number: <b>{{$order->number}}</b>
                                                    <br/>
                                                    Taken by: <b>{{$order->agentWhoTookTheOrder->fullName}}</b>
                                                    <small>({{$order->agentWhoTookTheOrder->business}})</small>

                                                    @if ($order->agentWhoDeliveredTheOrder)
                                                        <br/>
                                                        Delivered by:
                                                        <b>{{$order->agentWhoDeliveredTheOrder->fullName}}</b>
                                                        <small>({{$order->agentWhoDeliveredTheOrder->business}})</small>
                                                    @endif

                                                    <br/>
                                                    Total Without Fees: <b>{{$order->total}}</b> <br/>
                                                    Total With Fees:
                                                    <b>{{$order->total + $order->transport_fee + $order->service_fee}}</b>
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">Client</div>
                                                    <div class="list-group-item list-group-item-action">
                                                        @if($order->client)
                                                            <h5 class="mb-1">Name: <b>{{$order->client->fullName}}</b>
                                                            </h5>
                                                            <h5 class="mb-1">Phone: <b>{{$order->client->phone}}</b>
                                                            </h5>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">Beneficiary</div>
                                                    <div class="list-group-item list-group-item-action">
                                                        @if($order->beneficiary)
                                                            <h5 class="mb-1">Name:
                                                                <b>{{$order->beneficiary->fullName}}</b>
                                                            </h5>
                                                            <h5 class="mb-1">Phone:
                                                                <b>{{$order->beneficiary->phone}}</b>
                                                            </h5>
                                                            <h5 class="mb-1">Address:
                                                                <b>{{$order->beneficiary->address}}</b>
                                                            </h5>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">Fees</div>
                                                    <div class="list-group-item list-group-item-action">
                                                        <h5 class="mb-1">Transport Fee: <b>{{$order->transport_fee}}
                                                                dollars</b></h5>
                                                        <h5 class="mb-1">Service Fee: <b>{{$order->service_fee}}
                                                                dollars</b></h5>
                                                    </div>
                                                </div>

                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">State</div>
                                                    <div class="list-group-item list-group-item-action">
                                                        <h5 class="mb-1">Current State:
                                                            <b>{{ucfirst($order->state)}}</b></h5>
                                                    </div>
                                                </div>

                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">Receipt</div>
                                                    <div class="list-group-item list-group-item-action">
                                                        @if($order->receipt)
                                                            <img
                                                                    height="200"
                                                                    src="{{ $order->receipt_url }}"
                                                            />
                                                        @else
                                                            <h5 class="mb-1">This order has no receipt yet</h5>
                                                        @endif
                                                    </div>
                                                </div>

                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">Products</div>
                                                    @foreach($order->products as $product)
                                                        <div class="list-group-item list-group-item-action">
                                                            <h5 class="mb-1">Name: <b>{{$product->name}}</b></h5>
                                                            <h6 class="mb-1">Quantity: <b>{{$order->products_count}}</b>
                                                            </h6>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                {{--Packages--}}
                                                <div class="list-group w-100">
                                                    <div class="list-group-item disabled active">Packages</div>
                                                    @foreach($order->packages as $package)
                                                        <div class="list-group-item list-group-item-action">
                                                            <h5 class="mb-1">Name: <b>{{$package->name}}</b></h5>
                                                            <h6 class="mb-1">Quantity: <b>{{$order->packages_count}}</b>
                                                            </h6>
                                                        </div>
                                                    @endforeach
                                                </div>
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
                            @if($order->client)
                                <td>{{$order->client->fullName}}</td>
                            @endif
                            @if($order->beneficiary)
                                <td>{{$order->beneficiary->fullName}}</td>
                            @endif
                            <td>{{$order->transport_fee}} dollars</td>
                            <td>{{$order->service_fee}} dollars</td>
                            <td>{{$order->total}} dollars</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            {{ $orders->links() }}
        @endif
    </main>
@endsection
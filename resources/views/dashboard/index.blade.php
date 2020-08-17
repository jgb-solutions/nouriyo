@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Dashboard</h1>
        </div>

        <div class="row">
            <div class="col-sm-6">
                <div class="list-group w-100">
                    <div class="list-group-item disabled active">Orders Delivered Today</div>
                    @if(count($orders_of_today))
                        @foreach($orders_of_today as $order)
                            <div class="list-group-item list-group-item-action">
                                <h6 class="mb-1">
                                    Number: <a
                                            href="{{route('dashboard.orders', ['number' => $order->number])}}">
                                        <b>{{$order->number}}</b>
                                    </a>
                                </h6>
                                <h6 class="mb-1">Total Without Fees: <b>{{$order->total}} dollars</b></h6>
                                <h6 class="mb-1">Total With Fees:
                                    <b>{{$order->total + $order->transport_fee + $order->service_fee }} dollars</b>
                                </h6>

                                <h6 class="mb-1">State:
                                    <b>{{ucfirst($order->state)}}</b>
                                </h6>
                            </div>
                        @endforeach
                    @else
                        <div class="list-group-item list-group-item-action">
                            <h6 class="h6">There were no orders today</h6>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-sm-6">
                <div class="list-group w-100">
                    <div class="list-group-item disabled active">Orders Delivered Today</div>
                    @if(count($latest_orders))
                        @foreach($latest_orders as $order)
                            <div class="list-group-item list-group-item-action">
                                <h6 class="mb-1">
                                    Number: <a
                                            href="{{route('dashboard.orders', ['number' => $order->number])}}">
                                        <b>{{$order->number}}</b>
                                    </a>
                                </h6>
                                <h6 class="mb-1">Total Without Fees: <b>{{$order->total}} dollars</b></h6>
                                <h6 class="mb-1">Total With Fees:
                                    <b>{{$order->total + $order->transport_fee + $order->service_fee }} dollars</b>
                                </h6>

                                <h6 class="mb-1">State:
                                    <b>{{ucfirst($order->state)}}</b>
                                </h6>
                            </div>
                        @endforeach
                    @else
                        <div class="list-group-item list-group-item-action">
                            <h6 class="h6">There were no orders today</h6>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </main>
@endsection
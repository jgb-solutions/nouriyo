@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Dashboard</h1>
        </div>

        <div class="col-sm-4">
            <div class="list-group w-100">
                <div class="list-group-item disabled active">Client</div>
                <div class="list-group-item list-group-item-action">
                    <h5 class="mb-1">Name: <b>{{$order->client->fullName}}</b></h5>
                    <h5 class="mb-1">Phone: <b>{{$order->client->phone}}</b></h5>
                </div>
            </div>
            <div class="list-group w-100">
                <div class="list-group-item disabled active">Beneficiary</div>
                <div class="list-group-item list-group-item-action">
                    <h5 class="mb-1">Name: <b>{{$order->beneficiary->fullName}}</b>
                    </h5>
                    <h5 class="mb-1">Phone: <b>{{$order->beneficiary->phone}}</b>
                    </h5>
                    <h5 class="mb-1">Address:
                        <b>{{$order->beneficiary->address}}</b></h5>
                </div>
            </div>
            <div class="list-group w-100">
                <div class="list-group-item disabled active">Fees</div>
                <div class="list-group-item list-group-item-action">
                    <h5 class="mb-1">Transport Fee: <b>{{$order->transport_fee}} dollars</b></h5>
                    <h5 class="mb-1">Service Fee: <b>{{$order->service_fee}} dollars</b></h5>
                </div>
            </div>

            <div class="list-group w-100">
                <div class="list-group-item disabled active">State</div>
                <div class="list-group-item list-group-item-action">
                    <h5 class="mb-1">Current State:
                        <b>{{ucfirst($order->state)}}</b></h5>
                </div>
            </div>
        </div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4"></div>
    </main>
@endsection
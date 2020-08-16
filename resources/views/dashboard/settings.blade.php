@extends('layout.dashboard')

@section('content')
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Orders' Settings</h1>
        </div>

        <form method="post"
              action="{{route('dashboard.update-settings', $settings->id)}}">
            @method("put")
            @csrf

            <div class="row">
                <div class="col-6">
                    <div class="row">
                        <div class="form-group col-12">
                            <label for="transport_fee">Transport Fee <b>*</b></label>
                            <input type="number"
                                   required
                                   class="form-control"
                                   id="transport_fee"
                                   name="transport_fee"
                                   placeholder="Enter the transport fee for orders"
                                   step="0.01"
                                   min="0"
                                   value="{{!empty($settings) ? $settings->transport_fee : old('transport_fee')}}">
                        </div>

                        <div class="form-group col-12">
                            <label for="service_fee">Service Fee <b>*</b></label>
                            <input type="number"
                                   required
                                   class="form-control"
                                   id="service_fee"
                                   name="service_fee"
                                   placeholder="Enter the service fee for orders"`
                                   step="0.01"
                                   min="0"
                                   value="{{!empty($settings) ? $settings->service_fee : old('service_fee')}}">
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </main>
@endsection
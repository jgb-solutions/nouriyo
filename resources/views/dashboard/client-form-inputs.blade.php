<div class="row">
    <div class="form-group col-12">
        <label for="first_name">First Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="first_name"
               name="first_name"
               placeholder="Enter the first name"
               value="{{!empty($client) ? $client->first_name : old('first_name')}}">
    </div>

    <div class="form-group col-12">
        <label for="last_name">Last Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="last_name"
               name="last_name"
               placeholder="Enter the last name"
               value="{{!empty($client) ? $client->last_name : old('last_name')}}">
    </div>

    <div class="form-group col-12">
        <label for="phone">Phone <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="phone"
               name="phone"
               placeholder="Enter the phone number"
               value="{{!empty($client) ? $client->phone : old('phone')}}">
    </div>

    {{--<div class="form-group col-12">--}}
        {{--<label for="email">Email <b>*</b></label>--}}
        {{--<input type="email"--}}
               {{--class="form-control"--}}
               {{--id="email"--}}
               {{--name="email"--}}
               {{--placeholder="Enter the email"--}}
               {{--value="{{!empty($client) ? $client->email : old('email')}}">--}}
    {{--</div>--}}

    {{--<div class="form-group col-12">--}}
        {{--<label for="country">Country </label>--}}
        {{--<select class="form-control" id="exampleFormControlSelect1" name="country">--}}
            {{--<option disabled>Choose a country</option>--}}
            {{--@foreach($countries as $country)--}}
                {{--<option--}}
                        {{--value="{{$country}}"--}}
                        {{--{{ !empty($client) &&--}}
                                {{--($client->country == $country || old('country') == $country) ? 'selected' : ''}}--}}
                {{-->{{$country}}</option>--}}
            {{--@endforeach--}}
        {{--</select>--}}
    {{--</div>--}}

    {{--<div class="form-group col-12">--}}
        {{--<label for="state">State</label>--}}
        {{--<input type="text"--}}
               {{--class="form-control"--}}
               {{--id="state"--}}
               {{--name="state"--}}
               {{--placeholder="Enter the last name"--}}
               {{--value="{{!empty($client) ? $client->state : old('state')}}">--}}
    {{--</div>--}}

    {{--<div class="form-group col-12">--}}
        {{--<label for="city">City</label>--}}
        {{--<input type="text"--}}
               {{--class="form-control"--}}
               {{--id="city"--}}
               {{--name="city"--}}
               {{--placeholder="Enter the last name"--}}
               {{--value="{{!empty($client) ? $client->city : old('city')}}">--}}
    {{--</div>--}}

    {{--<div class="form-group col-12">--}}
        {{--<label for="address">Address</label>--}}
        {{--<textarea class="form-control"--}}
                  {{--id="address" rows="3"--}}
                  {{--name="address">{{!empty($client) ? $client->address : old('address')}}</textarea>--}}
    {{--</div>--}}
</div>
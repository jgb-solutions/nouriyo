<div class="row">
    <div class="form-group col-12">
        <label for="business">Business <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="business"
               name="business"
               placeholder="Enter the first name"
               value="{{!empty($agent) ? $agent->business : old('business')}}">
    </div>

    <div class="form-group col-12">
        <label for="first_name">First Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="first_name"
               name="first_name"
               placeholder="Enter the first name"
               value="{{!empty($agent) ? $agent->first_name : old('first_name')}}">
    </div>

    <div class="form-group col-12">
        <label for="last_name">Last Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="last_name"
               name="last_name"
               placeholder="Enter the last name"
               value="{{!empty($agent) ? $agent->last_name : old('last_name')}}">
    </div>

    <div class="form-group col-12">
        <label for="email">Email <b>*</b></label>
        <input type="email"
               required
               class="form-control"
               id="email"
               name="email"
               placeholder="Enter the email"
               value="{{!empty($agent) ? $agent->email : old('email')}}">
    </div>

    <div class="form-group col-12">
        <label for="password">
            {!! !empty($agent) || old('password') ? 'New Password' : 'Password <b>*</b>' !!}
        </label>
        <input type="password"
               {{!empty($agent) || old('password') ? '' : 'required' }}
               class="form-control"
               id="password"
               name="password"
               placeholder="Enter a new password to update it"
        >
    </div>

    <div class="form-group col-12">
        <label for="limit">Limit <b>*</b></label>
        <input type="number"
               required
               class="form-control"
               id="limit"
               name="limit"
               placeholder="Enter the limit of the agent"
               step="0.01"
               min="0"
               value="{{!empty($agent) ? $agent->limit : old('limit')}}">
    </div>

    <div class="form-group col-12">
        <label for="due">Total Due</label>
        <input type="number"
               required
               class="form-control"
               id="due"
               name="due"
               placeholder="Enter the total due by the agent"
               step="0.01"
               min="0"
               value="{{!empty($agent) ? $agent->due : old('due')}}">
    </div>

    <div class="form-group col-12">
        <label for="paid">Total Paid</label>
        <input type="number"
               required
               class="form-control"
               id="paid"
               name="paid"
               placeholder="Enter the total paid by the agent"
               step="0.01"
               min="0"
               value="{{!empty($agent) ? $agent->paid : old('paid')}}">
    </div>

    <div class="form-group col-12">
        <label for="country">Country </label>
        <select class="form-control" id="exampleFormControlSelect1" name="country">
            <option disabled>Choose a country</option>
            @foreach($countries as $country)
                <option
                        value="{{$country}}"
                        {{ !empty($agent) &&
                                ($agent->country == $country || old('country') == $country) ? 'selected' : ''}}
                >{{$country}}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-12">
        <label for="state">State</label>
        <input type="text"
               class="form-control"
               id="state"
               name="state"
               placeholder="Enter the last name"
               value="{{!empty($agent) ? $agent->state : old('state')}}">
    </div>

    <div class="form-group col-12">
        <label for="city">City</label>
        <input type="text"
               class="form-control"
               id="city"
               name="city"
               placeholder="Enter the last name"
               value="{{!empty($agent) ? $agent->city : old('city')}}">
    </div>

    <div class="form-group col-12">
        <label for="zip">Zip Code</label>
        <input type="text"
               class="form-control"
               id="zip"
               name="zip"
               placeholder="Enter the last name"
               value="{{!empty($agent) ? $agent->zip : old('zip')}}">
    </div>

    <div class="form-group col-12">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="active-{{!empty($agent) ? $agent->id : ''}}" name="active" {{
            !empty($agent) && ($agent->active || old('active')) ?
                    'checked' : ''
             }}>
            <label class="form-check-label" for="active-{{!empty($agent) ? $agent->id : ''}}">
                Active
            </label>
        </div>
    </div>


    <div class="form-group col-12">
        <label for="address">Address</label>
        <textarea class="form-control"
                  id="address" rows="3"
                  name="address">{{!empty($agent) ? $agent->address : old('address')}}</textarea>
    </div>
</div>
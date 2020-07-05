<div class="row">
    <div class="form-group col-12">
        <label for="first_name">First Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="first_name"
               name="first_name"
               placeholder="Enter the first name"
               value="{{!empty($beneficiary) ? $beneficiary->first_name : old('first_name')}}">
    </div>

    <div class="form-group col-12">
        <label for="last_name">Last Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="last_name"
               name="last_name"
               placeholder="Enter the last name"
               value="{{!empty($beneficiary) ? $beneficiary->last_name : old('last_name')}}">
    </div>

    <div class="form-group col-12">
        <label for="phone">Phone <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="phone"
               name="phone"
               placeholder="Enter the phone number"
               value="{{!empty($beneficiary) ? $beneficiary->phone : old('phone')}}">
    </div>

    <div class="form-group col-12">
        <label for="address">Address <b>*</b></label>
        <textarea class="form-control"
                  required
                  id="address" rows="3"
                  name="address">{{!empty($beneficiary) ? $beneficiary->address : old('address')}}</textarea>
    </div>
</div>
<div class="row">
    <div class="form-group col-12">
        <label for="first_name">First Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="first_name"
               name="first_name"
               placeholder="Enter the first name"
               value="{{!empty($admin) ? $admin->first_name : old('first_name')}}">
    </div>

    <div class="form-group col-12">
        <label for="last_name">Last Name <b>*</b></label>
        <input type="text"
               required
               class="form-control"
               id="last_name"
               name="last_name"
               placeholder="Enter the last name"
               value="{{!empty($admin) ? $admin->last_name : old('last_name')}}">
    </div>

    <div class="form-group col-12">
        <label for="email">Email <b>*</b></label>
        <input type="email"
               required
               class="form-control"
               id="email"
               name="email"
               placeholder="Enter the email"
               value="{{!empty($admin) ? $admin->email : old('email')}}">
    </div>

    <div class="form-group col-12">
        <label for="password">
            {!! !empty($admin) || old('password') ? 'New Password' : 'Password <b>*</b>' !!}
        </label>
        <input type="password"
               {{!empty($admin) || old('password') ? '' : 'required' }}
               class="form-control"
               id="password"
               name="password"
               placeholder="Enter a new password to update it"
        >
    </div>
</div>
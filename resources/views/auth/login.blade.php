@extends('layout.app')

@section('page')
@section('styles')
    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        html,
        body {
            height: 100%;
        }

        body {
            display: -ms-flexbox;
            display: flex;
            -ms-flex-align: center;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }

        .form-signin .checkbox {
            font-weight: 400;
        }

        .form-signin .form-control {
            position: relative;
            box-sizing: border-box;
            height: auto;
            padding: 10px;
            font-size: 16px;
        }

        .form-signin .form-control:focus {
            z-index: 2;
        }

        .form-signin input[type="email"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }

        .form-container {
            display: flex;
            flex: 1;
            justify-content: center;
            align-items: center;
        }

    </style>
@endsection

<div class="form-container">
    <form class="form-signin" method="post" action="{{route('auth.doLogin')}}">
        @csrf
        <h2 class="h2 mb-3 text-center text-uppercase">
            <img
                    src="{{asset('assets/images/logo.png')}}"
                    alt="{{config('app.name')}}"
                    class="img-fluid"
            />
        </h2>
        <label
                for="email"
                class="sr-only">
            Email address
        </label>
        <input
                name="email"
                type="email"
                id="email"
                class="form-control"
                placeholder="Email address"
                required autofocus>
        @error('email')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <label
                for="password"
                class="sr-only">Password
        </label>
        <input
                name="password"
                type="password"
                id="password"
                class="form-control"
                placeholder="Password"
                required>

        @error('password')
        <div class="alert alert-danger">{{ $message }}</div>
        @enderror

        <button class="btn btn-lg btn-primary btn-block" type="submit">Log In</button>
    </form>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col  mx-auto">
            <div class="text-center mb-6">
                <img src="{{ env('CDN_URL').$appSetting->website_logo}}" class="" alt="{{$appSetting->website_name}}">
            </div>
            <div class="row justify-content-center">

                <div class="col-md-8">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <div class="card-group mb-0">
                        <div class="card p-4">
                            <form method="POST" action="{{ route('password.email') }}" autocomplete="off">
                            @csrf
                            <div class="card-body">
                                <h3>{{ __('Reset Password') }}</h3>
                                <div class="input-group mb-4">
                                    <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                                    <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid state-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus placeholder="Email Address">

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-gradient-primary btn-block">{{ __('Send Password Reset Link') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card text-white bg-primary py-5 d-md-down-none login-transparent">
                            <div class="card-body text-center align-items-center">
                                <div>
                                    <h2>Forget it</h2>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit aliqua.</p>
                                    <a href="{{route('login')}}" class="btn btn-gradient-success active mt-0">send me back</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

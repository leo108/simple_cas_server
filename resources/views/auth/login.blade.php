@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        @if ($errors->has('global'))
                        <div class="alert alert-danger">{{ $errors->first('global') }}</div>
                        @endif
                        <h3 class="panel-title">Central Authentication Service</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route("cas_login_action", $origin_req) }}" method="post" role="form">
                            {{ csrf_field() }}
                            <fieldset>
                                <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                    <input class="form-control" placeholder="@lang('auth.username')" id="name" name="name" value="{{ old('name') }}" type="text" autofocus>
                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                    <input class="form-control" placeholder="@lang('auth.password')" id="password" name="password" type="password">
                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="1">@lang('auth.remember_me')
                                    </label>
                                    @if (config('cas.allow_reset_pwd'))
                                    <div class="pull-right">
                                        <a href="{{ route('request_pwd_reset_email_page') }}">@lang('passwords.forget_pwd')</a>
                                    </div>
                                    @endif
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" class="btn btn-lg btn-success btn-block">@lang('common.submit')</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

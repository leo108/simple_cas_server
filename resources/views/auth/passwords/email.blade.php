@extends('layouts.app')

<!-- Main Content -->
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                        <h3 class="panel-title">@lang('passwords.reset_pwd')</h3>
                    </div>
                    <div class="panel-body">
                        <form action="{{ route("send_pwd_reset_email") }}" method="post" role="form">
                            {{ csrf_field() }}
                            <fieldset>
                                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                    <input class="form-control" placeholder="@lang('passwords.email')" id="email" name="email" value="{{ old('email') }}" type="text" autofocus>
                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
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

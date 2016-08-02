@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Central Authentication Service
                            @if ($errors->has('global'))
                                <div class="alert alert-danger">{{ $errors->first('global') }}</div>
                            @endif
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-success">
                            @lang('auth.logged_in_as', ['name' => Auth::user()->name])
                        </div>
                        <button class="btn btn-primary pull-left" id="btn_change_pwd">@lang('auth.change_pwd')</button>
                        @if(Auth::user()->admin)
                        <a href="{{ route('admin_home') }}" class="btn-success btn col-md-offset-1">System Manage</a>
                        @endif
                        <button class="btn btn-danger pull-right" id="btn_logout">@lang('auth.logout')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    @include('vendor.bootbox')
    <script>
        var logoutUrl = '{{ route('cas_logout') }}';
        $('#btn_logout').click(function () {
            bootbox.confirm('@lang('message.confirm_logout')', function (ret) {
                if (ret) {
                    location.href = logoutUrl;
                }
            });
        });
    </script>
@endsection


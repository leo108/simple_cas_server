@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Central Authentication Service
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-success">
                            Welcome {{ Auth::user()->name }} !
                        </div>
                        <button class="btn btn-primary pull-left" id="btn_change_pwd">Change Password</button>
                        @if(Auth::user()->admin)
                        <a href="{{ route('admin_home') }}" class="btn-success btn col-md-offset-1">System Manage</a>
                        @endif
                        <button class="btn btn-danger pull-right" id="btn_logout">Logout</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

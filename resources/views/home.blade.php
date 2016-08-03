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
                            <a href="{{ route('admin_home') }}" class="btn-success btn col-md-offset-1">System
                                Manage</a>
                        @endif
                        <button class="btn btn-danger pull-right" id="btn_logout">@lang('auth.logout')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="change-pwd-dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('auth.change_pwd')</h4>
                </div>
                <div class="modal-body">
                    <form role="form" class="form-horizontal">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="old-pwd" class="col-sm-4 control-label">@lang('auth.old_pwd')</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="old-pwd"
                                       placeholder="@lang('auth.old_pwd')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new-pwd" class="col-sm-4 control-label">@lang('auth.new_pwd')</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="new-pwd"
                                       placeholder="@lang('auth.new_pwd')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="new-pwd2" class="col-sm-4 control-label">@lang('auth.new_pwd2')</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" id="new-pwd2"
                                       placeholder="@lang('auth.new_pwd2')">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.close')</button>
                    <button type="button" class="btn btn-primary" id="btn-save-pwd">@lang('common.ok')</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('javascript')
    @include('vendor.bootbox')
    <script>
        var logoutUrl = '{{ route('cas_logout') }}';
        var changePwdUrl = '{{ route('change_pwd', [], false) }}';
        var $pwdDialog = $('#change-pwd-dialog');
        $('#btn_logout').click(function () {
            bootbox.confirm('@lang('message.confirm_logout')', function (ret) {
                if (ret) {
                    location.href = logoutUrl;
                }
            });
        });

        $('#btn_change_pwd').click(function () {
            $pwdDialog.modal();
        });

        $('#btn-save-pwd').click(function () {
            $pwdDialog.find('div.form-group').removeClass('has-error');
            var map = {
                'old': 'old-pwd',
                'new1': 'new-pwd',
                'new2': 'new-pwd2'
            };
            var val = {};

            for (var x in map) {
                var $input = $('#' + map[x]);
                val[x] = $input.val();
                if ($input.val() == '') {
                    $input.closest('div.form-group').addClass('has-error');
                }
            }

            if (val['new1'] != val['new2']) {
                $('#new-pwd2').closest('div.form-group').addClass('has-error');
            }

            if ($pwdDialog.find('.has-error').length > 0) {
                return;
            }

            var req = {
                'old': val['old'],
                'new': val['new1'],
                '_token': $pwdDialog.find('form input[name=_token]').val()
            };
            $.post(changePwdUrl, req, function (ret) {
                alert(ret.msg);
                if (ret.code != 0) {
                    return;
                }
                $pwdDialog.modal('hide');
            }, 'json');
        });
    </script>
@endsection


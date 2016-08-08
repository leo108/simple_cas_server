@extends('layouts.admin')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">@lang('admin.menu.users')</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <form class="form-inline" role="form" id="search-form">
                    <div class="form-group">
                        <select class="form-control" name="enabled" v-model="enabled">
                            <option value="">@lang('admin.user.enabled_all')</option>
                            <option value="0">@lang('admin.user.enabled_no')</option>
                            <option value="1">@lang('admin.user.enabled_yes')</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input name="search" type="text" class="form-control" v-model="search" placeholder="@lang('admin.user.username')/@lang('admin.user.real_name')/@lang('admin.user.email')" />
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary">@lang('admin.search')</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="user-tbl">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('admin.user.username')</th>
                            <th>@lang('admin.user.email')</th>
                            <th>@lang('admin.user.real_name')</th>
                            <th>@lang('admin.user.enabled')</th>
                            <th>@lang('admin.user.admin')</th>
                            <th>@lang('admin.user.created_at')</th>
                            <th>@lang('admin.user.updated_at')</th>
                            <th>
                                <button class="btn btn-xs btn-primary" id="add-btn">{{ trans('admin.user.add') }}</button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr v-for="item in users">
                            <td>@{{ item.id }}</td>
                            <td>@{{ item.name }}</td>
                            <td>@{{ item.email }}</td>
                            <td>@{{ item.real_name }}</td>
                            <td>@{{{ bool2icon(item.enabled) }}}</td>
                            <td>@{{{ bool2icon(item.admin) }}}</td>
                            <td>@{{ item.created_at }}</td>
                            <td>@{{ item.updated_at }}</td>
                            <td>
                                <a href="javascript:void(0)" v-on:click="edit(item)">{{ trans('admin.edit') }}</a>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                    <div class="pull-left">@lang('admin.total') {{ $users->total() }}</div>
                    <div class="pull-right">{{ $users->appends($query)->links() }}</div>
                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /#page-wrapper -->

    <div class="modal fade" id="edit-dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('admin.user.add_or_edit')</h4>
                </div>
                <div class="modal-body">
                    <form role="form" class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" v-model="user.id" name="id">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('admin.user.username')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" v-model="user.name" name="name"
                                       placeholder="@lang('admin.user.username')" :disabled="isEdit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('admin.user.password')</label>
                            <div class="col-sm-6">
                                <input type="password" class="form-control" v-model="user.password" name="password"
                                       placeholder="@lang('admin.user.password')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('admin.user.real_name')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" v-model="user.real_name" name="real_name"
                                       placeholder="@lang('admin.user.real_name')">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('admin.user.email')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" v-model="user.email" name="email"
                                       placeholder="@lang('admin.user.email')" :disabled="isEdit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('admin.user.enabled')</label>
                            <div class="col-sm-6">
                                <input type="checkbox" class="form-control" v-model="user.enabled" name="enabled"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('admin.user.admin')</label>
                            <div class="col-sm-6">
                                <input type="checkbox" class="form-control" v-model="user.admin" name="admin"/>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('common.close')</button>
                    <button type="button" class="btn btn-primary" v-on:click="save">@lang('common.ok')</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection

@section('javascript')
    <script src="{{ elixir('js/vue.min.js') }}"></script>
    <script>
        window.pageData = {
            users: {!! json_encode($users) !!},
            query: {!! json_encode($query) !!}
        };
        var SAVE_USER_URL = '{{ route('admin_save_user', [], false) }}';
        $(function () {
            var $search = new Vue({
                el: '#search-form',
                data: pageData.query
            });

            var $userList = new Vue({
                el: '#user-tbl',
                data: {
                    users: pageData.users.data
                },
                methods: {
                    bool2icon: function (value) {
                        var cls = value ? 'fa-check' : 'fa-times';
                        return '<i class="fa ' + cls + '"></i>';
                    },
                    edit: function (item) {
                        $edit.isEdit = true;
                        $edit.user.id = item.id;
                        $edit.user.name = item.name;
                        $edit.user.real_name = item.real_name;
                        $edit.user.email = item.email;
                        $edit.user.password = '';
                        $edit.user.enabled = item.enabled;
                        $edit.user.admin = item.admin;
                        $('#edit-dialog').modal();
                    }
                }
            });

            var $edit = new Vue({
                el: '#edit-dialog',
                data: {
                    isEdit: false,
                    user: {
                        id: 0,
                        name: '',
                        real_name: '',
                        email: '',
                        password: '',
                        enabled: true,
                        admin: false
                    }
                },
                methods: {
                    save: function () {
                        $.post(SAVE_USER_URL, $('#edit-dialog').find('form').serialize(), function (ret) {
                            alert(ret.msg);
                            if (ret.code != 0) {
                                return;
                            }
                            if ($edit.isEdit) {
                                for (var x in $userList.users) {
                                    if ($userList.users[x].id == ret.data.id) {
                                        $userList.users.splice(x, 1, ret.data);
                                        break;
                                    }
                                }
                            } else {
                                $userList.users.unshift(ret.data);
                            }
                            $('#edit-dialog').modal('hide');
                        }, 'json');
                    }
                }
            });

            $('#add-btn').click(function () {
                $edit.isEdit = false;
                $edit.user.id = 0;
                $edit.user.name = '';
                $edit.user.real_name = '';
                $edit.user.email = '';
                $edit.user.password = '';
                $edit.user.enabled = true;
                $edit.user.admin = false;
                $('#edit-dialog').modal();
            });
        });
    </script>
@endsection
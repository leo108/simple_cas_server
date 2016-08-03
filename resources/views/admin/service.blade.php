@extends('layouts.admin')

@section('content')
    <div id="page-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">@lang('admin.menu.services')</h1>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <form class="form-inline" role="form" id="search-form">
                    <div class="form-group">
                        <input name="search" type="text" class="form-control" v-model="search"
                               placeholder="@lang('admin.service.name')/@lang('admin.service.hosts')"/>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary">@lang('admin.search')</button>
                    </div>
                </form>
            </div>
            <div class="col-lg-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover" id="service-tbl">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>@lang('admin.service.name')</th>
                            <th>@lang('admin.service.hosts')</th>
                            <th>@lang('admin.service.enabled')</th>
                            <th>@lang('admin.service.created_at')</th>
                            <th>
                                <button class="btn btn-xs btn-primary"
                                        id="add-btn">{{ trans('admin.service.add') }}</button>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="item in services">
                            <td>@{{ item.id }}</td>
                            <td>@{{ item.name }}</td>
                            <td>@{{{ displayHosts(item.hosts) }}}</td>
                            <td>@{{{ bool2icon(item.enabled) }}}</td>
                            <td>@{{ item.created_at }}</td>
                            <td>
                                <a href="javascript:void(0)" v-on:click="edit(item)">{{ trans('admin.edit') }}</a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="pull-left">@lang('admin.total') {{ $services->total() }}</div>
                    <div class="pull-right">{{ $services->appends($query)->links() }}</div>
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
                    <h4 class="modal-title">@lang('admin.service.add_or_edit')</h4>
                </div>
                <div class="modal-body">
                    <form role="form" class="form-horizontal">
                        {{ csrf_field() }}
                        <input type="hidden" v-model="service.id" name="id">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('admin.service.name')</label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" v-model="service.name" name="name"
                                       placeholder="@lang('admin.service.name')" :disabled="isEdit">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('admin.service.hosts')</label>
                            <div class="col-sm-6">
                                <textarea class="form-control" name="hosts" cols="30" rows="10" v-model="service.hosts"
                                          placeholder="@lang('admin.service.hosts_placeholder')"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">@lang('admin.service.enabled')</label>
                            <div class="col-sm-6">
                                <input type="checkbox" class="form-control" v-model="service.enabled" name="enabled"/>
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
            services: {!! json_encode($services) !!},
            query: {!! json_encode($query) !!}
        };
        var SAVE_SERVICE_URL = '{{ route('admin_save_service', [], false) }}';
        $(function () {
            var $search = new Vue({
                el: '#search-form',
                data: pageData.query
            });

            var $serviceList = new Vue({
                el: '#service-tbl',
                data: {
                    services: pageData.services.data
                },
                methods: {
                    bool2icon: function (value) {
                        var cls = value ? 'fa-check' : 'fa-times';
                        return '<i class="fa ' + cls + '"></i>';
                    },
                    displayHosts: function (hostArr) {
                        var arr = [];
                        for (var x in hostArr) {
                            arr.push(hostArr[x].host);
                        }
                        return arr.join("\n");
                    },
                    edit: function (item) {
                        $edit.isEdit = true;
                        $edit.service.id = item.id;
                        $edit.service.name = item.name;
                        $edit.service.enabled = item.enabled;
                        $edit.service.hosts = this.displayHosts(item.hosts);
                        $('#edit-dialog').modal();
                    }
                }
            });

            var $edit = new Vue({
                el: '#edit-dialog',
                data: {
                    isEdit: false,
                    service: {
                        id: 0,
                        name: '',
                        enabled: true,
                        hosts: ''
                    }
                },
                methods: {
                    save: function () {
                        $.post(SAVE_SERVICE_URL, $('#edit-dialog').find('form').serialize(), function (ret) {
                            alert(ret.msg);
                            if (ret.code != 0) {
                                return;
                            }
                            if ($edit.isEdit) {
                                for (var x in $serviceList.services) {
                                    if ($serviceList.services[x].id == ret.data.id) {
                                        $serviceList.services.splice(x, 1, ret.data);
                                        break;
                                    }
                                }
                            } else {
                                $serviceList.services.unshift(ret.data);
                            }
                            $('#edit-dialog').modal('hide');
                        }, 'json');
                    }
                }
            });

            $('#add-btn').click(function () {
                $edit.isEdit = false;
                $edit.service.id = 0;
                $edit.service.name = '';
                $edit.service.hosts = '';
                $('#edit-dialog').modal();
            });
        });
    </script>
@endsection
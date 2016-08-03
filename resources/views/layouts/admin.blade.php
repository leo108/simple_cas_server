<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CAS Server</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="{{ elixir('css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ elixir('css/font-awesome.min.css') }}">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ elixir('css/sb-admin-2.css') }}">
    <link rel="stylesheet" href="{{ elixir('css/metisMenu.min.css') }}">
    @yield('stylesheet')
</head>
<body>
<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
    <div class="navbar-header">
        <a class="navbar-brand" href="{{ route('admin_home', [], false) }}">PHP CAS</a>
    </div>
    <!-- /.navbar-header -->

    <ul class="nav navbar-top-links navbar-right">
        <li class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-user">
                <li><a href="{{ route('home'), [], false }}"><i class="fa fa-user fa-fw"></i> @lang('admin.back_to_front')</a>
                </li>
                <li class="divider"></li>
                <li><a href="{{ route('cas_logout', [], false) }}"><i class="fa fa-sign-out fa-fw"></i> @lang('auth.logout')</a>
                </li>
            </ul>
            <!-- /.dropdown-user -->
        </li>
        <!-- /.dropdown -->
    </ul>
    <!-- /.navbar-top-links -->

    <div class="navbar-default sidebar" role="navigation">
        <div class="sidebar-nav navbar-collapse">
            <ul class="nav" id="side-menu">
                <li>
                    <a href="{{ route('admin_home', [], false) }}"><i class="fa fa-dashboard fa-fw"></i> @lang('admin.menu.dashboard')</a>
                </li>
                <li>
                    <a href="{{ route('admin_user_list', [], false) }}"><i class="fa fa-user fa-fw"></i> @lang('admin.menu.users')</a>
                </li>
                <li>
                    <a href="{{ route('admin_service_list', [], false) }}"><i class="fa fa-list fa-fw"></i> @lang('admin.menu.services')</a>
                </li>
            </ul>
        </div>
        <!-- /.sidebar-collapse -->
    </div>
    <!-- /.navbar-static-side -->
</nav>

@yield('content')

<script src="{{ elixir('js/jquery.min.js') }}"></script>
<script src="{{ elixir('js/bootstrap.min.js') }}"></script>
<script src="{{ elixir('js/metisMenu.min.js') }}"></script>
<script>
    $(function() {
        $('#side-menu').metisMenu();
        $(window).bind("load resize", function() {
            var topOffset = 50;
            var width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
            if (width < 768) {
                $('div.navbar-collapse').addClass('collapse');
                topOffset = 100; // 2-row-menu
            } else {
                $('div.navbar-collapse').removeClass('collapse');
            }

            var height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
            height = height - topOffset;
            if (height < 1) height = 1;
            if (height > topOffset) {
                $("#page-wrapper").css("min-height", (height) + "px");
            }
        });
        var url = window.location;
        var element = $('ul.nav a').filter(function() {
            return this.href == url || url.href.indexOf(this.href) == 0;
        }).addClass('active').parent().parent().addClass('in').parent();
        if (element.is('li')) {
            element.addClass('active');
        }
    });
</script>
@yield('javascript')
</body>
</html>

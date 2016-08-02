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
    @yield('stylesheet')
</head>
<body>

    @yield('content')

    <script src="{{ elixir('js/jquery.min.js') }}"></script>
    <script src="{{ elixir('js/bootstrap.min.js') }}"></script>

    @yield('javascript')
</body>
</html>

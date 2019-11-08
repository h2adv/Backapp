
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Backapp</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

</head>
<body>
<div class="flex-center position-ref full-height">
    @if (Route::has('login'))
        <div class="top-right links">
            @auth
                <a href="{{ url('/home') }}">Home</a>
            @else
                <a href="{{ route('login') }}">Login</a>

                @if (Route::has('register'))
                    <a href="{{ route('register') }}">Register</a>
                @endif
            @endauth
        </div>
    @endif
    <div class="content">
        <div class="container">
            <div class="title m-b-md">
                Backupper
            </div>
            <div class="links">
                <a href="/">Home</a>
                <a href="<?php echo URL::to('/backups/get'); ?>">Backups</a>
                <a href="<?php echo URL::to('/backups/log'); ?>">History</a>
                <a href="<?php echo URL::to('/hosts/get'); ?>">Host</a>
                <a href="<?php echo URL::to('/target'); ?>">Target</a>
                <a href="<?php echo URL::to('/settings/get'); ?>">Setting</a>
            </div>
            <h2>
                @yield('title')
            </h2>
        </div>
        <div class="container" style="margin-bottom: 54px;">
            @yield('content')
        </div>
    </div>


</div>
<script src="{{ asset('js/app.js') }}" defer></script>
<script src="{{ asset('js/custom.js') }}" defer></script>
</body>
</html>


<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Backupper</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 200;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            max-width: 1170px;
            margin: auto;
            align-items: center;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .top-right {
            position: absolute;
            right: 10px;
            top: 18px;
        }

        .content {
            text-align: center;
        }

        .title {
            font-size: 54px;
        }

        .links {
            border-bottom: 1px solid #e9e9e9;
            padding-bottom: 25px;
            margin-bottom: 36px;
        }
        .links > a {
            color: #636b6f;
            padding: 0 25px;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .1rem;
            text-decoration: none;
            text-transform: uppercase;
        }
        .m-b-md {
            margin-bottom: 30px;
        }
        .title-list {
            text-align: right;
            width: 50%;
        }
        .value-list {
            text-align: left;
        }
        table {
            margin: 40px 0 50px 0!important;
        }
    </style>
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
                <a href="<?php echo URL::to('/backups/history'); ?>">History</a>
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
<script src="/js/app.js" type="text/javascript"></script>
<script src="/js/jquery.form.js" type="text/javascript"></script>
<script type="text/javascript">

    $(document).ready(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // TOGGLE HOST
        $('.host-active').on('click',function () {

            if($(this).prop("checked")){
                $(this).attr('value',1)
            }else{
                $(this).attr('value',0)
            }

            let id = $(this).parent("form").attr('id');
            let obj = $('#'+id+'.toggle-form');
            let form = obj[0];
            let data = obj.serialize();
            let url = form.action;
            console.log(form);
            console.log(url);
            console.log(data);

            form.ajaxForm({
                type: "POST",
                dataType: "json",
                url: url,
                data: data, // serializes the form's elements.
                success: function (data) {
                    console.log('Submission was successful.');
                    console.log(data);
                },
                error: function (data) {
                    console.log('An error occurred.');
                    console.log(data);
                },
            });

        });

        $('.host-delete').on('click',function () {

            let url = $(this).attr('data-url');
            let id = {id: $(this).attr('data-id')};
            let data =  JSON.stringify(id);
            console.log(url);
            console.log(id);

            $.ajax({
                type: 'POST',
                dataType: "json",
                data: id,
                url: url,
                success: function (data) {
                    let obj = data;
                    if(obj.response === true) {
                        location.reload();
                    }else{
                        console.log('Error');
                    }
                },
                error: function(data) {
                    console.log('Error');
                }
            });
        });


        $('.host-edit').on('click',function () {

            let url = $(this).attr('data-url');
            let id = {id: $(this).attr('data-id')};
            let data =  JSON.stringify(id);
            console.log(url);
            console.log(id);

            $.ajax({
                type: 'POST',
                dataType: "json",
                data: id,
                url: url,
                success: function (data) {
                    let obj = data;
                    if(obj.response === true) {
                        location.reload();
                    }else{
                        console.log('Error');
                    }
                },
                error: function(data) {
                    console.log('Error');
                }
            });
        });

        // SET AS LOCAL AN HOST
        $('.is-local').on('click', function(){
            if ($(this).is(':checked')) {
                $('.remote').hide();
                $('.localhost').show();

                $(this).val(1);
            }else{
                $('.remote').show();
                $('.localhost').hide();
                $(this).val(0);
            }
        });
            if ($('.is-local').is(':checked')) {
                $('.localhost').show();
                $('.remote').hide();
            }else{
                $('.remote').show();
                $('.localhost').hide();

            }


        // $('.backup-ftp-do').on('click',function () {
        //     $.ajax({
        //         type: 'POST',
        //         dataType: "json",
        //         url: '/backups/ftp-do/'+$(this).attr('data-id'),
        //         success: function (data) {
        //             console.log(data);
        //
        //             let obj = data;
        //             if(obj.result === "true") {
        //                 console.log(obj);
        //
        //                 // location.reload();
        //             }else{
        //                 console.log('Error');
        //             }
        //         },
        //         error: function(data) {
        //             console.log(data);
        //         }
        //     });
        // });

        $('.backup-mysql-do').on('click',function () {

            let data = {id: $(this).attr('data-id')};
            $.ajax({
                type: 'POST',
                dataType: "json",
                data: data,
                url: '/backups/sql-do',
                success: function (data) {
                    let obj = data;
                    console.log(obj);

                    if(obj.result === "true") {
                        console.log(obj);

                        // location.reload();
                    }else{
                        console.log('Error');
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            });
        });

    });
</script>
</body>
</html>

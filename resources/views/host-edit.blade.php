@extends('layouts.layout')
@section('title', 'Hosts')
@section('content')
    {{ Form::open(array('url' => 'hosts/edit', 'method' => 'post', 'id' => $host->id)) }}

    <table class="table">
        <thead>

        </thead>
        <tbody>

            </tr>
                <td>
                    <input type="checkbox" class="host-active" name="active" style="font-weight: bold"
                           @if ($host->active === 1)
                           value="1" checked
                           @else
                           value="0"
                        @endif
                    > active?
                </td>
            <tr>
            </tr>
                <td>
                    <input type="text" name="host_name" placeholder="host_name" value="{{$host->host_name}}">
                </td>
            <tr>
            </tr>
                <td>
                    <input type="text" name="ftp_host" placeholder="ftp_host" value="{{$host->ftp_host}}">
                </td>
            <tr>
            </tr>
                <td>
                    <input type="text" name="ftp_username" placeholder="ftp_username" value="{{$host->ftp_username}}">
                </td>
            <tr>
            </tr>
                <td>
                    <input type="text" name="ftp_password" placeholder="ftp_password" value="{{$host->ftp_password}}">
                </td>
            <tr>
            </tr>
                <td>
                    <input type="text" name="db_host" placeholder="db_host" value="{{$host->db_host}}">
                </td>
            <tr>
            </tr>
                <td>
                    <input type="text" name="db_username" placeholder="db_username" value="{{$host->db_username}}">
                </td>
            <tr>
            </tr>
                <td>
                    <input type="text" name="db_password" placeholder="db_password" value="{{$host->db_password}}">
                </td>

            </tr>
        </tbody>
    </table>
    <input type="hidden" name="id" placeholder="id" value="{{$host->id}}">

    <input type="submit" value="SAVE">
    {{ Form::close() }}
    <?php

            if($saved){echo 'Data saved';}
    ?>

@endsection


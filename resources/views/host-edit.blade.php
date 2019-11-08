@extends('layouts.layout')
@section('title', 'Hosts')
@section('content')

    {{ Form::open(array('url' => 'hosts/edit', 'method' => 'post', 'id' => $host->id)) }}

    <a href="{{ URL::previous() }}">back to list</a>

    <table class="table">
        <thead>

        </thead>
        <tbody>
            <tr>
                <td class="title-list">
                    is active
                </td>
                <td class="value-list">
                    <input type="checkbox" class="host-active" name="active" style="font-weight: bold"
                           @if ($host->active === 1)
                           value="1" checked
                           @else
                           value="0"
                        @endif
                    > active?
                </td>
            </tr>
            <tr>
                <td class="title-list">
                    is localhost
                </td>
                <td class="value-list">
                    <input type="checkbox" class="is-local" name="is_local" style="font-weight: bold"
                           @if ($host->is_local === 1)
                           value="1" checked
                           @else
                           value="0"
                        @endif
                    >
                </td>
            </tr>
            <tr>
                <td class="title-list">
                    host_name
                </td>
                <td class="value-list">
                    <input type="text" name="host_name" placeholder="host_name" value="{{$host->host_name}}">
                </td>
            </tr>
            <tr class="localhost">
                <td class="title-list">
                    localhost path
                </td>
                <td class="value-list">
                    <input type="text" name="local_path" placeholder="local_path" value="{{$host->local_path}}">
                </td>
            </tr>
            <tr>
                <td class="title-list">
                    domain
                </td>
                <td class="value-list">
                    <input type="text" name="domain" placeholder="domain" value="{{$host->domain}}">
                </td>
            </tr>
            <tr>
                <td class="title-list">
                    host_slug
                </td>
                <td class="value-list">
                    <input type="text" name="host_slug" placeholder="host_slug" value="{{$host->host_slug}}">
                </td>
            </tr>
            <tr class="remote">
                <td class="title-list">
                    ftp_host
                </td>
                <td class="value-list">
                    <input type="text" name="ftp_host" placeholder="ftp_host" value="{{$host->ftp_host}}">
                </td>
            </tr>
            <tr class="remote">
                <td class="title-list">
                    ftp_directory
                </td>
                <td class="value-list">
                    <input type="text" name="ftp_directory" placeholder="ftp_directory" value="{{$host->ftp_directory}}">
                </td>
            </tr>
            <tr class="remote">
                <td class="title-list">
                    ftp_username
                </td>
                <td class="value-list">
                    <input type="text" name="ftp_username" placeholder="ftp_username" value="{{$host->ftp_username}}">
                </td>
            </tr>
            <tr class="remote">
                <td class="title-list">
                    ftp_password
                </td>
                <td class="value-list">
                    <input type="text" name="ftp_password" placeholder="ftp_password" value="{{$host->ftp_password}}">
                </td>
            </tr>
            <tr>
                <td class="title-list">
                    db_host
                </td>
                <td class="value-list">
                    <input type="text" name="db_host" placeholder="db_host" value="{{$host->db_host}}">
                </td>
            </tr>
            <tr>
                <td class="title-list">
                    db_name
                </td>
                <td class="value-list">
                    <input type="text" name="db_name" placeholder="db_name" value="{{$host->db_name}}">
                </td>
            </tr>
            <tr>
                <td class="title-list">
                    db_username
                </td>
                <td class="value-list">
                    <input type="text" name="db_username" placeholder="db_username" value="{{$host->db_username}}">
                </td>
            </tr>
            <tr>
                <td class="title-list">
                    db_password
                </td>
                <td class="value-list">
                    <input type="text" name="db_password" placeholder="db_password" value="{{$host->db_password}}">
                </td>
            </tr>
        </tbody>
    </table>
    <input type="hidden" name="id" placeholder="id" value="{{$host->id}}">

    <input type="submit" value="SAVE">
    {{ Form::close() }}
    <p></p>



    <?php if($saved):?>
        <div class="alert alert-success" role="alert" style="width:320px;text-align: center; margin: auto;">
            Host saved
        </div>
    <?php endif; ?>

@endsection


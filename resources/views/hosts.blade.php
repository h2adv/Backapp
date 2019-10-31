@extends('layouts.layout')
@section('title', 'Hosts')
@section('content')
    <table class="table">
        <thead>
            <th></th>
            <th>host name</th>
            <th>domain</th>
            <th>is local</th>
            <th>local path</th>
            <th>ftp host</th>
            <th>ftp username</th>
            <th>ftp password</th>
            <th>db host</th>
            <th>db username</th>
            <th>db password</th>
            <th>actions</th>
        </thead>
        <tbody>
    @foreach ($hosts as $host)
        <tr>
            <td>
                <span style="font-weight: bold">[{{ $host->id }}]</span>
            </td>
            <td>
                <span style="font-weight: bold">{{ $host->host_name }}</span>
            </td>
            <td>
                <span style="font-weight: bold">{{ $host->domain }}:{{ $host->ftp_port }}</span>
            </td>
            <td>
                <span>{{ $host->is_local }}</span>
            </td>
            <td>
                <span>{{ $host->local_path }}</span>
            </td>
            <td>
                <span>{{ $host->ftp_host }}</span>
            </td>
            <td>
                <span>{{ $host->ftp_username }}</span>
            </td>
            <td>
                <span>{{ $host->ftp_password }}</span>
            </td>
            <td>
                <span>{{ $host->db_host }}</span>
            </td>
            <td>
                <span>{{ $host->db_username }}</span>
            </td>
            <td>
                <span>{{ $host->db_password }}</span>
            </td>
            <td>
                <a href="<?php echo URL::to('/host/edit',$host->id); ?>">[Edit]</a>
                {{--<a class="host-delete" href="#" data-url="{{ url('hosts/delete' ) }}" data-id="{{$host->id}}">[x]</a>--}}
                {{ Form::open(array('url' => 'hosts/toggle', 'method' => 'post', 'class' => 'toggle-form', 'id' => $host->id)) }}
                <input type="hidden" name="id" value="{{$host->id}}">
                <input type="checkbox" class="host-active" name="active" style="font-weight: bold"
                       @if ($host->active === 1)
                       value="1" checked
                       @else
                       value="0"
                    @endif
                >
                {{ Form::close() }}
            </td>
        </tr>
    @endforeach
        </tbody>
    </table>

@include('partials.new-host')
@endsection


@extends('layouts.layout')
@section('title', 'Hosts')
@section('content')
    <table class="table">
        <thead>
            <th>host name</th>
            <th>domain</th>
            <th>actions</th>
        </thead>
        <tbody>
    @foreach ($hosts as $host)
        <tr>
            <td>
                <span style="font-weight: bold">[{{ $host->id }}] - {{ $host->host_name }}</span>
            </td>
            <td>
                <span style="font-weight: bold">{{ $host->domain }}:{{ $host->ftp_port }}</span>
            </td>
            <td>
                <a href="<?php echo URL::to('/host/edit',$host->id); ?>">[Edit]</a>
                {{--<a class="host-delete" href="#" data-url="{{ url('hosts/delete' ) }}" data-id="{{$host->id}}">[x]</a>--}}
                {{ Form::open(array('url' => 'hosts/toggle', 'method' => 'post', 'class' => 'toggle-form', 'id' => $host->id)) }}
                <input type="hidden" name="id" value="{{$host->id}}"> [ Active <input type="checkbox" class="host-active" name="active" style="font-weight: bold"
                       @if ($host->active === 1)
                       value="1" checked
                       @else
                       value="0"
                    @endif
                > ]
                {{ Form::close() }}
            </td>
        </tr>
    @endforeach
        </tbody>
    </table>

@include('partials.new-host')
@endsection


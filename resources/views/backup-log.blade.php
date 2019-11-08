@extends('layouts.layout')
@section('title', 'Backup Logs')
@section('content')
    <table class="table">
        <thead>
            <th>id</th>
            <th>host</th>
            <th>result</th>
            <th>date</th>
        </thead>
        <tbody>
        @foreach ($backups as $backup)
            <tr>
                <td>
                    <span style="font-weight: bold">[{{ $backup->id }}]</span>
                </td>
                <td>
                    <span style="font-weight: bold">[{{ $backup->host_id }}] {{ $backup->host_name }}</span>
                </td>
                <td>
                    <span style="font-weight: bold">
                        @if( $backup->result == 0 )
                            Backup done
                        @else() Error @if( $backup->message != null )
                            : {{ str_limit($backup->message, $limit = 30, $end = '...') }}
                            @endif
                        @endif
                    </span>
                </td>

                <td>
                    <span>{{ $backup->created_at }}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

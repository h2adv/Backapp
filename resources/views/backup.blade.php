@extends('layouts.layout')
@section('title', 'Backup')
@section('content')
    <div class="alert message-backup" role="alert" style="display: none">
    </div>
    <div class="actions">
        <a href="#">Run all backups</a>
    </div>
    <table class="table">
        <thead>
        <th>action</th>
        <th>backup name</th>
        </thead>
        <tbody>
        @foreach ($hosts as $host)
            <tr>
                <td>
                    <span style="font-weight: bold">[{{ $host->id }}]</span>
                    <span style="font-weight: bold">{{ $host->host_name }}:</span>
                </td>
                <td>
                    <a data-id="{{$host->id}}"
                       class="backup-ftp-do" href="#">Backup ftp now
                    </a> |
                    <a data-id="{{$host->id}}" class="backup-mysql-do" href="#">Backup sql now</a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="backup-message"></div>
@endsection

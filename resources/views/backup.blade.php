@extends('layouts.layout')
@section('title', 'Backup')
@section('content')
    <div class="actions">
        <a href="#">Run backup</a>
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
                    <a data-id="{{$host->id}}"
                       class="backup-ftp-do" href="<?php echo URL::to('/backups/ftp-do',$host->id); ?>">Backup ftp now
                    </a> |
                    <a data-id="{{$host->id}}" class="backup-mysql-do" href="#">Backup sql now</a>
                </td>
                <td>
                    <span style="font-weight: bold">[{{ $host->id }}]</span>
                    <span style="font-weight: bold">{{ $host->host_name }}:</span>
                </td>
                <td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="backup-message"></div>
@endsection

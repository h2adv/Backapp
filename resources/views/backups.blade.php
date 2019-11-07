@extends('layouts.layout')
@section('title', 'Backups')
@section('content')
    <div class="actions">
        <a href="#">Run backup</a>
    </div>
    <table class="table">
        <thead>
        <th>id</th>
        <th>backup names</th>
        <th>date</th>
        </thead>
        <tbody>
        @foreach ($backups as $backup)
            <tr>
                <td>
                    <div class="spinner-border" role="status" style="display: none">
                        <span class="sr-only">Loading...</span>
                    </div>
                    <span style="font-weight: bold">[{{ $backup->id }}]</span>
                </td>
                <td>
                    <span style="font-weight: bold">{{ $backup->host_names }}:</span>
                </td>
                <td>
                    <span>{{ $backup->created_at }}</span>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection

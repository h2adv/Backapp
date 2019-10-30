@extends('layouts.layout')
@section('title', 'Settings')
@section('content')
    @foreach ($settings as $setting)
        <p><span style="font-weight: bold">{{ $setting->name }}</span>: {{ $setting->value }}</p>
    @endforeach
@endsection

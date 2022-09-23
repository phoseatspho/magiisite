@extends('layouts.app')

@section('title') Dev Log @endsection

@section('content')
{!! breadcrumbs(['Dev Log' => 'logs']) !!}
<h1>Dev Logs</h1>
@if(count($devLogses))
    {!! $devLogses->render() !!}
    @foreach($devLogses as $devLogs)
        @include('logs._logs', ['dev-logs' => $devLogs, 'page' => FALSE])
    @endforeach
    {!! $devLogses->render() !!}
@else
    <div>No development logs yet.</div>
@endif
@endsection
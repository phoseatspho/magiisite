@extends('layouts.app')

@section('title')
    Home
@endsection
@include('widgets._news', ['textPreview' => true])

@section('content')
    @if (Auth::check())
        @include('pages._dashboard')
    @else
        @include('pages._logged_out')
        <div><p>test test hello</p>
@include('widgets._news', ['textPreview' => true])</div>
    @endif
@endsection


@section('sidebar')
    @include('pages._sidebar')
@endsection

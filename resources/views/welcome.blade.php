@extends('layouts.app')

@section('title')
    Home
@endsection


@section('content')
test test hello 
    @if (Auth::check())
        @include('pages._dashboard')
    @else
        @include('pages._logged_out')

        @include('widgets._news', ['textPreview' => true])

    @endif
@endsection


@section('sidebar')
    @include('pages._sidebar')
@endsection

@extends('layouts.app')

@section('title')
    Home
@endsection

test test hellooo

@section('content')
    @if (Auth::check())
        @include('pages._dashboard')
    @else
        @include('pages._logged_out')
    @endif
@endsection

test hellooo

@section('sidebar')
    @include('pages._sidebar')
@endsection
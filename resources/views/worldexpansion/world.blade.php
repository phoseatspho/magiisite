@extends('worldexpansion.layout')

@section('title') {{$world->title}} @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', $world->title => 'world/info']) !!}

<h1>Explore Arcadia</h1>

<div class="site-page-content parsed-text">
<img src="../files/lorescroll.png" class="img-fluid">
<img src="../files/mapsprite.png" class="img-fluid">
    {!! $world->parsed_text !!}
</div>

@endsection

@extends('worldexpansion.layout')

@section('title') {{$world->title}} @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', $world->title => 'world/info']) !!}

<h1>Explore Arcadia</h1>
<div class="site-page-content parsed-text">
<div align="center">
<img src="../files/lorescroll.png" class="img-fluid" title="Lore">
<img src="../files/mapsprite.png" class="img-fluid">
<img src="../files/npcs.png" class="img-fluid">
<img src="../files/flora.png" class="img-fluid">
<img src="../files/fauna.png" class="img-fluid">
</div>
    {!! $world->parsed_text !!}
</div>


@endsection

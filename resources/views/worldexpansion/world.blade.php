@extends('worldexpansion.layout')

@section('title') {{$world->title}} @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', $world->title => 'world/info']) !!}

<h1>Explore Arcadia</h1>
<div align="center">
<div class="dashboardtext">
<div class="site-page-content parsed-text">
    <h3>Lore</h3>
    <div class="tooltip" title="Lore">
<img src="../files/lorescroll.png" class="img-fluid">
</div>
    <h3>Locations</h3>
<img src="../files/mapsprite.png" class="img-fluid">

    <h3>Npcs</h3>
<img src="../files/npcs.png" class="img-fluid">

    <h3>Flora</h3>
<img src="../files/flora.png" class="img-fluid">
<li class="nav-item" data-toggle="tooltip" title="Fauna">
    <h3>Fauna</h3>
<img src="../files/fauna.png" class="img-fluid">
</li>
    {!! $world->parsed_text !!}
</div>
</div>
</div>

@endsection

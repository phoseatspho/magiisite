@extends('worldexpansion.layout')

@section('title') {{$world->title}} @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', $world->title => 'world/info']) !!}

<h1>Explore Arcadia</h1>
<div class="dashboardtext">
<div class="site-page-content parsed-text">
<li class="nav-item" data-toggle="tooltip" title="Lore">
<img src="../files/lorescroll.png" class="img-fluid">
</li>
<li class="nav-item" data-toggle="tooltip" title="Locations">
<img src="../files/mapsprite.png" class="img-fluid">
</li>
<li class="nav-item" data-toggle="tooltip" title="NPCs">
<img src="../files/npcs.png" class="img-fluid">
</li>
<li class="nav-item" data-toggle="tooltip" title="Flora">
<img src="../files/flora.png" class="img-fluid">
</li>
<li class="nav-item" data-toggle="tooltip" title="Fauna">
<img src="../files/fauna.png" class="img-fluid">
</li>
    {!! $world->parsed_text !!}
</div>
</div>

@endsection

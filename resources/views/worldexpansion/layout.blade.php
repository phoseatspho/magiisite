@extends('layouts.app')

@section('title') 
    World :: 
    @yield('worldexpansion-title')
@endsection

@section('sidebar')
    @include('worldexpansion._sidebar')
@endsection

@section('content')
    @yield('worldexpansion-content')
    <div class="dashboardtext">
<div class="row justify-content-center">
<div class="site-page-content parsed-text">
<div class="col-6 col-md-3">
    <h3>Lore</h3>
<img src="../files/lorescroll.png" class="img-fluid">
</div>
<div class="col-6 col-md-3">
    <h3>Locations</h3>
<img src="../files/mapsprite.png" class="img-fluid">
</div>
<div class="col-6 col-md-3">
    <h3>Npcs</h3>
<img src="../files/npcs.png" class="img-fluid">
</div>
<div class="col-6 col-md-3">
    <h3>Flora</h3>
<img src="../files/flora.png" class="img-fluid">
</div>
<div class="col-6 col-md-3">
    <h3>Fauna</h3>
<img src="../files/fauna.png" class="img-fluid">
</div>
<div class="col-6 col-md-3">
    <h3>Fauna</h3>
<img src="../files/fauna.png" class="img-fluid">
</div>
    {!! $world->parsed_text !!}
</div>
</div>
</div>
@endsection

@section('scripts')
@parent
@endsection
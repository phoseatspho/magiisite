@extends('worldexpansion.layout')

@section('title') {{$world->title}} @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', $world->title => 'world/info']) !!}

<h1>Explore Arcadia</h1>
<div class="dashboardtext">

<div class="site-page-content parsed-text">
    <h3>Lore</h3>
<img src="../files/lorescroll.png" class="img-fluid">
    <h3>Locations</h3>
<img src="../files/mapsprite.png" class="img-fluid">

    <h3>Npcs</h3>
<img src="../files/npcs.png" class="img-fluid">

    <h3>Flora</h3>
<img src="../files/flora.png" class="img-fluid">
    <h3>Fauna</h3>
<img src="../files/fauna.png" class="img-fluid">
    {!! $world->parsed_text !!}
</div>
</div>

<div class="dashboardtext">
<div class="row justify-content-center">
<div class="col-6 col-md-3">
 
 <h3>Profile</h3>
<ul>
 <img src="../files/profile.png" class="img-fluid">
 <li><a href="{{ Auth::user()->url }}">Profile</a></li>
 <li><a href="https://magiispecies.com/characters">My Magii</a></li>
 <li><a href="https://magiispecies.com/inventory">Inventory</a></li>
 <li><a href="https://magiispecies.com/bank">Bank</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
 <h3>Info</h3>
<ul>
 <img src="../files/info.png" class="img-fluid">
 <li><a href="https://magiispecies.com/profile">Beginner Guide</a></li>
 <li><a href="https://magiispecies.com/characters">Species Info</a></li>
 <li><a href="https://magiispecies.com/inventory">Origin Lore</a></li>
 <li><a href="{{ url('faq') }}">FAQ</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
 <h3>Play</h3>
<ul>
  <img src="../files/play.png" class="img-fluid"> 
  <li><a href="https://www.magiispecies.com/dailies">Check In</a></li>
 <li><a href="https://www.magiispecies.com/prompts/prompts">Quests</a></li>
 <li><a href="https://www.magiispecies.com/foraging">Foraging</a></li>
 <li><a href="https://www.magiispecies.com/crafting">Crafting</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
  <h3>Explore</h3>
<ul>
  <img src="../files/explore.png" class="img-fluid">
  <li><a href="https://www.magiispecies.com/world/info">Arcadia</a></li>
 <li><a href="https://magiispecies.com/characters">Story</a></li>
 <li><a href="https://www.magiispecies.com/world/figure-categories">NPCs</a></li>
 <li><a href="https://www.magiispecies.com/shops">Shop</a></li>
</ul>
</div>
</div> 
</div>

@endsection

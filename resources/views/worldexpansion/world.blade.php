@extends('worldexpansion.layout')

@section('title') {{$world->title}} @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', $world->title => 'world/info']) !!}

<h1>Explore Arcadia</h1>
<div align="center">Click the sprites below to read more about the World of Arcadia and its inhabitants!
        <hr>
    <br>
    <br>
</div>
    <div class="dashboardtext">
<div class="row justify-content-center">
<div class="col-6 col-md-3">
 
 <h5>Lore</h5>
 <a href="https://www.magiispecies.com/world/library"><img src="../files/lorescroll.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Locations</h5>
 <a href="https://www.magiispecies.com/world/location-types"><img src="../files/mapsprite.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>NPCS</h5>
 <a href="https://www.magiispecies.com/sublist/NPCs"><img src="../files/npcs.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Flora</h5>
 <a href="https://www.magiispecies.com/world/flora-categories"><img src="../files/flora.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Fauna</h5>
 <a href="https://www.magiispecies.com/world/fauna-categories"><img src="../files/fauna.png" class="img-fluid"></a>
</div>


</div> 
</div>


@endsection

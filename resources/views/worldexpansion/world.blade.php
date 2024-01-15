@extends('worldexpansion.layout')

@section('title') {{$world->title}} @endsection

@section('content')
{!! breadcrumbs(['World' => 'world', $world->title => 'world/info']) !!}

<h1>Explore Arcadia</h1>
<div align="center">
<a href="https://www.magiispecies.com/world/concept-categories"><img src="../files/lorescroll.png" class="img-fluid" title="Lore"></a>
<a href="https://www.magiispecies.com/world/location-types"><img src="../files/mapsprite.png" class="img-fluid" title="Locations"></a>
<a href="https://www.magiispecies.com/world/figure-categories"><img src="../files/npcs.png" class="img-fluid" title="NPCs"></a>
<a href="https://www.magiispecies.com/world/flora-categories"><img src="../files/flora.png" class="img-fluid" title="Flora"></a>
<a href="https://www.magiispecies.com/world/fauna-categories"><img src="../files/fauna.png" class="img-fluid" title="Fauna"></a>

Click the sprites above to read more about the World of Arcadia and its inhabitants!
</div>


@endsection

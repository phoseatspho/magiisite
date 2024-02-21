@extends('world.layout')

@section('title')
    Home
@endsection

@section('content')
    {!! breadcrumbs(['Encyclopedia' => 'world']) !!}

    <div class="dashboardtext">
<div class="row justify-content-center">
<div class="col-6 col-md-3">
 
 <h3>Species</h3>
 <a href="https://www.magiispecies.com/world/species"><img src="../files/species.png" class="img-fluid"></a>
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

    <h1>World</h1>
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ asset('images/characters.png') }}" alt="Characters" />
                    <h5 class="card-title">Characters</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="{{ url('world/species') }}">Species</a></li>
                    <li class="list-group-item"><a href="{{ url('world/elements') }}">All Elements</a></li>
                    <li class="list-group-item"><a href="{{ url('world/subtypes') }}">Subtypes</a></li>
                    <li class="list-group-item"><a href="{{ url('world/rarities') }}">Rarities</a></li>
                    <li class="list-group-item"><a href="{{ url('world/trait-categories') }}">Trait Categories</a></li>
                    <li class="list-group-item"><a href="{{ url('world/traits') }}">All Traits</a></li>
                    <li class="list-group-item"><a href="{{ url('world/character-categories') }}">Character Categories</a></li>
                    <li class="list-group-item"><a href="{{ url('world/character-classes') }}">Character Classes</a></li>
                <li class="list-group-item"><a href="{{ url('world/'.__('transformations.transformations')) }}">{{ ucfirst(__('transformations.transformations')) }}</a></li>
                </ul>
            </div>
        </div>
       
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('images/inventory.png') }}" alt="Items and {{ ucfirst(__('awards.awards')) }}" />
                <h5 class="card-title">Items & {{ ucfirst(__('awards.awards')) }}</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="{{ url('world/item-categories') }}">Item Categories</a></li>
                <li class="list-group-item"><a href="{{ url('world/items') }}">All Items</a></li>
                <li class="list-group-item"><a href="{{ url('world/'.__('awards.award').'-categories') }}">{{ ucfirst(__('awards.award')) }} Categories</a></li>
                <li class="list-group-item"><a href="{{ url('world/'.__('awards.awards')) }}">All {{ ucfirst(__('awards.awards')) }}</a></li>
                <li class="list-group-item"><a href="{{ url('world/currencies') }}">Currencies</a></li>
                <li class="list-group-item"><a href="{{ url('world/collections') }}">Collections</a></li>
                <li class="list-group-item"><a href="{{ url('world/skill-categories') }}">Skill Categories</a></li>
                <li class="list-group-item"><a href="{{ url('world/skills') }}">All Skills</a></li>
                <li class="list-group-item"><a href="{{ url('world/currencies') }}">Currencies</a></li>
                <li class="list-group-item"><a href="{{ url('world/pet-categories') }}">Pet Categories</a></li>
                <li class="list-group-item"><a href="{{ url('world/pets') }}">All Pets</a></li>
                <li class="list-group-item"><a href="{{ url('world/weapon-categories') }}">Weapon Categories</a></li>
                <li class="list-group-item"><a href="{{ url('world/weapons') }}">All Weapons</a></li>
                <li class="list-group-item"><a href="{{ url('world/gear-categories') }}">Gear Categories</a></li>
                <li class="list-group-item"><a href="{{ url('world/gear') }}">All Gear</a></li>
                <li class="list-group-item"><a href="{{ url('world/recipes') }}">All Recipes</a></li>
            </ul>
        </div>
    </div>
</div>

@endsection

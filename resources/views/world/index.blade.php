@extends('world.layout')

@section('title')
    Home
@endsection

@section('content')
    {!! breadcrumbs(['Encyclopedia' => 'world']) !!}

    <div class="dashboardtext">
<div class="row justify-content-center">
<div class="col-6 col-md-3">
 
 <h5>Species</h5>
 <a href="https://www.magiispecies.com/world/species"><img src="../files/species.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Elements</h5>
 <a href="{{ url('world/elements') }}"><img src="../files/elements.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Currency</h5>
 <a href="{{ url('world/currencies') }}"><img src="../files/currencies.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Items</h5>
 <a href="{{ url('world/items') }}"><img src="../files/items.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Awards</h5>
 <a href="{{ url('world/'.__('awards.awards')) }}"><img src="../files/awards.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Collections</h5>
 <a href="{{ url('world/collections') }}"><img src="../files/collections.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Recipes</h5>
 <a href="{{ url('world/recipes') }}"><img src="../files/recipes.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Pets</h5>
 <a href="{{ url('world/pets') }}"><img src="../files/pets.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Weapons</h5>
 <a href="{{ url('world/weapons') }}"><img src="../files/weapons.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Equipment</h5>
 <a href="{{ url('world/gear') }}"><img src="../files/equipment.png" class="img-fluid"></a>
</div>

<div class="col-6 col-md-3">
 
 <h5>Skills</h5>
 <a href="{{ url('world/skills') }}"><img src="../files/skills.png" class="img-fluid"></a>
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

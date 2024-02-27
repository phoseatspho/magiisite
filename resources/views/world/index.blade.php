@extends('world.layout')

@section('world-title')
    Home
@endsection

@section('content')
    {!! breadcrumbs(['Encyclopedia' => 'world']) !!}

    <div align="center">Click the sprites below to view information about various aspects of the species & ARPG!
        <hr>
    <br>
    <br>
</div>
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

 
@endsection

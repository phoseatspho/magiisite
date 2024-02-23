<h1>Hey there, {!! Auth::user()->displayName !!}!</h1>
<div class="card mb-4 timestamp">
    <div class="card-body">
        <i class="far fa-clock"></i> {!! format_date(Carbon\Carbon::now()) !!}
    </div>
</div>
 <img src="../files/banner.png" class="img-fluid">
 <br> 
 <br> 
 <br>

 <div class="dashboardtext">
<div class="row justify-content-center">
<div class="col-6 col-md-3">
 
 <h2>Profile</h2>
<ul>
 <img src="../files/profile.png" class="img-fluid">
 <h6><li><a href="{{ Auth::user()->url }}">Profile</a></li>
 <li><a href="https://magiispecies.com/characters">My Magii</a></li>
 <li><a href="https://magiispecies.com/inventory">Inventory</a></li>
 <li><a href="https://magiispecies.com/bank">Bank</a></li></h6>
</ul>
</div>

<div class="col-6 col-md-3">
 <h2>Info</h2>
<ul>
 <img src="../files/info.png" class="img-fluid">
 <h5><li><a href="https://magiispecies.com/profile">Beginner Guide</a></li>
 <li><a href="https://magiispecies.com/characters">Species Info</a></li>
 <li><a href="https://magiispecies.com/inventory">Origin Lore</a></li>
 <li><a href="{{ url('faq') }}">FAQ</a></li></h5>
</ul>
</div>

<div class="col-6 col-md-3">
 <h2>Play</h2>
<ul>
  <img src="../files/play.png" class="img-fluid"> 
  <li style="font-size:20px"><a href="https://www.magiispecies.com/dailies">Check In</a></li>
 <li><a href="https://www.magiispecies.com/prompts/prompts">Quests</a></li>
 <li><a href="https://www.magiispecies.com/foraging">Foraging</a></li>
 <li><a href="https://www.magiispecies.com/crafting">Crafting</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
  <h2>Explore</h2>
<ul>
  <img src="../files/explore.png" class="img-fluid">
  <li><a href="https://www.magiispecies.com/world/info" style="font-size:18px">Arcadia</a></li>
 <li><a href="https://magiispecies.com/characters">Story</a></li>
 <li><a href="https://www.magiispecies.com/world/figure-categories">NPCs</a></li>
 <li><a href="https://www.magiispecies.com/shops">Shop</a></li>
</ul>
</div>
</div> 
</div>


@include('widgets._recent_gallery_submissions', ['gallerySubmissions' => $gallerySubmissions])

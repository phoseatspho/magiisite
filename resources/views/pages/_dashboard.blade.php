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
 
 <h3>&nbsp;&nbsp;&nbsp;&nbsp;Profile</h3>
<ul>
 <img src="../files/profile.png" class="img-fluid">
 <li style="font-size:17px"><a href="{{ Auth::user()->url }}">Profile</a></li>
 <li style="font-size:17px"><a href="https://magiispecies.com/characters">My Magii</a></li>
 <li style="font-size:17px"><a href="https://magiispecies.com/inventory">Inventory</a></li>
 <li style="font-size:17px"><a href="https://magiispecies.com/bank">Bank</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
 <h3>Info</h3>
<ul>
 <img src="../files/info.png" class="img-fluid">
 <li style="font-size:17px"><a href="https://www.magiispecies.com/info/guide">Beginner Guide</a></li>
 <li style="font-size:17px"><a href="https://www.magiispecies.com/world/species">Species Info</a></li>
 <li style="font-size:17px"><a href="{{ url('faq') }}">FAQ</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
 <h3>Play</h3>
<ul>
  <img src="../files/play.png" class="img-fluid"> 
  <li style="font-size:17px"><a href="https://www.magiispecies.com/dailies">Check In</a></li>
 <li style="font-size:17px"><a href="https://www.magiispecies.com/prompts/prompts">Quests</a></li>
 <li style="font-size:17px"><a href="https://www.magiispecies.com/higher-or-lower">Higher or Lower</a></li>
 <li style="font-size:17px"><a href="https://www.magiispecies.com/crafting">Crafting</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
  <h3>Explore</h3>
<ul>
  <img src="../files/explore.png" class="img-fluid">
  <li style="font-size:17px"><a href="https://www.magiispecies.com/world/info">Arcadia</a></li>
 <li style="font-size:17px"><a href="https://www.magiispecies.com/world/library">Lore</a></li>
 <li style="font-size:17px"><a href="https://www.magiispecies.com/sublist/NPCs">NPCs</a></li>
 <li style="font-size:17px"><a href="https://www.magiispecies.com/shops">Shop</a></li>
</ul>
</div>
</div> 
</div>

@include('widgets._news', ['textPreview' => true])
@include('widgets._recent_gallery_submissions', ['gallerySubmissions' => $gallerySubmissions])

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
 
 <h3>Profile</h3>
<ul>
 <img src="../files/profile.png" class="img-fluid">
 <li><a href="https://magiispecies.com/profile">Profile</a></li>
 <li><a href="https://magiispecies.com/characters">My Magii</a></li>
 <li><a href="https://magiispecies.com/inventory">Inventory</a></li>
 <li><a href="https://magiispecies.com/bank">Bank</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
 <h3>Info</h3>
<ul>
 <img src="../files/info.png" class="img-fluid">
 <li><a href="https://magiispecies.com/profile">Profile</a></li>
 <li><a href="https://magiispecies.com/characters">My Magii</a></li>
 <li><a href="https://magiispecies.com/inventory">Inventory</a></li>
 <li><a href="https://magiispecies.com/bank">Bank</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
 <h3>Play</h3>
<ul>
  <img src="../files/play.png" class="img-fluid"> 
  <li><a href="https://magiispecies.com/profile">Profile</a></li>
 <li><a href="https://magiispecies.com/characters">My Magii</a></li>
 <li><a href="https://magiispecies.com/inventory">Inventory</a></li>
 <li><a href="https://magiispecies.com/bank">Bank</a></li>
</ul>
</div>

<div class="col-6 col-md-3">
  <h3>Explore</h3>
<ul>
  <img src="../files/explore.png" class="img-fluid">
  <li><a href="https://magiispecies.com/profile">Profile</a></li>
 <li><a href="https://magiispecies.com/characters">My Magii</a></li>
 <li><a href="https://magiispecies.com/inventory">Inventory</a></li>
 <li><a href="https://magiispecies.com/bank">Bank</a></li>
</ul>
</div>
</div> 
</div>


@include('widgets._recent_gallery_submissions', ['gallerySubmissions' => $gallerySubmissions])

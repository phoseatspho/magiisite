<h1>Hey there, {!! Auth::user()->displayName !!}!</h1>
<div class="card mb-4 timestamp">
    <div class="card-body">
        <i class="far fa-clock"></i> {!! format_date(Carbon\Carbon::now()) !!}
    </div>
</div>
 <img src="../files/banner.png" class="img-fluid">
 <br> 

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
 <img src="../files/explore.png" class="img-fluid">
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


<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('images/account.png') }}" alt="Account" />
                <h5 class="card-title">Account</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="{{ Auth::user()->url }}">Profile</a></li>
                <li class="list-group-item"><a href="{{ url('account/settings') }}">User Settings</a></li>
                <li class="list-group-item"><a href="{{ url('trades/open') }}">Trades</a></li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('images/characters.png') }}" alt="Characters" />
                <h5 class="card-title">Characters</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="{{ url('characters') }}">My Characters</a></li>
                <li class="list-group-item"><a href="{{ url('characters/myos') }}">My MYO Slots</a></li>
                <li class="list-group-item"><a href="{{ url('characters/transfers/incoming') }}">Character Transfers</a></li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="{{ asset('images/inventory.png') }}" alt="Inventory" />
                <h5 class="card-title">Inventory</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="{{ url('inventory') }}">My Inventory</a></li>
                <li class="list-group-item"><a href="{{ Auth::user()->url . '/item-logs' }}">Item Logs</a></li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body text-center">
                <img src="{{ asset('images/currency.png') }}" alt="Bank" />
                <h5 class="card-title">Bank</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="{{ url('bank') }}">Bank</a></li>
                <li class="list-group-item"><a href="{{ Auth::user()->url . '/currency-logs' }}">Currency Logs</a></li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card mb-12">
            <div class="card-body text-center">
                <img src="{{ asset('images/awards.png') }}" />
                <h5 class="card-title">{{ ucfirst(__('awards.awards')) }}</h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><a href="{{ url(__('awards.awardcase')) }}">My {{ ucfirst(__('awards.awards')) }}</a></li>
                <li class="list-group-item"><a href="{{ Auth::user()->url . '/'.__('awards.award').'-logs' }}">{{ ucfirst(__('awards.award')) }} Logs</a></li>
            </ul>
        </div>
    </div>
</div>

@include('widgets._recent_gallery_submissions', ['gallerySubmissions' => $gallerySubmissions])

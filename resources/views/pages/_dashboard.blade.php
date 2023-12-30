<h1>Hey there, {!! Auth::user()->displayName !!}!</h1>
<div class="card mb-4 timestamp">
    <div class="card-body">
        <i class="far fa-clock"></i> {!! format_date(Carbon\Carbon::now()) !!}
    </div>
</div>

 <img src="https://images-wixmp-ed30a86b8c4ca887773594c2.wixmp.com/f/b6759b14-b430-4129-ba10-f0ee6193d692/dgn8agk-129800c1-0bbb-4e41-9397-2215d494d3f2.png/v1/fill/w_1280,h_431,q_80,strp/banner_by_phoseatspho_dgn8agk-fullview.jpg?token=eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJ1cm46YXBwOjdlMGQxODg5ODIyNjQzNzNhNWYwZDQxNWVhMGQyNmUwIiwiaXNzIjoidXJuOmFwcDo3ZTBkMTg4OTgyMjY0MzczYTVmMGQ0MTVlYTBkMjZlMCIsIm9iaiI6W1t7ImhlaWdodCI6Ijw9NDMxIiwicGF0aCI6IlwvZlwvYjY3NTliMTQtYjQzMC00MTI5LWJhMTAtZjBlZTYxOTNkNjkyXC9kZ244YWdrLTEyOTgwMGMxLTBiYmItNGU0MS05Mzk3LTIyMTVkNDk0ZDNmMi5wbmciLCJ3aWR0aCI6Ijw9MTI4MCJ9XV0sImF1ZCI6WyJ1cm46c2VydmljZTppbWFnZS5vcGVyYXRpb25zIl19.aP27QkLvQaj6TEbfJhPfxEVV_NAoIH8l4C3OpqwKV2w">

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

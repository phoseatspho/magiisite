<ul id="accordion">
    <li class="sidebar-header"><a href="{{ url('shops') }}" class="card-link">Shops</a></li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapseShops" aria-expanded="true" aria-controls="collapseShops" id="headingShops">
            Shops
        </div>
        <div class="collapse show" aria-labelledby="headingShops" data-parent="#accordion" id="collapseShops">
            @foreach($shops as $shop)
                @if($shop->is_staff)
                    @if(Auth::check() && Auth::user()->isstaff)
                        <div class="sidebar-item">
                            <a href="{{ $shop->url }}" class="{{ set_active('shops/'.$shop->id) }}">{{ $shop->name }}</a>
                        </div>
                    @endif
                @else
                    <div class="sidebar-item">
                        <a href="{{ $shop->url }}" class="{{ set_active('shops/'.$shop->id) }}">{{ $shop->name }}</a>
                    </div>
                @endif
            @endforeach
        </div>
    </li>

    @if(Auth::check())
        <li class="sidebar-section">
            <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseCurrencies" aria-expanded="false" aria-controls="collapseCurrencies" id="headingCurrencies">
                My Currencies
            </div>
            <div class="collapse" aria-labelledby="headingCurrencies" data-parent="#accordion" id="collapseCurrencies">
                @foreach(Auth::user()->getCurrencies(true) as $currency)
                    <div class="sidebar-item pr-3">
                        {!! $currency->display($currency->quantity) !!}
                    </div>
                @endforeach
            </div>
        </li>

        <li class="sidebar-section">
            <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseHistory" aria-expanded="false" aria-controls="collapseHistory" id="headingHistory">
                History
            </div>
            <div class="collapse" aria-labelledby="headingHistory" data-parent="#accordion" id="collapseHistory">
                <div class="sidebar-item">
                    <a href="{{ url('shops/history') }}" class="{{ set_active('shops/history') }}">My Purchase History</a>
                </div>
            </div>
        </li>
    @endif
</ul>

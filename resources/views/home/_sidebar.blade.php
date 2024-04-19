<ul id="accordion">
    <li class="sidebar-header"><a href="{{ url('/') }}" class="card-link">Home</a></li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapseInventory" aria-expanded="true" aria-controls="collapseInventory" id="headingInventory">
            Inventory
        </div>
        <div class="collapse show" aria-labelledby="headingInventory" data-parent="#accordion" id="collapseInventory">
            <div class="sidebar-item">
                <a href="{{ url('characters') }}" class="{{ set_active('characters') }}">My Characters</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('characters/myos') }}" class="{{ set_active('characters/myos') }}">My MYO Slots</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('pets') }}" class="{{ set_active('pets*') }}">My Pets</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('wishlists') }}" class="{{ set_active('wishlists*') }}">Wishlists</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('inventory') }}" class="{{ set_active('inventory*') }}">Inventory</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url(__('awards.awardcase')) }}" class="{{ set_active(__('awards.awardcase').'*') }}">Awardcase</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('bank') }}" class="{{ set_active('bank*') }}">Bank</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('level') }}" class="{{ set_active('level*') }}">Level Area</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('gears') }}" class="{{ set_active('gears*') }}">Gear</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('weapons') }}" class="{{ set_active('weapons*') }}">Weapons</a>
            </div>
            @if(Auth::check())
                <div class="sidebar-item">
                    <a href="{{ url(Auth::user()->url . '/level') }}" class="{{ set_active(Auth::user()->url . '/level') }}">Level Logs</a>
                </div>
            @endif
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseActivity" aria-expanded="false" aria-controls="collapseActivity" id="headingActivity">
            Activity
        </div>
        <div class="collapse" aria-labelledby="headingActivity" data-parent="#accordion" id="collapseActivity">
            <div class="sidebar-item">
                <a href="{{ url('submissions') }}" class="{{ set_active('submissions*') }}">Prompt Submissions</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('claims') }}" class="{{ set_active('claims*') }}">Claims</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('characters/transfers/incoming') }}" class="{{ set_active('characters/transfers*') }}">Character Transfers</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('trades/open') }}" class="{{ set_active('trades/open*') }}">Trades</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('collection') }}" class="{{ set_active('collection*') }}">Collections</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseCrafting" aria-expanded="false" aria-controls="collapseCrafting" id="headingCrafting">
            Crafting
        </div>
        <div class="collapse" aria-labelledby="headingCrafting" data-parent="#accordion" id="collapseCrafting">
            <div class="sidebar-item">
                <a href="{{ url('crafting') }}" class="{{ set_active('crafting') }}">My Recipes</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/recipes') }}" class="{{ set_active('world/recipes') }}">All Recipes</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports" id="headingReports">
            Reports
        </div>
        <div class="collapse" aria-labelledby="headingReports" data-parent="#accordion" id="collapseReports">
            <div class="sidebar-item">
                <a href="{{ url('reports') }}" class="{{ set_active('reports*') }}">Reports</a>
            </div>
        </div>
    </li>
</ul>

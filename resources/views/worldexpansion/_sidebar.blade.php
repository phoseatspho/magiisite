<ul id="accordion">
    <li class="sidebar-header"><a href="{{ url('world/info') }}" class="card-link">World Expanded</a></li>
    <li class="sidebar-section">
        <div class="sidebar-item"><a href="{{ url('world') }}">Encyclopedia</a></div>
    </li>
    @if (Settings::get('WE_glossary'))
        <li class="sidebar-section">
            <div class="sidebar-item"><a href="{{ url('world/glossary') }}" class="{{ set_active('world/glossary') }}">Glossary</a></div>
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapseGeography" aria-expanded="true" aria-controls="collapseGeography" id="headingGeography">
            World
        </div>
        <div class="collapse show" aria-labelledby="headingGeography" data-parent="#accordion" id="collapseGeography">
            <div class="sidebar-item">
                <a href="{{ url('world/location-types') }}" class="{{ set_active('world/location-types*') }}">Arcadia</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/locations') }}" class="{{ set_active('world/locations*') }}">All Locations</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseNature" aria-expanded="false" aria-controls="collapseNature" id="headingNature">
            Nature
        </div>
        <div class="collapse" aria-labelledby="headingNature" data-parent="#accordion" id="collapseNature">
            <div class="sidebar-item">
                <a href="{{ url('world/fauna-categories') }}" class="{{ set_active('world/fauna-categories*') }}">Fauna Types</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/faunas') }}" class="{{ set_active('world/faunas*') }}">All Fauna</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/flora-categories') }}" class="{{ set_active('world/flora-categories*') }}">Flora Types</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/floras') }}" class="{{ set_active('world/floras*') }}">All Flora</a>
            </div>
        </div>
    </li>
</ul>

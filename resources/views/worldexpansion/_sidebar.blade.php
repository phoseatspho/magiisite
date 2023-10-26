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
            Geography
        </div>
        <div class="collapse show" aria-labelledby="headingGeography" data-parent="#accordion" id="collapseGeography">
            <div class="sidebar-item">
                <a href="{{ url('world/location-types') }}" class="{{ set_active('world/location-types*') }}">Location Types</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/locations') }}" class="{{ set_active('world/locations*') }}">All Locations</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseCulture" aria-expanded="false" aria-controls="collapseCulture" id="headingCulture">
            History and Society
        </div>
        <div class="collapse" aria-labelledby="headingCulture" data-parent="#accordion" id="collapseCulture">
            <div class="sidebar-item">
                <a href="{{ url('world/event-categories') }}" class="{{ set_active('world/event-categories*') }}">Event Types</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/events') }}" class="{{ set_active('world/events*') }}">Events</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/figure-categories') }}" class="{{ set_active('world/figure-categories*') }}">Figure Types</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/figures') }}" class="{{ set_active('world/figures*') }}">Figures</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/faction-types') }}" class="{{ set_active('world/faction-types*') }}">Faction Types</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/factions') }}" class="{{ set_active('world/factions*') }}">All Factions</a>
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
            <div class="sidebar-item">
                <a href="{{ url('world/concept-categories') }}" class="{{ set_active('world/concept-categories*') }}">Concept Types</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/concepts') }}" class="{{ set_active('world/concepts*') }}">All Concepts</a>
            </div>
        </div>
    </li>
</ul>

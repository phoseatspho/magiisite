<ul id="accordion">
    <li class="sidebar-header"><a href="{{ url('masterlist') }}" class="card-link">Masterlist</a></li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapseMasterlist" aria-expanded="true" aria-controls="collapseMasterlist" id="headingMasterlist">
            Masterlist
        </div>
        <div class="collapse show" aria-labelledby="headingMasterlist" data-parent="#accordion" id="collapseMasterlist">
            <div class="sidebar-item">
                <a href="{{ url('masterlist') }}" class="{{ set_active('masterlist*') }}">Characters</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('myos') }}" class="{{ set_active('myos*') }}">MYO Slots</a>
            </div>
        </div>
    </li>

    @if(isset($sublists) && $sublists->count() > 0)
        <li class="sidebar-section">
            <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseSubMasterlists" aria-expanded="false" aria-controls="collapseSubMasterlists" id="headingSubMasterlists">
                Sub Masterlists
            </div>
            <div class="collapse" aria-labelledby="headingSubMasterlists" data-parent="#accordion" id="collapseSubMasterlists">
                @foreach($sublists as $sublist)
                    <div class="sidebar-item">
                        <a href="{{ url('sublist/'.$sublist->key) }}" class="{{ set_active('sublist/'.$sublist->key) }}">{{ $sublist->name }}</a>
                    </div>
                @endforeach
            </div>
        </li>
    @endif
</ul>

<ul id="accordion">
    <li class="sidebar-header"><a href="{{ url('world') }}" class="card-link">Encyclopedia</a></li>
    <li class="sidebar-section">
        <div class="sidebar-item"><a href="{{ url('world/info') }}">World Expanded</a></div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapseCharacters" aria-expanded="true" aria-controls="collapseCharacters" id="headingCharacters">
            Characters
        </div>
        <div class="collapse show" aria-labelledby="headingCharacters" data-parent="#accordion" id="collapseCharacters">
            <div class="sidebar-item">
                <a href="{{ url('world/species') }}" class="{{ set_active('world/species*') }}">{{ __('lorekeeper.specieses') }}</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/subtypes') }}" class="{{ set_active('world/subtypes*') }}">{{ __('lorekeeper.subtypes') }}</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/rarities') }}" class="{{ set_active('world/rarities*') }}">Rarities</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/trait-categories') }}" class="{{ set_active('world/trait-categories*') }}">Trait Categories</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/traits') }}" class="{{ set_active('world/traits*') }}">All Traits</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/character-categories') }}" class="{{ set_active('world/character-categories*') }}">Character Categories</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/'.__('transformations.transformations')) }}" class="{{ set_active('world/'.__('transformations.transformations')) }}">{{ ucfirst(__('transformations.transformations')) }}</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseItems" aria-expanded="false" aria-controls="collapseItems" id="headingItems">
            Items
        </div>
        <div class="collapse" aria-labelledby="headingItems" data-parent="#accordion" id="collapseItems">
            <div class="sidebar-item">
                <a href="{{ url('world/item-categories') }}" class="{{ set_active('world/item-categories*') }}">Item Categories</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/items') }}" class="{{ set_active('world/items*') }}">All Items</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/currencies') }}" class="{{ set_active('world/currencies*') }}">Currencies</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/collections') }}" class="{{ set_active('world/collections*') }}">Collections</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/collection-categories') }}" class="{{ set_active('world/collection-categories*') }}">Collection Categories</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapse{{ ucfirst(__('awards.awards')) }}" aria-expanded="false" aria-controls="collapse{{ ucfirst(__('awards.awards')) }}" id="heading{{ ucfirst(__('awards.awards')) }}">
            {{ ucfirst(__('awards.awards')) }}
        </div>
        <div class="collapse" aria-labelledby="heading{{ ucfirst(__('awards.awards')) }}" data-parent="#accordion" id="collapse{{ ucfirst(__('awards.awards')) }}">
            <div class="sidebar-item">
                <a href="{{ url('world/'. __('awards.award') .'-categories') }}" class="{{ set_active('world/'. __('awards.award') .'-categories*') }}">{{ ucfirst(__('awards.award')) }} Categories</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/'. __('awards.awards')) }}" class="{{ set_active('world/'. __('awards.awards') .'*') }}">All {{ ucfirst(__('awards.awards')) }}</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseLevels" aria-expanded="false" aria-controls="collapseLevels" id="headingLevels">
            Levels
        </div>
        <div class="collapse" aria-labelledby="headingLevels" data-parent="#accordion" id="collapseLevels">
            <div class="sidebar-item">
                <a href="{{ url('world/levels/user') }}" class="{{ set_active('world/levels/user*') }}">User Levels</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/levels/character') }}" class="{{ set_active('world/levels/character*') }}">Character Levels</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/stats') }}" class="{{ set_active('world/stats*') }}">Stats</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseClaymore" aria-expanded="false" aria-controls="collapseClaymore" id="headingClaymore">
            Claymore
        </div>
        <div class="collapse" aria-labelledby="headingClaymore" data-parent="#accordion" id="collapseClaymore">
            <div class="sidebar-item">
                <a href="{{ url('world/weapon-categories') }}" class="{{ set_active('world/weapon-categories*') }}">Weapon Categories</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/weapons') }}" class="{{ set_active('world/weapons*') }}">All Weapons</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/gear-categories') }}" class="{{ set_active('world/gear-categories*') }}">Gear Categories</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('world/gear') }}" class="{{ set_active('world/gear*') }}">All Gear</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseRecipes" aria-expanded="false" aria-controls="collapseRecipes" id="headingRecipes">
            Recipes
        </div>
        <div class="collapse" aria-labelledby="headingRecipes" data-parent="#accordion" id="collapseRecipes">
            <div class="sidebar-item">
                <a href="{{ url('world/recipes') }}" class="{{ set_active('world/recipes*') }}">All Recipes</a>
            </div>
        </div>
    </li>
</ul>

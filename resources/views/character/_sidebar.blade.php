<ul id="accordion">
    <li class="sidebar-header"><a href="{{ $character->url }}" class="card-link">{{ $character->slug }}</a></li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapse{{ ucfirst(__('lorekeeper.character')) }}" aria-expanded="true" aria-controls="collapse{{ ucfirst(__('lorekeeper.character')) }}" id="heading{{ ucfirst(__('lorekeeper.character')) }}">
            {{ ucfirst(__('lorekeeper.character')) }}
        </div>
        <div class="collapse show" aria-labelledby="heading{{ ucfirst(__('lorekeeper.character')) }}" data-parent="#accordion" id="collapse{{ ucfirst(__('lorekeeper.character')) }}">
            <div class="sidebar-item">
                <a href="{{ $character->url }}" class="{{ set_active('character/'.$character->slug) }}">Information</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/profile' }}" class="{{ set_active('character/'.$character->slug.'/profile') }}">Profile</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/gallery' }}" class="{{ set_active('character/'.$character->slug.'/gallery') }}">Gallery</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/pets' }}" class="{{ set_active('character/' . $character->slug . '/pets') }}">Pets</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/inventory' }}" class="{{ set_active('character/'.$character->slug.'/inventory') }}">Inventory</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/bank' }}" class="{{ set_active('character/'.$character->slug.'/bank') }}">Bank</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/'.__('awards.awardcase') }}" class="{{ set_active('character/'.$character->slug.'/'.__('awards.awardcase')) }}">{{ucfirst(__('awards.awards'))}}</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/level' }}" class="{{ set_active('character/'.$character->slug.'/level') }}">Level Logs</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseHistory" aria-expanded="false" aria-controls="collapseHistory" id="headingHistory">
            History
        </div>
        <div class="collapse" aria-labelledby="headingHistory" data-parent="#accordion" id="collapseHistory">
            <div class="sidebar-item">
                <a href="{{ $character->url . '/images' }}" class="{{ set_active('character/'.$character->slug.'/images') }}">Images</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/change-log' }}" class="{{ set_active('character/'.$character->slug.'/change-log') }}">Change Log</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/ownership' }}" class="{{ set_active('character/'.$character->slug.'/ownership') }}">Ownership History</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/item-logs' }}" class="{{ set_active('character/'.$character->slug.'/item-logs') }}">Item Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/currency-logs' }}" class="{{ set_active('character/'.$character->slug.'/currency-logs') }}">Currency Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/skill-logs' }}" class="{{ set_active('character/'.$character->slug.'/skill-logs') }}">Skill Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/submissions' }}" class="{{ set_active('character/'.$character->slug.'/submissions') }}">Submissions</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/'.__('awards.award').'-logs' }}" class="{{ set_active('character/'.$character->slug.'/'.__('awards.award').'-logs') }}">{{ucfirst(__('awards.award'))}} Logs</a>
            </div>
        </div>
    </li>

    @if(Auth::check() && (Auth::user()->id == $character->user_id || Auth::user()->hasPower('manage_characters')))
        <li class="sidebar-section">
            <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseLevels" aria-expanded="false" aria-controls="collapseLevels" id="headingLevels">
                Levels + Stats
            </div>
            <div class="collapse" aria-labelledby="headingLevels" data-parent="#accordion" id="collapseLevels">
                <div class="sidebar-item">
                    <a href="{{ $character->url . '/level-area' }}" class="{{ set_active('character/'.$character->slug.'/level-area') }}">Level Area</a>
                </div>
                <div class="sidebar-item">
                    <a href="{{ $character->url . '/stats-area' }}" class="{{ set_active('character/'.$character->slug.'/stats-area') }}">Stats Area</a>
                </div>
            </div>
        </li>

        <li class="sidebar-section">
            <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings" id="headingSettings">
                Settings
            </div>
            <div class="collapse" aria-labelledby="headingSettings" data-parent="#accordion" id="collapseSettings">
                <div class="sidebar-item">
                    <a href="{{ $character->url . '/profile/edit' }}" class="{{ set_active('character/'.$character->slug.'/profile/edit') }}">Edit Profile</a>
                </div>
                <div class="sidebar-item">
                    <a href="{{ $character->url . '/transfer' }}" class="{{ set_active('character/'.$character->slug.'/transfer') }}">Transfer</a>
                </div>
                @if(Auth::user()->id == $character->user_id)
                    <div class="sidebar-item">
                        <a href="{{ $character->url . '/approval' }}" class="{{ set_active('character/'.$character->slug.'/approval') }}">Update Design</a>
                    </div>
                @endif
            </div>
        </li>
    @endif
</ul>

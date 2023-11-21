<ul id="accordion">
    <li class="sidebar-header"><a href="{{ $character->url }}" class="card-link">{{ $character->fullName }}</a></li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapse{{ucfirst(__('lorekeeper.character'))}}" aria-expanded="true" aria-controls="collapse{{ucfirst(__('lorekeeper.character'))}}" id="heading{{ucfirst(__('lorekeeper.character'))}}">
            {{ucfirst(__('lorekeeper.character'))}}
        </div>
        <div class="collapse show" aria-labelledby="heading{{ucfirst(__('lorekeeper.character'))}}" data-parent="#accordion" id="collapse{{ucfirst(__('lorekeeper.character'))}}">
            <div class="sidebar-item">
                <a href="{{ $character->url . '/profile' }}" class="{{ set_active('myo/'.$character->id.'/profile') }}">Profile</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseHistory" aria-expanded="false" aria-controls="collapseHistory" id="headingHistory">
            History
        </div>
        <div class="collapse" aria-labelledby="headingHistory" data-parent="#accordion" id="collapseHistory">
            <div class="sidebar-item">
                <a href="{{ $character->url . '/change-log' }}" class="{{ set_active('myo/'.$character->id.'/change-log') }}">Change Log</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $character->url . '/ownership' }}" class="{{ set_active('myo/'.$character->id.'/ownership') }}">Ownership History</a>
            </div>
        </div>
    </li>

    @if(Auth::check() && (Auth::user()->id == $character->user_id || Auth::user()->hasPower('manage_characters')))
        <li class="sidebar-section">
            <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseSettings" aria-expanded="false" aria-controls="collapseSettings" id="headingSettings">
                Settings
            </div>
            <div class="collapse" aria-labelledby="headingSettings" data-parent="#accordion" id="collapseSettings">
                <div class="sidebar-item">
                    <a href="{{ $character->url . '/profile/edit' }}" class="{{ set_active('myo/'.$character->id.'/profile/edit') }}">Edit Profile</a>
                </div>
                <div class="sidebar-item">
                    <a href="{{ $character->url . '/transfer' }}" class="{{ set_active('myo/'.$character->id.'/transfer') }}">Transfer</a>
                </div>
                @if(Auth::user()->id == $character->user_id)
                    <div class="sidebar-item">
                        <a href="{{ $character->url . '/approval' }}" class="{{ set_active('myo/'.$character->id.'/approval') }}">Submit MYO Design</a>
                    </div>
                @endif
            </div>
        </li>
    @endif
</ul>

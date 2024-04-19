<ul id="accordion">
    <li class="sidebar-header"><a href="{{ $user->url }}" class="card-link">{{ Illuminate\Support\Str::limit($user->name, 10, $end='...') }}</a></li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapseGallery" aria-expanded="false" aria-controls="collapseGallery" id="headingGallery">
            Gallery
        </div>
        <div class="collapse show" aria-labelledby="headingGallery" data-parent="#accordion" id="collapseGallery">
            <div class="sidebar-item">
                <a href="{{ $user->url.'/gallery' }}" class="{{ set_active('user/'.$user->name.'/gallery*') }}">Gallery</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/favorites' }}" class="{{ set_active('user/'.$user->name.'/favorites*') }}">Favorites</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/favorites/own-characters' }}" class="{{ set_active('user/'.$user->name.'/favorites/own-characters*') }}">Own Character Favorites</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseUser" aria-expanded="false" aria-controls="collapseUser" id="headingUser">
            User
        </div>
        <div class="collapse" aria-labelledby="headingUser" data-parent="#accordion" id="collapseUser">
            <div class="sidebar-item">
                <a href="{{ $user->url.'/wishlists' }}" class="{{ set_active('user/'.$user->name.'/wishlists*') }}">Wishlists</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/aliases' }}" class="{{ set_active('user/'.$user->name.'/aliases*') }}">Aliases</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/characters' }}" class="{{ set_active('user/'.$user->name.'/characters*') }}">Characters</a>
            </div>
            @if(isset($sublists) && $sublists->count() > 0)
                @foreach($sublists as $sublist)
                    <div class="sidebar-item">
                        <a href="{{ $user->url.'/sublist/'.$sublist->key }}" class="{{ set_active('user/'.$user->name.'/sublist/'.$sublist->key) }}">{{ $sublist->name }}</a>
                    </div>
                @endforeach
            @endif
            <div class="sidebar-item">
                <a href="{{ $user->url.'/myos' }}" class="{{ set_active('user/'.$user->name.'/myos*') }}">MYO Slots</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/inventory' }}" class="{{ set_active('user/'.$user->name.'/inventory*') }}">Inventory</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/'.__('awards.awardcase') }}" class="{{ set_active('user/'.$user->name.'/awardcase*') }}">Awardcase</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/bank' }}" class="{{ set_active('user/'.$user->name.'/bank*') }}">Bank</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/borders' }}" class="{{ set_active('user/'.$user->name.'/borders*') }}">Borders</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/level' }}" class="{{ set_active('user/'.$user->name.'/level*') }}">Level Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/pets' }}" class="{{ set_active('user/'.$user->name.'/pets*') }}">Pets</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/armoury' }}" class="{{ set_active('user/'.$user->name.'/armoury*') }}">Armoury</a>
            </div>
        </div>
    </li>

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseHistory" aria-expanded="false" aria-controls="collapseHistory" id="headingHistory">
            History
        </div>
        <div class="collapse" aria-labelledby="headingHistory" data-parent="#accordion" id="collapseHistory">
            <div class="sidebar-item">
                <a href="{{ $user->url.'/ownership' }}" class="{{ set_active('user/'.$user->name.'/ownership*') }}">Ownership History</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/item-logs' }}" class="{{ set_active('user/'.$user->name.'/item-logs*') }}">Item Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/currency-logs' }}" class="{{ set_active('user/'.$user->name.'/currency-logs*') }}">Currency Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/'.__('awards.award').'-logs' }}" class="{{ set_active('user/'.$user->name.'/award-logs*') }}">{{ucfirst(ucfirst(__('awards.award')))}} Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/border-logs' }}" class="{{ set_active('user/'.$user->name.'/border-logs*') }}">Border Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/pet-logs' }}" class="{{ set_active('user/'.$user->name.'/pet-logs*') }}">Pet Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/submissions' }}" class="{{ set_active('user/'.$user->name.'/submissions*') }}">Submissions</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/collection-logs' }}" class="{{ set_active('user/'.$user->name.'/collection-logs*') }}">Collection Logs</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ $user->url.'/recipe-logs' }}" class="{{ set_active('user/'.$user->name.'/recipe-logs*') }}">Recipe Logs</a>
            </div>
        </div>
    </li>

    @if (Auth::check() && Auth::user()->hasPower('edit_user_info'))
        <li class="sidebar-section">
            <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseAdmin" aria-expanded="false" aria-controls="collapseAdmin" id="headingAdmin">
                Admin
            </div>
            <div class="collapse" aria-labelledby="headingAdmin" data-parent="#accordion" id="collapseAdmin">
                <div class="sidebar-item">
                    <a href="{{ $user->adminUrl }}">Edit User</a>
                </div>
            </div>
        </li>
    @endif
</ul>

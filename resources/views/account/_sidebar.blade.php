<ul id="accordion">
    <li class="sidebar-header"><a href="{{ url('/') }}" class="card-link">Home</a></li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapseAccount" aria-expanded="true" aria-controls="collapseAccount" id="headingAccount">
            Account
        </div>
        <div class="collapse show" aria-labelledby="headingAccount" data-parent="#accordion" id="collapseAccount">
            <div class="sidebar-item">
                <a href="{{ url('notifications') }}" class="{{ set_active('notifications') }}">Notifications</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('account/settings') }}" class="{{ set_active('account/settings') }}">Settings</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('account/aliases') }}" class="{{ set_active('account/aliases') }}">Aliases</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('account/bookmarks') }}" class="{{ set_active('account/bookmarks') }}">Bookmarks</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('account/deactivate') }}" class="{{ set_active('account/deactivate') }}">Deactivate</a>
            </div>
        </div>
    </li>
</ul>

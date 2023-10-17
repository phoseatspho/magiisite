<div class="scroll">
    <div class="scroll-header"></div>
    <div class="scroll-body">
        <div class="scroll-heading">Navigation</div>
        <hr>
        <ul class="nav nav-pills scroll-nav d-flex nav-justified justify-content-center" id="pills-tab" role="tablist">
            <li class="nav-item" data-toggle="tooltip" title="Home">
                <a class="nav-link @sectionMissing('sidebar') active @endif" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">
                    <i class="fas fa-home"></i>
                </a>
            </li>

            @if(Auth::check())
                <li class="nav-item" data-toggle="tooltip" title="Profile">
                    <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">
                        <i class="fas fa-user"></i>
                    </a>
                </li>

                <li class="nav-item" data-toggle="tooltip" title="Play">
                    <a class="nav-link" id="pills-play-tab" data-toggle="pill" href="#pills-play" role="tab" aria-controls="pills-play" aria-selected="false">
                        <i class="fas fa-gamepad"></i>
                    </a>
                </li>
            @endif

            <li class="nav-item" data-toggle="tooltip" title="Explore">
                <a class="nav-link" id="pills-explore-tab" data-toggle="pill" href="#pills-explore" role="tab" aria-controls="pills-explore" aria-selected="false">
                    <i class="fas fa-map"></i>
                </a>
            </li>

            @hasSection('sidebar')
                <li class="nav-item" data-toggle="tooltip" title="Links">
                    <a class="nav-link active" id="pills-links-tab" data-toggle="pill" href="#pills-links" role="tab" aria-controls="pills-links" aria-selected="false">
                        <i class="fas fa-map-marker-alt"></i>
                    </a>
                </li>
            @endif
        </ul>

        <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade @sectionMissing('sidebar') show active @endif" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                <ul class="scroll-list">
                    <li>
                        @if(Auth::check() && Auth::user()->is_news_unread && Config::get('lorekeeper.extensions.navbar_news_notif'))
                            <a class="d-flex text-warning" href="{{ url('news') }}"><strong>News</strong><i class="fas fa-bell"></i></a>
                        @else
                            <a href="{{ url('news') }}">News</a>
                        @endif
                    </li>
                    <li>
                        @if(Auth::check() && Auth::user()->is_sales_unread && Config::get('lorekeeper.extensions.navbar_news_notif'))
                            <a class="d-flex text-warning" href="{{ url('sales') }}"><strong>Adopts</strong><i class="fas fa-bell"></i></a>
                        @else
                            <a href="{{ url('sales') }}">Adopts</a>
                        @endif
                    </li>
                    <li><a href="{{ url('masterlist') }}">Masterlist</a></li>
                    <li><a href="{{ url('/') }}">Events</a></li>
                    <li><a href="https://discord.gg/magiispecies">Discord</a></li>
                </ul>
            </div>

            @if(Auth::check())
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                    <ul class="scroll-list">
                        <li><a href="{{ Auth::user()->url }}">My Profile</a></li>
                        <li><a href="{{ url('characters') }}">My Characters</a></li>
                        <li><a href="{{ url('characters/myos') }}">My MYOs</a></li>
                        <li><a href="{{ url('bank') }}">Bank</a></a></li>
                        <li><a href="{{ url('inventory') }}">Inventory</a></li>
                        <li><a href="{{ url('awardcase') }}">Awards</a></li>
                    </ul>
                </div>

                <div class="tab-pane fade" id="pills-play" role="tabpanel" aria-labelledby="pills-play-tab">
                    <ul class="scroll-list">
                        <li><a href="{{ url(__('dailies.dailies')) }}">Daily Check In</a></li>
                        <li><a href="{{ url('prompts/prompts') }}">Quests</a></li>
                        <li><a href="{{ url('foraging') }}">Foraging</a></li>
                        <li><a href="{{ url('collection') }}">Collections</a></a></li>
                        <li><a href="{{ url('activities') }}">Activities</a></li>
                        <li><a href="{{ url('fetch') }}">Fetch Quests</a></li>
                        <li><a href="{{ url('crafting') }}">Crafting</a></li>
                        <li><a href="{{ url('raffles') }}">Raffles</a></li>
                    </ul>
                </div>
            @endif

            <div class="tab-pane fade" id="pills-explore" role="tabpanel" aria-labelledby="pills-explore-tab">
                <ul class="scroll-list">
                    <li><a href="{{ url('world') }}">Encyclopaedia</a></li>
                    <li><a href="{{ url('world/info') }}">World Expanded</a></li>
                    <li><a href="{{ url('shops') }}">Shops</a></li>
                    <li><a href="{{ url('gallery') }}">Gallery</a></a></li>
                    <li><a href="{{ url('/') }}">Custom Link 1</a></li>
                    <li><a href="{{ url('/') }}">Custom Link 2</a></li>
                </ul>
            </div>

            @hasSection('sidebar')
                <div class="tab-pane fade show active" id="pills-links" role="tabpanel" aria-labelledby="pills-links-tab">
                    @yield('sidebar')
                </div>
            @endif
        </div>

        @if(Request::is('/'))
            <hr>

            <div class="my-3 px-3">
                <a class="btn btn-primary my-1" href="{{ url('/') }}">
                    Button 1
                </a>

                <a class="btn btn-primary my-1" href="{{ url('/') }}">
                    Button 2
                </a>
            </div>
        @endif
    </div>
    <div class="scroll-footer"></div>
</div>

<nav class="navbar navbar-expand-md navbar-light">
    <ul class="navbar-nav ml-auto mr-auto">
        <li class="nav-item"><a href="{{ url('info/about') }}" class="nav-link">About</a></li>
        <li class="nav-item"><a href="{{ url('info/terms') }}" class="nav-link">Terms</a></li>
        <li class="nav-item"><a href="{{ url('info/privacy') }}" class="nav-link">Privacy</a></li>
        <li class="nav-item"><a href="{{ url('reports/bug-reports') }}" class="nav-link">Bug Reports</a></li>
        <li class="nav-item"><a href="{{ url('team') }}" class="nav-link">Team</a></li>
        <li class="nav-item"><a href="{{ url('credits') }}" class="nav-link">Credits</a></li>
    </ul>
</nav>
<div class="copyright">&copy; {{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} v{{ config('lorekeeper.settings.version') }} {{ Carbon\Carbon::now()->year }}</div>
<div class="site-footer-image">
    <img src="{{ asset('files/footer.png') }}" class="img-fluid"/>
</div>

@if (config('lorekeeper.extensions.scroll_to_top'))
    @include('widgets/_scroll_to_top')
@endif

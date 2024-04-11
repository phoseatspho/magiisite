<ul id="accordion">
    <li class="sidebar-header"><a href="{{ url('gallery') }}" class="card-link">Gallery</a></li>

    @auth
        <li class="sidebar-section">
            <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapseSubmissions" aria-expanded="true" aria-controls="collapseSubmissions" id="headingSubmissions">
                My Submissions
            </div>
            <div class="collapse show" aria-labelledby="headingSubmissions" data-parent="#accordion" id="collapseSubmissions">
                <div class="sidebar-item">
                    <a href="{{ url('gallery/submissions/pending') }}" class="{{ set_active('gallery/submissions*') }}">My Submission Queue</a>
                </div>
                <div class="sidebar-item">
                    <a href="{{ url('user/' . Auth::user()->name . '/gallery') }}" class="{{ set_active('user/' . Auth::user()->name . '/gallery') }}">My Gallery</a>
                </div>
                <div class="sidebar-item">
                    <a href="{{ url('user/' . Auth::user()->name . '/favorites') }}" class="{{ set_active('user/' . Auth::user()->name . '/favorites') }}">My Favorites</a>
                </div>
            </div>
        </li>
    @endauth

    @if (config('lorekeeper.extensions.show_all_recent_submissions.enable') && config('lorekeeper.extensions.show_all_recent_submissions.links.sidebar'))
<li class="sidebar-section">
            <div class="sidebar-item"><a href="{{ url('gallery/all') }}" class="{{ set_active('gallery/all') }}">All Recent Submissions</a></div>
        </li>
@endif

    @if ($galleryPage && $sideGallery->children->count())
        <li class="sidebar-section">
            <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapse{{ $sideGallery->name }}" aria-expanded="false" aria-controls="collapse{{ $sideGallery->name }}" id="heading{{ $sideGallery->name }}">
                {{ $sideGallery->name }}: Sub-Galleries
            </div>
            <div class="collapse" aria-labelledby="heading{{ $sideGallery->name }}" data-parent="#accordion" id="collapse{{ $sideGallery->name }}">
                @foreach ($sideGallery->children()->visible()->get() as $child)
                    <div class="sidebar-item">
                        <a href="{{ url('gallery/' . $child->id) }}" class="{{ set_active('gallery/' . $child->id) }}">{{ $child->name }}</a>
                    </div>
                @endforeach
            </div>
        </li>
    @endif

    @if ($galleryPage && $sideGallery->siblings() && $sideGallery->siblings->count())
        <li class="sidebar-section">
            <div class="sidebar-section-header">{{ $sideGallery->parent->name }}: Sub-Galleries</div>
            @foreach ($sideGallery->siblings()->visible()->get() as $sibling)
<div class="sidebar-item"><a href="{{ url('gallery/' . $sibling->id) }}" class="{{ set_active('gallery/' . $sibling->id) }}">{{ $sibling->name }}</a></div>
@endforeach
        </li>
    @endif

    @if ($galleryPage && $sideGallery->avunculi() && $sideGallery->avunculi->count())
        <li class="sidebar-section">
            <div class="sidebar-section-header">{{ $sideGallery->parent->parent->name }}: Sub-Galleries</div>
            @foreach ($sideGallery->avunculi()->visible()->get() as $avunculus)
<div class="sidebar-item"><a href="{{ url('gallery/' . $avunculus->id) }}" class="{{ set_active('gallery/' . $avunculus->id) }}">{{ $avunculus->name }}</a></div>
@endforeach
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseGalleries" aria-expanded="false" aria-controls="collapseGalleries" id="headingGalleries">
            Galleries
        </div>
        <div class="collapse" aria-labelledby="headingGalleries" data-parent="#accordion" id="collapseGalleries">
            @foreach ($sidebarGalleries as $gallery)
                <div class="sidebar-item">
                    <a href="{{ url('gallery/' . $gallery->id) }}" class="{{ set_active('gallery/.$gallery->id') }}">{{ $gallery->name }}</a>
                </div>
            @endforeach
        </div>
    </li>
</ul>

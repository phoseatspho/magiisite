@extends('galleries.layout')

@section('gallery-title')
    {{ $submission->displayTitle }}
@endsection

@section('meta-img')
    {{ isset($submission->hash) ? $submission->thumbnailUrl : asset('images/meta-image.png') }}
@endsection

@section('gallery-content')
        {!! breadcrumbs(['gallery' => 'gallery', $submission->gallery->displayName => 'gallery/' . $submission->gallery->id, $submission->displayTitle => 'gallery/view/' . $submission->id]) !!}

        <h1>
            @if (!$submission->isVisible)
    <i class="fas fa-eye-slash"></i>
    @endif {{ $submission->displayTitle }}
            <div class="float-right">
                @if (Auth::check())
    {!! Form::open(['url' => '/gallery/favorite/' . $submission->id]) !!}
                    @if ($submission->user->id != Auth::user()->id && $submission->collaborators->where('user_id', Auth::user()->id)->first() == null && $submission->isVisible)
    {!! Form::button('<i class="fas fa-star"></i> ', [
        'class' => 'btn ' . ($submission->favorites->where('user_id', Auth::user()->id)->first() == null ? 'btn-outline-primary' : 'btn-primary'),
        'data-toggle' => 'tooltip',
        'title' => ($submission->favorites->where('user_id', Auth::user()->id)->first() == null ? 'Add to' : 'Remove from') . ' your Favorites',
        'type' => 'submit',
    ]) !!}
    @endif
                    @if ($submission->user->id == Auth::user()->id || Auth::user()->hasPower('manage_submissions'))
    <a class="btn btn-outline-primary" href="/gallery/queue/{{ $submission->id }}" data-toggle="tooltip" title="View Log Details"><i class="fas fa-clipboard-list"></i></a>
                        <a class="btn btn-outline-primary" href="/gallery/edit/{{ $submission->id }}"><i class="fas fa-edit"></i> Edit</a>
    @endif
                    {!! Form::close() !!}
    @endif
            </div>
            <div class="col-md text-right">
                {{ $submission->favorites->count() }} Favorite{{ $submission->favorites->count() != 1 ? 's' : '' }} ・ {{ $commentCount }} Comment{{ $commentCount != 1 ? 's' : '' }}
            </div>
        </diV>
    </div>

    <!-- Main Content -->
    @if (isset($submission->parsed_text) && $submission->parsed_text)
        <div class="card mx-md-4 mb-4">
            <div class="card-body">
    @endif
    @if (isset($submission->hash) && $submission->hash)
        <div class="text-center mb-4">
            <a href="{{ $submission->imageUrl }}" data-lightbox="entry" data-title="{{ $submission->displayTitle }}">
                <img src="{{ $submission->imageUrl }}" class="image" style="max-width:100%; {{ isset($submission->parsed_text) && $submission->parsed_text ? 'max-height:50vh;' : 'max-height:70vh;' }} border-radius:.5em;" data-toggle="tooltip"
                    title="Click to view larger size" alt="{{ $submission->displayTitle }}" />
            </a>
        </div>
    @endif
    @if (isset($submission->parsed_text) && $submission->parsed_text)
        {!! $submission->parsed_text !!}
    @endif
    @if (isset($submission->parsed_text) && $submission->parsed_text)
        </div>
        </div>
    @endif

    <!-- Submission Info -->
    <div class="row mx-md-2 mb-4">
        <div class="col-md mb-4">
            <div class="row mb-4 no-gutters">
                <div class="col-md-2 mb-4 mobile-hide text-center">
                    <a href="/user/{{ $submission->user->name }}"><img src="{{ $submission->user->avatarUrl }}" style="border-radius:50%; margin-right:25px; max-width:100%;" data-toggle="tooltip" title="{{ $submission->user->name }}"
                            alt="{{ $submission->user->name }}'s Avatar" />{!! $submission->user->userBorder !!}</a>
                </div>
                <div class="col-md text-right">
                    {{ $submission->favorites->count() }} Favorite{{ $submission->favorites->count() != 1 ? 's' : '' }} ・ {{ $commentCount }} Comment{{ $commentCount != 1 ? 's' : '' }}
                </div>
            </diV>
        </div>

        <!-- Main Content -->
        @if (isset($submission->parsed_text) && $submission->parsed_text)
    <div class="card mx-md-4 mb-4">
                <div class="card-body">
    @endif
        @if (isset($submission->hash) && $submission->hash)
    <div class="text-center mb-4">
                <a href="{{ $submission->imageUrl }}" data-lightbox="entry" data-title="{{ $submission->displayTitle }}">
                    <img src="{{ $submission->imageUrl }}" class="image" style="max-width:100%; {{ isset($submission->parsed_text) && $submission->parsed_text ? 'max-height:50vh;' : 'max-height:70vh;' }} border-radius:.5em;" data-toggle="tooltip"
                        title="Click to view larger size" alt="{{ $submission->displayTitle }}" />
                </a>
            </div>
    @endif
        @if (isset($submission->parsed_text) && $submission->parsed_text)
    {!! $submission->parsed_text !!}
    @endif
        @if (isset($submission->parsed_text) && $submission->parsed_text)
    </div>
            </div>
    @endif

        <!-- Submission Info -->
        <div class="row mx-md-2 mb-4">
            <div class="col-md mb-4">
                <div class="row mb-4 no-gutters">
                    <div class="col-md-2 mb-4 mobile-hide text-center">
                        <a href="/user/{{ $submission->user->name }}"><img src="{{ $submission->user->avatarUrl }}" style="border-radius:50%; margin-right:25px; max-width:100%;" data-toggle="tooltip" title="{{ $submission->user->name }}"
                                alt="{{ $submission->user->name }}'s Avatar" /></a>
                    </div>
                    <div class="col-md ml-md-2">
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ $submission->displayTitle }}
                                    <a class="float-right" href="{{ url('reports/new?url=') . $submission->url }}"><i class="fas fa-exclamation-triangle" data-toggle="tooltip" title="Click here to report this submission." style="opacity: 50%;"></i></a>
                                </h5>
                                <div class="float-right">
                                    @if (Auth::check() && ($submission->user->id != Auth::user()->id && $submission->collaborators->where('user_id', Auth::user()->id)->first() == null) && $submission->isVisible)
    {!! Form::open(['url' => '/gallery/favorite/' . $submission->id]) !!}
                                        {{ $submission->favorites->count() }} {!! Form::button('<i class="fas fa-star"></i> ', [
                                            'style' => 'border:0; border-radius:.5em;',
                                            'class' => $submission->favorites->where('user_id', Auth::user()->id)->first() != null ? 'btn-success' : '',
                                            'data-toggle' => 'tooltip',
                                            'title' => ($submission->favorites->where('user_id', Auth::user()->id)->first() == null ? 'Add to' : 'Remove from') . ' your Favorites',
                                            'type' => 'submit',
                                        ]) !!} ・ {{ $commentCount }} <i class="fas fa-comment"></i>
                                        {!! Form::close() !!}
@else
    {{ $submission->favorites->count() }} <i class="fas fa-star" data-toggle="tooltip" title="Favorites"></i> ・ {{ $commentCount }} <i class="fas fa-comment" data-toggle="tooltip" title="Comments"></i>
    @endif
                                </div>
                                In {!! $submission->gallery->displayName !!} ・ By {!! $submission->credits !!}
                                @if (isset($submission->content_warning))
    ・ <span class="text-danger"><strong>Content Warning:</strong></span> {!! nl2br(htmlentities($submission->content_warning)) !!}
    @endif
                            </div>
                            <div class="card-body">
                                {!! $submission->parsed_description ? $submission->parsed_description : '<i>No description provided.</i>' !!}

                                <hr />
                                <p>
                                    <strong>Submitted By</strong> {!! $submission->user->displayName !!}
                                    @if ($submission->prompt_id)
    <strong>for</strong> {!! $submission->prompt->displayName !!}
    @endif
                                    @if ($submission->favorites->count())
    ・ <a class="view-favorites" href="#">View Favorites</a>
    @endif
                                    <br />
                                    <strong>Submitted:</strong> {!! pretty_date($submission->created_at) !!} ・
                                    <strong>Last Updated:</strong> {!! pretty_date($submission->updated_at) !!}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                @if ($submission->collaborators->count())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>Collaborators</h5>
                        </div>
                        <div class="card-body">
                            {!! $submission->parsed_description ? $submission->parsed_description : '<i>No description provided.</i>' !!}

                            <hr />
                            <p>
                                <strong>Submitted By</strong> {!! $submission->user->displayName !!}
                                @if ($submission->prompt_id)
                                    <strong>for</strong> {!! $submission->prompt->displayName !!}
                                @endif
                                @if($submission->location_id && ($submission->location->is_active || (Auth::check() && Auth::user()->isStaff)))
                                    ・ <strong>Location:</strong> {!! $submission->location->fullDisplayNameUC !!}
                                @endif
                                @if ($submission->favorites->count())
                                    ・ <a class="view-favorites" href="#">View Favorites</a>
                                @endif
                                <br />
                                <strong>Submitted:</strong> {!! pretty_date($submission->created_at) !!} ・
                                <strong>Last Updated:</strong> {!! pretty_date($submission->updated_at) !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments -->
        @if ($submission->isVisible)
    <div class="container">
                @comments(['model' => $submission, 'perPage' => 5])
            </div>
    @endif

@endsection

@section('scripts')
    @parent
        <script>
            $(document).ready(function() {
                $('.view-favorites').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('gallery/view/favorites') }}/{{ $submission->id }}", 'Favorited By');
                });
            });
        </script>
@endsection

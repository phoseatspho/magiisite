<div class="row world-entry">

    <div class="col-md-3 world-entry-image"><a href="{{ $border->imageUrl }}" data-lightbox="entry"
            data-title="{{ $border->name }}"><img src="{{ $border->imageUrl }}" class="world-entry-image"
                alt="{{ $border->name }}" /></a>
        {!! $border->preview(Auth::check() ? Auth::user()->id : '') !!}</div>
    <div class="{{ $border->imageUrl ? 'col-md-9' : 'col-12' }}">
        <h3>
            {!! $border->displayName !!}@if (isset($border->idUrl) && $border->idUrl)
                <a href="{{ $border->idUrl }}" class="world-entry-search text-muted"><i class="fas fa-search"></i></a>
            @endif
            @if ($border->admin_only)
                <i class="fas fa-user-lock text-warning" data-toggle="tooltip"
                    title="This border is exclusive to staff members."></i>
            @else
                @if (!$border->is_default)
                    @if (Auth::check() && Auth::user()->hasBorder($border->id))
                        <i class="fas fa-lock-open" data-toggle="tooltip" title="You have this border!"></i>
                    @else
                        <i class="fas fa-lock" style="opacity:0.5" data-toggle="tooltip"
                            title="You do not have this border."></i>
                    @endif
                @else
                    <i class="fas fa-user" data-toggle="tooltip"
                        title="This border is automatically available to all users."></i>
                @endif
            @endif
        </h3>
        <div class="world-entry-text parsed-text">
            @if (isset($border->category) && $border->category)
                <div class="col-md">
                    <p><strong>Category:</strong> {!! $border->category->displayName !!}</p>
                </div>
            @endif
            {!! $border->parsed_description !!}
        </div>
    </div>
</div>

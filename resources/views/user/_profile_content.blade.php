@if ($deactivated)
    <div style="filter:grayscale(1); opacity:0.75">
@endif



            @if ($user->settings->is_fto)
                <div class="col-md-1 text-center">
                    <span class="btn badge-success float-md-right" data-toggle="tooltip" title="This user has not owned any characters from this world before.">FTO</span>
                </div>
            @endif
        </div>




<h2>
<a href="{{ $user->url.'/characters' }}">Characters</a>
    @if (isset($sublists) && $sublists->count() > 0)
        @foreach ($sublists as $sublist)
            / <a href="{{ $user->url . '/sublist/' . $sublist->key }}">{{ $sublist->name }}</a>
        @endforeach
    @endif
</h2>

@foreach ($characters->take(4)->get()->chunk(4) as $chunk)
    <div class="row mb-4">
        @foreach ($chunk as $character)
            <div class="col-md-3 col-6 text-center">
                <div>
                    <a href="{{ $character->url }}"><img src="{{ $character->image->thumbnailUrl }}" class="img-thumbnail" alt="{{ $character->fullName }}" /></a>
                </div>
                <div class="mt-1">
                    <a href="{{ $character->url }}" class="h5 mb-0">
                        @if (!$character->is_visible)
                            <i class="fas fa-eye-slash"></i>
                        @endif {{ Illuminate\Support\Str::limit($character->fullName, 20, $end = '...') }}
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endforeach

<div class="text-right"><a href="{{ $user->url . '/characters' }}">View all...</a></div>
<hr class="mb-5" />

<div class="row col-12">
    <div class="col-md-8">

        @comments(['model' => $user->profile, 'perPage' => 5])

    </div>
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5>Mention This User</h5>
            </div>
            <div class="card-body">
                In the rich text editor:
                <div class="alert alert-secondary">
                    {{ '@' . $user->name }}
                </div>
                In a comment:
                <div class="alert alert-secondary">
                    [{{ $user->name }}]({{ $user->url }})
                </div>
                <hr>
                <div class="my-2"><strong>For Names and Avatars:</strong></div>
                In the rich text editor:
                <div class="alert alert-secondary">
                    {{ '%' . $user->name }}
                </div>
                In a comment:
                <div class="alert alert-secondary">
                    [![{{ $user->name }}'s Avatar]({{ $user->avatarUrl }})]({{ $user->url }}) [{{ $user->name }}]({{ $user->url }})
                </div>
            </div>
            @if (Auth::check() && Auth::user()->isStaff)
                <div class="card-footer">
                    <h5>[ADMIN]</h5>
                    Permalinking to this user, in the rich text editor:
                    <div class="alert alert-secondary">
                        [user={{ $user->id }}]
                    </div>
                    Permalinking to this user's avatar, in the rich text editor:
                    <div class="alert alert-secondary">
                        [userav={{ $user->id }}]
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@if ($deactivated)
    </div>
@endif

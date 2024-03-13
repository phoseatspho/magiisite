@if ($deactivated)
    <div style="filter:grayscale(1); opacity:0.75">
@endif

<div class="col-lg-12 mb-3">
    <div class="center">
        <!-- User Icon -->
        {!! $user->userBorder !!}
    </div>

    <div class="col">
        <!-- Username & optional FTO Badge -->
        <div class="row no-gutters">
            <div class=" h3 text-center text-md-left">
             
          
            {!! $user->displayName !!} 
                <a href="{{ url('reports/new?url=') . $user->url }}"><i class="fas fa-exclamation-triangle fa-xs" data-toggle="tooltip" title="Click here to report this user." style="opacity: 50%; font-size:0.5em;"></i></a>
            </div>
            <div class="col-md-1 text-center">
                <span class="badge badge-success float-md-right" data-toggle="tooltip" title="This user has not owned any characters from this world before.">FTO</span>
            </div>
</div>

 <!-- User Information -->
 <div class="col">
 

            <div class="row col-sm-9">
                <div class="col-lg-2 col-md-3 col-4">
                    <h6>Alias</h6>
                </div>
                <div class="col-lg-10 col-md-9 col-8 pl-3">{!! $user->displayAlias !!}</div>
            </div>

            <div class="row col-sm-7">
                <div class="col-md-3 col-4">
                    <h6>Joined</h6>
                </div>
                <div class="col-md-9 col-8">{!! format_date($user->created_at, false) !!}</div>
            </div>
            <div class="row col-sm-9">
                <div class="col-lg-2 col-md-3 col-4">
                    <h6>Rank</h6>
                </div>

<div class="col-lg-10 col-md-9 col-8">{!! $user->rank->displayName !!} {!! add_help($user->rank->parsed_description) !!}</div>
            </div>

            @if ($user->birthdayDisplay && isset($user->birthday))
                <div class="row col-sm-7">
                    <div class="col-md-3 col-4">
                        <h6>Bday</h6>
                    </div>
                    <div class="col-md-9 col-8">{!! $user->birthdayDisplay !!}</div>
                </div>
            @endif
        </div>
    </div>
</div>


@if (isset($user->profile->parsed_text))
    <div class="card mb-3" style="clear:both;">
        <div class="card-body">
            {!! $user->profile->parsed_text !!}
        </div>
    </div>
@endif

<div class="card-deck mb-4 profile-assets" style="clear:both;">
    <div class="card profile-currencies profile-assets-card">
        <div class="card-body text-center">
            <h5 class="card-title">Bank</h5>
            <div class="profile-assets-content">
                @foreach ($user->getCurrencies(false) as $currency)
                    <div>{!! $currency->display($currency->quantity) !!}</div>
                @endforeach
            </div>
            <div class="text-right"><a href="{{ $user->url . '/bank' }}">View all...</a></div>
        </div>
    </div>
    <div class="card profile-inventory profile-assets-card">
        <div class="card-body text-center">
            <h5 class="card-title">Inventory</h5>
            <div class="profile-assets-content">
                @if (count($items))
                    <div class="row">
                        @foreach ($items as $item)
                            <div class="col-md-3 col-6 profile-inventory-item">
                                @if ($item->imageUrl)
                                    <img src="{{ $item->imageUrl }}" data-toggle="tooltip" title="{{ $item->name }}" alt="{{ $item->name }}" />
                                @else
                                    <p>{{ $item->name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div>No items owned.</div>
                @endif
            </div>
            <div class="text-right"><a href="{{ $user->url . '/inventory' }}">View all...</a></div>
        </div>
    </div>
</div>

<div class="card mb-3">
        <div class="card-body text-center">
            <h5 class="card-title">{{ ucfirst(__('awards.awards')) }}</h5>
            <div class="card-body">
                @if(count($awards))
                    <div class="row">
                        @foreach($awards as $award)
                            <div class="col-md-3 col-6 profile-inventory-item">
                                @if($award->imageUrl)
                                    <img src="{{ $award->imageUrl }}" data-toggle="tooltip" title="{{ $award->name }}" />
                                @else
                                    <p>{{ $award->name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div>No {{ __('awards.awards') }} earned.</div>
                @endif
            </div>
            <div class="text-right"><a href="{{ $user->url.'/'.__('awards.awardcase') }}">View all...</a></div>
      </div>
    
</div>

<div class="card-deck mb-4 profile-assets" style="clear:both;">
    <div class="card profile-inventory profile-assets-card">
        <div class="card-body text-center">
            <h5 class="card-title">Completed Collections</h5>
            <div class="profile-assets-content">
                @if(count($collections))
                    <div class="row">
                        @foreach($collections as $collection)
                            <div class="col-md-3 col-6 profile-inventory-item">
                                @if($collection->imageUrl)
                                    <img src="{{ $collection->imageUrl }}" data-toggle="tooltip" title="{{ $collection->name }}" alt="{{ $collection->name }}"/>
                                @else
                                    <p>{{ $collection->name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div>No collections completed.</div>
                @endif
            </div>
            <div class="text-right"><a href="{{ $user->url.'/collection-logs' }}">View all...</a></div>
        </div>
    </div>
</div>

<div class="card-deck mb-4 profile-assets">
    <div class="card profile-currencies profile-assets-card">
        <div class="card-body text-center">
            <h5 class="card-title">Pets</h5>
            <div class="card-body">
                @if(count($pets))
                    <div class="row">
                        @foreach($pets as $pet)
                            <div class="col profile-inventory-item">
                                <a href="#" class="inventory-stack"><img src="{{ $pet->variantimage($pet->pivot->variant_id) }}" class="img-fluid" style="width:25%;" data-toggle="tooltip" title="{{ $pet->name }}" alt="{{ $pet->name }}" />
                            </div>
                        @endforeach
                    </div>
                @else
                    <div>No pets owned.</div>
                @endif
            </div>
            <div class="text-right"><a href="{{ $user->url.'/pets' }}">View all...</a></div>
        </div>
    </div>
</div>

<div class="card profile-inventory profile-assets-card">
        <div class="card-body text-center">
            <h5 class="card-title">Armoury</h5>
            <div class="card-body">
                @if(count($armours))
                    <div class="row">
                        @foreach($armours as $armour)
                            <div class="col-md-3 col-6 profile-inventory-item">
                                @if($armour->imageUrl)
                                <img src="{{ $armour->imageUrl }}" data-toggle="tooltip" title="{{ $armour->name }}" alt="{{ $armour->name }}"/>
                                @else
                                    <p>{{ $armour->name }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div>No weapons or gear owned.</div>
                @endif
            </div>
            <div class="text-right"><a href="{{ $user->url.'/armoury' }}">View all...</a></div>
        </div>
</div>

<h2>
    <a href="{{ $user->url . '/characters' }}">Characters</a>
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

<div class="row col-sm-7">
                <div class="col-md-3 col-4">
                    <h6>Joined</h6>
                </div>
                <div class="col-md-9 col-8">{!! format_date($user->created_at, false) !!}</div>
            </div>

@if ($deactivated)
    </div>
@endif
{{-- Image Data --}}
<div class="col-md-5 d-flex">
    <div class="card character-bio w-100">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a class="nav-link active" id="infoTab-{{ $image->id }}" data-toggle="tab" href="#info-{{ $image->id }}" role="tab">Info</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="notesTab-{{ $image->id }}" data-toggle="tab" href="#notes-{{ $image->id }}" role="tab">Notes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="creditsTab-{{ $image->id }}" data-toggle="tab" href="#credits-{{ $image->id }}" role="tab">Credits</a>
                </li>
                @if (isset($showMention) && $showMention)
                    <li class="nav-item">
                        <a class="nav-link" id="mentionTab-{{ $image->id }}" data-toggle="tab" href="#mention-{{ $image->id }}" role="tab">Mention</a>
                    </li>
                @endif
                @if (Auth::check() && Auth::user()->hasPower('manage_characters'))
                    <li class="nav-item">
                        <a class="nav-link" id="settingsTab-{{ $image->id }}" data-toggle="tab" href="#settings-{{ $image->id }}" role="tab"><i class="fas fa-cog"></i></a>
                    </li>
                @endif
            </ul>
        </div>
        <div class="card-body tab-content">
            <div class="text-right mb-1">
                <div class="badge badge-primary">Image #{{ $image->id }}</div>
            </div>
            @if (!$image->character->is_myo_slot && !$image->is_valid)
                <div class="alert alert-danger">
                    This version of this {{ __('lorekeeper.character') }} is outdated, and only noted here for recordkeeping purposes. Do not use as an official reference.
                </div>
            @endif

            {{-- Basic info --}}
            <div class="tab-pane fade show active" id="info-{{ $image->id }}">
            <div class="row">
                    <div class="col-lg-4 col-md-6 col-4"><h6>CLASS</h6></div>
                    <div class="col-lg-8 col-md-6 col-8">{!! $image->character->class_id ? $image->character->class->displayName : 'None' !!} 
                        @if(Auth::check())
                        @if(Auth::user()->isStaff || Auth::user()->id == $image->character->user_id && $image->character->class_id == null) 
                        <a href="#" class="btn btn-outline-info btn-sm edit-class ml-1" data-id="{{ $image->character->id }}"><i class="fas fa-cog"></i></a>
                        @endif
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-4">
                        <h6>SPECIES</h6>
                    </div>
                    <div class="col-lg-8 col-md-6 col-8">{!! $image->species_id ? $image->species->displayName : 'None' !!}</div>
                </div>
                @if ($image->subtype_id)
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-4">
                            <h5>Subtype</h5>
                        </div>
                        <div class="col-lg-8 col-md-6 col-8">{!! $image->subtype_id ? $image->subtype->displayName : 'None' !!}</div>
                    </div>
                @endif
                @if ($image->transformation_id)
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-4">
                            <h5>{{ ucfirst(__('transformations.form')) }} {!! add_help('The main image is always the active image') !!}</h5>
                        </div>
                        <div class="col-lg-8 col-md-6 col-8">
                            <a href="{{ $image->transformation->url }}">
                                {!! $image->transformation->displayName !!}
                            </a>
                        </div>
                    </div>
                @endif
                @if($image->character->homeSetting)
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-4"><h5>Home</h5></div>
                        <div class="col-lg-8 col-md-6 col-8">{!! $image->character->location ? $image->character->location : 'None' !!}</div>
                    </div>
                @endif
                @if($image->character->factionSetting)
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-4"><h5>Faction</h5></div>
                        <div class="col-lg-8 col-md-6 col-8">{!! $image->character->faction ? $image->character->currentFaction : 'None' !!}{!! $character->factionRank ? ' ('.$character->factionRank->name.')' : null !!}</div>
                    </div>
                @endif
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-4">
                        <h6>RARITY</h6>
                    </div>
                    <div class="col-lg-8 col-md-6 col-8">{!! $image->rarity_id ? $image->rarity->displayName : 'None' !!}</div>
                </div>
                @php
                    // check if there is a type for this object if not passed
                    // for characters first check subtype (since it takes precedence)
                    $type = \App\Models\Element\Typing::where('typing_model', 'App\Models\Character\CharacterImage')
                        ->where('typing_id', $image->id)
                        ->first();
                    if (!isset($type) && $image->subtype_id) {
                        $type = \App\Models\Element\Typing::where('typing_model', 'App\Models\Species\Subtype')
                            ->where('typing_id', $image->subtype_id)
                            ->first();
                    }
                    if (!isset($type)) {
                        $type = \App\Models\Element\Typing::where('typing_model', 'App\Models\Species\Species')
                            ->where('typing_id', $image->species_id)
                            ->first();
                    }
                    $type = $type ?? null;
                @endphp
                @if ($type || (Auth::check() && Auth::user()->hasPower('manage_characters')))
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-4">
                            <h6>ELEMENT</h6>
                        </div>
                        <div class="col-lg-8 col-md-6 col-8 row">
                            <h5>{!! $type?->displayElements !!}</h5>
                            @if (Auth::check() && Auth::user()->hasPower('manage_characters'))
                                {!! add_help('Typing is assigned on an image basis') !!}
                                <div class="ml-auto">
                                    <a href="#" class="btn btn-outline-info btn-sm edit-typing" data-id="{{ $image->id }}">
                                        <i class="fas fa-cog"></i> {{ $type ? 'Edit' : 'Create' }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                

                

                @if ($image->character->pets)
                <div class="mb-1">
                    <div><h6>FAMILIARS</h6></div>
                        <div class="text-center row">
                        @foreach($image->character->pets as $pet)
                            <div class="ml-3 mr-3">
                                @if($pet->has_image)
                                <img src="{{ $pet->imageUrl }}" data-toggle="tooltip" title="{{ $pet->pet->name }}" style="max-width: 75px;"/>
                                @elseif($pet->pet->imageurl)
                                <img src="{{ $pet->pet->imageUrl }}" data-toggle="tooltip" title="{{ $pet->pet->name }}" style="max-width: 75px;"/>
                                @else {!!$pet->pet->displayName !!}
                                @endif
                                <br>
                                <span class="text-light badge badge-dark" style="font-size:95%;">{!! $pet->pet_name !!}</span>
                            </div>
                        @endforeach
                   </div>
                </div>
@endif
                <div class="mb-1">
                    <div><h6>EQUIPMENT</h6></div>
                        <div class="text-center row">
                        @foreach($image->character->gear as $gear)
                            <div class="ml-3 mr-3">
                                @if($gear->has_image)
                                <img src="{{ $gear->imageUrl }}" data-toggle="tooltip" title="{{ $gear->gear->name }}" style="max-width: 75px;"/>
                                @elseif($gear->gear->imageurl)
                                <img src="{{ $gear->gear->imageUrl }}" data-toggle="tooltip" title="{{ $gear->gear->name }}" style="max-width: 75px;"/>
                                @else {!!$gear->gear->displayName !!}
                                @endif
                            </div>
                        @endforeach
                        </div>
                </div>

                <div class="mb-1">
                    <div><h6>WEAPONS</h6></div>
                        <div class="text-center row">
                        @foreach($image->character->weapons as $weapon)
                            <div class="ml-3 mr-3">
                                @if($weapon->has_image)
                                <img src="{{ $weapon->imageUrl }}" data-toggle="tooltip" title="{{ $weapon->weapon->name }}" style="max-width: 75px;"/>
                                @elseif($weapon->weapon->imageurl)
                                <img src="{{ $weapon->weapon->imageUrl }}" data-toggle="tooltip" title="{{ $weapon->weapon->name }}" style="max-width: 75px;"/>
                                @else {!!$weapon->weapon->displayName !!}
                                @endif
                            </div>
                        @endforeach
                        </div>
                </div>
            </div>

            {{-- Image notes --}}
            <div class="tab-pane fade" id="notes-{{ $image->id }}">
                @if ($image->parsed_description)
                    <div class="parsed-text imagenoteseditingparse">{!! $image->parsed_description !!}</div>
                @else
                    <div class="imagenoteseditingparse">No additional notes given.</div>
                @endif
                @if (Auth::check() && Auth::user()->hasPower('manage_characters'))
                    <div class="mt-3">
                        <a href="#" class="btn btn-outline-info btn-sm edit-notes" data-id="{{ $image->id }}"><i class="fas fa-cog"></i> Edit</a>
                    </div>
                @endif
            </div>

            {{-- Image credits --}}
            <div class="tab-pane fade" id="credits-{{ $image->id }}">

                <div class="row mb-2">
                    <div class="col-lg-4 col-md-6 col-4">
                        <h6>DESIGN</h6>
                    </div>
                    <div class="col-lg-8 col-md-6 col-8">
                        @foreach ($image->designers as $designer)
                            <div>{!! $designer->displayLink() !!}</div>
                        @endforeach
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-4">
                        <h6>ART</h6>
                    </div>
                    <div class="col-lg-8 col-md-6 col-8">
                        @foreach ($image->artists as $artist)
                            <div>{!! $artist->displayLink() !!}</div>
                        @endforeach
                    </div>
                </div>

                @if (Auth::check() && Auth::user()->hasPower('manage_characters'))
                    <div class="mt-3">
                        <a href="#" class="btn btn-outline-info btn-sm edit-credits" data-id="{{ $image->id }}"><i class="fas fa-cog"></i> Edit</a>
                    </div>
                @endif
            </div>

            @if (isset($showMention) && $showMention)
                {{-- Mention This tab --}}
                <div class="tab-pane fade" id="mention-{{ $image->id }}">
                    <div class="imagenoteseditingparse">
                        In the rich text editor:
                        <div class="alert alert-secondary">
                            [character={{ $character->slug }}]
                        </div>
                        In a comment:
                        <div class="alert alert-secondary">
                            [{{ $character->fullName }}]({{ $character->url }})
                        </div>
                    </div>
                    <hr>
                    <div class="my-2"><strong>For Thumbnails:</strong></div>
                    <div class="imagenoteseditingparse">
                        In the rich text editor:
                        <div class="alert alert-secondary">
                            [charthumb={{ $character->slug }}]
                        </div>
                        In a comment:
                        <div class="alert alert-secondary">
                            [![Thumbnail of {{ $character->fullName }}]({{ $character->image->thumbnailUrl }})]({{ $character->url }})
                        </div>
                    </div>
                </div>
            @endif

            @if (Auth::check() && Auth::user()->hasPower('manage_characters'))
                <div class="tab-pane fade" id="settings-{{ $image->id }}">
                    {!! Form::open(['url' => 'admin/character/image/' . $image->id . '/settings']) !!}
                    <div class="form-group">
                        {!! Form::checkbox('is_visible', 1, $image->is_visible, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                        {!! Form::label('is_visible', 'Is Viewable', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, the image will not be visible by anyone without the Manage Masterlist power.') !!}
                    </div>
                    <div class="form-group">
                        {!! Form::checkbox('is_valid', 1, $image->is_valid, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
                        {!! Form::label('is_valid', 'Is Valid', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If this is turned off, the image will still be visible, but displayed with a note that the image is not a valid reference.') !!}
                    </div>
                    <div class="text-right">
                        {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
                    </div>
                    {!! Form::close() !!}
                    <hr />
                    <div class="text-right">
                        @if ($character->character_image_id != $image->id)
                            <a href="#" class="btn btn-outline-info btn-sm active-image" data-id="{{ $image->id }}">Set Active</a>
                        @endif <a href="#" class="btn btn-outline-info btn-sm reupload-image" data-id="{{ $image->id }}">Reupload Image</a> <a href="#" class="btn btn-outline-danger btn-sm delete-image"
                            data-id="{{ $image->id }}">Delete</a>
                    </div>
                </div>
            @endif
        </div>
    </div>

</div>


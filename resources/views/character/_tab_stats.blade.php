<div class="row">
    <div class="col-lg-3 col-4">
        <h6>Owner</h6>
    </div>
    <div class="col-lg-9 col-8">{!! $character->displayOwner !!}</div>
</div>
@if (!$character->is_myo_slot)
    <div class="row">
        <div class="col-lg-3 col-4">
            <h6>Type</h6>
        </div>
        <div class="col-lg-9 col-8">{!! $character->category->displayName !!}</div>
    </div>
@endif
<div class="row">
    <div class="col-lg-3 col-4">
        <h6 class="mb-0">Created</h6>
    </div>
    <div class="col-lg-9 col-8">{!! format_date($character->created_at) !!}</div>
</div>

<hr />


<h6>
    <i class="text-{{ $character->is_giftable ? 'success far fa-circle' : 'danger fas fa-times' }} fa-fw mr-2"></i> {{ $character->is_giftable ? 'Can' : 'Cannot' }} be gifted
</h6>
<h6>
    <i class="text-{{ $character->is_tradeable ? 'success far fa-circle' : 'danger fas fa-times' }} fa-fw mr-2"></i> {{ $character->is_tradeable ? 'Can' : 'Cannot' }} be traded
</h6>
<h6>
    <i class="text-{{ $character->is_sellable ? 'success far fa-circle' : 'danger fas fa-times' }} fa-fw mr-2"></i> {{ $character->is_sellable ? 'Can' : 'Cannot' }} be sold
</h6>
@if ($character->sale_value > 0)
    <div class="row">
        <div class="col-lg-3 col-4">
            <h6>Sale Value</h6>
        </div>
        <div class="col-lg-9 col-8">
            {{ Config::get('lorekeeper.settings.currency_symbol') }}{{ $character->sale_value }}
        </div>
    </div>
@endif
@if ($character->transferrable_at && $character->transferrable_at->isFuture())
    <div class="row">
        <div class="col-lg-3 col-4">
            <h5>Cooldown</h5>
        </div>
        <div class="col-lg-9 col-8">Cannot be transferred until {!! format_date($character->transferrable_at) !!}</div>
    </div>
@endif
@if (Auth::check() && Auth::user()->hasPower('manage_characters'))
    <div class="mt-3">
        <a href="#" class="btn btn-outline-info btn-sm edit-stats" data-{{ $character->is_myo_slot ? 'id' : 'slug' }}="{{ $character->is_myo_slot ? $character->id : $character->slug }}"><i class="fas fa-cog"></i> Edit</a>
    </div>
@endif

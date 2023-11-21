@extends('admin.layout')

@section('admin-title') {{ $drop->id ? 'Edit' : 'Create' }} Pet Drop @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Pet Drops' => 'admin/data/pet-drops', ($drop->id ? 'Edit' : 'Create').' Drop Data' => $drop->id ? 'admin/data/pet-drops/edit/'.$drop->id : 'admin/data/pet-drops/create']) !!}

<h1>{{ $drop->id ? 'Edit' : 'Create' }} Pet Drop Data
    @if($drop->id)
        <a href="#" class="btn btn-danger float-right delete-drop-button">Delete Drop</a>
    @endif
</h1>

{!! Form::open(['url' => $drop->id ? 'admin/data/pet-drops/edit/'.$drop->id : 'admin/data/pet-drops/create']) !!}

<h2>Basic Information</h2>

<div class="form-group">
    {!! Form::label('Pet') !!}
    {!! Form::select('pet_id', $pets, $drop->pet_id, ['class' => 'form-control']) !!}
</div>

<h4>Groups</h4>
<p>
    Every pet of the above selected pet is sorted into a "group" - these groups are used for different item drops, which can be set in this form after the pet drop is initially created.
    These groups can be either assigned at pet creation (either at random or manually after selecting an applicable pet) or may be assigned after pet creation in the pet's "Collect" page, accessed via the pet sidebar on applicable pets.
</p>
<div class="float-right mb-3">
    <a href="#" class="btn btn-info" id="addLoot">Add Group</a>
</div>
<table class="table table-sm" id="lootTable">
    <thead>
        <tr>
            <th width="25%">Group Label {!! add_help('This label will be shown to users.') !!}</th>
            <th width="10%">Weight {!! add_help('A higher weight means a pet is more likely to be randomly assigned to this group upon creation. Weights have to be integers above 0 (round positive number, no decimals) and do not have to add up to be a particular number.') !!}</th>
            <th width="20%">Chance {!! add_help('Calculated automatically based on the weights. A pet has this percentage of chance of being automatically sorted into this group.') !!}</th>
            <th width="10%"></th>
        </tr>
    </thead>
    <tbody id="lootTableBody">
        @if($drop->id)
            @foreach($drop->parameters as $label=>$weight)
                <tr class="drop-row">
                    <td class="drop-row-select">{!! Form::text('label[]', $label, ['class' => 'form-control']) !!}</td>
                    <td class="drop-row-weight">{!! Form::number('weight[]', $weight, ['class' => 'form-control drop-weight']) !!}</td>
                    <td class="drop-row-chance"></td>
                    <td class="text-right"><a href="#" class="btn btn-danger remove-drop-button">Remove</a></td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>

<h4>Drop Frequency</h4>
Select how often drops should occur.
<div class="d-flex my-2">
    {!! Form::number('drop_frequency', $drop->id ? $drop->data['frequency']['frequency'] : null, ['class' => 'form-control mr-2', 'placeholder' => 'Drop Frequency']) !!}
    {!! Form::select('drop_interval', ['hour' => 'Hour', 'day' => 'Day', 'month' => 'Month', 'year' => 'Year'], $drop->id ? $drop->data['frequency']['interval'] : null, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Drop Interval']) !!}
</div>
<div class="form-group">
    {!! Form::label('cap', 'Drop Cap (Optional)', ['class' => 'form-label ml-3']) !!} {!! add_help('How many batches of drops are allowed to accumulate. Either set to 0 or unset to allow unlimited accumulation.') !!}
    {!! Form::number('cap', $drop->id ? $drop->cap : null, ['class' => 'form-control mr-2', 'placeholder' => 'Drop Cap']) !!}
</div>

<div class="form-group">
    {!! Form::checkbox('is_active', 1, $drop->id ? $drop->isActive : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
    {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Whether or not drops for this pet are active. Impacts variants as well.') !!}
</div>

<div class="form-group">
    {!! Form::label('drop_name', 'Drop Name', ['class' => 'form-label']) !!} {!! add_help('What drops are referred to on pet pages. Impacts variants as well. Should be singular.') !!}
    {!! Form::text('drop_name', isset($drop->data['drop_name']) ? $drop->data['drop_name'] : null, ['class' => 'form-control']) !!}
</div>

@if($drop->id)
    <h2>Dropped Items</h2>
    Select an item for each group of this pet to drop, and/or for each group to drop per variant of this pet. Leave the item field blank to disable drops for the group. To clear a previously set input, simply backspace when it's selected.
    <div class="card card-body my-2">
        @foreach($drop->parameters as $label=>$weight)
            <div class="mb-2">
                <h5>{{ $label }}</h5>
                <div class="form-group">
                    {!! Form::label('Item and Min/Max Quantity Dropped') !!} {!! add_help('Select an item for this group to drop, and the minimum and maximum quantity that should be dropped. If only one quantity is set, or they are both the same number, the same quantity will be dropped each time.') !!}
                    <div id="itemList">
                        <div class="d-flex mb-2">
                            {!! Form::select('item_id[pet]['.$label.']', $items, isset($drop->data['items']['pet'][$label]) ? $drop->data['items']['pet'][$label]['item_id'] : null, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Select Item']) !!}
                            {!! Form::number('min_quantity[pet]['.$label.']', isset($drop->data['items']['pet'][$label]) ? $drop->data['items']['pet'][$label]['min'] : null, ['class' => 'form-control mr-2', 'placeholder' => 'Minimum Quantity']) !!}
                            {!! Form::number('max_quantity[pet]['.$label.']', isset($drop->data['items']['pet'][$label]) ?  $drop->data['items']['pet'][$label]['max'] : null, ['class' => 'form-control mr-2', 'placeholder' => 'Maximum Quantity']) !!}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <h3>Variants</h3>
    @if($drop->pet->variants->count())
        @foreach($drop->pet->variants as $variant)
            <div class="card card-body mb-2">
                <div class="card-title">
                    <h4>{{ $variant->variant_name.' '.$variant->pet->name }}</h4>
                </div>
                @foreach($drop->parameters as $label=>$weight)
                    <div class="mb-2">
                        <h5>{{ $label }}</h5>
                        <div class="form-group">
                            {!! Form::label('Item and Min/Max Quantity Dropped') !!} {!! add_help('Select an item for this group to drop, and the minimum and maximum quantity that should be dropped. If only one quantity is set, or they are both the same number, the same quantity will be dropped each time.') !!}
                            <div id="itemList">
                                <div class="d-flex mb-2">
                                    {!! Form::select('item_id['.$variant->id.']['.$label.']', $items, isset($drop->data['items'][$variant->id][$label]) ? $drop->data['items'][$variant->id][$label]['item_id'] : null, ['class' => 'form-control mr-2 default item-select', 'placeholder' => 'Select Item']) !!}
                                    {!! Form::number('min_quantity['.$variant->id.']['.$label.']', isset($drop->data['items'][$variant->id][$label]) ? $drop->data['items'][$variant->id][$label]['min'] : null, ['class' => 'form-control mr-2', 'placeholder' => 'Minimum Quantity']) !!}
                                    {!! Form::number('max_quantity['.$variant->id.']['.$label.']', isset($drop->data['items'][$variant->id][$label]) ? $drop->data['items'][$variant->id][$label]['max'] : null, ['class' => 'form-control mr-2', 'placeholder' => 'Maximum Quantity']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @else
        <p>No variants found for this pet.</p>
    @endif
@endif

<div class="text-right">
    {!! Form::submit($drop->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

<div id="dropRowData" class="hide">
    <table class="table table-sm">
        <tbody id="dropRow">
            <tr class="drop-row">
                <td class="drop-row-select">{!! Form::text('label[]', null, ['class' => 'form-control']) !!}</td>
                <td class="drop-row-weight">{!! Form::text('weight[]', 1, ['class' => 'form-control drop-weight']) !!}</td>
                <td class="drop-row-chance"></td>
                <td class="text-right"><a href="#" class="btn btn-danger remove-drop-button">Remove</a></td>
            </tr>
        </tbody>
    </table>
</div>

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.delete-drop-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/data/pet-drops/delete') }}/{{ $drop->id }}", 'Delete Drop');
    });

    var $lootTable  = $('#lootTableBody');
    var $dropRow = $('#dropRow').find('.drop-row');
    var $itemSelect = $('#dropRowData').find('.item-select');
    var $currencySelect = $('#dropRowData').find('.currency-select');
    var $tableSelect = $('#dropRowData').find('.table-select');
    var $noneSelect = $('#dropRowData').find('.none-select');

    refreshChances();
    $('#lootTableBody .selectize').selectize();
    attachRemoveListener($('#lootTableBody .remove-drop-button'));

    $('#addLoot').on('click', function(e) {
        e.preventDefault();
        var $clone = $dropRow.clone();
        $lootTable.append($clone);
        attachRewardTypeListener($clone.find('.reward-type'));
        attachRemoveListener($clone.find('.remove-drop-button'));
        attachWeightListener($clone.find('.drop-weight'));
        refreshChances();
    });

    $('.reward-type').on('change', function(e) {
        var val = $(this).val();
        var $cell = $(this).parent().find('.drop-row-select');

        var $clone = null;
        if(val == 'Item') $clone = $itemSelect.clone();
        else if (val == 'Currency') $clone = $currencySelect.clone();
        else if (val == 'LootTable') $clone = $tableSelect.clone();
        else if (val == 'None') $clone = $noneSelect.clone();

        $cell.html('');
        $cell.append($clone);
    });

    function attachRewardTypeListener(node) {
        node.on('change', function(e) {
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.drop-row-select');

            var $clone = null;
            if(val == 'Item') $clone = $itemSelect.clone();
            else if (val == 'Currency') $clone = $currencySelect.clone();
            else if (val == 'LootTable') $clone = $tableSelect.clone();
            else if (val == 'None') $clone = $noneSelect.clone();

            $cell.html('');
            $cell.append($clone);
            $clone.selectize();
        });
    }

    function attachRemoveListener(node) {
        node.on('click', function(e) {
            e.preventDefault();
            $(this).parent().parent().remove();
            refreshChances();
        });
    }

    function attachWeightListener(node) {
        node.on('change', function(e) {
            refreshChances();
        });
    }

    function refreshChances() {
        var total = 0;
        var weights = [];
        $('#lootTableBody .drop-weight').each(function( index ) {
            var current = parseInt($(this).val());
            total += current;
            weights.push(current);
        });


        $('#lootTableBody .drop-row-chance').each(function( index ) {
            var current = (weights[index] / total) * 100;
            $(this).html(current.toString() + '%');
        });
    }

    $('.default.item-select').selectize();
});

</script>
@endsection

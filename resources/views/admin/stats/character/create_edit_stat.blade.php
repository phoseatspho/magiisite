@extends('admin.layout')

@section('admin-title') Stats @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Stats' => 'admin/stats', ($stat->id ? 'Edit' : 'Create').' Stat' => $stat->id ? 'admin/stats/edit/'.$stat->id : 'admin/stats/create']) !!}

<h1>{{ $stat->id ? 'Edit' : 'Create' }} Stat
    @if($stat->id)
        <a href="#" class="btn btn-outline-danger float-right delete-stat-button">Delete Stat</a>
    @endif
</h1>

{!! Form::open(['url' => $stat->id ? 'admin/stats/edit/'.$stat->id : 'admin/stats/create']) !!}

<h3>Basic Information</h3>

<div class="row">
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Name') !!}
            {!! Form::text('name', $stat->name, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Abbreviation') !!}
            {!! Form::text('abbreviation', $stat->abbreviation, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<div class="form-group">
    {!! Form::label('default') !!} {!! add_help('This is the \'default\' or \'starter\' amount of stat. Can be negative. If negative, all level ups will apply as if the base was 1.') !!}
    {!! Form::number('base', $stat->base, ['class' => 'form-control',]) !!}
</div>

<p>Multiplier can apply to step (e.g (current + step) X Multiplier) or just to current. Leave step blank if you want it to apply just to current</p>
<p>If a stat calculation is a decimal it will round to the nearest whole number.</p>
<div class="row">
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Step (optional)') !!} {!! add_help('If you want a stat to increase more than by 1 per level up, enter a unique step here.') !!}
            {!! Form::text('step', $stat->step, ['class' => 'form-control']) !!}
        </div>
    </div>
    <div class="col-md">
        <div class="form-group">
            {!! Form::label('Multiplier (optional)') !!} {!! add_help('If you want the stat to increase based on a multiplication set it here.') !!}
            {!! Form::text('multiplier', $stat->multiplier, ['class' => 'form-control']) !!}
        </div>
    </div>
</div>

<p>A max level can be applied if you want to cap the levels a character can gain</p>
<div class="form-group">
    {!! Form::label('Max level (optional)') !!}
    {!! Form::text('max_level', $stat->max_level, ['class' => 'form-control']) !!}
</div>

@if($stat->id)
    <div class="form-group">
        {!! Form::label('Species / Subtypes') !!} {!! add_help('Allow only the selected species / subtypes to have this skill.') !!}
        <div id="featureList">
            @foreach($stat->species as $species)
                <div class="d-flex mb-2">
                    <div class="col-md-5">
                        {!! Form::select('types[]', ['species' => 'Species', 'subtype' => 'Subtype'], !$species->is_subtype ? 'species' : 'subtype', ['class' => 'form-control mr-2 type', 'placeholder' => 'Select Type']) !!}
                    </div>
                    <div class="col-md-6 typeid">
                        @if($species->type == 'species')
                            {!! Form::select('type_ids[]', !$species->is_subtype ? $specieses : $subtypes, $species->species_id, ['class' => 'form-control mr-2 feature-select species', 'placeholder' => 'Select Species']) !!}
                        @else
                            {!! Form::select('type_ids[]', !$species->is_subtype ? $specieses : $subtypes, $species->species_id, ['class' => 'form-control mr-2 feature-select subtype', 'placeholder' => 'Select Subtype']) !!}
                        @endif
                    </div>
                    <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
                </div>
            @endforeach
        </div>
        <div><a href="#" class="btn btn-primary" id="add-feature">Add Species</a></div>
    </div>
@endif

<div class="text-right">
    {!! Form::submit($stat->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}

@if ($stat->id)
    <div class="feature-row hide mb-2">
        <div class="col-md-5">
            {!! Form::select('types[]', ['species' => 'Species', 'subtype' => 'Subtype'], null, ['class' => 'form-control mr-2 type', 'placeholder' => 'Select Type']) !!}
        </div>
        <div class="col-md-6 typeid">
        </div>
        <a href="#" class="remove-feature btn btn-danger mb-2">×</a>
    </div>

    <div class="hide">
        <div class="original species">
            {!! Form::select('type_ids[]', $specieses, null, ['class' => 'form-control mr-2 feature-select species', 'placeholder' => 'Select Species']) !!}
        </div>
        <div class="original subtype">
            {!! Form::select('type_ids[]', $subtypes, null, ['class' => 'form-control mr-2 feature-select subtype', 'placeholder' => 'Select Subtype']) !!}
        </div>
    </div>
@endif

@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {

    $('.original.feature-select').selectize();
    $('#add-feature').on('click', function(e) {
        e.preventDefault();
        addFeatureRow();
    });
    $('.remove-feature').on('click', function(e) {
        e.preventDefault();
        removeFeatureRow($(this));
    })
    function addFeatureRow() {
        var $clone = $('.feature-row').clone();
        $('#featureList').append($clone);
        $clone.removeClass('hide feature-row');
        $clone.addClass('d-flex');
        $clone.find('.remove-feature').on('click', function(e) {
            e.preventDefault();
            removeFeatureRow($(this));
        })
        $clone.find('.feature-select').selectize();
        attachTypeChangeListener($clone.find('.type'));
    }
    function removeFeatureRow($trigger) {
        $trigger.parent().remove();
    }
    function attachTypeChangeListener(node) {
        node.on('change', function(e) {
            e.preventDefault();
            var val = $(this).val();
            var $cell = $(this).parent().parent().find('.typeid');
            var $clone = null;
            if(val == 'species') {
                $clone = $('.original.species').clone();
            } else if(val == 'subtype') {
                $clone = $('.original.subtype').clone();
            }
            $cell.html($clone);
            $clone.removeClass('hide original');
        });
    }

    $('.delete-stat-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/stats/delete') }}/{{ $stat->id }}", 'Delete Stat');
    });
});

</script>
@endsection

@extends('admin.layout')

@section('admin-title') Prizes @endsection

@section('admin-content')
{!! breadcrumbs(['Admin Panel' => 'admin', 'Prizes' => 'admin/prizecodes/', ($prize->id ? 'Edit' : 'Create').' Prize' => $prize->id ? 'admin/prizecodes/edit/'.$prize->id : 'admin/prizecodes/create']) !!}

<h1>{{ $prize->id ? 'Edit' : 'Create' }} Prize
    @if($prize->id)
        <a href="#" class="btn btn-outline-danger float-right delete-prize-button">Delete Prize</a>
    @endif
</h1>

{!! Form::open(['url' => $prize->id ? 'admin/prizecodes/edit/'.$prize->id : 'admin/prizecodes/create', 'files' => true]) !!}

<h3>Basic Information</h3>

<div class="form-group">
    {!! Form::label('Name') !!}
    {!! Form::text('name', $prize->name, ['class' => 'form-control']) !!}
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('start_at', 'Start Time (Optional)') !!} {!! add_help('Codes cannot be redeemed before the starting time.') !!}
            {!! Form::text('start_at', $prize->start_at, ['class' => 'form-control datepicker']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('end_at', 'End Time (Optional)') !!} {!! add_help('Codes cannot be redeemed before the ending time.') !!}
            {!! Form::text('end_at', $prize->end_at, ['class' => 'form-control datepicker']) !!}
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::label('use_limit', 'Use Limit (Optional)') !!} {!! add_help('How many times a code can be used before it becomes unusable. Leave set to 0 for infinite uses.') !!}
            {!! Form::number('use_limit', $prize->use_limit ? $prize->use_limit : 0, ['class' => 'form-control mb-1']) !!}
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            {!! Form::checkbox('is_active', 1, $prize->id ? $prize->is_active : 1, ['class' => 'form-check-input', 'data-toggle' => 'toggle']) !!}
            {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Codes that are not active will not be redeemable. The start/end time hide settings override this setting, i.e. if this is set to active, it will still be unredeemable outside of the start/end times.') !!}
        </div>
    </div>
</div>


<h3>Prize Rewards</h3>
@include('widgets._prize_reward_select', ['rewards' => $prize->rewards])

<div class="text-right">
    {!! Form::submit($prize->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}


@include('widgets._prize_reward_select_row', ['items' => $items, 'currencies' => $currencies, 'tables' => $tables, 'raffles' => $raffles])



@endsection

@section('scripts')
@parent

@include('js._prize_reward_js')

<script>
$( document ).ready(function() {    
    $('.delete-prize-button').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('admin/prizecodes/delete') }}/{{ $prize->id }}", 'Delete Code');
    });

    
    $( ".datepicker" ).datetimepicker({
        dateFormat: "yy-mm-dd",
        timeFormat: 'HH:mm:ss',
    });

    $('.is-limit-class').change(function(e){
        console.log(this.checked)
        $('.limit-form-group').css('display',this.checked ? 'block' : 'none')
    })
    $('.limit-form-group').css('display',$('.is-limit-class').prop('checked') ? 'block' : 'none')
});
    
</script>
@endsection
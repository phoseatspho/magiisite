@extends('admin.layout')

@section('admin-title')
    Rank Rewards
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Rank Rewards' => 'admin/rank-rewards',
        ($rankreward->id ? 'Edit' : 'Create') . ' Rank Reward' => $rankreward->id
            ? 'admin/rank-rewards/edit/' . $rankreward->id
            : 'admin/rank-rewards/create',
    ]) !!}

    <h1>{{ $rankreward->id ? 'Edit' : 'Create' }} Rank Reward
        @if ($rankreward->id)
            <a href="#" class="btn btn-danger float-right delete-rankreward-button">Delete Rank Reward</a>
        @endif
    </h1>

    {!! Form::open([
        'url' => $rankreward->id ? 'admin/rank-rewards/edit/' . $rankreward->id : 'admin/rank-rewards/create',
    ]) !!}

    <h3>Basic Information</h3>

     <div class="form-group">
        {!! Form::label('Name') !!} {!! add_help('Purely an internal name for admin purposes') !!}
        {!! Form::text('name', $rankreward->name, ['class' => 'form-control']) !!}
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('reward_time', 'Reward Time') !!}
                {!! Form::select(
                    'reward_time',
                    [1 => 'Every Day', 2 => 'Every Week', 3 => 'Every Month'],
                    $rankreward->reward_time ?? 2,
                    ['class' => 'form-control'],
                ) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::checkbox('is_active', 1, $rankreward->id ? $rankreward->is_active : 1, [
                    'class' => 'form-check-input',
                    'data-toggle' => 'toggle',
                ]) !!}
                {!! Form::label('is_active', 'Is Active', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If inactive, this reward will not be given out.') !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Rank Effected') !!}
        {!! Form::select('rank_id', $ranks, $rankreward->rank_id, ['class' => 'form-control']) !!}
    </div>

    <h3>Rewards</h3>
    <p>Rewards are credited to all users who are in this rank when the interval comes up.</p>
    @include('widgets._loot_select', [
        'loots' => $rankreward->data,
        'showLootTables' => true,
        'showRaffles' => true,
    ])

    <div class="text-right">
        {!! Form::submit($rankreward->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @include('widgets._loot_select_row', [
        'items' => $items,
        'currencies' => $currencies,
        'tables' => $tables,
        'raffles' => $raffles,
        'showLootTables' => true,
        'showRaffles' => true,
    ])
@endsection

@section('scripts')
    @parent
    @include('js._loot_js', ['showLootTables' => true, 'showRaffles' => true])
    <script>
        $(document).ready(function() {
            $('.delete-rankreward-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/rank-rewards/delete') }}/{{ $rankreward->id }}",
                    'Delete Rank Reward');
            });
        });
    </script>
@endsection

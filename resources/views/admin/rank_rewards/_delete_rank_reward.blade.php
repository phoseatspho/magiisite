@if($rankreward)
    {!! Form::open(['url' => 'admin/rank-rewards/delete/'.$rankreward->id]) !!}

    <p>You are about to delete the rank reward <strong>{{ $rankreward->name }}</strong>. This is not reversible.</p>
    <p>Are you sure you want to delete <strong>{{ $rankreward->name }}</strong>?</p>

    <div class="text-right">
        {!! Form::submit('Delete Rank Reward', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else 
    Invalid rankreward selected.
@endif
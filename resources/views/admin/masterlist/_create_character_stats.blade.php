@foreach($stats as $stat)
    {!! Form::label($stat->name) !!}
    {!! Form::number('stats['.$stat->id.']', $stat->base, ['class' => 'form-control m-1',]) !!}
@endforeach

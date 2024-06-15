@if ($carousel)
    {!! Form::open(['url' => 'admin/data/carousel/delete/' . $carousel->id]) !!}

    <div class="text-right">
        {!! Form::submit('Delete Carousel', ['class' => 'btn btn-danger']) !!}
    </div>

    {!! Form::close() !!}
@else
    Invalid carousel selected.
@endif

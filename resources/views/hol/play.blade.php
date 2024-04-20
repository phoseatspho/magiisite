{!! Form::open(['url' => 'higher-or-lower/play/guess']) !!}
<div class="text-center">
    <p>The number to guess with is: <strong>{{ $number }}</strong></p>
    <p>What is your guess: do you think the random number is <strong>higher</strong> or
        <strong>lower</strong>?
    </p>
    <div class="row col-12 mb-2">
        <div class="col-md-10">
            {!! Form::hidden('number', $number) !!}
            {!! Form::select('guess', ['higher' => 'Higher', 'lower' => 'Lower'], null, [
                'class' => 'form-control',
                'placeholder' => 'Select Guess',
            ]) !!}
        </div>
        <div class="col-md-2">
            {!! Form::submit('Guess!', ['class' => 'btn btn-primary']) !!}
        </div>
    </div>
</div>
{!! Form::close() !!}

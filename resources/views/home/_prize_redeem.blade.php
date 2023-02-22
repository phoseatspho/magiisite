@extends('home.layout')

@section('home-title') Redeem Code @endsection

@section('home-content')
{!! breadcrumbs(['Code Redemption' => 'coderedeem']) !!}

<h1>
Code Redemption
</h1>
<p> Here you can redeem a code for prizes. Check in with the site's social media and updates to see if any codes have been posted.</p>

<hr>
{!! Form::open(['url' => 'redeem-code/redeem']) !!}
    <div>
        <div class="form-group mr-3 mb-3 mx-5">
            {!! Form::text('query', Request::get('query'), ['class' => 'form-control', 'id' => 'query']) !!}
        </div>
        <div class="form-group mb-3">
            {!! Form::submit('Redeem', ['class' => 'btn btn-primary', 'id' => 'search']) !!}
        </div>
    </div>
{!! Form::close() !!}




@endsection


@section('scripts')
@endsection
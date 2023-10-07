@extends('home.layout')

@section('home-title') Redeem Code @endsection

@section('home-content')
{!! breadcrumbs(['Code Redemption' => 'redeem-code']) !!}

<h1>
Code Redemption
</h1>
<p> Here you can redeem a code for prizes. Check in with the site's social media and updates to see if any codes have been posted.</p>

<hr> 
<div class="text-center">
{!! Form::open(['url' => 'redeem-code/redeem']) !!}
{!! Form::text('code') !!} 
<br>
<br>
<div class="text-center">
    {!! Form::submit( 'Redeem', ['class' => 'btn btn-primary']) !!}
</div>

{!! Form::close() !!}
</div>

<div class="text-right mb-4">
    <a href="{{ url(Auth::user()->url.'/redeem-logs') }}">View logs...</a>
</div>


@endsection


@section('scripts')
@endsection
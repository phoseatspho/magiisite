@extends('home.layout')

@section('home-title') Redeem Code @endsection

@section('home-content')
{!! breadcrumbs(['Code Redemption' => 'coderedeem']) !!}

<h1>
Code Redemption
</h1>
<p> Here you can redeem a code for prizes. Check in with the site's social media and updates to see if any codes have been posted.</p>

<hr>
<form method="POST" action="{{ 'redeem-code/redeem' }}">
    @csrf
            <div class="form-group row">
                <label for="name" class="col-md-4 col-form-label text-md-right">Enter Key </label>

                <div class="col-md-6">
                    <input id="code" type="text" class="form-control{{ $errors->has('code') ? ' is-invalid' : '' }}" name="code" value="{{ old('code') }}" required autofocus>

                    @if ($errors->has('code'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('code') }}</strong>
                        </span>
                    @endif
                </div>
            </div>
</form>




@endsection


@section('scripts')
@endsection
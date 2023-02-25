@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Code Logs @endsection

@section('profile-content')
{!! breadcrumbs(['Code Redemption' => 'redeem-code', 'Logs' => 'redeem-logs']) !!}

<div class="text-right mb-3">
<a class="btn btn-primary" href="{{ url('redeem-code') }}"> Back to Redemption</a>
</div>

<h1>
    {!! $user->displayName !!}'s Code Logs
</h1>

{!! $logs->render() !!}
<div class="row ml-md-2 mb-4">
  <div class="d-flex row flex-wrap col-12 mt-1 pt-1 px-0 ubt-bottom">
    <div class="col-6 col-md-2 font-weight-bold">Code Name</div>
    <div class="col-6 col-md-2 font-weight-bold">Date</div>
  </div>
    @foreach($logs as $log)
        @include('user._redeem_log_row', ['log' => $log, 'owner' => $user])
    @endforeach
</div>
{!! $logs->render() !!}

@endsection
@extends('dailies.layout')

@section('dailies-title') {{ $daily->name }} @endsection

@section('dailies-content')
{!! breadcrumbs([ucfirst(__('dailies.dailies')) => __('dailies.dailies'), $daily->name => $daily->url]) !!}

<h1>
    {{ $daily->name }}
</h1>


<div class="text-center">
    @if($daily->has_image)<img src="{{ $daily->dailyImageUrl }}" style="max-width:100%" alt="{{ $daily->name }}" />@endif
    <p>{!! $daily->parsed_description !!}</p>
</div>
<div class="text-center">
    <hr>
    <small>
    @if($daily->daily_timeframe == 'lifetime')
    You will be able to collect rewards once.
    @else
    You will be able to collect rewards {!! $daily->daily_timeframe !!}.
    @endif
    @if(Auth::check() && isset($cooldown))
    You can collect rewards {!! pretty_date($cooldown) !!}!
    @endif
    </small>
</div>

@if(Auth::User())
    @if($daily->has_button_image)
    <div class="row justify-content-center mt-2">
        <form action="" method="post">
            @csrf
            <button class="btn" style="background-color:transparent;" name="daily_id" value="{{ $daily->id }}">
            <img src="{{ $daily->buttonImageUrl }}" class="w-100" style="max-width:200px;"/>
            </button>
        </form>
    </div>
    @else
    <div class="row justify-content-center mt-2">
        <form action="" method="post">
            @csrf
            <button class="btn btn-primary" name="daily_id" value="{{ $daily->id }}">Collect Reward!</button>
        </form>
    </div>
    @endif

    @if($daily->progress_display != 'none')
    <div class="card mt-5">
        <div class="card-header">
            <h4 class="m-0 align-items-center">Progress {!! add_help(($daily->is_streak) ? 'Progress will reset if you miss collecting your reward in the given timeframe.' : 'Progress is safe even if you miss collecting your reward in the given timeframe.') !!}</h4> 
        </div>

        <div class="card-body row p-0 m-auto w-100">
            @foreach($daily->rewards()->get()->groupBy('step') as $step => $rewards)
                @if($step > 0)
                <div class="col-lg-2 col-6 w-100 {{ ($step > ($timer->step ?? 0)) ? 'bg-dark text-light' : '' }} text-center justify-content-center border p-0">
                    <div class="row w-100 p-1 m-auto {{ ($step <= ($timer->step ?? 0)) ? 'btn-primary' : 'bg-dark text-light border' }}">
                        <div class="col-lg col-6 h-100">
                            <h5 class="p-1 m-0">{{ $step }}</h5>
                        </div>
                        <div class="col p-0">
                        <h5 class="p-1 m-0">@if($step > ($timer->step ?? 0))<i class="fa fa-lock"></i> Locked @else <i class="fa fa-unlock"></i> Unlocked  @endif</h5>
                        </div>
                    </div>
                    <div class="row w-100 p-0 m-auto">
                            @if($daily->progress_display =='all' || ($step <= ($timer->step ?? 0)))
                                @foreach($rewards as $reward)
                                    <div class="col-6">
                                            @if($reward->rewardImage)<div class="row justify-content-center"><img src="{{ $reward->rewardImage }}" alt="{{ $reward->reward()->first()->name }}" style="max-width:75px;width:100%;"/></div>@endif
                                            <div class="row justify-content-center">{{$reward->quantity}} {{$reward->reward()->first()->name}}</div>
                                    
                                    </div>
                                @endforeach
                            @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif


@else
<div class="row mt-2 mb-2 justify-content-center">
    <div class="alert alert-danger" role="alert">
    You must be logged in to collect {{ __('dailies.dailies') }}!
    </div>
</div>
@endif


@endsection

@section('scripts')
<script>

</script>
@endsection

@extends('home.layout')

@section('home-title') Awardcase @endsection

@section('home-content')
{!! breadcrumbs(['Awards' => 'Awards']) !!}

<h1>
    Awards
</h1>

<p>These are the awards you've earned for participating on this site.</p>
@foreach($awards as $categoryId=>$categoryAwards)
    <div class="card mb-3 awardcase-category">
        <h5 class="card-header awardcase-header">
            {!! isset($categories[$categoryId]) ? '<a href="'.$categories[$categoryId]->searchUrl.'">'.$categories[$categoryId]->name.'</a>' : 'Miscellaneous' !!}
        </h5>
        <div class="card-body awardcase-body">
            @foreach($categoryAwards->chunk(4) as $chunk)
                <div class="row mb-3">
                    @foreach($chunk as $awardId=>$stack)
                        <div class="col-sm-3 col-6 text-center awardcase-award" data-id="{{ $stack->first()->pivot->id }}" data-name="{{ $user->name }}'s {{ $stack->first()->name }}">
                            <div class="mb-1">
                                <a href="#" class="awardcase-stack {{ $stack->first()->is_featured ? 'alert alert-success' : '' }}"><img src="{{ $stack->first()->imageUrl }}" /></a>
                            </div>
                            <div>
                                <a href="#" class="awardcase-stack awardcase-stack-name">{{ $stack->first()->name }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endforeach
<div class="text-right mb-4">
    <a href="{{ url(Auth::user()->url.'/award-logs') }}">View logs...</a>
</div>

<h3>
    In Progress Awards
</h3>
<div class="card mb-3 awardcase-category">
    <div class="card-body awardcase-body">
        @php
            $completedAwards = $user->awards()->pluck('award_id')->toArray();
            $inProgressAwards = \App\Models\Award\Award::whereNotIn('id', $completedAwards)->get();
            // get rid of any that do not have progressions
            $inProgressAwards = $inProgressAwards->filter(function($award) {
                return $award->progressions->count() > 0;
            });
        @endphp
        @if(!$inProgressAwards->count())
            <p class="text-success">You have completed all available awards. Yay!</p>
        @else
            @foreach($inProgressAwards as $award)
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-9"><h5>{!!$award->displayName !!}</h5></div>
                            <div class="col-3 text-right"><h5>({{ $award->progressionProgress($user) }}/{{count($award->progressions)}})</h5></div>
                        </div>
                    </div>
                    <div class="card-body row mb-3">
                        @if($award->imageUrl)
                            <div class="col-md-3 world-entry-image border-right">
                                <a href="{{ $award->imageUrl }}" data-lightbox="entry" data-title="{{ $award->name }}"><img src="{{ $award->imageUrl }}" class="world-entry-image img-fluid" /></a>
                            </div>
                        @endif
                        <div class="{{ $award->imageUrl ? 'col-md-9' : 'col-md-12'}}">
                            @foreach($award->progressions->chunk(4) as $chunk)
                                <div class="row mb-3">
                                    @foreach($chunk as $progression)
                                        <div class="col-md-3">
                                            {!! $progression->unlocked(Auth::user()) !!}
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            <div class="text-right mb-0">
                                @if($award->progressionProgress(Auth::user()) == count($award->progressions) && $award->canClaim(Auth::user()))
                                    <div class="mt-2">
                                        {!! Form::open(['url' => 'awardcase/claim/'.$award->id]) !!}
                                            {!! Form::submit('Claim Award', ['class' => 'btn btn-primary']) !!}
                                        {!! Form::close() !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>

@endsection
@section('scripts')
<script>

$(document).ready(function() {
    $('.awardcase-stack').on('click', function(e) {
        e.preventDefault();
        var $parent = $(this).parent().parent();
        loadModal("{{ url('awards') }}/" + $parent.data('id'), $parent.data('name'));
    });
});

</script>
@endsection

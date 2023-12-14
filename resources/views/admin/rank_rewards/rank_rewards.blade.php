@extends('admin.layout')

@section('admin-title')
    Rank Rewards
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Rank Rewards' => 'admin/rank-rewards']) !!}

    <h1>Rank Rewards</h1>

    <p>This is a list of rank rewards that have been created.</p>
    <p>You can set all users of a certain rank to recieve rewards every X amount of time, so long as they are in that rank
        and the reward is active.</p>

    <div class="text-right mb-3">
        <a class="btn btn-primary" href="{{ url('admin/rank-rewards/create') }}"><i class="fas fa-plus"></i> Create New Rank
            Reward</a>
    </div>

    @if (!isset($rankrewards) || !count($rankrewards))
        <p>No rank rewards found.</p>
    @else
        {!! $rankrewards->render() !!}

        <div class="row ml-md-2">
            <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
                <div class="col-6 col-md-2 font-weight-bold">Name</div>
                <div class="col-6 col-md-2 font-weight-bold">Rank</div>
                <div class="col-6 col-md-2 font-weight-bold">Active</div>
                <div class="col-6 col-md-4 font-weight-bold">Give Reward Every...</div>
            </div>
            @foreach ($rankrewards as $rankreward)
                <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
                    <div class="col-6 col-md-2">
                        {!! $rankreward->name !!}
                    </div>
                    <div class="col-6 col-md-2">
                        {!! $rankreward->rank->name !!}
                    </div>
                    <div class="col-6 col-md-2">
                        {!! $rankreward->is_active ? '<i class="text-success fas fa-check"></i>' : '' !!}
                    </div>
                    <div class="col-6 col-md-4 text-truncate">
                        @if ($rankreward->reward_time == 1)
                            Day
                        @elseif($rankreward->reward_time == 2)
                            Week
                        @else
                            Month
                        @endif
                    </div>
                    <div class="col-3 col-md-1 text-right">
                        <a href="{{ url('admin/rank-rewards/edit/' . $rankreward->id) }}"
                            class="btn btn-primary py-0 px-2">Edit</a>
                    </div>
                </div>
            @endforeach
        </div>

        {!! $rankrewards->render() !!}

        <div class="text-center mt-4 small text-muted">{{ $rankrewards->total() }}
            result{{ $rankrewards->total() == 1 ? '' : 's' }} found.</div>
    @endif

@endsection

@section('scripts')
    @parent
@endsection

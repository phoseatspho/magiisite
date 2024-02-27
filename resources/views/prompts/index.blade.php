@extends('prompts.layout')

@section('prompts-title')
    Home
@endsection

@section('content')
    {!! breadcrumbs(['Quests' => 'prompts']) !!}

    <h1>Quests</h1>
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="{{ asset('images/inventory.png') }}" alt="Prompts" />
                    <h5 class="card-title">Quests</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><a href="{{ url('prompts/prompt-categories') }}">Quest Categories</a></li>
                    <li class="list-group-item"><a href="{{ url('prompts/prompts') }}">All Quests</a></li>
                </ul>
            </div>
        </div>
    </div>
@endsection

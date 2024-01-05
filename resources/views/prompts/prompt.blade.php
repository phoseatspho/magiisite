@extends('prompts.layout')

@section('title')
    Prompts:: {{ $prompt->name }}
@endsection

@section('content')
    {!! breadcrumbs(['Quests' => 'prompts', 'All Quests' => 'prompts/prompts', $prompt->name => 'prompts/' . $prompt->id]) !!}
    @include('prompts._prompt_entry', ['prompt' => $prompt, 'isPage' => true])
@endsection

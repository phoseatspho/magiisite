@extends('prompts.layout')

@section('prompts-title')
    All Prompts
@endsection

@section('content')
    {!! breadcrumbs(['Quests' => 'prompts', 'All Quests' => 'prompts/prompts']) !!}
    <h1>All Quests</h1>

    
        {!! Form::open(['method' => 'GET', 'class' => '']) !!}
        <div class="form-inline justify-content-end">
            <div class="form-group ml-3 mb-3">
                {!! Form::text('name', Request::get('name'), ['class' => 'form-control', 'placeholder' => 'Name']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('prompt_category_id', $categories, Request::get('prompt_category_id'), ['class' => 'form-control']) !!}
            </div>
            <div class="form-group ml-3 mb-3">
                {!! Form::select('open_prompts', ['any' => 'Any Status', 'open' => 'Open Prompts', 'closed' => 'Closed Prompts'], Request::get('open_prompts') ?? 'any', ['class' => 'form-control selectize']) !!}
            </div>
            
            {!! Form::close() !!}
        </div>

{!! $prompts->render() !!}
@foreach($prompts as $prompt)
    <div class="card mb-3">
        @if($prompt->parent_id)
            @php 
                if(Auth::check()) $submission = DB::table('submissions')->where('user_id', Auth::user()->id)->where('prompt_id', $prompt->parent_id)->where('status', 'Approved')->count();    
            @endphp
            @if(!Auth::check() || $submission < $prompt->parent_quantity)
        <div class="card-body" style="background-color:#ddd;">
            @include('prompts._prompt_denied_entry', ['prompt' => $prompt])
            @else
        <div class="card-body">
            @include('prompts._prompt_entry', ['prompt' => $prompt])
            @endif
        @else
        <div class="card-body">
            @include('prompts._prompt_entry', ['prompt' => $prompt])
        @endif
        </div>
    </div>
  
@endforeach

            {!! $prompts->render() !!}

            <div class="text-center mt-4 small text-muted">{{ $prompts->total() }} result{{ $prompts->total() == 1 ? '' : 's' }} found.</div>
@endsection

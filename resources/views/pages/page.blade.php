@extends('layouts.app')

@section('title')
    {{ $page->title }}
@endsection

@section('content')
    <x-admin-edit title="Page" :object="$page" />
    {!! breadcrumbs([$page->title => $page->url]) !!}
    <h1>{{ $page->title }}</h1>


    <div class="site-page-content parsed-text">
        {!! $page->parsed_text !!}
    </div>

    @if ($page->can_comment)
        <div class="container">
            @comments([
                'model' => $page,
                'perPage' => 5,
                'allow_dislikes' => $page->allow_dislikes,
            ])
        </div>

        <div class="site-page-content parsed-text">
            {!! $page->parsed_text !!}
        </div>

        @if ($page->can_comment)
    <div class="container">
                @comments([
                    'model' => $page,
                    'perPage' => 5,
                    'allow_dislikes' => $page->allow_dislikes,
                ])
            </div>
    @endif
@endsection

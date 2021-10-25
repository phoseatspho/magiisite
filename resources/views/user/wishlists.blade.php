@extends('user.layout')

@section('profile-title') {{ $user->name }}'s Wishlists @endsection

@section('profile-content')
{!! breadcrumbs(['Users' => 'users', $user->name => $user->url, 'Wishlists' => $user->url . '/wishlists']) !!}

<h1>Wishlists</h1>

{!! $wishlists->render() !!}

<div class="row ml-md-2 mb-4">
    <div class="d-flex row flex-wrap col-12 pb-1 px-0 ubt-bottom">
        <div class="col-5 col-md-4 font-weight-bold">Name</div>
    </div>
    <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
        <div class="col-5 col-md-4"> Default</div>
        <div class="col-3 col-md text-right">
            <div class="btn btn-primary btn-sm">
                <a href="{{ url('user/'.$user->name.'/wishlists/default') }}">View</a>
            </div>
        </div>
    </div>
    @foreach($wishlists as $wishlist)
        <div class="d-flex row flex-wrap col-12 mt-1 pt-2 px-0 ubt-top">
            <div class="col-5 col-md-4"> {{ $wishlist->name }}</div>
            <div class="col-3 col-md text-right">
                <div class="btn btn-primary btn-sm">
                    <a href="{{ url('user/'.$user->name.'/wishlists/'.$wishlist->id) }}">View</a>
                </div>
            </div>
        </div>
    @endforeach
</div>

{!! $wishlists->render() !!}

@endsection

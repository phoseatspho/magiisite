@extends('home.layout')

@section('home-title') Wishlists @endsection

@section('home-content')
{!! breadcrumbs(['Wishlists' => 'wishlists']) !!}

<h1>
    Wishlists
    <div class="float-right">
        <a href="#" class="btn btn-success create-wishlist"><i class="fas fa-plus"></i> Create Wishlist</a>
    </div>
</h1>

<p>These are your item wishlists. Click on the name of any wishlist to be taken to its page, where you can view and edit it as well as the items in it.</p>

@if($wishlists->count())
    <div class="row mb-2">
        @foreach($wishlists as $wishlist)
            <div class="col-md-4 mb-2">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5><a href="{{ url('wishlists/'.$wishlist->id) }}">{{ $wishlist->name }}</a></h5>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p>No wishlists found!</p>
@endif

@endsection
@section('scripts')
<script>

$( document ).ready(function() {
    $('.create-wishlist').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('wishlists/create') }}", 'Create Wishlist');
    });
});

</script>
@endsection

@extends('home.layout')

@section('home-title') Wishlists: {{ $wishlist->name }} @endsection

@section('home-content')
{!! breadcrumbs(['Wishlists' => 'wishlists', $wishlist->name => 'wishlists/'.$wishlist->id]) !!}

<h1>Wishlist: {{ $wishlist->name }}</h1>

<div class="text-right">
    <a href="#" class="btn btn-secondary edit-wishlist">Edit Wishlist</a>
    <a href="#" class="btn btn-danger delete-wishlist">Delete Wishlist</a>
</div>

@endsection
@section('scripts')
<script>

$( document ).ready(function() {
    $('.edit-wishlist').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('wishlists/edit/'.$wishlist->id) }}", 'Edit Wishlist');
    });

    $('.delete-wishlist').on('click', function(e) {
        e.preventDefault();
        loadModal("{{ url('wishlists/delete/'.$wishlist->id) }}", 'Delete Wishlist');
    });
});

</script>
@endsection

@extends('home.layout')

@section('home-title') Wishlists: {{ $wishlist->name }} @endsection

@section('home-content')
{!! breadcrumbs(['Wishlists' => 'wishlists', $wishlist->name => 'wishlists/'.$wishlist->id]) !!}

<h1>
    Wishlist: {{ $wishlist->name }}
    <div class="float-right">
        <a href="#" class="btn btn-secondary edit-wishlist">Edit Wishlist</a>
        <a href="#" class="btn btn-danger delete-wishlist">Delete Wishlist</a>
    </div>
</h1>

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

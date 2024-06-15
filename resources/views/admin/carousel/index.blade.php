@extends('admin.layout')

@section('admin-title')
    File Manager
@endsection

@section('admin-content')
    {!! breadcrumbs(['Admin Panel' => 'admin', 'Carousel' => 'admin/data/carousel']) !!}

    <h1>Carousel Manager</h1>

    <p>This page allows you to upload carousel images for the front page.</p>

    {!! Form::open(['url' => 'admin/data/carousel/create', 'files' => true]) !!}

    <div class="p-4">
        <div class="form-group">
            {!! Form::label('link') !!}
            {!! Form::text('link', '', ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('alt text') !!} {!! add_help('This is for accessibility purposes.') !!}
            {!! Form::text('alt_text', '', ['class' => 'form-control']) !!}
        </div>

        <div class="form-group">
            {!! Form::label('Image') !!}
            <div>{!! Form::file('image') !!}</div>
        </div>

        <div class="text-right">
            {!! Form::submit('Create', ['class' => 'btn btn-primary']) !!}
        </div>

        {!! Form::close() !!}
    </div>

    <table class="table table-sm">
        <thead>
            <tr>
                <th>Image</th>
                <th>Link</th>
                <th>Alt Text</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($carousels as $carousel)
                <tr>
                    <td>
                        <a href="">{{ $carousel->image }}</a>
                    </td>
                    <td>
                        <a href="">{{ $carousel->link }}</a>
                    </td>
                    <td>
                        <a href="">{{ $carousel->alt_text }}</a>
                    </td>
                    <td class="text-right">
                        <a href="#" class="btn btn-outline-danger btn-sm delete-carousel" data-name="{{ $carousel }}">Delete</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection

@section('scripts')
    @parent
    @if (isset($carousel))
        <script>
            $(document).ready(function() {
                $('.delete-carousel').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('admin/data/carousel/delete') }}/{{ $carousel->id }}", 'Delete Carousel');
                });
            });
        </script>
    @endif
@endsection

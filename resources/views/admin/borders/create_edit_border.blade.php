@extends('admin.layout')

@section('admin-title')
    Borders
@endsection

@section('admin-content')
    {!! breadcrumbs([
        'Admin Panel' => 'admin',
        'Borders' => 'admin/data/borders',
        ($border->id ? 'Edit' : 'Create') . ' Border' => $border->id
            ? 'admin/data/borders/edit/' . $border->id
            : 'admin/data/borders/create',
    ]) !!}

    <h1>{{ $border->id ? 'Edit' : 'Create' }} Border
        @if ($border->id)
            <a href="#" class="btn btn-outline-danger float-right delete-border-button">Delete Border</a>
        @endif
    </h1>

    {!! Form::open([
        'url' => $border->id ? 'admin/data/borders/edit/' . $border->id : 'admin/data/borders/create',
        'files' => true,
    ]) !!}

    <h3>Basic Information</h3>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Name') !!}
                {!! Form::text('name', $border->name, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Border Category (Optional)') !!}
                {!! Form::select('border_category_id', $categories, $border->border_category_id, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>

    <h3>Image</h3>
    <p>An image is required. You can't have a border with no image!</p>

    <div class="row">
        @if ($border->id)
            <div class="col-md-2">
                <div class="form-group">
                    <h5>Image</h5>
                    <img src="{{ $border->imageUrl }}" class="mw-100" style="width:125px; height:125px;" />
                    <br>
                </div>
                <div class="form-group">
                    <h5>In Action</h5>
                    {!! $border->preview() !!}
                    <br>
                </div>
            </div>
        @endif
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('Border Image') !!}
                <div>{!! Form::file('image') !!}</div>
                <div class="text-muted">Supports .png and .gif</div>
            </div>
            <div class="form-group">
                {!! Form::label('Border Style (Required)') !!}{!! add_help(
                    'Choose how the border will display around an icon. It can display over or under the user\'s icon.',
                ) !!}
                {!! Form::select('border_style', ['0' => 'Under', '1' => 'Over'], $border->border_style, [
                    'class' => 'form-control',
                    'placeholder' => 'Select a Type',
                ]) !!}
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::checkbox('is_default', 1, $border->is_default, [
                    'class' => 'form-check-input',
                    'data-toggle' => 'toggle',
                ]) !!}
                {!! Form::label('is_default', 'Default Border', ['class' => 'form-check-label ml-3']) !!} {!! add_help('If enabled, this border will be automatically available for any users.') !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::checkbox('is_active', 1, $border->is_active, [
                    'class' => 'form-check-input',
                    'data-toggle' => 'toggle',
                ]) !!}
                {!! Form::label('is_active', 'Active?', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Users can\'t see or select this border if it isn\'t visible.') !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::checkbox('admin_only', 1, $border->admin_only, [
                    'class' => 'form-check-input',
                    'data-toggle' => 'toggle',
                ]) !!}
                {!! Form::label('admin_only', 'Staff Only?', ['class' => 'form-check-label ml-3']) !!} {!! add_help('Only users who are staff can select this border if turned on.') !!}
            </div>
        </div>
    </div>

    <div class="form-group">
        {!! Form::label('Description (Optional)') !!}
        {!! Form::textarea('description', $border->description, ['class' => 'form-control wysiwyg']) !!}
    </div>

    <div class="text-right">
        {!! Form::submit($border->id ? 'Edit' : 'Create', ['class' => 'btn btn-primary']) !!}
    </div>

    {!! Form::close() !!}

    @if ($border->id)
        <h3>Preview</h3>
        <div class="card mb-3">
            <div class="card-body">
                @include('world._border_entry', ['border' => $border])
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('.selectize').selectize();
            $('.delete-border-button').on('click', function(e) {
                e.preventDefault();
                loadModal("{{ url('admin/data/borders/delete') }}/{{ $border->id }}", 'Delete Border');
            });
        });
    </script>
@endsection

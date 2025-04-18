@extends('layouts.app')

@section('title')
    Frequently Asked Questions
@endsection

@section('content')
    {!! breadcrumbs(['FAQ' => 'faq']) !!}
    <h1>FAQ</h1>
    <p>
        You can search the FAQ by tag, category, or keyword. If you can't find what you're looking for, feel free to reach out to staff.
    </p>

    {{-- search bar --}}
    <div class="form-group">
        {!! Form::text('content', null, ['class' => 'form-control col-md-8 mx-auto', 'id' => 'search', 'placeholder' => 'Search by Keyword(s)']) !!}
    </div>
    <div class="form-group">
        {!! Form::select('tags[]', $tags, null, ['class' => 'form-control col-md-6 mx-auto', 'multiple', 'id' => 'tags', 'placeholder' => 'Select Categories']) !!}
    </div>

    <div id="results">
        @include('browse._faq_content', ['faqs' => $faqs])
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            $('#tags').selectize({
                maxItems: 5
            });

            // search on keyword change
            $('#search').change(function(e) {
                e.preventDefault();
                var search = $(this).val();
                var tags = $('#tags').val();
                // ajax
                $.ajax({
                    url: '{{ url('faq/search') }}',
                    type: 'GET',
                    data: {
                        content: search,
                        tags: tags
                    },
                    success: function(data) {
                        $('#results').fadeOut(200);
                        $('#results').html(data);
                        $('#results').fadeIn(200);
                    }
                });
            });

            // search on tag change
            $('#tags').change(function(e) {
                e.preventDefault();
                var search = $('#search').val();
                var tags = $(this).val();
                // ajax
                $.ajax({
                    url: '{{ url('faq/search') }}',
                    type: 'GET',
                    data: {
                        content: search,
                        tags: tags
                    },
                    success: function(data) {
                        $('#results').fadeOut(200);
                        $('#results').html(data);
                        $('#results').fadeIn(200);
                    }
                });
            });
        });
    </script>
@endsection

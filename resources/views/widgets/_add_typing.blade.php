@php
    $elements = \App\Models\Element\Element::orderBy('name')->pluck('name', 'id');
    $type = $type ?? null;
@endphp

<div class="card p-4 mb-2 mt-2" id="typing-card">
    <h3>Typings</h3>

    <p>You can add typings to this object by selecting an element from the dropdown below and clicking "Add Typing".
        <br><b>You can have a maximum of 2 typings on an object.</b>
    </p>

    <div class="typing">
        <div id="elements">
            @if ($object->type || $type)
                @foreach ($object->type->element_ids as $id)
                    <div class="form-group">
                        {!! Form::label('Element') !!}
                        {!! Form::select('element_ids[]', $elements, $id, ['class' => 'form-control selectize', 'placeholder' => 'Select Element']) !!}
                    </div>
                @endforeach
            @endif
        </div>
        <div class="btn btn-secondary" id="add-element">Add Element</div>
        <div class="btn btn-primary" id="submit-typing">Add Typing</div>
    </div>

    <div class="alert alert-success success hide"></div>
    <div class="alert alert-danger error hide"></div>
</div>

<div class="form-group hide element-row">
    {!! Form::label('Element') !!}
    {!! Form::select('element_ids[]', $elements, null, ['class' => 'form-control select', 'placeholder' => 'Select Element']) !!}
</div>

<script>
    $(document).ready(function() {
        $('.selectize').selectize();

        // add element
        $('#add-element').on('click', function(e) {
            e.preventDefault();
            // make sure there are less than 2 elements
            if ($('#elements').find('.form-group').length >= 2) {
                return;
            }
            var $clone = $('.element-row').clone();
            $('#elements').append($clone);
            $clone.removeClass('hide element-row');
            $clone.find('select').selectize();
        });

        // ajax on add typing
        $('#submit-typing').on('click', function(e) {
            e.preventDefault();
            var $typing = $('.typing');
            var $submit = $typing.find('#submit-typing');
            var $error = $typing.find('.error');
            var $success = $typing.find('.success');

            $submit.addClass('disabled');
            $error.addClass('d-none');
            $success.addClass('d-none');

            $.ajax({
                url: "{{ url('admin/typing') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    typing_model: '{{ json_encode(get_class($object)) }}',
                    typing_id: '{{ $object->id }}',
                    element_ids: $('#elements').find('select').map(function() {
                        return $(this).val();
                    }).get()
                },
                success: function(data) {
                    console.log('success');
                    console.log(data);
                    $success.removeClass('d-none');
                    $success.html('Typing added.');
                    $('#typing-card').html(data);
                },
                error: function(data) {
                    console.log('error');
                    console.log(data);
                    $('#typing-card').html(data);
                }
            });
        });
    });
</script>


<h1>{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }}</h1>
@include('widgets._carousel')
{!! $about->parsed_text !!}
@include('widgets._news', ['textPreview' => true])
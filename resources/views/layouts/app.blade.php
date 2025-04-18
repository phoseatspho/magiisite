<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
    header('Permissions-Policy: interest-cohort=()');
    ?>

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (config('lorekeeper.extensions.use_recaptcha'))
        <!-- ReCaptcha v3 -->
        {!! RecaptchaV3::initJs() !!}
    @endif

    <title>{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')</title>

    <!-- Primary Meta Tags -->
    <meta name="title" content="{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')">
    <meta name="description" content="@if (View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('lorekeeper.settings.site_desc', 'A Lorekeeper ARPG') }} @endif">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ config('app.url', 'http://localhost') }}">
    <meta property="og:image" content="@if (View::hasSection('meta-img')) @yield('meta-img') @else {{ asset('images/meta-image.png') }} @endif">
    <meta property="og:title" content="{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')">
    <meta property="og:description" content="@if (View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('lorekeeper.settings.site_desc', 'A Lorekeeper ARPG') }} @endif">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ config('app.url', 'http://localhost') }}">
    <meta property="twitter:image" content="@if (View::hasSection('meta-img')) @yield('meta-img') @else {{ asset('images/meta-image.png') }} @endif">
    <meta property="twitter:title" content="{{ config('lorekeeper.settings.site_name', 'Lorekeeper') }} -@yield('title')">
    <meta property="twitter:description" content="@if (View::hasSection('meta-desc')) @yield('meta-desc') @else {{ config('lorekeeper.settings.site_desc', 'A Lorekeeper ARPG') }} @endif">

    <!-- No AI scraping directives -->
    <meta name="robots" content="noai">
    <meta name="robots" content="noimageai">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/site.js') }}"></script>
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap4-toggle.min.js') }}"></script>
    <script src="{{ asset('js/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/jquery.tinymce.min.js') }}"></script>
    <script src="{{ asset('js/lightbox.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap-colorpicker.min.js') }}"></script>
    <script src="{{ asset('js/selectize.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui-timepicker-addon.js') }}"></script>
    <script src="{{ asset('js/croppie.min.js') }}"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/lorekeeper.css') }}" rel="stylesheet">
    <link href="{{ asset('css/magii.css') }}" rel="stylesheet">

    {{-- Font Awesome --}}
    <link href="{{ asset('css/all.min.css') }}" rel="stylesheet">

    {{-- jQuery UI --}}
    <link href="{{ asset('css/jquery-ui.min.css') }}" rel="stylesheet">

    {{-- Bootstrap Toggle --}}
    <link href="{{ asset('css/bootstrap4-toggle.min.css') }}" rel="stylesheet">


    <link href="{{ asset('css/lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/jquery-ui-timepicker-addon.css') }}" rel="stylesheet">
    <link href="{{ asset('css/croppie.css') }}" rel="stylesheet">
    <link href="{{ asset('css/selectize.bootstrap4.css') }}" rel="stylesheet">

    @if (file_exists(public_path() . '/css/custom.css'))
        <link href="{{ asset('css/custom.css') . '?v=' . filemtime(public_path('css/lorekeeper.css')) }}" rel="stylesheet">
    @endif

    @include('feed::links')
</head>

<body>
    <div id="app">
        <a href="https://www.magiispecies.com">
        <div class="site-header-image" id="header" style="background-image: url('{{ asset('images/header.png') }}');"></div></a>

        <main class="container-fluid">
            <div class="row px-1 px-lg-0">
                <div class="sidebar col-lg-2" id="sidebar">
                    @include('layouts._left')
                </div>

                <div class="main-content col-lg-8 px-0">
                    @include('layouts._nav')
                    <div class="site-mobile-header bg-primary">
                        <a href="#" class="btn btn-sm btn-mobile" id="mobileMenuButton">Menu <i class="fas fa-bars ml-1"></i></a>
                        <a href="#" class="btn btn-sm btn-mobile" id="mobileFeaturedButton"><i class="fas fa-star mr-1"></i> Featured</a>
                    </div>

                    
                        @if (Settings::get('is_maintenance_mode'))
                            <div class="alert alert-secondary">
                                The site is currently in maintenance mode!
                                @if (!Auth::check() || !Auth::user()->hasPower('maintenance_access'))
                                    You can browse public content, but cannot make any submissions.
                                @endif
                            </div>
                        @endif
                    
                    <div class="p-4">
                    @if (Auth::check() && !config('lorekeeper.extensions.navbar_news_notif'))
                            @if (Auth::user()->is_news_unread)
                                <div class="alert alert-info"><img src="../files/newsalert.png" width="4%" height="auto"> <a href="{{ url('news') }}">Psst! There's news!</a></div>
                            @endif
                            @if (Auth::user()->is_sales_unread)
                                <div class="alert alert-info"><a href="{{ url('sales') }}">There is a new sales post!</a></div>
                            @endif
                        @endif
                        @include('flash::message')
                        @yield('content')
                    </div>
                </div>

                <div class="sidebar col-lg-2" id="rightSidebar">
                    @include('layouts._right')
                </div>
            </div>
        </main>

        <div class="site-footer mt-md-4" id="footer">
            @include('layouts._footer')
        </div>

        <div class="modal fade" id="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span class="modal-title h5 mb-0"></span>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                    </div>
                </div>
            </div>
        </div>

        @yield('scripts')
        @include('layouts._pagination_js')
        <script>
            $(function() {
                $('[data-toggle="tooltip"]').tooltip({
                    html: true
                });
                $('.cp').colorpicker();
                tinymce.init({
                    selector: '.wysiwyg',
                    height: 500,
                    menubar: false,
                    convert_urls: false,
                    plugins: [
                        'advlist autolink lists link image charmap print preview anchor',
                        'searchreplace visualblocks code fullscreen spoiler',
                        'insertdatetime media table paste code help wordcount'
                    ],
                    toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | spoiler-add spoiler-remove | removeformat | code',
                    content_css: [
                        '{{ asset('css/app.css') }}',
                        '{{ asset('css/lorekeeper.css') }}'
                    ],
                    spoiler_caption: 'Toggle Spoiler',
                    target_list: false
                });

                var $mobileMenuButton = $('#mobileMenuButton');
                var $leftCloseButton = $('#leftCloseButton')
                var $sidebar = $('#sidebar');
                $('#mobileMenuButton').on('click', function(e) {
                    e.preventDefault();
                    $sidebar.toggleClass('active');
                });
                $('#leftCloseButton').on('click', function(e) {
                    e.preventDefault();
                    $sidebar.css('transition', 'left 0ms linear 350ms, opacity 350ms linear 0ms').delay(350)
                        .queue(function (next) {
                            $(this).css('transition', 'opacity 350ms linear 100ms');
                            next();
                        })
                    $sidebar.toggleClass('active');
                });

                var $mobileFeaturedButton = $('#mobileFeaturedButton');
                var $rightCloseButton = $('#rightCloseButton')
                var $featuredSidebar = $('#rightSidebar');
                $('#mobileFeaturedButton').on('click', function(e) {
                    e.preventDefault();
                    $featuredSidebar.toggleClass('active');
                });
                $('#rightCloseButton').on('click', function(e) {
                    e.preventDefault();
                    $featuredSidebar.css('transition', 'left 0ms linear 350ms, opacity 350ms linear 0ms').delay(350)
                        .queue(function (next) {
                            $(this).css('transition', 'opacity 350ms linear 100ms');
                            next();
                        })
                    $featuredSidebar.toggleClass('active');
                });

                $('.inventory-log-stack').on('click', function(e) {
                    e.preventDefault();
                    loadModal("{{ url('items') }}/" + $(this).data('id') + "?read_only=1", $(this).data('name'));
                });

                $('.spoiler-text').hide();
                $('.spoiler-toggle').click(function() {
                    $(this).next().toggle();
                });
            });
        </script>
    </div>
    
</body>

</html>

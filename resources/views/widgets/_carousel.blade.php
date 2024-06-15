<div id="carouselDashboard" class="carousel slide" data-ride="carousel" data-interval="{{ Settings::get('carousel_speed') }}">
    <div class="carousel-inner">
        @foreach (\App\Models\Carousel\Carousel::all() as $carousel)
            @if ($loop->first)
                <div class="carousel-item active"><a href="{{ $carousel->link }}"><img class="d-block w-100" src="{{ $carousel->imageURL }}" alt="{{ $carousel->alt_text }}"></a></div>
            @else
                <div class="carousel-item"><a href="{{ $carousel->link }}"><img class="d-block w-100" src="{{ $carousel->imageURL }}" alt="{{ $carousel->alt_text }}"></a></div>
            @endif
        @endforeach
    </div>
    <a class="carousel-control-prev" href="#carouselDashboard" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#carouselDashboard" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

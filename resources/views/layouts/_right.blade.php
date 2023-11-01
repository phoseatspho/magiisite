<div class="d-block d-lg-none my-3">
    <a href="#" class="btn" id="rightCloseButton"><i class="fas fa-times-circle fa-2x"></i></a>
</div>

<div class="scroll">
    <div class="scroll-header"></div>
    <div class="scroll-body">
        <div class="scroll-heading">Featured Magii</div>
        <hr>
        @if (isset($featured) && $featured)
            <div class="scroll-featured">
                <a href="{{ $featured->url }}"><img src="{{ $featured->image->thumbnailUrl }}" class="img-thumbnail" /></a>

                <div class="mt-1">
                    <a href="{{ $featured->url }}" class="scroll-featured-name">
                        @if(!$featured->is_visible) <i class="fas fa-eye-slash"></i> @endif {{ $featured->fullName }}
                    </a>
                </div>
                <hr>
            </div>
        @else
            <p class="pt-3">There is no featured Magii.</p>
            <hr class="mb-2">
        @endif
        <p class="dark px-2">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras sed convallis enim, eu ullamcorper lorem. Ut porttitor tempus nibh. Pellentesque id semper diam, non maximus dui. Nunc lacinia sem ac lorem consectetur tempus.</p>
    </div>
    <div class="scroll-footer"></div>
</div>


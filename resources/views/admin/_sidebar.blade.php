<ul id="accordion">
    <li class="sidebar-header"><a href="{{ url('admin') }}" class="card-link">Admin Home</a></li>

    @foreach(Config::get('lorekeeper.admin_sidebar') as $key => $section)
        @if(Auth::user()->isAdmin || Auth::user()->hasPower($section['power']))
            <li class="sidebar-section">
                <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapse{{ str_replace(' ', '', $key) }}" aria-expanded="false" aria-controls="collapse{{ str_replace(' ', '', $key) }}" id="heading{{ str_replace(' ', '', $key) }}">
                    {{ str_replace(' ', '', $key) }}
                </div>
                <div class="collapse" aria-labelledby="heading{{ str_replace(' ', '', $key) }}" data-parent="#accordion" id="collapse{{ str_replace(' ', '', $key) }}">
                    @foreach($section['links'] as $item)
                        <div class="sidebar-item">
                            <a href="{{ url($item['url']) }}" class="{{ set_active($item['url'] . '*') }}">{{ $item['name'] }}</a>
                        </div>
                    @endforeach
                </div>
            </li>
        @endif
    @endforeach

</ul>
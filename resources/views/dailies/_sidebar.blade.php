<ul id="accordion">
    <li class="sidebar-header"><a href="{{ url(__('dailies.dailies')) }}" class="card-link">{{__('dailies.dailies')}}</a></li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapse{{__('dailies.dailies')}}" aria-expanded="true" aria-controls="collapse{{__('dailies.dailies')}}" id="heading{{__('dailies.dailies')}}">
            {{__('dailies.dailies')}}
        </div>
        <div class="collapse show" aria-labelledby="heading{{__('dailies.dailies')}}" data-parent="#accordion" id="collapse{{__('dailies.dailies')}}">
            @foreach($dailies as $daily)
                <div class="sidebar-item">
                    <a href="{{ $daily->url }}" class="{{ set_active('dailies/'.$daily->id) }}">{{ $daily->name }}</a>
                </div>
            @endforeach
        </div>
    </li>
</ul>

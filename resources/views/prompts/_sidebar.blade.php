<ul id="accordion">
    <li class="sidebar-header"><a class="card-link"><a href="{{ url('prompts') }}">Quests</a></a></li>

    <li class="sidebar-section">
        <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapsePrompts" aria-expanded="true" aria-controls="collapsePrompts" id="headingPrompts">
            Quests
        </div>
        <div class="collapse show" aria-labelledby="headingPrompts" data-parent="#accordion" id="collapsePrompts">
            <div class="sidebar-item">
                <a href="{{ url('prompts/prompt-categories') }}" class="{{ set_active('prompts/prompt-categories*') }}">Quest Categories</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('prompts/prompts') }}" class="{{ set_active('prompts/prompts*') }}">Quest Prompts</a>
            </div>
        </div>
    </li>
</ul>

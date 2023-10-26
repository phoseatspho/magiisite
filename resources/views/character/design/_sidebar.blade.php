<ul id="accordion">
    @if(isset($request))

        <li class="sidebar-section">
            <div class="sidebar-section-header" data-toggle="collapse" data-target="#collapseCurrentRequest" aria-expanded="true" aria-controls="collapseCurrentRequest" id="headingCurrentRequest">
                Current Request
            </div>
            <div class="collapse show" aria-labelledby="headingCurrentRequest" data-parent="#accordion" id="collapseCurrentRequest">
                <div class="sidebar-item">
                    <a href="{{ $request->url }}" class="{{ set_active('designs/'.$request->id) }}">View</a>
                </div>
                <div class="sidebar-item">
                    <a href="{{ $request->url . '/comments' }}" class="{{ set_active('designs/' . $request->id . '/comments') }}">Comments</a>
                </div>
                <div class="sidebar-item">
                    <a href="{{ $request->url . '/image' }}" class="{{ set_active('designs/' . $request->id . '/image') }}">Image</a>
                </div>
                <div class="sidebar-item">
                    <a href="{{ $request->url . '/addons' }}" class="{{ set_active('designs/' . $request->id . '/addons') }}">Add-ons</a>
                </div>
                <div class="sidebar-item">
                    <a href="{{ $request->url . '/traits' }}" class="{{ set_active('designs/' . $request->id . '/traits') }}">Traits</a>
                </div>
            </div>
        </li>
    @endif

    <li class="sidebar-section">
        <div class="sidebar-section-header collapsed" data-toggle="collapse" data-target="#collapseDesignApprovals" aria-expanded="false" aria-controls="collapseDesignApprovals" id="headingDesignApprovals">
            Design Approvals
        </div>
        <div class="collapse" aria-labelledby="headingDesignApprovals" data-parent="#accordion" id="collapseDesignApprovals">
            <div class="sidebar-item">
                <a href="{{ url('designs') }}" class="{{ set_active('designs') }}">Drafts</a>
            </div>
            <div class="sidebar-item">
                <a href="{{ url('designs/pending') }}" class="{{ set_active('designs/*') }}">Submissions</a>
            </div>
        </div>
    </li>
</ul>

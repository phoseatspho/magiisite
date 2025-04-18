<div class="row world-entry">
    @if ($prompt->has_image)
        <div class="col-md-3 world-entry-image"><a href="{{ $prompt->imageUrl }}" data-lightbox="entry" data-title="{{ $prompt->name }}"><img src="{{ $prompt->imageUrl }}" class="world-entry-image" alt="{{ $prompt->name }}" /></a></div>
    @endif
    <div class="{{ $prompt->has_image ? 'col-md-9' : 'col-12' }}">
        <x-admin-edit title="Prompt" :object="$prompt" />
        <div class="mb-3">
            @if (isset($isPage))
                <h1 class="mb-0">{!! $prompt->name !!}</h1>
            @else
<h2 class="mb-0"><a href="{{ url('prompts/' . $prompt->id) }}">{!! $prompt->name !!}</a></h2>
            @endif
            @if ($prompt->prompt_category_id)
                <div><strong>Category: </strong>{!! $prompt->category->displayName !!}</div>
            @endif
            @if ($prompt->start_at && $prompt->start_at->isFuture())
                <div><strong>Starts: </strong>{!! format_date($prompt->start_at) !!} ({{ $prompt->start_at->diffForHumans() }})</div>
            @endif
            @if ($prompt->end_at)
                <div><strong>Ends: </strong>{!! format_date($prompt->end_at) !!} ({{ $prompt->end_at->diffForHumans() }})</div>
            @endif
        </div>
        <div class="world-entry-text">
            <p>{{ $prompt->summary }}</p>
            <h3 class="mb-3"><a data-toggle="collapse" href="#prompt-{{ $prompt->id }}" @if (isset($isPage)) aria-expanded="true" @endif)>Details <i class="fas fa-angle-down"></i></a></h3>
            <div class="collapse @if (isset($isPage)) show @endif mb-5" id="prompt-{{ $prompt->id }}">
                @if ($prompt->parsed_description)
                    {!! $prompt->parsed_description !!}
@else
<p>No further details.</p>
                @endif
                @if ($prompt->hide_submissions == 1 && isset($prompt->end_at) && $prompt->end_at > Carbon\Carbon::now())
                    <p class="text-info">Submissions to this prompt are hidden until this prompt ends.</p>
@elseif($prompt->hide_submissions == 2)
<p class="text-info">Submissions to this prompt are hidden.</p>
                @endif
            </div>
            <h3>Rewards</h3>
            @if (!count($prompt->rewards))
                No rewards.
@else
<table class="table table-sm">
                    <thead>
                        <tr>
                            <th width="70%">Reward</th>
                            <th width="30%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prompt->rewards as $reward)
<tr>
                                <td>{!! $reward->reward->displayName !!}</td>
                                <td>{{ $reward->quantity }}</td>
                            </tr>
@endforeach
                    </tbody>
                </table>
            @endif
            <hr>
            <h4>Skills</h4>
            @if(!count($prompt->skills))
                No skill increase.
            @else
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th width="70%">Skill</th>
                            <th width="30%">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prompt->skills as $skill)
                            <tr>
                                <td>{!! $skill->skill->displayName !!} 
                                    @if($skill->skill->parent)
                                    <br><span class="text-danger">This skill requires {!! $skill->skill->parent->displayname !!} level {{ $skill->skill->parent_level }} on all focus characters.</span>
                                    @endif
                                    @if($skill->skill->prerequisite)
                                    <br><span class="text-danger">This skill requires {!! $skill->skill->prerequisite->displayname !!} on all focus characters.</span>
                                    @endif
                                </td>
                                <td>{{ $skill->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            <hr>
            <h4>Stat & Level Rewards</h4>
            @if($prompt->expreward)
            <div class="row">
                <div class="col">
                    @if(!$prompt->expreward->user_exp && !$prompt->expreward->user_points)
                    No user rewards.
                    @else
                    {{ $prompt->expreward->user_exp ? $prompt->expreward->user_exp : 0  }} user EXP
                        <br>
                    {{ $prompt->expreward->user_points ? $prompt->expreward->user_points : 0  }} user points
                    @endif
                </div>
                <div class="col">
                    @if(!$prompt->expreward->chara_exp && !$prompt->expreward->chara_points)
                    No character rewards.
                    @else
                    {{ $prompt->expreward->chara_exp ? $prompt->expreward->chara_exp : 0  }} character EXP
                        <br>
                    {{ $prompt->expreward->chara_points ? $prompt->expreward->chara_points : 0  }} character points
                    @endif
                </div>
            </div>
            @else
                @if(Auth::check() && Auth::user()->isStaff)<div class="alert alert-warning">There is currently no EXP rewards in existance on this prompt. Please press "edit" in the prompt admin page to allow it to generate! Users will see a blank block until it is generated.</div>@endif
            @endif
        </div>
        <div class="text-right mt-1">
            @if($prompt->level_req)
            <p class="text-danger">This prompt requires you to be at least level {{ $prompt->level_req }}</p>
            @endif
            
            @if($prompt->children)
            <h4 class="mt-2">Unlocks</h4>
                @foreach($prompt->children as $children)
                    {!! $children->displayname !!}
                @endforeach
            @endif
        </div>
        <div class="text-right">
            @if($prompt->parent_id)
                <p class="text-success">You have unlocked this prompt by completing {!! $prompt->parent->displayName !!} {{ $prompt->parent_quantity }} {{ $prompt->parent_quantity > 1 ? 'times' : 'time'}}.</p>
                @endif
            @if($prompt->end_at && $prompt->end_at->isPast())
                <span class="text-secondary">This prompt has ended.</span>
@elseif($prompt->start_at && $prompt->start_at->isFuture())
<span class="text-secondary">This prompt is not open for submissions yet.</span>
@else
<a href="{{ url('submissions/new?prompt_id=' . $prompt->id) }}" class="btn btn-primary">Submit Prompt</a>
        @endunless
</div>
</div>
</div>

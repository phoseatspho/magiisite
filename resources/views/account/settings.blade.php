@extends('account.layout')

@section('account-title')
    Settings
@endsection

@section('account-content')
    {!! breadcrumbs(['My Account' => Auth::user()->url, 'Settings' => 'account/settings']) !!}

    <h1>Settings</h1>


    <div class="card p-3 mb-2">
        <h3>Avatar</h3>
        @if (Auth::user()->isStaff)
            <div class="alert alert-info">For admins - note that .GIF avatars leave a tmp file in the directory (e.g php2471.tmp). There is an automatic schedule to delete these files.
            </div>
        @endif
        {!! Form::open(['url' => 'account/avatar', 'files' => true]) !!}
        <div class="form-group row">
            {!! Form::label('avatar', 'Update Profile Image', ['class' => 'col-md-2 col-form-label']) !!}
            <div class="col-md-10">
                {!! Form::file('avatar', ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    <div class="card p-3 mb-2">
        <h3>Border</h3>
        <p>Change your onsite border. </p>
        {!! Form::open(['url' => 'account/border']) !!}
        <div class="form-group row">
            <label class="col-md-2 col-form-label">Border</label>
            <div class="col-md-10">
                {!! Form::select('border', $borders, Auth::user()->border_id, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}

        <h3 class="text-center">Your Borders</h3>
        @if ($default->count())
            <h4>Default</h4>
            <div class="row mb-3">
                @foreach ($default as $border)
                    <div class="class="col-md-3 col-6 mb-3 text-center">
                        <div class="shop-image">
                            {!! $border->preview() !!}
                        </div>
                        <div class="shop-name mt-1 text-center">
                            <h5>{!! $border->displayName !!}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        @if (Auth::user()->borders->count())
            <h4>Unlocked</h4>
            <div class="row mb-3">
                @foreach (Auth::user()->borders as $border)
                    <div class="class="col-md-3 col-6 mb-3 text-center">
                        <div class="shop-image">
                            {!! $border->preview() !!}
                        </div>
                        <div class="shop-name mt-1 text-center">
                            <h5>{!! $border->displayName !!}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        @if (Auth::user()->isStaff)
            @if ($admin->count())
                <h4>Staff-Only</h4>
                <small>You can see these as a member of staff</small>
                <div class="row mb-3">
                    @foreach ($admin as $border)
                        <div class="class="col-md-3 col-6 mb-3 text-center">
                            <div class="shop-image">
                                {!! $border->preview() !!}
                            </div>
                            <div class="shop-name mt-1 text-center">
                                <h5>{!! $border->displayName !!}</h5>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
        <div class="text-right mb-4">
            <a href="{{ url(Auth::user()->url . '/border-logs') }}">View logs...</a>
        </div>
    </div>

    @if (config('lorekeeper.settings.allow_username_changes'))
        <div class="card p-3 mb-2">
            <h3>Change Username</h3>
            @if (config('lorekeeper.settings.username_change_cooldown'))
                <div class="alert alert-info">
                    You can change your username once every {{ config('lorekeeper.settings.username_change_cooldown') }} days.
                </div>
                @if (Auth::user()->logs()->where('type', 'Username Change')->orderBy('created_at', 'desc')->first())
                    <div class="alert alert-warning">
                        You last changed your username on {{ Auth::user()->logs()->where('type', 'Username Change')->orderBy('created_at', 'desc')->first()->created_at->format('F jS, Y') }}.
                        <br />
                        <b>
                            You will be able to change your username again on
                            {{ Auth::user()->logs()->where('type', 'Username Change')->orderBy('created_at', 'desc')->first()->created_at->addDays(config('lorekeeper.settings.username_change_cooldown'))->format('F jS, Y') }}.
                        </b>
                    </div>
                @endif
            @endif
            {!! Form::open(['url' => 'account/username']) !!}
            <div class="form-group row">
                <label class="col-md-2 col-form-label">Username</label>
                <div class="col-md-10">
                    {!! Form::text('username', Auth::user()->name, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="text-right">
                {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    @endif

@if($user_enabled == 1 || (Auth::user()->isStaff && $user_enabled == 2))
<div class="card p-3 mb-2">
    <h3>Home Location <span class="text-muted">({{ ucfirst($location_interval) }})</span></h3>
    @if(Auth::user()->isStaff && $user_enabled == 2)
        <div class="alert alert-warning">You can edit this because you are a staff member. Normal users cannot edit their own locations freely.</div>
    @endif
    @if($char_enabled == 1)
        <div class="alert alert-warning">Your characters will have the same home as you.</div>
    @endif
    @if(Auth::user()->canChangeLocation)
        {!! Form::open(['url' => 'account/location']) !!}
            <div class="form-group row">
                <label class="col-md-2 col-form-label">Location</label>
                <div class="col-md-9">
                {!! Form::select('location', [0=>'Choose a Location'] + $locations, isset(Auth::user()->home_id) ? Auth::user()->home_id : 0, ['class' => 'form-control selectize']) !!}
                </div>
                <div class="col-md text-right">
                    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>
        {!! Form::close() !!}
    @else
        <div class="alert alert-warning">
        <strong>You can't change your location right now.</strong>
        You last changed it on {!! format_date(Auth::user()->home_changed, false) !!}.
        Home locations can be changed {{ $location_interval }}.
        </div>
    @endif
</div>
@endif

@if($user_faction_enabled == 1 || (Auth::user()->isStaff && $user_faction_enabled == 2))
<div class="card p-3 mb-2">
    <h3>Faction <span class="text-muted">({{ ucfirst($location_interval) }})</span></h3>
    @if(Auth::user()->isStaff && $user_faction_enabled == 2)
        <div class="alert alert-warning">You can edit this because you are a staff member. Normal users cannot edit their own faction freely.</div>
    @endif
    @if($char_faction_enabled == 1)
        <div class="alert alert-warning">Your characters will have the same faction as you.</div>
    @endif
    @if(Auth::user()->canChangeFaction)
        <p>Please note that changing your faction will remove you from any special ranks and reset your faction standing!</p>
        {!! Form::open(['url' => 'account/faction']) !!}
            <div class="form-group row">
                <label class="col-md-2 col-form-label">Faction</label>
                <div class="col-md-9">
                {!! Form::select('faction', [0=>'Choose a Faction'] + $factions, isset(Auth::user()->faction_id) ? Auth::user()->faction_id : 0, ['class' => 'form-control selectize']) !!}
                </div>
                <div class="col-md text-right">
                    {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
                </div>
            </div>
        {!! Form::close() !!}
    @else
        <div class="alert alert-warning">
        <strong>You can't change your faction right now.</strong>
        You last changed it on {!! format_date(Auth::user()->faction_changed, false) !!}.
        Faction can be changed {{ $location_interval }}.
        </div>
    @endif
</div>
@endif

    <div class="card p-3 mb-2">
        <h3>Profile</h3>
        {!! Form::open(['url' => 'account/profile']) !!}
        <div class="form-group">
            {!! Form::label('text', 'Profile Text') !!}
            {!! Form::textarea('text', Auth::user()->profile->text, ['class' => 'form-control wysiwyg']) !!}
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    @if(Auth::user()->isStaff)
    @include('widgets._staff_profile_form', ['user' => Auth::user(), 'adminView' => 0])
@endif

    <div class="card p-3 mb-2">
        <h3>Birthday Publicity</h3>
        {!! Form::open(['url' => 'account/dob']) !!}
        <div class="form-group row">
            <label class="col-md-2 col-form-label">Setting</label>
            <div class="col-md-10">
                {!! Form::select(
                    'birthday_setting',
                    ['0' => '0: No one can see your birthday.', '1' => '1: Members can see your day and month.', '2' => '2: Anyone can see your day and month.', '3' => '3: Full date public.'],
                    Auth::user()->settings->birthday_setting,
                    ['class' => 'form-control'],
                ) !!}
            </div>
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    <div class="card p-3 mb-2">
        <h3>Email Address</h3>
        <p>Changing your email address will require you to re-verify your email address.</p>
        {!! Form::open(['url' => 'account/email']) !!}
        <div class="form-group row">
            <label class="col-md-2 col-form-label">Email Address</label>
            <div class="col-md-10">
                {!! Form::text('email', Auth::user()->email, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    <div class="card p-3 mb-2">
        <h3>Change Password</h3>
        {!! Form::open(['url' => 'account/password']) !!}
        <div class="form-group row">
            <label class="col-md-2 col-form-label">Old Password</label>
            <div class="col-md-10">
                {!! Form::password('old_password', ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 col-form-label">New Password</label>
            <div class="col-md-10">
                {!! Form::password('new_password', ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="form-group row">
            <label class="col-md-2 col-form-label">Confirm New Password</label>
            <div class="col-md-10">
                {!! Form::password('new_password_confirmation', ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="text-right">
            {!! Form::submit('Edit', ['class' => 'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
    </div>

    <div class="card p-3 mb-2">
        <h3>Two-Factor Authentication</h3>

        <p>Two-factor authentication acts as a second layer of protection for your account. It uses an app on your phone-- such as Google Authenticator-- and information provided by the site to generate a random code that changes frequently.</p>

        <div class="alert alert-info">
            Please note that two-factor authentication is only used when logging in directly to the site (with an email address and password), and not when logging in via an off-site account. If you log in using an off-site account, consider enabling
            two-factor authentication on that site instead!
        </div>

        @if (!isset(Auth::user()->two_factor_secret))
            <p>In order to enable two-factor authentication, you will need to scan a QR code with an authenticator app on your phone. Two-factor authentication will not be enabled until you do so and confirm by entering one of the codes provided by your
                authentication app.</p>
            {!! Form::open(['url' => 'account/two-factor/enable']) !!}
            <div class="text-right">
                {!! Form::submit('Enable', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        @elseif(isset(Auth::user()->two_factor_secret))
            <p>Two-factor authentication is currently enabled.</p>

            <h4>Disable Two-Factor Authentication</h4>
            <p>To disable two-factor authentication, you must enter a code from your authenticator app.</p>
            {!! Form::open(['url' => 'account/two-factor/disable']) !!}
            <div class="form-group row">
                <label class="col-md-2 col-form-label">Code</label>
                <div class="col-md-10">
                    {!! Form::text('code', null, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="text-right">
                {!! Form::submit('Disable', ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        @endif
    </div>
@endsection

@section('scripts')
@parent
<script>
$( document ).ready(function() {
    $('.selectize').selectize();
});
@if(Auth::user()->isStaff)
        @include('js._website_links_js')
    @endif

</script>
@endsection

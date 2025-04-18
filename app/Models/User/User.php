<?php

namespace App\Models\User;

use Settings;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Config;
use Carbon\Carbon;

use App\Models\Character\Character;
use App\Models\Character\CharacterBookmark;
use App\Models\Character\CharacterImageCreator;
use App\Models\Comment\CommentLike;
use App\Models\Currency\Currency;
use App\Models\Currency\CurrencyLog;
use App\Models\Gallery\GalleryCollaborator;
use App\Models\Gallery\GalleryFavorite;
use App\Models\Gallery\GallerySubmission;
use App\Models\Border\Border;
use App\Models\User\UserBorder;
use App\Models\User\UserBorderLog;
use App\Models\Notification;
use App\Models\Rank\Rank;
use App\Models\Rank\RankPower;
use App\Traits\Commenter;
use App\Models\Item\ItemLog;
use App\Models\Stat\ExpLog;
use App\Models\Stat\StatTransferLog;
use App\Models\Level\LevelLog;
use App\Models\Pet\PetLog;
use App\Models\Claymore\GearLog;
use App\Models\Claymore\WeaponLog;
use App\Models\User\UserWeapon;
use App\Models\User\UserPet;
use App\Models\User\UserGear;
use App\Models\Shop\ShopLog;
use App\Models\Award\AwardLog;
use App\Models\User\UserCharacterLog;
use App\Models\Submission\Submission;
use App\Models\Submission\SubmissionCharacter;
use App\Models\WorldExpansion\FactionRank;
use App\Models\WorldExpansion\FactionRankMember;
use App\Models\Item\Item;

use App\Models\Collection\Collection;
use App\Models\User\UserCollection;
use App\Models\User\UserCollectionLog;

use App\Models\User\UserVolume;
use App\Models\Volume\Volume;
use App\Models\Volume\Book;

use App\Models\Character\CharacterDesignUpdate;
use App\Models\Character\CharacterTransfer;
use App\Models\Trade;
use App\Models\Recipe\Recipe;
use App\Models\User\UserRecipeLog;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail {
    use Commenter, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'alias', 'rank_id', 'email', 'email_verified_at', 'password', 'is_news_unread',  'is_dev_logs_unread', 'is_banned', 'has_alias', 'avatar', 'is_sales_unread', 'birthday',
        'referred_by', 'is_deactivated', 'deactivater_id', 'home_id', 'home_changed', 'faction_id', 'faction_changed', 'border_id', 
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday'          => 'datetime',
    ];

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    protected $dates = ['birthday', 'home_changed', 'faction_changed'];

    /**
     * Accessors to append to the model.
     *
     * @var array
     */
    protected $appends = [
        'verified_name',
    ];

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = true;


    /**********************************************************************************************

        RELATIONS

     **********************************************************************************************/

    /**
     * Get all of the user's update logs.
     */
    public function logs() {
        return $this->hasMany('App\Models\User\UserUpdateLog');
    }

    /**
     * Get user settings.
     */
    public function settings() {
        return $this->hasOne(UserSettings::class);
    }

    /**
     * Get user-editable profile data.
     */
    public function profile() {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Gets the account that deactivated this account.
     */
    public function deactivater() {
        return $this->belongsTo(self::class, 'deactivater_id');
    }

    /**
     * Get user settings.
     */
    public function level()
    {
        return $this->hasOne('App\Models\Level\UserLevel');
    }

    /** 
     * Get user staff profile data.
     */
    public function staffProfile()
    {
        return $this->hasOne('App\Models\User\StaffProfile');
    }


    /**
     * Get the user's aliases.
     */
    public function aliases() {
        return $this->hasMany(UserAlias::class);
    }

    /**
     * Get the user's primary alias.
     */
    public function primaryAlias() {
        return $this->hasOne(UserAlias::class)->where('is_primary_alias', 1);
    }

    /**
     * Get the user's notifications.
     */
    public function notifications() {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get all the user's characters, regardless of whether they are full characters of myo slots.
     */
    public function allCharacters() {
        return $this->hasMany(Character::class)->orderBy('sort', 'DESC');
    }

    /**
     * Get the user's characters.
     */
    public function characters() {
        return $this->hasMany(Character::class)->where('is_myo_slot', 0)->orderBy('sort', 'DESC');
    }

    /**
     * Get the user's MYO slots.
     */
    public function myoSlots() {
        return $this->hasMany(Character::class)->where('is_myo_slot', 1)->orderBy('id', 'DESC');
    }

    /**
     * Get the user's rank data.
     */
    public function rank() {
        return $this->belongsTo(Rank::class);
    }

    /**
     * Get the user's rank data.
     */
    public function home()
    {
        return $this->belongsTo('App\Models\WorldExpansion\Location', 'home_id');
    }

    /**
     * Get the user's rank data.
     */
    public function faction()
    {
        return $this->belongsTo('App\Models\WorldExpansion\Faction', 'faction_id');
    }

    /**
     * Get the user's items.
     */
    public function items() {
        return $this->belongsToMany(Item::class, 'user_items')->withPivot('count', 'data', 'updated_at', 'id')->whereNull('user_items.deleted_at');
    }

    /**
     * Get the user's items.
     */
    public function recipes()
    {
        return $this->belongsToMany('App\Models\Recipe\Recipe', 'user_recipes')->withPivot('id');
    }

    /**
     * Returns user's foraging stats
     */
    public function foraging()
    {
        return $this->hasOne('App\Models\User\UserForaging');
    }
    
    /** 
     * Get the user's awards.
     */
    public function awards()
    {
        return $this->belongsToMany('App\Models\Award\Award', 'user_awards')->withPivot('count', 'data', 'updated_at', 'id')->whereNull('user_awards.deleted_at');
    }

    /**
     * Get all of the user's gallery submissions.
     */
    public function gallerySubmissions() {
        return $this->hasMany(GallerySubmission::class)
            ->where('user_id', $this->id)
            ->orWhereIn('id', GalleryCollaborator::where('user_id', $this->id)->where('type', 'Collab')->pluck('gallery_submission_id')->toArray())
            ->visible($this)->accepted()->orderBy('created_at', 'DESC');
    }

    /**
     * Get all of the user's favorited gallery submissions.
     */
    public function galleryFavorites() {
        return $this->hasMany(GalleryFavorite::class)->where('user_id', $this->id);
    }

     /**
     * Get the user's pets.
     */
    public function pets()
     {
        return $this->belongsToMany('App\Models\Pet\Pet', 'user_pets')->withPivot('data', 'updated_at', 'id', 'variant_id', 'character_id', 'pet_name', 'has_image', 'evolution_id')->whereNull('user_pets.deleted_at');
    }

    /**
     * Get the user's weapons.
     */
    public function weapons()
    {
        return $this->belongsToMany('App\Models\Claymore\Weapon', 'user_weapons')->withPivot('data', 'updated_at', 'id', 'character_id', 'has_image')->whereNull('user_weapons.deleted_at');
    }

    /**
     * Get the user's gears.
     */
    public function gears()
    {
        return $this->belongsToMany('App\Models\Claymore\Gear', 'user_gears')->withPivot('data', 'updated_at', 'id', 'character_id', 'has_image')->whereNull('user_gears.deleted_at');
    }
    
    /**
     * Get all of the user's character bookmarks.
     */
    public function bookmarks() {
        return $this->hasMany(CharacterBookmark::class)->where('user_id', $this->id);
    }

    /**
     * Get the user's current discord chat level.
     */
    public function discord()
    {
        return $this->belongsTo('App\Models\User\UserDiscordLevel', 'user_id');
    }

    /**
     * Gets all of a user's liked / disliked comments.
     */
    public function commentLikes() {
        return $this->hasMany(CommentLike::class);
    }

     /**
     * Get all of the user's wishlists.
     */
    public function wishlists()
    {
        return $this->hasMany('App\Models\User\Wishlist')->where('user_id', $this->id);
    }

    /**
     * Get user's unlocked borders.
     */
    public function borders() {
        return $this->belongsToMany('App\Models\Border\Border', 'user_borders')->withPivot('id');
    }

    /**
     * Get the border associated with this user.
     */
    public function border() 
    {
        return $this->belongsTo('App\Models\Border\Border', 'border_id');
    }

    /**
     * Get the user's areas.
     */
    public function areas()
    {
        return $this->belongsToMany('App\Models\Cultivation\CultivationArea', 'user_area', 'user_id', 'area_id');
    }


    /**********************************************************************************************

        SCOPES

     **********************************************************************************************/

    /**
     * Scope a query to only include visible (non-banned) users.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVisible($query) {
        return $query->where('is_banned', 0)->where('is_deactivated', 0);
    }

    /**
     * Scope a query to only show deactivated accounts.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisabled($query) {
        return $query->where('is_deactivated', 1);
    }

    /**
     * Scope a query based on the user's primary alias.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $reverse
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAliasSort($query, $reverse = false) {
        return $query->leftJoin('user_aliases', 'users.id', '=', 'user_aliases.user_id')
            ->orderByRaw('user_aliases.alias IS NULL ASC, user_aliases.alias '.($reverse ? 'DESC' : 'ASC'));
    }

    /**********************************************************************************************

        ACCESSORS

     **********************************************************************************************/

    /**
     * Get the user's alias.
     *
     * @return string
     */
    public function getVerifiedNameAttribute() {
        return $this->name.($this->hasAlias ? '' : ' (Unverified)');
    }

    /**
     * Checks if the user has an alias (has an associated dA account).
     *
     * @return bool
     */
    public function getHasAliasAttribute() {
        if (!config('lorekeeper.settings.require_alias')) {
            return true;
        }

        return $this->attributes['has_alias'];
    }

    /**
     * Checks if the user has an admin rank.
     *
     * @return bool
     */
    public function getIsAdminAttribute() {
        return $this->rank->isAdmin;
    }

    /**
     * Checks if the user is a staff member with powers.
     *
     * @return bool
     */
    public function getIsStaffAttribute() {
        return RankPower::where('rank_id', $this->rank_id)->exists() || $this->isAdmin;
    }

    /**
     * Checks if the user has the given power.
     *
     * @param mixed $power
     *
     * @return bool
     */
    public function hasPower($power) {
        return $this->rank->hasPower($power);
    }

    /**
     * Gets the powers associated with the user's rank.
     *
     * @return array
     */
    public function getPowers() {
        return $this->rank->getPowers();
    }

    /**
     * Gets the user's profile URL.
     *
     * @return string
     */
    public function getUrlAttribute() {
        return url('user/'.$this->name);
    }

    /**
     * Gets the URL for editing the user in the admin panel.
     *
     * @return string
     */
    public function getAdminUrlAttribute() {
        return url('admin/users/'.$this->name.'/edit');
    }

    /**
     * Displays the user's name, linked to their profile page.
     *
     * @return string
     */
    public function getDisplayNameAttribute() {
        return ($this->is_banned ? '<strike>' : '').'<a href="'.$this->url.'" class="display-user" style="'.($this->rank->color ? 'color: #'.$this->rank->color.';' : '').($this->is_deactivated ? 'opacity: 0.5;' : '').'"><i class="'.($this->rank->icon ? $this->rank->icon : 'fas fa-user').' mr-1" style="opacity: 50%;"></i>'.$this->name.'</a>'.($this->is_banned ? '</strike>' : '');
    }

    /**
     * Gets the user's last username change.
     *
     * @return string
     */
    public function getPreviousUsernameAttribute() {
        // get highest id
        $log = $this->logs()->whereIn('type', ['Username Changed', 'Name/Rank Change'])->orderBy('id', 'DESC')->first();
        if (!$log) {
            return null;
        }

        return $log->data['old_name'];
    }

    /**
     * Displays the user's name, linked to their profile page.
     *
     * @return string
     */
    public function getCommentDisplayNameAttribute() {
        return ($this->is_banned ? '<strike>' : '').'<small><a href="'.$this->url.'" class="btn btn-primary btn-sm"'.($this->rank->color ? 'style="background-color: #'.$this->rank->color.'!important;color:#000!important;' : '').($this->is_deactivated ? 'opacity: 0.5;' : '').'"><i class="'.($this->rank->icon ? $this->rank->icon : 'fas fa-user').' mr-1" style="opacity: 50%;"></i>'.$this->name.'</a></small>'.($this->is_banned ? '</strike>' : '');
    }

    /**
     * Displays the user's primary alias.
     *
     * @return string
     */
    public function getDisplayAliasAttribute() {
        if (!config('lorekeeper.settings.require_alias') && !$this->attributes['has_alias']) {
            return '(No Alias)';
        }
        if (!$this->hasAlias) {
            return '(Unverified)';
        }

        return $this->primaryAlias->displayAlias;
    }

    /**
     * Displays the user's avatar.
     *
     * @return string
     */
    public function getAvatar() {
        return $this->avatar;
    }

    /**
     * Gets the display URL for a user's avatar, or the default avatar if they don't have one.
     *
     * @return url
     */
    public function getAvatarUrlAttribute() {
        if ($this->avatar == 'default.jpg' && config('lorekeeper.extensions.use_gravatar')) {
            // check if a gravatar exists
            $hash = md5(strtolower(trim($this->email)));
            $url = 'https://www.gravatar.com/avatar/'.$hash.'??d=mm&s=200';
            $headers = @get_headers($url);

            if (!preg_match('|200|', $headers[0])) {
                return url('images/avatars/default.jpg');
            } else {
                return 'https://www.gravatar.com/avatar/'.$hash.'?d=mm&s=200';
            }
        }

        return url('images/avatars/'.$this->avatar.'?v='.filemtime(public_path('images/avatars/'.$this->avatar)));
    }

    /**
     * Gets the user's log type for log creation.
     *
     * @return string
     */
    public function getLogTypeAttribute() {
        return 'User';
    }

     /**
     * Checks if the user can change location.
     *
     * @return string
     */
    public function getCanChangeLocationAttribute()
    {
        if(!isset($this->home_changed)) return true;
        $limit = Settings::get('WE_change_timelimit');
        switch($limit){
            case 0:
                return true;
            case 1:
                // Yearly
                if(now()->year == $this->home_changed->year) return false;
                else return true;

            case 2:
                // Quarterly
                if(now()->year != $this->home_changed->year) return true;
                if(now()->quarter != $this->home_changed->quarter) return true;
                else return false;

            case 3:
                // Monthly
                if(now()->year != $this->home_changed->year) return true;
                if(now()->month != $this->home_changed->month) return true;
                else return false;

            case 4:
                // Weekly
                if(now()->year != $this->home_changed->year) return true;
                if(now()->week != $this->home_changed->week) return true;
                else return false;

            case 5:
                // Daily
                if(now()->year != $this->home_changed->year) return true;
                if(now()->month != $this->home_changed->month) return true;
                if(now()->day != $this->home_changed->day) return true;
                else return false;

            default:
                return true;
        }
    }

    /**
     * Get's user birthday setting.
     */
    public function getBirthdayDisplayAttribute() {
        //
        $icon = null;
        $bday = $this->birthday;
        if (!isset($bday)) {
            return 'N/A';
        }

        if ($bday->format('d M') == Carbon::now()->format('d M')) {
            $icon = '<i class="fas fa-birthday-cake ml-1"></i>';
        }
        //
        switch ($this->settings->birthday_setting) {
            case 0:
                return null;
                break;
            case 1:
                if (Auth::check()) {
                    return $bday->format('d M').$icon;
                }
                break;
            case 2:
                return $bday->format('d M').$icon;
                break;
            case 3:
                return $bday->format('d M Y').$icon;
                break;
        }
    }

    /**
     * Check if user is of age.
     */
    public function getcheckBirthdayAttribute() {
        $bday = $this->birthday;
        if (!$bday || $bday->diffInYears(carbon::now()) < 13) {
            return false;
        } else {
            return true;
        }
    }

     /**
     * Get the user's completed collections.
     */
    public function collections()
    {
        return $this->belongsToMany('App\Models\Collection\Collection', 'user_collections')->withPivot('id');
    }

    public function getIncompletedCollectionsAttribute()
    { 
        return Collection::visible()->whereNotIn('id', UserCollection::where('user_id',$this->id)->pluck('collection_id')->unique());

    }

     /**
     * Checks if the user can change faction.
     *
     * @return string
     */
    public function getCanChangeFactionAttribute()
    {
        if(!isset($this->faction_changed)) return true;
        $limit = Settings::get('WE_change_timelimit');
        switch($limit){
            case 0:
                return true;
            case 1:
                // Yearly
                if(now()->year == $this->faction_changed->year) return false;
                else return true;

            case 2:
                // Quarterly
                if(now()->year != $this->faction_changed->year) return true;
                if(now()->quarter != $this->faction_changed->quarter) return true;
                else return false;

            case 3:
                // Monthly
                if(now()->year != $this->faction_changed->year) return true;
                if(now()->month != $this->faction_changed->month) return true;
                else return false;

            case 4:
                // Weekly
                if(now()->year != $this->faction_changed->year) return true;
                if(now()->week != $this->faction_changed->week) return true;
                else return false;

            case 5:
                // Daily
                if(now()->year != $this->faction_changed->year) return true;
                if(now()->month != $this->faction_changed->month) return true;
                if(now()->day != $this->faction_changed->day) return true;
                else return false;

            default:
                return true;
        }
    }

    /**
     * Get user's faction rank.
     */
    public function getFactionRankAttribute()
    {
        if(!isset($this->faction_id) || !$this->faction->ranks()->count()) return null;
        if(FactionRankMember::where('member_type', 'user')->where('member_id', $this->id)->first()) return FactionRankMember::where('member_type', 'user')->where('member_id', $this->id)->first()->rank;
        if($this->faction->ranks()->where('is_open', 1)->count()) {
            $standing = $this->getCurrencies(true)->where('id', Settings::get('WE_faction_currency'))->first();
            if(!$standing) return $this->faction->ranks()->where('is_open', 1)->where('breakpoint', 0)->first();
            return $this->faction->ranks()->where('is_open', 1)->where('breakpoint', '<=', $standing->quantity)->orderBy('breakpoint', 'DESC')->first();
        }
    }



 
    /**********************************************************************************************

        OTHER FUNCTIONS

     **********************************************************************************************/

    /**
     * Checks if the user can edit the given rank.
     *
     * @param mixed $rank
     *
     * @return bool
     */
    public function canEditRank($rank) {
        return $this->rank->canEditRank($rank);
    }

    /**
     * Get the user's held currencies.
     *
     * @param bool $showAll
     *
     * @return \Illuminate\Support\Collection
     */
    public function getCurrencies($showAll = false) {
        // Get a list of currencies that need to be displayed
        // On profile: only ones marked is_displayed
        // In bank: ones marked is_displayed + the ones the user has

        $owned = UserCurrency::where('user_id', $this->id)->pluck('quantity', 'currency_id')->toArray();

        $currencies = Currency::where('is_user_owned', 1);
        if ($showAll) {
            $currencies->where(function ($query) use ($owned) {
                $query->where('is_displayed', 1)->orWhereIn('id', array_keys($owned));
            });
        } else {
            $currencies = $currencies->where('is_displayed', 1);
        }

        $currencies = $currencies->orderBy('sort_user', 'DESC')->get();

        foreach ($currencies as $currency) {
            $currency->quantity = $owned[$currency->id] ?? 0;
        }

        return $currencies;
    }

    /**
     * Get the user's held currencies as an array for select inputs.
     *
     * @param mixed $isTransferrable
     *
     * @return array
     */
    public function getCurrencySelect($isTransferrable = false) {
        $query = UserCurrency::query()->where('user_id', $this->id)->leftJoin('currencies', 'user_currencies.currency_id', '=', 'currencies.id')->orderBy('currencies.sort_user', 'DESC');
        if ($isTransferrable) {
            $query->where('currencies.allow_user_to_user', 1);
        }

        return $query->get()->pluck('name_with_quantity', 'currency_id')->toArray();
    }

    /**
     * Get the user's currency logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getCurrencyLogs($limit = 10) {
        $user = $this;
        $query = CurrencyLog::with('currency')->where(function ($query) use ($user) {
            $query->with('sender')->where('sender_type', 'User')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards', 'Gallery Submission Reward']);
        })->orWhere(function ($query) use ($user) {
            $query->with('recipient')->where('recipient_type', 'User')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Get the user's exp logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getExpLogs($limit = 10)
    {
        $user = $this;
        $query = ExpLog::where(function($query) use ($user) {
            $query->with('sender')->where('sender_type', 'User')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function($query) use ($user) {
            $query->with('recipient')->where('recipient_type', 'User')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's stat logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getStatLogs($limit = 10)
    {
        $user = $this;
        $query = StatTransferLog::where(function($query) use ($user) {
            $query->with('sender')->where('sender_type', 'User')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function($query) use ($user) {
            $query->with('recipient')->where('recipient_type', 'User')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's level logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getLevelLogs($limit = 10)
    {
        $user = $this;
        $query = LevelLog::where(function($query) use ($user) {
            $query->with('recipient')->where('leveller_type', 'User')->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's item logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getItemLogs($limit = 10) {
        $user = $this;
        $query = ItemLog::with('item')->where(function ($query) use ($user) {
            $query->with('sender')->where('sender_type', 'User')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function ($query) use ($user) {
            $query->with('recipient')->where('recipient_type', 'User')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }
        /**
     * Get the user's award logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getAwardLogs($limit = 10)
    {
        $user = $this;
        $query = AwardLog::with('award')->where(function($query) use ($user) {
            $query->with('sender')->where('sender_type', 'User')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function($query) use ($user) {
            $query->with('recipient')->where('recipient_type', 'User')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's pet logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getPetLogs($limit = 10) {
        $user = $this;
        $query = PetLog::with('sender')->with('recipient')->with('pet')->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Staff Removal']);
        })->orWhere(function ($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Get the user's weapon logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getWeaponLogs($limit = 10)
    {
        $user = $this;
        $query = WeaponLog::with('sender')->with('recipient')->with('weapon')->where(function($query) use ($user) {
            $query->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Staff Removal']);
        })->orWhere(function($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's gear logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getGearLogs($limit = 10)
    {
        $user = $this;
        $query = GearLog::with('sender')->with('recipient')->with('gear')->where(function($query) use ($user) {
            $query->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Staff Removal']);
        })->orWhere(function($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's recipe logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getRecipeLogs($limit = 10)
    {
        $user = $this;
        $query = UserRecipeLog::with('recipe')->where(function($query) use ($user) {
            $query->with('sender')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function($query) use ($user) {
            $query->with('recipient')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Get the user's shop purchase logs.
     *
     * @param int $limit
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
     */
    public function getShopLogs($limit = 10) {
        $user = $this;
        $query = ShopLog::where('user_id', $this->id)->with('character')->with('shop')->with('item')->with('currency')->orderBy('id', 'DESC');
        if ($limit) {
            return $query->take($limit)->get();
        } else {
            return $query->paginate(30);
        }
    }

    /**
     * Get the user's character ownership logs.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getOwnershipLogs() {
        $user = $this;
        $query = UserCharacterLog::with('sender.rank')->with('recipient.rank')->with('character')->where(function ($query) use ($user) {
            $query->where('sender_id', $user->id)->whereNotIn('log_type', ['Character Created', 'MYO Slot Created', 'Character Design Updated', 'MYO Design Approved']);
        })->orWhere(function ($query) use ($user) {
            $query->where('recipient_id', $user->id);
        })->orderBy('id', 'DESC');

        return $query->paginate(30);
    }

    /**
     * Checks if there are characters credited to the user's alias and updates ownership to their account accordingly.
     */
    public function updateCharacters() {
        if (!$this->attributes['has_alias']) {
            return;
        }

        // Pluck alias from url and check for matches
        $urlCharacters = Character::whereNotNull('owner_url')->pluck('owner_url', 'id');
        $matches = [];
        $count = 0;
        foreach ($this->aliases as $alias) {
            // Find all urls from the same site as this alias
            foreach ($urlCharacters as $key=> $character) {
                preg_match_all(config('lorekeeper.sites.'.$alias->site.'.regex'), $character, $matches[$key]);
            }
            // Find all alias matches within those, and update the character's owner
            foreach ($matches as $key=> $match) {
                if ($match[1] != [] && strtolower($match[1][0]) == strtolower($alias->alias)) {
                    Character::find($key)->update(['owner_url' => null, 'user_id' => $this->id]);
                    $count += 1;
                }
            }
        }

        //
        if ($count > 0) {
            $this->settings->is_fto = 0;
        }
        $this->settings->save();
    }

    /**
     * Checks if there are art or design credits credited to the user's alias and credits them to their account accordingly.
     */
    public function updateArtDesignCredits() {
        if (!$this->attributes['has_alias']) {
            return;
        }

        // Pluck alias from url and check for matches
        $urlCreators = CharacterImageCreator::whereNotNull('url')->pluck('url', 'id');
        $matches = [];
        foreach ($this->aliases as $alias) {
            // Find all urls from the same site as this alias
            foreach ($urlCreators as $key=> $creator) {
                preg_match_all(config('lorekeeper.sites.'.$alias->site.'.regex'), $creator, $matches[$key]);
            }
            // Find all alias matches within those, and update the relevant CharacterImageCreator
            foreach ($matches as $key=> $match) {
                if ($match[1] != [] && strtolower($match[1][0]) == strtolower($alias->alias)) {
                    CharacterImageCreator::find($key)->update(['url' => null, 'user_id' => $this->id]);
                }
            }
        }
    }

    /**
     * Get the user's submissions.
     *
     * @param mixed|null $user
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getSubmissions($user = null) {
        return Submission::with('user')->with('prompt')->viewable($user ? $user : null)->where('user_id', $this->id)->orderBy('id', 'DESC')->paginate(30);
    }

    /**
     * Checks if the user has bookmarked a character.
     * Returns the bookmark if one exists.
     *
     * @param mixed $character
     *
     * @return \App\Models\Character\CharacterBookmark
     */
    public function hasBookmarked($character) {
        return CharacterBookmark::where('user_id', $this->id)->where('character_id', $character->id)->first();
    }

     /**
     * Checks if the user has the named recipe
     *
     * @return bool
     */
    public function hasRecipe($recipe_id)
    {
        $recipe = Recipe::find($recipe_id);
        $user_has = $this->recipes->contains($recipe);
        $default = !$recipe->needs_unlocking;
        return $default ? true : $user_has;
    }


    /**
     * Returned recipes listed that are owned
     * Reversal simply
     *
     * @return object
     */
    public function ownedRecipes($ids, $reverse = false)
    {
        $recipes = Recipe::find($ids); $recipeCollection = [];
        foreach($recipes as $recipe)
        {
            if($reverse) {
                if(!$this->recipes->contains($recipe)) $recipeCollection[] = $recipe;
            }
            else {
                if($this->recipes->contains($recipe)) $recipeCollection[] = $recipe;
            }
        }
        return $recipeCollection;
    }


    /**
     * Get the user's collection logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getCollectionLogs($limit = 10)
    {
        $user = $this;
        $query = UserCollectionLog::with('collection')->where(function($query) use ($user) {
            $query->with('sender')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function($query) use ($user) {
            $query->with('recipient')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

     /**
     * Checks if the user has the named collection
     *
     * @return bool
     */
    public function hasCollection($collection_id)
    {
        $collection = Collection::find($collection_id);
        $user_has = $this->collections->contains($collection);
        return $user_has;
    }
    

    /**
     * Returned collections listed that are completed
     * Reversal simply
     *
     * @return object
     */
    public function ownedCollections($ids, $reverse = false)
    {
        $collections = Collection::find($ids); $collectionCollection = [];
        foreach($collections as $collection)
        {
            if($reverse) {
                if(!$this->collections->contains($collection)) $collectionCollection[] = $collection;
            }
            else {
                if($this->collections->contains($collection)) $collectionCollection[] = $collection;
            }
        }
        return $collectionCollection;
    }

 /**
     * Get the user's redeem logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getRedeemLogs($limit = 10)
    {
        $user = $this;
        $query = UserPrizeLog::with('prize')->where('user_id', $user->id)->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

    /**
     * Check if user completed the fetch
     *
     * @return int
     */
    public function getFetchCooldownAttribute()
    {
        // Fetch log for most recent collection
        $log = ItemLog::where('sender_id', $this->id)->where('log_type', 'Turned in for Fetch Quest')->orderBy('id', 'DESC')->first();
        // If there is no log, by default, the cooldown is null
        if(!$log) return null;
        // If the cooldown would already be up, it is null
        if($log->created_at->addMinutes(60) <= Carbon::now()) return null;
        // Otherwise, calculate the remaining time
        return $log->created_at->addMinutes(60);
        return null;
    }

     /**
     * Get the user's owned volumes.
     */
    public function volumes()
    {
        return $this->belongsToMany('App\Models\Volume\Volume', 'user_volumes')->withPivot('id');
    }

    /**
     * Get the user's volume logs.
     *
     * @param  int  $limit
     * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
     */
    public function getVolumeLogs($limit = 10)
    {
        $user = $this;
        $query = UserVolumeLog::with('volume')->where(function($query) use ($user) {
            $query->with('sender')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
        })->orWhere(function($query) use ($user) {
            $query->with('recipient')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
        })->orderBy('id', 'DESC');
        if($limit) return $query->take($limit)->get();
        else return $query->paginate(30);
    }

     /**
     * Checks if the user has the named volume
     *
     * @return bool
     */
    public function hasVolume($volume_id)
    {
        $volume = Volume::find($volume_id);
        $user_has = $this->volumes->contains($volume);
        return $user_has;
    }


    /**
     * Returned volume listed that are owned
     * Reversal simply
     *
     * @return object
     */
    public function ownedVolumes($ids, $reverse = false)
    {
        $volumes = Volume::find($ids); $ownedvolumes = [];
        foreach($volumes as $volume)
        {
            if($reverse) {
                if(!$this->volumes->contains($volume)) $ownedvolumes[] = $volume;
            }
            else {
                if($this->volumes->contains($volume)) $ownedvolumes[] = $volume;
            }
        }
        return $ownedvolumes;
    }

    /**
    * Get the user's border logs.
    *
    * @param  int  $limit
    * @return \Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator
    */
   public function getBorderLogs($limit = 10)
   {
       $user = $this;
       $query = UserBorderLog::with('border')->where(function($query) use ($user) {
           $query->with('sender')->where('sender_id', $user->id)->whereNotIn('log_type', ['Staff Grant', 'Prompt Rewards', 'Claim Rewards']);
       })->orWhere(function($query) use ($user) {
           $query->with('recipient')->where('recipient_id', $user->id)->where('log_type', '!=', 'Staff Removal');
       })->orderBy('id', 'DESC');
       if($limit) return $query->take($limit)->get();
       else return $query->paginate(30);
   }

  /**
    * Checks if the user has the named border
    *
    * @return bool
    */
   public function hasBorder($border_id)
   {
       $border = Border::find($border_id);
       $user_has = $this->borders->contains($border);
       $default = $border->is_default;
       return $default ? true : $user_has;
   }


   /**
    * display the user's icon and border styling
    *
    */
   public function getUserBorderAttribute()
   {
       //basically just an ugly ass string of html for copypasting use
       //would you want to keep posting this everywhere? yeah i thought so. me neither
       //there's probably a less hellish way to do this but it beats having to paste this over everywhere... EVERY SINGLE TIME.
       //especially with the checks

       //if the user has a border, we apply it
       if ($this->border_id) {
           //but first check the frame style

           //under style
           if ($this->border->border_style) {
               return '<div style="width:125px; height:125px; float:left; border-radius:50%; margin-right:25px;">
                   <!-- avatar -->
                   <img class="avatar" src="' .
                   $this->avatarUrl .
                   '" style="position: absolute; border-radius:50%; width:125px; height:125px;" alt="' .
                   $this->name .
                   '">
                   <!-- frame -->
                   <img src="' .
                   $this->border->imageUrl .
                   '" style="position: absolute;width:125px; height:125px;"  alt="avatar frame">
               </div>';

               //then over style
           } else {
               return '<div style="width:125px; height:125px; float:left; border-radius:50%; margin-right:25px;">
                   <!-- frame -->
                   <img src="' .
                   $this->border->imageUrl .
                   '" style="position: absolute;width:125px; height:125px;"  alt="avatar frame">
                   <!-- avatar -->
                   <img class="avatar" src="' .
                   $this->avatarUrl .
                   '" style="position: absolute; border-radius:50%; width:125px; height:125px;" alt="' .
                   $this->name .
                   '">
               </div>';
           }
           //if no border return standard avatar style
       } else {
           return '<div style="width:125px; height:125px; float:left; border-radius:50%; margin-right:25px;">
       <img src="' .
               $this->avatarUrl .
               '" style="position: absolute; border-radius:50%; width:125px; height:125px;" alt="' .
               $this->name .
               '"> </div>';
       }
    }
}
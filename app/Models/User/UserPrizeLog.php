<?php

namespace App\Models\User;

use Config;
use DB;
use Carbon\Carbon;
use App\Models\Model;

class UserPrizeLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prize_id', 'user_id', 'claimed_at'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_prize_logs';

    /**
     * Dates on the model to convert to Carbon instances.
     *
     * @var array
     */
    public $dates = ['claimed_at'];

    /**********************************************************************************************
        SCOPES
    **********************************************************************************************/

    /**********************************************************************************************
        RELATIONS
    **********************************************************************************************/

    /**
     * Get the participating user.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User\User', 'user_id');
    }

    /**
     * Get the advent calendar being participated in.
     */
    public function prize()
    {
        return $this->belongsTo('App\Models\PrizeCode', 'prize_id');
    }

    /**********************************************************************************************
        ACCESSORS
    **********************************************************************************************/

    /**
     * Get the item data that will be added to the stack as a record of its source.
     *
     * @return string
     */
    public function getItemDataAttribute()
    {
        return $this->user->displayName.'Redeemed '.$this->prize->name. '.';
    }

}
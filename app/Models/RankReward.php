<?php

namespace App\Models;

use App\Models\Model;
use App\Traits\Commentable;

class RankReward extends Model
{
    use Commentable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','rank_id', 'data', 'is_active', 'reward_time'];

    /**
     * Validation rules for rank rewards.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required',
        'rank_id' => 'required',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rank_rewards';

    /**********************************************************************************************

    RELATIONS

     **********************************************************************************************/

    /**
     * Get the user's rank data.
     */
    public function rank()
    {
        return $this->belongsTo('App\Models\Rank\Rank', 'rank_id');
    }

    /**
     * Gets the decoded output json
     *
     * @return array
     */
    public function getRewardsAttribute()
    {
        $rewards = [];
        if ($this->data) {
            $assets = $this->getRewardItemsAttribute();

            foreach ($assets as $type => $a) {
                $class = getAssetModelString($type, false);
                foreach ($a as $id => $asset) {
                    $rewards[] = (object) [
                        'rewardable_type' => $class,
                        'rewardable_id' => $id,
                        'quantity' => $asset['quantity'],
                    ];
                }
            }
        }
        return $rewards;
    }

    /**
     * Interprets the json output and retrieves the corresponding items
     *
     * @return array
     */
    public function getRewardItemsAttribute()
    {
        return parseAssetData(json_decode($this->data, true));
    }

}

<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Relations\Pivot;

class WishlistItem extends Pivot
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wishlist_id', 'item_id', 'count'
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_wishlist_items';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Whether the model contains timestamps to be saved and updated.
     *
     * @var string
     */
    public $timestamps = false;

    /**********************************************************************************************

        RELATIONS

    **********************************************************************************************/

    /**
     * Get the wishlist this item belongs to.
     */
    public function wishlist()
    {
        return $this->belongsTo('App\Models\User\UserWishlist', 'wishlist_id');
    }

    /**
     * Get the corresponding item.
     */
    public function item()
    {
        return $this->belongsTo('App\Models\Item\Item');
    }

}

<?php namespace App\Services;

use DB;

use App\Services\Service;

use App\Models\User\Wishlist;
use App\Models\User\WishlistItem;
use App\Models\Item\Item;

class WishlistManager extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Wishlist Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation, modification and usage of user wishlists.
    |
    */

    /**
     * Create a wishlist.
     *
     * @param  array                     $data
     * @param  \App\Models\User\User     $user
     * @return \App\Models\User\Wishlist|bool
     */
    public function createWishlist($data, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the user does not already have a wishlist with this name
            if(Wishlist::where('user_id', $user->id)->where('name', $data['name'])->exists()) throw new \Exception('You have already created a wishlist with this name.');

            // Create the wishlist
            $wishlist = Wishlist::create([
                'user_id' => $user->id,
                'name' => $data['name']
            ]);

            return $this->commitReturn($wishlist);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a wishlist.
     *
     * @param  array                     $data
     * @param  \App\Models\User\Wishlist $wishlist
     * @param  \App\Models\User\User     $user
     * @return \App\Models\User\Wishlist|bool
     */
    public function updateWishlist($data, $wishlist, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the wishlist exists and the user can edit it/it belongs to them
            if(!$wishlist) throw new \Exception('Invalid wishlist.');
            if($wishlist->user_id != $user->id) throw new \Exception('This wishlist does not belong to you.');
            // Check that the user does not already have a wishlist with this name
            if(Wishlist::where('user_id', $user->id)->where('name', $data['name'])->exists()) throw new \Exception('You have already created a wishlist with this name.');

            // Update the wishlist
            $wishlist->update($data);

            return $this->commitReturn($wishlist);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Delete a wishlist.
     *
     * @param  \App\Models\User\Wishlist $wishlist
     * @param  \App\Models\User\User     $user
     * @return bool
     */
    public function deleteWishlist($wishlist, $user)
    {
        DB::beginTransaction();

        try {
            // Check that the wishlist exists and the user can edit it/it belongs to them
            if(!$wishlist) throw new \Exception('Invalid wishlist.');
            if($wishlist->user_id != $user->id) throw new \Exception('This wishlist does not belong to you.');

            // Delete all items in the wishlist

            // Then delete the wishlist itself
            $wishlist->delete();

            return $this->commitReturn(true);
        } catch(\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

}

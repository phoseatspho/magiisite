<?php namespace App\Services;

use App\Models\Item\Item;
use App\Models\RankReward;
use App\Models\Rank\Rank;
use App\Services\Service;
use DB;

class RankRewardService extends Service
{
    /*
    |--------------------------------------------------------------------------
    | Item Service
    |--------------------------------------------------------------------------
    |
    | Handles the creation and editing of item categories and items.
    |
     */

    /**********************************************************************************************

    ITEM CATEGORIES

     **********************************************************************************************/

    /**
     * Create a rank reward.
     *
     * @param  array                 $data
     * @param  \App\Models\User\User $user
     * @return \App\Models\Item\RankReward|bool
     */
    public function createRankReward($data, $user)
    {
        DB::beginTransaction();

        try {
            if (!isset($data['reward_time'])) {
                $data['reward_time'] = 2;
            }

            // More specific validation
            if ((isset($data['rank_id']) && $data['rank_id']) && !Rank::where('id', $data['rank_id'])->exists()) {
                throw new \Exception("The selected rank is invalid.");
            }

            if(!isset($data['rewardable_type'])) throw new \Exception('Please add a reward.');

            if(!isset($data['is_active'])) $data['is_active'] = 0;

            $rankreward = RankReward::create($data);
            $rankreward->data = $this->populateRewards($data);
            $rankreward->save();

            return $this->commitReturn($rankreward);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Update a rank reward.
     *
     * @param  \App\Models\Item\RankReward  $rankreward
     * @param  array                          $data
     * @param  \App\Models\User\User          $user
     * @return \App\Models\Item\RankReward|bool
     */
    public function updateRankReward($rankreward, $data, $user)
    {
        DB::beginTransaction();

        try {
            if (!isset($data['reward_time'])) {
                $data['reward_time'] = 2;
            }

            // More specific validation
            if ((isset($data['rank_id']) && $data['rank_id']) && !Rank::where('id', $data['rank_id'])->exists()) {
                throw new \Exception("The selected rank is invalid.");
            }

            if(!isset($data['rewardable_type'])) throw new \Exception('Please add a reward.');

            if(!isset($data['is_active'])) $data['is_active'] = 0;

            $rankreward->update($data);
            $rankreward->data = $this->populateRewards($data);
            $rankreward->save();

            return $this->commitReturn($rankreward);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Delete a rank reward.
     *
     * @param  \App\Models\Item\RankReward  $rankreward
     * @return bool
     */
    public function deleteRankReward($rankreward)
    {
        DB::beginTransaction();

        try {

            $rankreward->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }
        return $this->rollbackReturn(false);
    }

    /**
     * Creates the assets json from rewards
     *
     * @param  \App\Models\Recipe\Recipe   $recipe
     * @param  array                       $data
     */
    private function populateRewards($data)
    {
        if (isset($data['rewardable_type'])) {
            // The data will be stored as an asset table, json_encode()d.
            // First build the asset table, then prepare it for storage.
            $assets = createAssetsArray();
            foreach ($data['rewardable_type'] as $key => $r) {
                switch ($r) {
                    case 'Item':
                        $type = 'App\Models\Item\Item';
                        break;
                    case 'Currency':
                        $type = 'App\Models\Currency\Currency';
                        break;
                    case 'LootTable':
                        $type = 'App\Models\Loot\LootTable';
                        break;
                    case 'Raffle':
                        $type = 'App\Models\Raffle\Raffle';
                        break;
                }
                $asset = $type::find($data['rewardable_id'][$key]);
                addAsset($assets, $asset, $data['quantity'][$key]);
            }

            return getDataReadyAssets($assets);
        }
        return null;
    }

}
